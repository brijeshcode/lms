<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Models\LiveClasses\LiveClass;
use App\Models\Sale\Packager\Package;
use App\Models\Sale\Packager\PackageLiveClass;
use App\Models\Sale\Packager\PackageRecordedClass;
use App\Models\Sale\Packager\PackageRecordedSubject;
use App\Models\Sale\Packager\PackageSubjectChapter;
use App\Models\Sale\Packager\PackageTestSeries;
use App\Models\Setup\Chapter;
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
        $chapters = Chapter::select('id', 'class_id', 'subject_id', 'name')->whereActive(1)->get();
        $liveClasses = LiveClass::select('id', 'title')->whereActive(1)->get();
        $testSeries = TestSeries::select('id', 'title')->whereActive(1)->get();
        return Inertia::render('Sale/Package/Create', compact('recordedClasses', 'subjects','testSeries', 'chapters', 'liveClasses'));
    }

    public function store(Request $request)
    {
        $this->validateFull($request);
        \DB::transaction(function() use ($request) {

            $package = Package::create($request->only('title', 'description', 'start', 'end', 'regular_price', 'sell_price', 'is_free', 'note', 'active'));

            if (is_object($request->image)) {
                $package->image = uploadFile($request->image);
                $package->save();
            }
            if (!is_null($request->test_serires)) {
                $package->testSerires()->createMany($request->test_serires);
            }
            if (!is_null($request->live_classes)) {
                $package->liveClasses()->createMany($request->live_classes);
            }
            if (!is_null($request->recorded_classes)) {
                $package->recordedClasses()->createMany($request->recorded_classes);
            }
            if (!empty($request->recorded_subjects)) {
                foreach ($request->recorded_subjects as $key => $subject) {
                    $packageSubject = PackageRecordedSubject::create([
                        'package_id' => $package->id,
                        'recorded_class_id' => $subject['recorded_class_id'],
                        'recorded_subject_id' => $subject['recorded_subject_id'],
                        'description' => $subject['description']
                    ]);

                    foreach ($subject['chapters'] as $key => $chapter) {
                        PackageSubjectChapter::create([
                            'package_id' => $packageSubject->package_id,
                            'class_id' => $chapter['class_id'],
                            'subject_id' => $chapter['subject_id'],
                            'class_id' => $chapter['class_id'],
                            'chapter_id' => $chapter['chapter_id'],
                            'is_free' => $chapter['is_free'],
                            'package_subject_id' => $packageSubject->id
                        ]);
                    }
                }
            }
            // $package->recordedSubjects()->createMany($request->recorded_subjects);
        });
        return redirect(route('package.index'))->with('type', 'success')->with('message', 'Package added successfully !!');
    }

    public function edit(Package $package)
    {
        $package->load('testSerires', 'recordedSubjects', 'recordedClasses', 'recordedSubjects.chapters.chapter', 'liveClasses');
        $recordedClasses = StudentClass::select('id', 'name')->whereActive(1)->has('subjects')->get();
        $chapters = Chapter::select('id', 'class_id', 'subject_id', 'name')->whereActive(1)->get();
        $subjects = Subject::select('id', 'name', 'class_id')->whereActive(1)->get();
        $liveClasses = LiveClass::select('id', 'title')->whereActive(1)->get();
        $testSeries = TestSeries::select('id', 'title')->whereActive(1)->get();
        return Inertia::render('Sale/Package/Create', compact('recordedClasses', 'subjects', 'liveClasses', 'testSeries', 'package', 'chapters'));
    }

    public function update(Request $request, Package $package)
    {
        $this->validateFull($request);
        \DB::transaction(function() use ($request, $package) {

            if (is_object($request->image)) {
                trashedFile($package->image);
                $package->image = uploadFile($request->image);
                $package->save();
            }
            // $this->updatePackgaeItems($request, $package);
            $package->update($request->only('title', 'description','start', 'end', 'regular_price', 'sell_price', 'is_free', 'note', 'active'));
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
                    ->only('test_series_id', 'is_free', 'description')
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

        if (!empty($requestData)) {
                foreach ($requestData as $key => $subject) {

                }
            }

        $currentItems = collect($requestData)->where('id', '!=', '');
       /* $currentItems->map(function ($subject){
            PackageRecordedSubject::whereId($subject['id'])
                ->update(collect($subject)
                    ->only('live_class_id', 'recorded_subject_id', 'description')
                    ->toArray()
                );


                foreach ($subject['chapters'] as $key => $chapter) {
                    PackageSubjectChapter::where('package_subject_id', $subject['id'])
                    ->update([
                        'class_id' => $chapter['class_id'],
                        'subject_id' => $chapter['subject_id'],
                        'class_id' => $chapter['class_id'],
                        'chapter_id' => $chapter['chapter_id'],
                        'is_free' => $chapter['is_free']
                    ]);
                }
        });*/

        // delete removed package subject
        dd($packages->recordedSubjects()->whereNotIn('id', $currentItems->pluck('id')));
        $packages->recordedSubjects()->whereNotIn('id', $currentItems->pluck('id'))->chapters()->delete();
        $packages->recordedSubjects()->whereNotIn('id', $currentItems->pluck('id'))->delete();


        // create new package subject
        if (!empty(collect($requestData)->where('id' ,'' ))) {
            foreach (collect($requestData)->where('id' ,'' ) as $key => $subject) {
                $packageSubject = PackageRecordedSubject::create([
                    'package_id' => $packages->id,
                    'recorded_class_id' => $subject['recorded_class_id'],
                    'recorded_subject_id' => $subject['recorded_subject_id'],
                    'description' => $subject['description']
                ]);

                foreach ($subject['chapters'] as $key => $chapter) {
                    PackageSubjectChapter::create([
                        'package_id' => $packageSubject->package_id,
                        'class_id' => $chapter['class_id'],
                        'subject_id' => $chapter['subject_id'],
                        'class_id' => $chapter['class_id'],
                        'chapter_id' => $chapter['chapter_id'],
                        'is_free' => $chapter['is_free'],
                        'package_subject_id' => $packageSubject->id
                    ]);
                }
            }
        }
        // $packages->recordedSubjects()->createMany(collect($requestData)->where('id' ,'' ));
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
