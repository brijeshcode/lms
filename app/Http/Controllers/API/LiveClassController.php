<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\LiveClasses\LiveClass;
use App\Models\LiveClasses\LiveSession;
use App\Traits\ApiResponsable;
use Illuminate\Http\Request;

class LiveClassController extends Controller
{
    use ApiResponsable;

    public function allClasses()
    {
        $classes = LiveClass::select('id','class_id', 'subject_id', 'chapter_id', 'title', 'description', 'image', 'note', 'active')->whereActive(1)->get();
        return $this->apiResponse(200, 'success', 'Live Classes lists', ['classes' => $classes]);
    }

    public function classSessions($liveClassId)
    {
        $sessions = LiveSession::select('id','live_class_id', 'date', 'timing', 'link', 'description', 'note', 'active')->where('live_class_id',$liveClassId)->whereActive(1)->get();
        return $this->apiResponse(200, 'success', 'Live Classes Sessions lists', ['sessions' => $sessions]);
    }

}
