<?php

namespace App\Http\Controllers\TestSeries;

use App\Http\Controllers\Controller;
use App\Models\Setup\Chapter;
use App\Models\Setup\StudentClass;
use App\Models\Setup\Subject;
use App\Models\TestSeries\Question;
use App\Models\TestSeries\TestSeries;
use App\Models\TestSeries\TestSeriesQuestion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TestSeriesController extends Controller
{
    public function index(Request $request)
    {
        $testseries = TestSeries::select('id', 'class_id', 'subject_id', 'chapter_id', 'title', 'note', 'active', 'time_duration','display_instant_result')
        ->with('chapter:id,name', 'stClass:id,name', 'subject:id,name')
            ->when($request->search, function ($query, $search){
                $query->where('title', 'like', '%'. $search . '%');
                $query->orWhere('body', 'like', '%'. $search . '%');
                $query->orWhere('note', 'like', '%'. $search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString()
            ;
        return Inertia::render('TestSeries/TestSeries/Index' , compact('testseries'));
    }

    public function create()
    {
        $classes = StudentClass::select('id', 'name')->whereActive(1)->has('questions')->get();
        $subjects = Subject::select('id', 'name', 'class_id')->whereActive(1)->has('questions')->get();
        $chapters = Chapter::select('id','name','subject_id')->whereActive(1)->has('questions')->get();
        $questions = Question::select('id','title','subject_id')->whereActive(1)->get();
        return Inertia::render('TestSeries/TestSeries/Create', compact('classes', 'subjects', 'chapters', 'questions'));
    }

    public function store(Request $request)
    {
        $this->validateFull($request);
        \DB::transaction(function() use ($request) {
            TestSeries::create($request->only( 'class_id', 'subject_id', 'chapter_id', 'title', 'body', 'display_instant_result', 'time_duration', 'note', 'active'))->questions()->createMany($request->questions);
        });
        return redirect(route('testseries.index'))->with('type', 'success')->with('message', 'TestSeries added successfully !!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort(404);
    }


    public function edit(TestSeries $testseries)
    {
        $classes = StudentClass::select('id', 'name')->whereActive(1)->has('subjects')->has('chapters')->get();
        $subjects = Subject::select('id', 'name', 'class_id')->whereActive(1)->has('chapters')->get();
        $chapters = Chapter::select('id','name','subject_id')->whereActive(1)->get();
        $questions = Question::select('id','title','subject_id')->whereActive(1)->get();
        $testseries->load('questions:id,question_id,test_series_id,question_index,positive_mark,negative_mark')->only('id', 'class_id', 'subject_id', 'chapter_id', 'title','body', 'note', 'active', 'questions');
        return Inertia::render('TestSeries/TestSeries/Create', compact('testseries', 'classes','subjects', 'chapters', 'questions'));
    }

    public function update(Request $request, TestSeries $testseries)
    {
        $this->validateFull($request);

        \DB::transaction(function() use ($request, $testseries) {

            // update existing questions
            $currentItems = collect($request->questions)->where('id', '!=', '');
            $currentItems->map(function ($testseriesQuestion){
                TestSeriesQuestion::whereId($testseriesQuestion['id'])
                    ->update(collect($testseriesQuestion)
                        ->only('question_index', 'question_id', 'positive_mark', 'negative_mark')
                        ->toArray()
                    );
            });

            // delete removed questions
            $testseries->questions()->whereNotIn('id', $currentItems->pluck('id'))->delete();
            // create new questions
            $testseries->questions()->createMany(collect($request->questions)->where('id' ,'' ));

            $testseries->update($request->only('class_id', 'subject_id', 'chapter_id', 'title', 'body', 'display_instant_result', 'time_duration', 'note', 'active'));
        });
        return redirect(route('testseries.index'))->with('type', 'success')->with('message', 'TestSeries updated successfully !!');
    }


    private function validateFull($request)
    {
        $request->validate(
            [
                'title' => 'required|min:4',
                'class_id' => 'required',
                'subject_id' => 'required',
                'chapter_id' => 'required',
                'questions' => "array|min:2",
                'questions.*.question_index' => "required",
                'questions.*.question_id' => "required",
                'questions.*.positive_mark' => "required|numeric|min:0",
                'questions.*.negative_mark' => "required|numeric|min:0",
            ],
            [
                'title.required' => 'Title is empty.',
                'title.min' => "Title should be more then 3 characters.",
                'class_id.required' => 'The class field is required.',
                'subject_id.required' => 'The subject field is required.',
                'chapter_id.required' => 'The chapter field is required.',
                'questions.*.question_id.required' => "Question not selected.",
                'questions.*.question_index.required' => 'Index is empty.',
                'questions.*.positive_mark.required' => 'Field is empty.',
                'questions.*.negative_mark.required' => 'Field is empty.',
                'questions.*.positive_mark.numeric' => 'Number only.',
                'questions.*.negative_mark.numeric' => 'Number only.',
                'questions.*.positive_mark.min' => 'Minimum zero.',
                'questions.*.negative_mark.min' => 'Minimum zero.',
            ]
        );
    }
}
