<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Sale\Packager\Package;
use App\Traits\ApiResponsable;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PackagesController extends Controller
{
    use ApiResponsable;
    public function packages()
    {
        $packages = Package::select('id','title', 'description', 'image', 'regular_price',  'sell_price', 'start', 'end' ,'is_free', 'access_forever')->whereActive(1)->get();
        return $this->apiResponse(200, 'success', 'Packages lists', ['packages' => $packages]);
    }

    public function show($packageId)
    {
        $packages = Package::select('id','title', 'description', 'image', 'regular_price',  'sell_price', 'start', 'end' ,'is_free', 'access_forever')
        ->with('liveClasses.liveClass:id,title,description,image')
        ->with('liveClasses:id,package_id,live_class_id,description,active')
        ->with('recordedClasses.recordedClass:id,name,description')
        ->with('recordedClasses:id,package_id,recorded_class_id,description,active')
        ->with('recordedSubjects.chapters.chapter:id,name,description')
        ->with('recordedSubjects.chapters:id,package_id,package_subject_id,subject_id,chapter_id,is_free')
        // ->with('recordedSubjects.chapters')
        ->with('recordedSubjects.subject:id,name,description')
        ->with('recordedSubjects:id,package_id,recorded_class_id,recorded_subject_id,description,active')
        ->with('testSerires.testseries:id,title,body,time_duration,display_instant_result,active')
        ->with('testSerires:id,package_id,test_series_id,description,is_free,active')
        ->whereId($packageId)
        ->whereActive(1)->get();
        return $this->apiResponse(200, 'success', 'Package Detail', ['packages' => $packages]);
    }
}
