<?php

namespace App\Models\Quizzer;

use App\Traits\Authorable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizQuestion extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Authorable;
    protected $fillable = [  'question_index', 'question_id', 'quiz_id', 'positive_mark', 'negative_mark'];

}
