<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function lesson_content(){
        return $this->belongsTo(LessonContent::class);
    }
}
