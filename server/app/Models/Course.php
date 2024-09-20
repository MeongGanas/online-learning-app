<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function sets()
    {
        return $this->hasOne(Set::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}
