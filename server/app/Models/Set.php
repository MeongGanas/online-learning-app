<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Set extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function lessons()
    {
        return $this->hasOne(Lesson::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
