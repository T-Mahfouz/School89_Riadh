<?php

namespace App\Http\Controllers\userRoles;

use App\Chat;
use App\Http\Controllers\Controller;
use App\Post;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SuperVisorController extends Controller
{

	public function __construct()
	{
		$this->middleware('auth');
		$this->middleware('supervisor');
	}

	public function index(Request $request)
	{
		$admins = count(User::where('role_id',4)->get());
		$principles = count(User::where('role_id',3)->get());
		$teachers = count(User::where('role_id',2)->get());
		$parents = count(User::where('role_id',1)->get());
		$posts = count(Post::where('post_id',0)->get());
		$messages = count(Chat::all());

		return view('userRoles.supervisor.home')->with([
			'admins' => $admins,
			'principles' => $principles,
			'teachers' => $teachers,
			'parents' => $parents,
			'posts' => $posts,
			'messages' => $messages
		]);
	}

	public function editUserView(Request $request)
	{
		$userID = $request->uid;
		$role = $request->urole;

		$user = User::find($userID);
		if(!$user || $user->role->name != $role){
			session()->flash('feedback','User with this role not found');
			return redirect()->back();

		}
		
		return view('userRoles.supervisor.edituser')->with([
			'user' => $user
		]);
	}

	public function editUser(Request $request)//Edit Admin
	{
		//return $request->all();
		$userID = $request->uid;
		$user = User::find($userID);
		if(!$user){
			session()->flash('feedback','User not found');
			return redirect()->back();
		}

		$phoneNotValid = $this->validatePhone($request);
		if($phoneNotValid){
			session()->flash('feedback',$phoneNotValid);
			return redirect()->back();
		}

		$phone = $request->phone;
		$name = $request->name;
		if(!$name)
			$name = $user->name;

		if($request->password){
			$passwordNotValid = $this->validatePassword($request);
			if($passwordNotValid){
				session()->flash('feedback',$passwordNotValid);
				return redirect()->back();
			}
			$user->password = bcrypt($request->password);
		}

		$user->name = $name;
		$user->phone = $phone;

		$user->update();
		
		session()->flash('feedback','User data has been updated successfully.');
		return redirect()->back();
	}

	public function deleteUser(Request $request)//Delete Admin
	{
		$userID = $request->uid;
		$user = User::find($userID);
		if(!$user){
			session()->flash('feedback','User not found');
			return redirect()->back();
		}
		$user->delete();
		session()->flash('feedback','User has been deleted successfully.');
		return redirect()->back();
	}

	public function addUserView(Request $request)
	{
		$role = $request->urole;
		return view('userRoles.supervisor.adduser')->with(['role'=>$role]);
	}

	public function addUser(Request $request)
	{
		$phone = '00966'.$request->phone;
		$request->merge(['phone' => $phone]);

		$notValid = $this->addUserValidation($request);
		if($notValid){
			session()->flash('feedback',$notValid);
			return redirect()->back();
		}

		$role = $request->urole;
		$roleID = 1;
		switch ($role) {
			case 'admin':
			$roleID = 4;
			break;
			case 'principle':
			$roleID = 3;
			break;
			case 'teacher':
			$roleID = 2;
			break;
			case 'parent':
			$roleID = 1;
			break;

			default:
			{
				session()->flash('feedback','non valid role');
				return redirect()->back();
			}
			break;
		}

		$user = User::create([
			'name' => $request->name,
			'image' => 'avatar.jpg',
			'phone' => $phone,
			'password' => bcrypt($request->password),
			'role_id' => $roleID,
		]);

		$user->status = 1;
		$user->update();

		session()->flash('feedback','User has been added successfully.');
		return redirect()->back();
	}

	/*for admins*/
	public function admins(Request $request)// Get Admins
	{
		$admins = User::where('role_id',4)->get();

		return view('userRoles.supervisor.admins')->with([
			'admins' => $admins
		]);
	}

	/*for principles*/
	public function principles(Request $request)
	{
		$principles = User::where('role_id',3)->get();

		return view('userRoles.supervisor.principles')->with([
			'principles' => $principles
		]);
	}

	/*for teachers*/
	public function teachers(Request $request)
	{
		$teachers = User::where('role_id',2)->get();
		return view('userRoles.supervisor.teachers')->with([
			'teachers' => $teachers
		]);
	}

	/*for parents*/
	public function parents(Request $request)
	{
		$parents = User::where('role_id',1)->get();

		return view('userRoles.supervisor.parents')->with([
			'parents' => $parents
		]);
	}

	/*for posts*/
	public function posts(Request $request)
	{
		$posts = Post::where('post_id',0)->get();
		$allUsers = User::all();
		$users = array();
		foreach($allUsers as $user){
			if(count($user->posts)){
				$users[] = $user;
			}
		}

		return view('userRoles.supervisor.posts')->with([
			'users' => $users,
			'posts' => $posts
		]);
	}

	/*for messages*/
	public function messages(Request $request)
	{
		$messages = Chat::all();
		$allUsers = User::all();
		$users = array();
		foreach($allUsers as $user){
			if(count($user->messages)){
				$users[] = $user;
			}
		}

		return view('userRoles.supervisor.messages')->with([
			'users' => $users,
			'messages' => $messages
		]);
	}


	public function validatePhone(Request $request)
	{
		$phone = '00966'.$request->phone;
		$request->merge(['phone' => $phone]);

		$error = "";
		$valid = Validator::make($request->only('phone'), 
			['phone' => 'digits:14|unique:users,phone,'.$request->uid]);
		if($valid->fails())
		{
			$errors = $valid->errors()->toArray();
			$error = implode("",$errors['phone']); 
		}
		return $error;
	}

	public function validatePassword(Request $request)
	{
		$error = "";
		$valid = Validator::make($request->only('password'), 
			['password' => 'string|min:4']);
		if($valid->fails())
		{
			$errors = $valid->errors()->toArray();
			$error = implode("",$errors['password']); 
		}
		return $error;
	}

	public function addUserValidation(Request $request)
	{
		$error = "";
		/*password*/
		$passwordValid = Validator::make($request->only('password'), 
			['password' => 'required|string|min:4']);
		if($passwordValid->fails())
		{
			$errors = $passwordValid->errors()->toArray();
			$error = implode("",$errors['password']); 
		}
		/*phone*/
		$phoneValid = Validator::make($request->only('phone'), 
			['phone' => 'required|digits:14|unique:users,phone,'.$request->uid]);
		if($phoneValid->fails())
		{
			$errors = $phoneValid->errors()->toArray();
			$error = implode("",$errors['phone']); 
		}
		/*name*/
		$nameValid = Validator::make($request->only('name'), 
			['name' => 'required']);
		if($nameValid->fails())
		{
			$errors = $nameValid->errors()->toArray();
			$error = implode("",$errors['name']); 
		}

		return $error;
	}



}
