<?php

namespace App\Models\Sale\Packager;

use App\Models\Setup\Chapter;
use App\Traits\Authorable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageSubjectChapter extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Authorable;
    protected $fillable = ['package_subject_id', 'package_id', 'class_id', 'subject_id','chapter_id', 'is_free'];

    protected $casts = [
      'is_free' => 'boolean'
    ];

    public function chapter()
    {
      return $this->belongsTo(Chapter::class, 'chapter_id');
    }

}
