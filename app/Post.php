<?php

namespace App;

use App\Like;
use App\PostImage;
use App\PostPermition;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
	
    protected $guarded = [];

    public function user()
    {

    	return $this->belongsTo(User::class,'user_id');
    }

    public function image()
    {

    	return $this->hasOne(PostImage::class,'post_id');
    }
    
    public function permition()
    {

        return $this->hasOne(PostPermition::class,'post_id');
    }

    public function likes()
    {

        return $this->hasMany(Like::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class,'post_id');
    }

    public function comments()
    {
        return $this->hasMany(Post::class,'post_id');
    }

    public function isLiked()
    {
        $user = Auth::user();
        $isLiked = 0;
        $like = Like::where([
            ['post_id','=',$this->id],
            ['user_id','=',$user->id]
        ])->first();
        if($like)
            $isLiked = 1;

        return $isLiked;
    }







}
