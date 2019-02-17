<?php

namespace App;

use App\Student;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    public function students()
    {
    	return $this->belongsToMany(Student::class,'student_subjects');
    }

    public function teachers()
    {
    	return $this->belongsToMany(User::class,'teachers');
    }


}
