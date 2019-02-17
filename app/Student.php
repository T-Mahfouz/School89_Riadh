<?php

namespace App;

use App\Level;
use App\Sort;
use App\Subject;
use App\Teacher;
use App\User;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{

    public function parent()
    {
    	return $this->belongsTo(User::class,'parent_id');
    }

    public function level()
    {
    	return $this->belongsTo(Level::class,'level_id');
    }

    public function class()
    {
    	return $this->belongsTo(Sort::class,'class_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class,'student_subjects');
    }

    public function studentTeachers($id)
    {
        $childId = (int)$id;
        $child = $this->where('id',$childId)->first();
        $classId = $child->class_id;
        $teachers = Teacher::distinct('user_id')
        ->where('class_id',$classId)->get();

        $ids = array();
        foreach ($teachers as $teacher)
            $ids[] = $teacher->user_id;

        return User::whereIn('id',$ids)->get();
    }



}
