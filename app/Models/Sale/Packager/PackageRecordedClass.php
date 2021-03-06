<?php

namespace App\Models\Sale\Packager;

use App\Models\Setup\StudentClass;
use App\Traits\Authorable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageRecordedClass extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Authorable;
    protected $fillable = [ 'package_id', 'recorded_class_id', 'description', 'note', 'active'];

    protected $casts = [
      'active' => 'boolean',
    ];

    public function recordedClass()
    {
      return $this->belongsTo(StudentClass::class, 'recorded_class_id');
    }
}
