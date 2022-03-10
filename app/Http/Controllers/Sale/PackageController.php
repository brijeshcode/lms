<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Models\LiveClasses\LiveClass;
use App\Models\Sale\Packager\Package;
use App\Models\Sale\Packager\PackageLiveClass;
use App\Models\Sale\Packager\PackageRecordedClass;
use App\Models\Sale\Packager\PackageRecordedSubject;
use App\Models\Sale\Packager\PackageTestSeries;
use App\Models\Setup\StudentClass;
use App\Models\Setup\Subject;
use App\Models\TestSeries\TestSeries;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $packages = Package::select('id','title', 'description', 'image', 'regular_price', 'sell_price', 'start', 'end', 'is_free', 'note', 'active')
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
        $recordedClasses = StudentClass::select('id', 'name')->whereActive(1)->has('subjects')->get();
        $subjects = Subject::select('id', 'name', 'class_id')->whereActive(1)->get();
        $liveClasses = LiveClass::select('id', 'title')->whereActive(1)->get();
        $testSeries = TestSeries::select('id', 'title')->whereActive(1)->get();
        return Inertia::render('Sale/Package/Create', compact('recordedClasses', 'subjects','testSeries', 'liveClasses'));
    }

    public function store(Request $request)
    {
        $this->validateFull($request);
        \DB::transaction(function() use ($request) {
            $package = Package::create($request->only('title', 'description', 'image',  'start', 'end', 'regular_price', 'sell_price', 'is_free', 'note', 'active'));
            $package->testSerires()->createMany($request->test_serires);
            $package->liveClasses()->createMany($request->live_classes);
            $package->recordedSubjects()->createMany($request->recorded_subjects);
            $package->recordedClasses()->createMany($request->recorded_classes);
        });
        return redirect(route('package.index'))->with('type', 'success')->with('message', 'Package added successfully !!');
    }

    public function edit(Package $package)
    {
        $package->load('testSerires', 'recordedSubjects', 'recordedClasses', 'liveClasses');
        $recordedClasses = StudentClass::select('id', 'name')->whereActive(1)->has('subjects')->get();
        $subjects = Subject::select('id', 'name', 'class_id')->whereActive(1)->get();
        $liveClasses = LiveClass::select('id', 'title')->whereActive(1)->get();
        $testSeries = TestSeries::select('id', 'title')->whereActive(1)->get();
        return Inertia::render('Sale/Package/Create', compact('recordedClasses', 'subjects', 'liveClasses', 'testSeries', 'package'));
    }

    public function update(Request $request, Package $package)
    {
        $this->validateFull($request);
        \DB::transaction(function() use ($request, $package) {
        $this->updatePackgaeItems($request, $package);
        $package->update($request->only('title', 'description', 'image','start', 'end', 'regular_price', 'sell_price', 'is_free', 'note', 'active'));
        });
        return redirect(route('package.index'))->with('type', 'success')->with('message', 'Package updated successfully !!');
    }

    private function updatePackgaeItems($request, $package){
        $this->updateTestSeries($request->test_serires, $package);
        $this->updateLiveClasses($request->live_classes, $package);
        $this->updateRecordedClasses($request->recorded_classes, $package);
        $this->updateRecordedSubjects($request->recorded_subjects, $package);
    }

    private function updateTestSeries($requestData, $packages){
        $currentItems = collect($requestData)->where('id', '!=', '');
        $currentItems->map(function ($item){
            PackageTestSeries::whereId($item['id'])
                ->update(collect($item)
                    ->only('test_series_id', 'description')
                    ->toArray()
                );
        });

        // delete removed questions
        $packages->testSerires()->whereNotIn('id', $currentItems->pluck('id'))->delete();
        // create new questions
        $packages->testSerires()->createMany(collect($requestData)->where('id' ,'' ));
    }

    private function updateLiveClasses($requestData, $packages){
        $currentItems = collect($requestData)->where('id', '!=', '');
        $currentItems->map(function ($item){
            PackageLiveClass::whereId($item['id'])
                ->update(collect($item)
                    ->only('live_class_id', 'description')
                    ->toArray()
                );
        });

        // delete removed questions
        $packages->liveClasses()->whereNotIn('id', $currentItems->pluck('id'))->delete();
        // create new questions
        $packages->liveClasses()->createMany(collect($requestData)->where('id' ,'' ));
    }

    private function updateRecordedClasses($requestData, $packages){
        $currentItems = collect($requestData)->where('id', '!=', '');
        $currentItems->map(function ($item){
            PackageRecordedClass::whereId($item['id'])
                ->update(collect($item)
                    ->only('recorded_class_id', 'description')
                    ->toArray()
                );
        });

        // delete removed questions
        $packages->recordedClasses()->whereNotIn('id', $currentItems->pluck('id'))->delete();
        // create new questions
        $packages->recordedClasses()->createMany(collect($requestData)->where('id' ,'' ));
    }

    private function updateRecordedSubjects($requestData, $packages){
        $currentItems = collect($requestData)->where('id', '!=', '');
        $currentItems->map(function ($item){
            PackageRecordedSubject::whereId($item['id'])
                ->update(collect($item)
                    ->only('live_class_id', 'recorded_subject_id', 'description')
                    ->toArray()
                );
        });

        // delete removed questions
        $packages->recordedSubjects()->whereNotIn('id', $currentItems->pluck('id'))->delete();
        // create new questions
        $packages->recordedSubjects()->createMany(collect($requestData)->where('id' ,'' ));
    }

    private function validateFull($request)
    {
        $tempName = 'Package';
        $request->validate(
            [
                'title' => 'required|min:4',
                'regular_price' => "required|numeric|min:0",
                'sell_price' => "required|numeric|min:0|lte:regular_price",
                'start' =>  'required|date',
                'end' =>  'nullable|after:start',
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
                'start.required' => 'Start package date required.',
                'end.after' => 'End should be after Start.',

            ]
        );
    }
}
