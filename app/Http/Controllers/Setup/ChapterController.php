<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Models\Setup\Chapter;
use App\Models\Setup\StudentClass;
use App\Models\Setup\Subject;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChapterController extends Controller
{

    public function index(Request $request)
    {
        $chapters = Chapter::select('id', 'class_id', 'subject_id', 'name', 'note', 'active', 'is_free')
            ->with('subject:id,name,class_id', 'subject.studentClass:id,name')
            ->when($request->search, function ($query, $search){
                $query->where('name', 'like', '%'. $search . '%');
                $query->orWhere('note', 'like', '%'. $search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString()
            ;
        return Inertia::render('Setup/Chapter/Index' , compact('chapters'));
    }

    public function create()
    {
        $classes = StudentClass::select('id', 'name')->whereActive(1)->has('subjects')->get();
        $subjects = Subject::select('id', 'name', 'class_id')->whereActive(1)->get();
        return Inertia::render('Setup/Chapter/Create', compact('classes', 'subjects'));
    }

    public function store(Request $request)
    {
        $this->validateFull($request);
        Chapter::create($request->only('name', 'class_id', 'subject_id', 'description', 'note', 'active', 'is_free'));
        return redirect(route('chapter.index'))->with('type', 'success')->with('message', 'Chapter added successfully !!');
    }


    public function show($id)
    {
        //
    }

    public function edit(Chapter $chapter)
    {
        $classes = StudentClass::select('id', 'name')->whereActive(1)->has('subjects')->get();
        $subjects = Subject::select('id', 'name', 'class_id')->whereActive(1)->get();
        $chapter->load('subject:id,class_id' )->only('id','name', 'class_id', 'description','note', 'active', 'subject_id', 'subject', 'is_free');
        return Inertia::render('Setup/Chapter/Create', compact('chapter', 'classes', 'subjects'));
    }

    public function update(Request $request, Chapter $chapter)
    {
        $this->validateFull($request);
        $chapter->update($request->only('name',  'class_id', 'subject_id', 'description', 'note', 'active', 'is_free'));
        return redirect(route('chapter.index'))->with('type', 'success')->with('message', 'Chapter updated successfully !!');
    }

    private function validateFull($request)
    {
        $tempName = 'Chapter';
        $request->validate(
            [
                'name' => 'required|max:50',
                'subject_id' => 'required'
            ],
            [
                'name.required' => $tempName .' Name is empty.',
                'subject_id.required' => 'Subject Not selected'
            ]
        );
    }
}
