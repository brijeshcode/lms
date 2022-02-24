<?php

namespace App\Models\Setup;

use App\Traits\Authorable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Topic extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Authorable;
    protected $fillable = [ 'class_id', 'subject_id', 'chapter_id', 'name', 'description', 'note', 'active'];

    protected $casts = [
      'active' => 'boolean',
    ];

    public function chapter()
    {
      return $this->belongsTo(Chapter::class);
    }
}
