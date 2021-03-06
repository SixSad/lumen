<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{

    protected $table = 'courses';
    public $timestamps = false;
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    public function capacity()
    {
        return $this->student_capacity;
    }

    public function courses(): HasMany
    {
        return $this->hasMany('App\Models\CourseUser', 'course_id');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany('App\Models\Lesson');
    }
}
