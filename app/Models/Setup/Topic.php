<?php

namespace App\Models\Setup;

use App\Models\Setup\StudentClass;
use App\Models\Setup\Subject;
use App\Traits\Authorable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Topic extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Authorable;
    protected $fillable = [ 'class_id', 'subject_id', 'chapter_id', 'name', 'description', 'note', 'active' ];

    protected $casts = [
      'active' => 'boolean',
    ];

    public function chapter()
    {
      return $this->belongsTo(Chapter::class);
    }

    public function subject()
    {
      return $this->belongsTo(Subject::class);
    }

    public function stClass()
    {
      return $this->belongsTo(StudentClass::class, 'class_id');
    }
}
