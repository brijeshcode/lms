<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TestSeries\TestSeries;
use App\Traits\ApiResponsable;
use Illuminate\Http\Request;

class TestSeriesController extends Controller
{
    use ApiResponsable;
    public function getTests()
    {
        $testseries = TestSeries::select('id', 'class_id', 'subject_id', 'chapter_id', 'title', 'active', 'time_duration')->whereActive(1)->get();
        return $this->apiResponse(200, 'success', 'Test Series Lists !!', ['testseries' => $testseries]);
    }

    public function getTest($testSeries)
    {

        $testSeries = TestSeries::select('id', 'class_id', 'subject_id', 'chapter_id', 'title', 'active', 'time_duration')
            ->with(
                'questions:id,question_index,test_series_id,question_id,positive_mark,negative_mark',
                'questions.question:id,title,body,type,active',
                'questions.options:question_id,option_number,option,explanation,is_correct'
            )
            ->whereId($testSeries)
            ->whereActive(1)
            ->first();

        if (empty($testSeries->toArray())) {
            return $this->apiResponse(404, 'fail', 'Test not found !!', ['test' => $testSeries]);
        }
        return $this->apiResponse(200, 'success', 'Test detail !!', ['test' => $testSeries]);
    }
}
