<?php

namespace App\Models\Sale\Packager;

use App\Models\TestSeries\TestSeries;
use App\Traits\Authorable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageTestSeries extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Authorable;
    protected $fillable = [ 'package_id', 'is_free', 'test_series_id', 'description', 'note', 'active'];

    protected $casts = [
      'active' => 'boolean',
      'is_free' => 'boolean'
    ];

    public function testseries()
    {
      return $this->belongsTo(TestSeries::class, 'test_series_id');
    }
}
