<?php

namespace App;

use App\Level;
use App\Student;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Sort extends Model
{
    public function Students()
    {
    	return $this->hasMany(Student::class,'class_id');
    }

    public function teachers()
    {	
        return $this->belongsToMany(User::class,'teachers','class_id','user_id');
    }
    
    public function classes()
    {
        return $this->belongsTo(Level::class,"level_id");
    }
    



}
