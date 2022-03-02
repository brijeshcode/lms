<?php

namespace App\Models\TestSeries;

use App\Traits\Authorable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestSeriesQuestion extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Authorable;
    protected $fillable = [  'question_index', 'question_id', 'test_series_id', 'positive_mark', 'negative_mark'];

}
