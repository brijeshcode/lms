<?php

namespace App\Models\Shopping;

use App\Traits\Authorable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    use Authorable;
    protected $fillable = [ 'active'];

    protected $casts = [
      'active' => 'boolean',
    ];

}
