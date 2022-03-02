<?php

namespace App\Models\TestSeries;

use App\Models\TestSeries\Question;
use App\Traits\Authorable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Option extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Authorable;
    protected $fillable = [ 'question_id', 'option', 'option_number', 'is_correct', 'explanation'];

    protected $casts = [
      'is_correct' => 'boolean'
    ];
    public function question()
    {
      return $this->belongsTo(Question::class);
    }

}
