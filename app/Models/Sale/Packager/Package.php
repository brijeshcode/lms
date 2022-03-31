<?php

namespace App\Models\Sale\Packager;

use App\Models\Sale\Packager\PackageLiveClass;
use App\Models\Sale\Packager\PackageRecordedClass;
use App\Models\Sale\Packager\PackageRecordedSubject;
use App\Models\Sale\Packager\PackageTestSeries;
use App\Traits\Authorable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Authorable;
    protected $fillable = [ 'title', 'description', 'image', 'regular_price',  'sell_price', 'start', 'end' ,'is_free', 'access_forever', 'note', 'active'];

    protected $casts = [
      'active' => 'boolean',
      'access_forever' => 'boolean',
      'is_free' => 'boolean'
    ];

    protected $appends = [
        'image_url',
    ];

    public function getImageUrlAttribute()
    {
        if (isset($this->attributes['image'])) {
            return url('/'). $this->attributes['image'];
        }
    }
    public function liveClasses()
    {
        return $this->hasMany(PackageLiveClass::class);
    }

    public function recordedClasses()
    {
        return $this->hasMany(PackageRecordedClass::class);
    }

    public function recordedSubjects()
    {
        return $this->hasMany(PackageRecordedSubject::class);
    }

    public function testSerires()
    {
        return $this->hasMany(PackageTestSeries::class);
    }
}
