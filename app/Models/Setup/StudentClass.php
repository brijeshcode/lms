<?php

namespace App\Models\Setup;

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

    public function subjects()
    {
      return $this->hasMany(Subject::class, 'class_id');
    }

}
