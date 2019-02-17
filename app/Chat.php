<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $guarded = [];

    public function from()
    {
    	return $this->belongsTo(User::class,'from_id');
    }

    public function to()
    {
    	return $this->belongsTo(User::class,'to_id');
    }
    
    
}
