<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function completed_lessons()
    {
        return $this->hasMany(CompletedLesson::class);
    }

    public function sets()
    {
        return $this->belongsTo(Set::class);
    }

    public function lesson_contents()
    {
        return $this->hasMany(LessonContent::class);
    }
}
