<?php

namespace App\Models\LiveClasses;

use App\Traits\Authorable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LiveSession extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Authorable;
    protected $fillable = ['live_class_id', 'date', 'timing', 'link', 'description', 'note', 'active'];

    protected $casts = [
      'active' => 'boolean',
    ];

    public function liveClass()
    {
      return $this->belongsTo(LiveClass::class);
    }

}
