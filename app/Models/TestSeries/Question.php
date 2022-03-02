<?php

namespace App\Models\TestSeries;

use App\Models\TestSeries\Option;
use App\Models\Setup\Chapter;
use App\Models\Setup\StudentClass;
use App\Models\Setup\Subject;
use App\Models\Setup\Topic;
use App\Traits\Authorable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Authorable;
    protected $fillable = [ 'class_id', 'subject_id', 'chapter_id', 'topic_id', 'title', 'body', 'type', 'note', 'active'];

    protected $casts = [
      'active' => 'boolean',
    ];

    public function options(){
      return $this->hasMany(Option::class);
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
