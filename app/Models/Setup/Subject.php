<?php

namespace App\Models\Setup;

use App\Models\Setup\Chapter;
use App\Models\Setup\StudentClass;
use App\Traits\Authorable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Authorable;
    protected $fillable = [ 'class_id', 'name', 'description', 'note', 'active'];

    protected $casts = [
      'active' => 'boolean',
    ];

    public function studentClass()
    {
      return $this->belongsTo(StudentClass::class, 'class_id');
    }

    public function chapters()
    {
      return $this->hasMany(Chapter::class);
    }
}
