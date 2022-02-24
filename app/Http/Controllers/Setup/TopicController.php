<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Models\Setup\Chapter;
use App\Models\Setup\StudentClass;
use App\Models\Setup\Subject;
use App\Models\Setup\Topic;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TopicController extends Controller
{

    public function index(Request $request)
    {
        $topics = Topic::select('id', 'class_id', 'subject_id', 'chapter_id', 'name', 'note', 'active')
        ->with('chapter:id,name', 'stClass:id,name', 'subject:id,name')
            ->when($request->search, function ($query, $search){
                $query->where('name', 'like', '%'. $search . '%');
                // $query->orWhere('description', 'like', '%'. $search . '%');
                $query->orWhere('note', 'like', '%'. $search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString()
            ;
        return Inertia::render('Setup/Topic/Index' , compact('topics'));
    }

    public function create()
    {
        $classes = StudentClass::whereActive(1)->has('subjects')->get();
        $subjects = Subject::whereActive(1)->has('chapters')->get();
        $chapters = Chapter::whereActive(1)->get();
        return Inertia::render('Setup/Topic/Create', compact('classes', 'subjects', 'chapters'));
    }

    public function store(Request $request)
    {
        $this->validateFull($request);
        Topic::create($request->only( 'class_id', 'subject_id','chapter_id', 'name', 'description', 'note', 'active'));
        return redirect(route('topic.index'))->with('type', 'success')->with('message', 'Topic added successfully !!');
    }


    public function show($id)
    {
        //
    }

    public function edit(Topic $topic)
    {
        $classes = StudentClass::select('id', 'name')->whereActive(1)->has('subjects')->get();
        $subjects = Subject::select('id', 'name', 'class_id')->whereActive(1)->get();
        $chapters = Chapter::whereActive(1)->get();
        $topic->load('chapter:id,subject_id' )->only('id', 'class_id', 'subject_id','chapter_id', 'name', 'description','note', 'active', 'subject_id', 'subject');
        $topic->only('id', 'chapter_id', 'name', 'description', 'note', 'active');
        return Inertia::render('Setup/Topic/Create', compact('topic', 'classes','subjects', 'chapters'));
    }

    public function update(Request $request, Topic $topic)
    {
        $this->validateFull($request);
        $topic->update($request->only('chapter_id', 'name', 'description', 'note', 'active'));
        return redirect(route('topic.index'))->with('type', 'success')->with('message', 'Topic updated successfully !!');
    }

    private function validateFull($request)
    {
        $tempName = 'Topic';
        $request->validate(
            [
                'name' => 'required|max:50',
                'chapter_id' => 'required'
            ],
            [
                'name.required' => $tempName .' Name is empty.',
                'chapter_id.required' => $tempName . ' class field is required.'
            ]
        );
    }
}
