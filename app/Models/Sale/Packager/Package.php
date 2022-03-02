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
    protected $fillable = [ 'title', 'description', 'image', 'regular_price', 'sell_price', 'is_free', 'note', 'active'];

    protected $casts = [
      'active' => 'boolean',
      'is_free' => 'boolean',
    ];

}
