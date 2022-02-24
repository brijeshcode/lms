<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Models\Setup\StudentClass;
use App\Models\Setup\Subject;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubjectController extends Controller
{

    public function index(Request $request)
    {
        $subjects = Subject::select('id', 'class_id', 'name', 'note', 'active')
        ->with('studentClass:id,name')
            ->when($request->search, function ($query, $search){
                $query->where('name', 'like', '%'. $search . '%');
                // $query->orWhere('description', 'like', '%'. $search . '%');
                $query->orWhere('note', 'like', '%'. $search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString()
            ;
        return Inertia::render('Setup/Subject/Index' , compact('subjects'));
    }

    public function create()
    {
        $classes = StudentClass::whereActive(1)->get();
        return Inertia::render('Setup/Subject/Create', compact('classes'));
    }

    public function store(Request $request)
    {
        $this->validateFull($request);
        Subject::create($request->only( 'class_id', 'name', 'description', 'note', 'active'));
        return redirect(route('subject.index'))->with('type', 'success')->with('message', 'Subject added successfully !!');
    }


    public function show($id)
    {
        //
    }

    public function edit(Subject $subject)
    {
        $classes = StudentClass::whereActive(1)->get();
        $subject = $subject->only('id', 'class_id', 'name', 'description', 'note', 'active');
        return Inertia::render('Setup/Subject/Create', compact('subject', 'classes'));
    }

    public function update(Request $request, Subject $subject)
    {
        $this->validateFull($request);
        $subject->update($request->only('class_id', 'name', 'description', 'note', 'active'));
        return redirect(route('subject.index'))->with('type', 'success')->with('message', 'Subject updated successfully !!');
    }

    private function validateFull($request)
    {
        $tempName = 'Subject';
        $request->validate(
            [
                'name' => 'required|max:50',
                'class_id' => 'required'
            ],
            [
                'name.required' => $tempName .' Name is empty.',
                'class_id.required' => $tempName . ' class field is required.'
            ]
        );
    }
}
