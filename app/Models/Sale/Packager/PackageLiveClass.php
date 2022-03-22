<?php

namespace App\Models\Sale\Packager;

use App\Models\LiveClasses\LiveClass;
use App\Models\Sale\Packager\Package;
use App\Traits\Authorable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageLiveClass extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Authorable;
    protected $fillable = [ 'package_id', 'live_class_id', 'description', 'note', 'active'];

    protected $casts = [
      'active' => 'boolean',
    ];

    public function package()
    {
      return $this->belongsTo(Package::class);
    }

    public function liveClass()
    {
      return $this->belongsTo(LiveClass::class, 'live_class_id');
    }
}
