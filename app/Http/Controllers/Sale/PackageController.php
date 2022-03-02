<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Models\Sale\Packager\Package;
use App\Models\Setup\StudentClass;
use App\Models\Setup\Subject;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $packages = Package::select('id','title', 'description', 'image', 'regular_price', 'sell_price', 'is_free', 'note', 'active')
            ->when($request->search, function ($query, $search){
                $query->where('title', 'like', '%'. $search . '%');
                $query->orWhere('description', 'like', '%'. $search . '%');
                $query->orWhere('regular_price', 'like', '%'. $search . '%');
                $query->orWhere('sell_price', 'like', '%'. $search . '%');
                $query->orWhere('note', 'like', '%'. $search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString()
            ;
        return Inertia::render('Sale/Package/Index' , compact('packages'));
    }

    public function create()
    {
        $classes = StudentClass::select('id', 'name')->whereActive(1)->has('subjects')->get();
        $subjects = Subject::select('id', 'name', 'class_id')->whereActive(1)->get();
        return Inertia::render('Sale/Package/Create', compact('classes', 'subjects'));
    }

    public function store(Request $request)
    {
        $this->validateFull($request);
        \DB::transaction(function() use ($request) {
            Package::create($request->only('title', 'description', 'image', 'regular_price', 'sell_price', 'is_free', 'note', 'active'));
        });
        return redirect(route('package.index'))->with('type', 'success')->with('message', 'Package added successfully !!');
    }

    public function edit(Package $package)
    {
        $classes = StudentClass::select('id', 'name')->whereActive(1)->has('subjects')->get();
        $subjects = Subject::select('id', 'name', 'class_id')->whereActive(1)->get();
        return Inertia::render('Sale/Package/Create', compact('classes', 'subjects', 'package'));
    }

    public function update(Request $request, Package $package)
    {
        $this->validateFull($request);
        $package->update($request->only('title', 'description', 'image', 'regular_price', 'sell_price', 'is_free', 'note', 'active'));
        return redirect(route('package.index'))->with('type', 'success')->with('message', 'Package updated successfully !!');
    }

    private function validateFull($request)
    {
        // dd($request->all());
        $tempName = 'Package';
        $request->validate(
            [
                'title' => 'required|min:4',
                'regular_price' => "required|numeric|min:0",
                'sell_price' => "required|numeric|min:0|lte:regular_price",
                /*'class_id' => 'required',
                'subject_id' => 'required',
                'chapter_id' => 'required',
                'options' => "array|min:2",
                'options.*.option' => "required|string|min:3",
                'options.*.option_number' => "required|string|min:1",
                'options.*.is_correct' => "required|boolean",*/
            ],
            [
                'title.required' => 'Title is empty.',
                'title.min' => "Invalid {$tempName}.",
                'regular_price.required' => ' Regular Price is empty.',
                'regular_price.numeric' => ' Regular Price must be a number.',
                'regular_price.min' => ' Regular Price should be zero or greater.',
                'sell_price.required' => ' Sell Price is empty.',
                'sell_price.numeric' => ' Sell Price must be a number.',
                'sell_price.min' => ' Sell Price should be zero or greater.',
                'sell_price.lte' => ' Sell Price cannot be greater then Regular price.',

                /*'class_id.required' => 'The class field is required.',
                'subject_id.required' => 'The subject field is required.',
                'chapter_id.required' => 'The chapter field is required.',
                'options.*.option.required' => 'Option is empty.',
                'options.*.option_number.required' => 'Index is empty.',*/
            ]
        );
    }
}
