<?php

namespace App\Models\Setup;

use App\Models\TestSeries\Question;
use App\Models\Setup\Chapter;
use App\Models\Setup\Subject;
use App\Traits\Authorable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentClass extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Authorable;
    protected $fillable = [ 'name', 'description', 'note', 'active'];

    protected $casts = [
      'active' => 'boolean',
    ];

    public function questions()
    {
      return $this->hasMany(Question::class, 'class_id');
    }

    public function subjects()
    {
      return $this->hasMany(Subject::class, 'class_id');
    }
    public function chapters()
    {
      return $this->hasMany(Chapter::class, 'class_id');
    }

}
