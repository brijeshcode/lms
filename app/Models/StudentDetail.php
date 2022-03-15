<?php

namespace App\Models;

use App\Traits\Authorable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Authorable;
    protected $fillable = [ 'student_id', 'key', 'value', 'note', 'active'];

    protected $casts = [
      'active' => 'boolean',
    ];

    public function user()
    {
      return $this->belongsTo(User::class, 'student_id');
    }
}
