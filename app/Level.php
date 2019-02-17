<?php

namespace App;

use App\Sort;
use App\Student;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{

    public function Students()
    {
    	
    	return $this->hasMany(Student::class,'level_id');
    }

    public function teachers()
    {

        return $this->belongsToMany(User::class,'teachers');
    }
    
    public function classes()
    {
        return $this->hasMany(Sort::class,"level_id");
    }



}
