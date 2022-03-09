<?php

namespace App\Http\Controllers\LiveClasses;

use App\Http\Controllers\Controller;
use App\Models\LiveClasses\LiveClass;
use App\Models\Setup\Chapter;
use App\Models\Setup\StudentClass;
use App\Models\Setup\Subject;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LiveClassController extends Controller
{
    public function index(Request $request)
    {
        $liveClasses = LiveClass::select('id','class_id', 'subject_id', 'chapter_id', 'title', 'description', 'image', 'note', 'active')
            ->with('chapter:id,name', 'stClass:id,name', 'subject:id,name')
            ->when($request->search, function ($query, $search){
                $query->where('title', 'like', '%'. $search . '%');
                $query->orWhere('description', 'like', '%'. $search . '%');
                $query->orWhere('note', 'like', '%'. $search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString()
            ;
        return Inertia::render('Live/Class/Index' , compact('liveClasses'));
    }

    public function create()
    {
        $classes = StudentClass::whereActive(1)->has('chapters')->get();
        $subjects = Subject::whereActive(1)->has('chapters')->get();
        $chapters = Chapter::whereActive(1)->get();
        return Inertia::render('Live/Class/Create', compact('classes', 'chapters', 'subjects'));
    }

    public function store(Request $request)
    {
        $this->validateFull($request);
        \DB::transaction(function() use ($request) {
            LiveClass::create($request->only('class_id', 'subject_id', 'chapter_id', 'title', 'description', 'image', 'note', 'active'));
        });
        return redirect(route('liveClass.index'))->with('type', 'success')->with('message', 'Live class added successfully !!');
    }

    public function edit(LiveClass $liveClass)
    {
        $classes = StudentClass::whereActive(1)->has('chapters')->get();
        $subjects = Subject::whereActive(1)->has('chapters')->get();
        $chapters = Chapter::whereActive(1)->get();
        return Inertia::render('Live/Class/Create', compact('classes', 'subjects', 'liveClass', 'chapters'));
    }

    public function update(Request $request, LiveClass $liveClass)
    {
        $this->validateFull($request);
        $liveClass->update($request->only('class_id', 'subject_id', 'chapter_id', 'title', 'description', 'image', 'note', 'active'));
        return redirect(route('liveClass.index'))->with('type', 'success')->with('message', 'Live class updated successfully !!');
    }

    private function validateFull($request)
    {
        $tempName = 'Package';
        $request->validate(
            [
                'title' => 'required|min:4',
                'class_id' => 'required',
                'subject_id' => 'required',
                'chapter_id' => 'required',
            ],
            [
                'title.required' => 'Title is empty.',
                'class_id.required' => 'The class field is required.',
                'subject_id.required' => 'The subject field is required.',
                'chapter_id.required' => 'The chapter field is required.',


            ]
        );
    }
}
