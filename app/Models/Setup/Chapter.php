<?php

namespace App\Models\Setup;

use App\Models\Quizzer\Question;
use App\Models\Setup\Subject;
use App\Traits\Authorable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chapter extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Authorable;
    protected $fillable = [ 'class_id', 'subject_id', 'name', 'description', 'note', 'active'];

    protected $casts = [
      'active' => 'boolean',
    ];

    public function subject()
    {
      return $this->belongsTo(Subject::class);
    }

    public function questions()
    {
      return $this->hasMany(Question::class);
    }
}
