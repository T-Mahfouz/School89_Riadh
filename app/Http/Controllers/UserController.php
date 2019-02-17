<?php

namespace App\Http\Controllers;

use App\Like;
use App\Post;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Lcobucci\JWT\Parser;

class UserController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
		$this->middleware('block');
		$this->middleware('verified');
	}

	public function profileDetails(Request $request)
	{
		$userId = $request->uid;
		$user = User::find($userId);
		if($user)
		{
			if($user['bio'] == null)
				$user['bio'] = "";
			$subjects = $user->subjects->toArray();
			$posts = count($user->posts);
			$user['followers'] = $user->followers($userId);
			$user['numberOfPosts'] = $posts;
			if($user['posts'])
				unset($user['posts']);
			if($user['subjects'])
				unset($user['subjects']);

			$names = array();
			foreach ($subjects as $item) {
				$names[] = $item['name'];
			}
			$names = array_unique($names);
			$allsubjs = array();
			foreach($names as $item)
				$allsubjs[] = $item;
				
			$user['subjects'] = $allsubjs;	
			
			if($request->wantsJson())
				return response()->json([
					'status_code' => 200,
					'message' => 'تمت العملية بنجاح.',
					'data' => $user
				],200);
		}
		else
		{
			$errmsg = "هذا الحساب غير موجود";
			if($request->wantsJson())
				return response()->json([
					'status_code' => 404,
					'message' => $errmsg,
					'data' => []
				],200);
		}
	}

	public function userPosts(Request $request)
	{
		$userId = $request->uid;
		$user = User::find($userId);

		if(!$user)
		{
			$errmsg = "404 هذا الحساب غير موجود";
			if($request->wantsJson())
				return response()->json([
					'status_code' => 404,
					'message' => $errmsg,
					'data' => []
				],200);	
		}


		$posts = array();
		$allPosts = Post::select('posts.*','u.name as userName','u.image as userImage','pi.image as postImage')
				->leftJoin('users as u', 'u.id', '=', 'posts.user_id')
				->leftJoin('post_images as pi', 'pi.post_id', '=', 'posts.id')
				->leftJoin('likes as l', 'l.post_id', '=', 'posts.id')
				->where([
					['posts.user_id','=',$userId],
					['posts.post_id','=',0],
				])
				->orderBy('posts.created_at','DESC')->distinct()
				->get();


		foreach ($allPosts as $item) 
        {             
            $likes = Like::where('post_id',$item->id)->count();
            $item['isLiked'] = $item->isLiked();
            $item['likes'] = $likes;
            $posts[] = $item;
              
        }

        if($posts)
		{
			if($request->wantsJson())
				return response()->json([
					'status_code' => 200,
					'message' => 'تمت العملية بنجاح.',
					'data' => $posts
				],200);
		}
		else
		{
			$errmsg = "لا توجد منشورات لهذا المستخدم حتى الآن";
			if($request->wantsJson())
				return response()->json([
					'status_code' => 404,
					'message' => $errmsg,
					'data' => []
				],200);
		}

		

	}

	public function whoAmI(Request $request)
	{
		$me = Auth::user()->toArray();
		$myId = Auth::user()->id;
		if($me['bio'] == null)
			$me['bio'] = "";
			
		$me['followers'] = Auth::user()->followers($myId);
		$me['roleName'] = Auth::user()->role->name;
		if($request->wantsJson())
			return response()->json([
				'status_code' => 200,
				'message' => 'تمت العملية بنجاح.',
				'data' => $me,
			],200);
	}

	public function followers(Request $request)
	{
		$followers = Auth::user()->followers();
		if($request->wantsJson())
			return response()->json([
				'status_code' => 200,
				'message' => 'تمت العملية بنجاح.',
				'data' => [
					'followers' => $followers
				]
			],200);
	}
	
	public function settings(Request $re)
	{
		$validationErrors = "";
		
		$user = Auth::user();
		
		$nameValidator = Validator::make($re->only('name'),
		    ['name' => 'max:1000|min:3']);
		
		if($nameValidator->fails())
		{
		    $errors = $nameValidator->errors()->toArray();
		    $validationErrors = implode("",$errors['name']);
		    if($re->wantsJson())
		        return response()->json([
		            'status_code' => 400,
		            'message' => $validationErrors,
		            'data' => []
		        ],400);
		}
		
		
		$bio = Auth::user()->bio;
		if($re->bio)
		    $bio = $re->bio;
		
		$phone = Auth::user()->phone;
		if($re->phone)
		{
		    if(strcmp('00966'.$re->phone, $user->phone))
		    {
		        $phone = '00966'.$re->phone;
		        $re->merge(['phone' => $phone]);
		
		
		        $phonevalidator = Validator::make($re->only('phone'),
		            ['phone' => 'digits:14|unique:users,phone,$parent->id']);
		
		        if($phonevalidator->fails())
		        {
		            $errors=$phonevalidator->errors()->toArray();
		            $validationErrors = implode("",$errors['phone']);
		            
		            if($re->wantsJson())
		                return response()->json([
		                    'status_code' => 400,
		                    'message' => $validationErrors,
		                    'data' => []
		                ],400);
		        }
		
		
		    }
		}
		
		$password = Auth::user()->password;
		
		if($re->password)
		{
		    $passvalidator = Validator::make($re->only('password'),
		        ['password' => 'min:4']);
		
		    if($passvalidator->fails())
		    {
		        $errors=$passvalidator->errors()->toArray();
		        $validationErrors = implode("",$errors['password']);
		        if($re->wantsJson())
		            return response()->json([
		                'status_code' => 400,
		                'message' => $validationErrors,
		                'data' => []
		            ],400);
		    }
		
		    $password = bcrypt($re->password);
		}
		
		$fileName = $user->image;
		
		if($re->hasfile('image'))
		{
		    $destination = public_path('images/users');
		    $extension = $re->file('image')->getClientOriginalExtension();
		    $fileName = strtolower(rand(99999,99999999).uniqid().'.'.$extension);
		
		    $re->file('image')->move($destination, $fileName);
		}
		
		$user->name = $re->name;
		$user->phone = $phone;
		$user->bio = $bio;
		$user->password = $password;
		$user->image = $fileName;
		
		if($user->update())
		{
		    if($re->wantsJson())
		        return response()->json([
		            'status_code' => 201,
		            'message' => "تم التعديل بنجاح.",
		            'data' => Auth::user()
		        ],200);
		
		    session()->flash('updated','Update Succeed.');
		}
		
		//return redirect()->route('parents.showSettings');
	}
	
	public function logout(Request $request)
	{
		$uid = (int)$request->uid;
		$user = User::find($uid);
		if(Auth::user()->id == $uid){
		        $user->firebase_token = null;
		        $user->last_login = Carbon::now();
		        $user->update();
		
		 }else{
		        $json = [
		            'status_code' => 401,
		             'message' => 'غير مصرح لك.',
		             'data' => [],
		     ];
		}
		$value = $request->bearerToken();
		$id= (new Parser())->parse($value)->getHeader('jti');
		
		$token= DB::table('oauth_access_tokens')
		     ->where('id', '=', $id)
		        ->update(['revoked' => true]);
		
		 $json = [
		     'status_code' => 200,
		     'message' => 'تم تسجيل الخروج.',
		     'data' =>[],
		 ];
		 return response()->json($json, '200');
	
	}
	
    public function principle(Request $re)
    {
        $principle = User::where('role_id',3)->first();
        $posts = count($principle->posts);
	$principle['followers'] = $principle->followers($principle->id);
	$principle['numberOfPosts'] = $posts;
	if($principle['posts'])
		unset($principle['posts']);
        //$principle['followers'] = $principle->followers();
        //$principle['posts'] = count($principle->posts);
        
        if($re->wantsJson())
            return response()->json([
                'status_code' => 200,
                'message' => "تمت العملية بنجاح.",
                'data' => $principle
            ],200);

        
    } 
    
    
    
    

}
