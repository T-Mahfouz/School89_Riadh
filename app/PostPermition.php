<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostPermition extends Model
{
	protected $guarded = [];

	public function post()
	{
		return $this->belongsTo(Post::class,'post_id');
	}
	
}
