<?php

namespace App\Models\Sale\Packager;

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

    /*public function getStartAttribute()
    {
        return date('d-m-Y @ H:i A', strtotime($this->start));
    }*/
    protected $casts = [
      'active' => 'boolean',
      'access_forever' => 'boolean',
      'is_free' => 'boolean',
      // 'start' => 'date',
      // 'end' => 'date',
    ];

}
