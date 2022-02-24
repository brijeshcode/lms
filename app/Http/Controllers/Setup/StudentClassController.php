<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Models\Setup\StudentClass;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentClassController extends Controller
{

    public function index(Request $request)
    {
        $classes = StudentClass::select('id','name', 'note', 'active')
            ->when($request->search, function ($query, $search){
                $query->where('name', 'like', '%'. $search . '%');
                // $query->orWhere('description', 'like', '%'. $search . '%');
                $query->orWhere('note', 'like', '%'. $search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString()
            ;
        return Inertia::render('Setup/Class/Index' , compact('classes'));
    }

    public function create()
    {
        return Inertia::render('Setup/Class/Create');
    }

    public function store(Request $request)
    {
        $this->validateFull($request);
        StudentClass::create($request->only('name', 'description', 'note', 'active'));
        return redirect(route('studentclasses.index'))->with('type', 'success')->with('message', 'Class added successfully !!');
    }


    public function show($id)
    {
        //
    }

    public function edit(StudentClass $studentclass)
    {
        $studentclass = $studentclass->only('id','name', 'description',  'note',  'active');
        return Inertia::render('Setup/Class/Create', compact('studentclass'));
    }

    public function update(Request $request, StudentClass $studentclass)
    {
        // dd($studentclass);
        $this->validateFull($request);
        $studentclass->update($request->only('name', 'description', 'note', 'active'));
        return redirect(route('studentclasses.index'))->with('type', 'success')->with('message', 'Class updated successfully !!');
    }

    private function validateFull($request)
    {
        $tempName = 'Class';
        $request->validate(
            [
                'name' => 'required|max:50',
            ],
            [
                'name.required' => $tempName .' Name is empty.'
            ]
        );
    }
}
