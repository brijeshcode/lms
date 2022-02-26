<?php

namespace App\Models\Quizzer;

use App\Models\Quizzer\QuizQuestion;
use App\Models\Setup\Chapter;
use App\Models\Setup\StudentClass;
use App\Models\Setup\Subject;
use App\Models\Setup\Topic;
use App\Traits\Authorable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Authorable;
    protected $fillable = [ 'class_id', 'subject_id', 'chapter_id', 'topic_id', 'title', 'body', 'time_duration', 'display_instant_result', 'note', 'active'];

    protected $casts = [
      'active' => 'boolean',
      'display_instant_result' => 'boolean'
    ];

    public function questions(){
      return $this->hasMany(QuizQuestion::class);
    }

    public function stClass()
    {
      return $this->belongsTo(StudentClass::class, 'class_id');
    }

    public function subject()
    {
      return $this->belongsTo(Subject::class);
    }

    public function chapter()
    {
      return $this->belongsTo(Chapter::class);
    }

    public function topic()
    {
      return $this->belongsTo(Topic::class);
    }
}
