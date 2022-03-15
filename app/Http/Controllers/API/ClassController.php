<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Setup\Chapter;
use App\Models\Setup\StudentClass;
use App\Models\Setup\Subject;
use App\Traits\ApiResponsable;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    use ApiResponsable;

    public function allClasses()
    {
        $classes = StudentClass::select('id', 'name', 'description', 'note')->whereActive(1)->get();
        return $this->apiResponse(200, 'success', 'Class lists', ['classes' => $classes]);
    }

    public function classSubjects(Request $request, $class)
    {
        $subjects = Subject::select('id', 'class_id', 'name', 'description', 'note')->whereActive(1)->where('class_id', $class)->get();
        return $this->apiResponse(200, 'success', 'Subject lists', ['subjects' => $subjects]);
    }

    public function classChapters(Request $request, $class, $subject)
    {
        $chapter = Chapter::select('id', 'class_id', 'subject_id', 'name', 'description', 'note')->whereActive(1)->where('class_id', $class)->where('subject_id', $subject)->get();
        return $this->apiResponse(200, 'success', 'Chapter lists', ['chapter' => $chapter]);
    }

    public function subjectChapters(Request $request, $subject)
    {
        $chapter = Chapter::select('id', 'class_id', 'subject_id', 'name', 'description', 'note')->whereActive(1)->where('subject_id', $subject)->get();
        return $this->apiResponse(200, 'success', 'Chapter lists', ['chapter' => $chapter]);
    }


}
