<?php

namespace App\Http\Controllers\TestSeries;

use App\Http\Controllers\Controller;
use App\Models\TestSeries\Option;
use App\Models\TestSeries\Question;
use App\Models\Setup\Chapter;
use App\Models\Setup\StudentClass;
use App\Models\Setup\Subject;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $questions = Question::select('id', 'class_id', 'subject_id', 'chapter_id', 'title', 'note', 'active')
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
        return Inertia::render('TestSeries/Question/Index' , compact('questions'));
    }

    public function create()
    {
        $classes = StudentClass::whereActive(1)->has('chapters')->get();
        $subjects = Subject::whereActive(1)->has('chapters')->get();
        $chapters = Chapter::whereActive(1)->get();
        return Inertia::render('TestSeries/Question/Create', compact('classes', 'subjects', 'chapters'));
    }

    public function store(Request $request)
    {
        $this->validateFull($request);
        \DB::transaction(function() use ($request) {
            Question::create($request->only( 'class_id', 'subject_id', 'chapter_id', 'title', 'body', 'note', 'active'))->options()->createMany($request->options);
        });
        return redirect(route('question.index'))->with('type', 'success')->with('message', 'Question added successfully !!');
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


    public function edit(Question $question)
    {
        $classes = StudentClass::select('id', 'name')->whereActive(1)->has('chapters')->get();
        $subjects = Subject::select('id', 'name', 'class_id')->has('chapters')->whereActive(1)->get();
        $chapters = Chapter::whereActive(1)->get();
        $question->load('options:id,question_id,option_number,option,is_correct,explanation')->only('id', 'class_id', 'subject_id', 'chapter_id', 'title','body', 'note', 'active', 'options');
        return Inertia::render('TestSeries/Question/Create', compact('question', 'classes','subjects', 'chapters'));
    }

    public function update(Request $request, Question $question)
    {
        $this->validateFull($request);

        \DB::transaction(function() use ($request, $question) {

            // update existing options
            $currentItems = collect($request->options)->where('id', '!=', '');
            $currentItems->map(function ($option){
                Option::whereId($option['id'])
                    ->update(collect($option)
                        ->only('option_number','option','is_correct','explanation')
                        ->toArray()
                    );
            });

            // delete removed options
            $question->options()->whereNotIn('id', $currentItems->pluck('id'))->delete();
            // create new options
            $question->options()->createMany(collect($request->options)->where('id' ,'' ));

            $question->update($request->only('class_id', 'subject_id', 'chapter_id', 'title', 'body', 'note', 'active'));
        });
        return redirect(route('question.index'))->with('type', 'success')->with('message', 'Question updated successfully !!');
    }


    private function validateFull($request)
    {
        // dd($request->all());
        $tempName = 'Quesiton';
        $request->validate(
            [
                'title' => 'required|min:4',
                'class_id' => 'required',
                'subject_id' => 'required',
                'chapter_id' => 'required',
                'options' => "array|min:2",
                'options.*.option' => "required|string|min:3",
                'options.*.option_number' => "required|string|min:1",
                'options.*.is_correct' => "required|boolean",
            ],
            [
                'title.required' => $tempName .' is empty.',
                'title.min' => "Invalid {$tempName}.",
                'class_id.required' => 'The class field is required.',
                'subject_id.required' => 'The subject field is required.',
                'chapter_id.required' => 'The chapter field is required.',
                'options.*.option.required' => 'Option is empty.',
                'options.*.option_number.required' => 'Index is empty.',
            ]
        );
    }
}
