<?php

namespace App\Http\Controllers\Quizzer;

use App\Http\Controllers\Controller;
use App\Models\Quizzer\Question;
use App\Models\Quizzer\Quiz;
use App\Models\Quizzer\QuizQuestion;
use App\Models\Setup\Chapter;
use App\Models\Setup\StudentClass;
use App\Models\Setup\Subject;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $quizzes = Quiz::select('id', 'class_id', 'subject_id', 'chapter_id', 'title', 'note', 'active', 'time_duration','display_instant_result')
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
        return Inertia::render('Quizzer/Quiz/Index' , compact('quizzes'));
    }

    public function create()
    {
        $classes = StudentClass::select('id', 'name')->whereActive(1)->has('questions')->get();
        $subjects = Subject::select('id', 'name', 'class_id')->whereActive(1)->has('questions')->get();
        $chapters = Chapter::select('id','name','subject_id')->whereActive(1)->has('questions')->get();
        $questions = Question::select('id','title','subject_id')->whereActive(1)->get();
        return Inertia::render('Quizzer/Quiz/Create', compact('classes', 'subjects', 'chapters', 'questions'));
    }

    public function store(Request $request)
    {
        $this->validateFull($request);
        \DB::transaction(function() use ($request) {
            Quiz::create($request->only( 'class_id', 'subject_id', 'chapter_id', 'title', 'body', 'display_instant_result', 'time_duration', 'note', 'active'))->questions()->createMany($request->questions);
        });
        return redirect(route('quiz.index'))->with('type', 'success')->with('message', 'Quiz added successfully !!');
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


    public function edit(Quiz $quiz)
    {
        $classes = StudentClass::select('id', 'name')->whereActive(1)->has('subjects')->has('chapters')->get();
        $subjects = Subject::select('id', 'name', 'class_id')->whereActive(1)->has('chapters')->get();
        $chapters = Chapter::select('id','name','subject_id')->whereActive(1)->get();
        $questions = Question::select('id','title','subject_id')->whereActive(1)->get();
        $quiz->load('questions:id,question_id,quiz_id,question_index,positive_mark,negative_mark')->only('id', 'class_id', 'subject_id', 'chapter_id', 'title','body', 'note', 'active', 'questions');
        return Inertia::render('Quizzer/Quiz/Create', compact('quiz', 'classes','subjects', 'chapters', 'questions'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $this->validateFull($request);

        \DB::transaction(function() use ($request, $quiz) {

            // update existing questions
            $currentItems = collect($request->questions)->where('id', '!=', '');
            $currentItems->map(function ($quizQuestion){
                QuizQuestion::whereId($quizQuestion['id'])
                    ->update(collect($quizQuestion)
                        ->only('question_index', 'question_id', 'positive_mark', 'negative_mark')
                        ->toArray()
                    );
            });

            // delete removed questions
            $quiz->questions()->whereNotIn('id', $currentItems->pluck('id'))->delete();
            // create new questions
            $quiz->questions()->createMany(collect($request->questions)->where('id' ,'' ));

            $quiz->update($request->only('class_id', 'subject_id', 'chapter_id', 'title', 'body', 'display_instant_result', 'time_duration', 'note', 'active'));
        });
        return redirect(route('quiz.index'))->with('type', 'success')->with('message', 'Quiz updated successfully !!');
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
                'questions' => "array|min:2",
                'questions.*.question_index' => "required",
                'questions.*.question_id' => "required",
                'questions.*.positive_mark' => "required|numeric|min:0",
                'questions.*.negative_mark' => "required|numeric|min:0",
            ],
            [
                'title.required' => 'Title is empty.',
                'title.min' => "Invalid {$tempName}.",
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
