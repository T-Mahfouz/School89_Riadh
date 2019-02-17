<?php

namespace App\Http\Controllers\userRoles;

use App\About;
use App\BlockedUser;
use App\Chat;
use App\Http\Controllers\Controller;
use App\Level;
use App\Sort;
use App\Subject;
use App\Teacher;
use App\UltraMsgContent;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PrincipleController extends Controller
{

	/**
	* Create a new controller instance.
	*
	* @return void
	*/
	public function __construct()
	{
	$this->middleware('auth');
	$this->middleware('principle');
	}
	
	/**
	* Show the application dashboard.
	*
	* @return \Illuminate\Http\Response
	*/
	public function index()
	{
	return view('userRoles.principle.home');
	}
	
	public function teachers(Request $re)
	{
        Carbon::setLocale('ar');
		$teachers = array();
        $isOnline = 0;
        $allteachers = User::where([
        	['id','!=',265],
        	['role_id','=',2]
        ])->get();
        foreach ($allteachers as $item) {
            $schedule = array();
            if($item->isOnline())
		        $isOnline = 1;
		    $item['isOnline'] = $isOnline;
		    if($item->last_login)
		    	$item['lastSeen'] = $item->last_login->diffForHumans();
		    else
		    	$item['lastSeen'] = "";
		    	
		    $duties = Teacher::where('user_id',$item->id)->get();
	            if($duties)
	            {
	                foreach($duties as $value)
	                {
	                    $schedule[] = $value;
	                }
	            }
	            foreach($schedule as $sc)
	            {
	                unset($sc['id']);
	                unset($sc['user_id']);
	                unset($sc['created_at']);
	                unset($sc['updated_at']);
	
	                $sub = Subject::find($sc->subject_id);
	                $lev = Level::find($sc->level_id);
	                $cla = Sort::find($sc->class_id);
					
					if($sub)
	                	$sc['subjectName'] = $sub->name;
	                else
	                	$sc['subjectName'] = "Not existed.";
	                if($lev)
	                	$sc['levelName'] = $lev->name;
	                else
	                	$sc['levelName'] = "Not existed.";
	                if($cla)
	                	$sc['className'] = $cla->name;
	                else
	                	$sc['className'] = "Not existed.";
	            }
	            
	
	            $item['schedule'] = $schedule;
	            
	            
		    $teachers[] = $item;
		}
		
		if($re->wantsJson())
		    return response()->json([
		        'status_code' => 200,
		        'message' => 'Request process succeed.',
		        'data' => $teachers
		    ],200);
		
		return view('userRoles.principle.teachers',with([
		    'teachers' => $teachers
		]));
	}
	
	public function parents(Request $re)
	{
		$allParents = User::where('role_id',1)->get();
		$parents = array();
		foreach($allParents as $item){
			if($item->isBlocked())
				$item['isBlocked'] = 1;
			else
				$item['isBlocked'] = 0;
			
			$parents[] = $item;
		}
		
		if($re->wantsJson())
		    return response()->json([
		        'status_code'=> 200,
		        'message'=>'Request process succeed.',
		        'data'=>$parents
		    ],200);
		
		
		/*return view('userRoles.admin.parents',with([
		    'parents' => $parents
		]));*/
	}
	
	public function contactus(Request $re)
	{
		$adPrincs = User::whereIn('role_id',[3,4])->pluck('id');
		
		$msgs = array();
		$allMsgs = Chat::whereIn('to_id',$adPrincs)->get();
		foreach ($allMsgs as $msg){
		       $sender = User::find($msg->from_id); 
		       $msg['senderName'] = $sender->name;
		       $msg['senderImage'] = $sender->image;
		
		       $msgs[] = $msg;
		}
		
		
		if($re->wantsJson())
		    return response()->json([
		        'status_code' => 200,
		        'message' => 'Request process succeed.',
		        'data' => $msgs
		    ],200);
		
		return view('userRoles.principle.contactus',with([
		    'msgs' => $msgs
		]));    
	}
	
	public function sendView()
	{
		return view('userRoles.principle.send');
	}

	public function editTeacher(Request $re)
    {

    	$teacher = new Teacher();
    	$tid = $re->tid;
    	$user = User::find($tid);
    	if(!$user)
    	{
    		$errMsg = "هذا الحساب غير موجود.";
            if($re->wantsJson())
            return response()->json([
                'status_code'=> 404,
                'message'=>$errMsg,
                'data'=>[]
            ],404);
            
        }

        if($user->role_id != 2){
            if($re->wantsJson())
                return response()->json([
                    'status_code'=> 400,
                    'message'=>"هذا المستخدم ليس مدرس",
                    'data'=>[]
                ],400);
        }

        $schedules = $re->schedule;
        if(count($schedules) > 0){
            foreach ($schedules as $schedule){

                $ifexisted = Teacher::where([
                    ['user_id','=',$tid],
                    ['level_id','=',$schedule['level_id']],
                    ['class_id','=',$schedule['class_id']],
                    ['subject_id','=',$schedule['subject_id']],
                ])->first();
                if($ifexisted)
                    continue;

                $teacher = new Teacher();
                $teacher->user_id = $user->id;
                $teacher->level_id = $schedule['level_id'];
                $teacher->class_id = $schedule['class_id'];
                $teacher->subject_id = $schedule['subject_id'];
                $added = $teacher->save(); 
            }
            $last = Teacher::where('user_id',$tid)
                ->orderBy('created_at','DESC')
                ->first();
                if($re->wantsJson())
                    return response()->json([
                        'status_code'=> 201,
                        'message'=>'Adding process succeed.',
                        'data'=>$last
                    ],200);
        }

    	return redirect()->back();
    }
    
    public function unassignclass(Request $re)
    {
    	$teacherId = $re->tid;
        $classId = $re->cid;
    	$subjectId = $re->sid;
    	$deleted = array();
        $user = User::find($teacherId);
    	$teacher = Teacher::where([
            ['user_id','=',$teacherId],
            ['class_id','=',$classId],
            ['subject_id','=',$subjectId]
        ])->first();
        $tclasses = Teacher::where([
    		['user_id','=',$teacherId],
    		['class_id','=',$classId],
            ['subject_id','=',$subjectId]
    	])->get();

    	if($teacher)
    	{
    		foreach ($tclasses as $item){
    	    	$deleted[] = $item->delete();
    	    }
            $classes = $user->classes;
            if($re->wantsJson())
                return response()->json([
                    'status_code'=> 204,
                    'message'=>'Delete process succeed.',
                    'data'=>$classes
                ],200);
    	}
    	else
    	{
			$errMsg = "Teacher not found.";
            if($re->wantsJson())
                return response()->json([
                    'status_code'=> 404,
                    'message'=>$errMsg,
                    'data'=>[]
                ],200);
    	}
    	return redirect()->back();
    }
	
	public function addTeacher(Request $re)
	{
		$schedules = $re->schedule;
	    $class = $re->class;
	    $subject = $re->subject;
	    
	    $oldPhone = $re->phone;
		$phone = '00966'.$re->phone;
	        $re->merge(['phone' => $phone]);
	        $last = User::where('role_id',2)
	                      ->orderBy('created_at','DESC')->first();

		 $check = $this->validator($re);
	        if($check)
	        {
	            if($re->wantsJson())
	                return response()->json([
	                    'status_code' => 400,
	                    'message' => $check,
	                    'data' => []
	                ],400);
	        }

		$user = User::create([
			'name' => $re->name,         
			'phone' => $re->phone,
			'image' => 'avatar.jpg',
			'role_id' => 2,
			'password' => bcrypt($re->password),
		]);

		$inviteMsg = UltraMsgContent::where('type','inviteTeacher')->first();

		$message = $inviteMsg->content." ".$oldPhone.", ".$re->password;
		//$message = "عزيزتى المعلمة تدعوك المدرسة الابتدائية 89 بالرياض للاشتراك بتطبيق المدرسة الرسمى علما بأن اسم المستخدم: ".$oldPhone." وكلمة السر: ".$re->password;

        $user->status = 1;
        $user->update();
        $user->sendVC($message,$user->phone);

        if($user)
        {
	        if(count($schedules) > 0){
	            foreach ($schedules as $schedule) {
	                    $teacher = new Teacher();
	                    $teacher->user_id = $user->id;
	                    $teacher->level_id = $schedule['level_id'];
	                    $teacher->class_id = $schedule['class_id'];
	                    $teacher->subject_id = $schedule['subject_id'];
	
	                    $added = $teacher->save();
	            }
	        }

            $subjects = $user->subjects->toArray();
            if($user['subjects'])
                unset($user['subjects']);

            $names = array();
            foreach ($subjects as $item) {
                $names[] = $item['name'];
            }
            $names = array_unique($names);
            $user['subjects'] = $names;

            return response()->json([
                'status_code'=> 201,
                'message'=>'Adding process succeed.',
                'data'=>$user
            ],200);
        }
		

		//return redirect()->route('admins.edit.teacher.view',['tid'=>$user->id]);   
	}
	
	
	
	public function onlineTeachers(Request $request)
	{
	$teachers = User::where('role_id',2)->get();
	$online = array();
	foreach ($teachers as $teacher){
	    if($teacher->isOnline())
	        $online[] = $teacher;
	}
	
	if($request->wantsJson())
	    return response()->json([
	        'status_code' => 200,
	        'message' => 'Request process succeed.',
	        'data' => $online
	    ],200);
	}
	
	public function blockParent(Request $request)
	{
	$parentId = $request->pid;
	$parent = User::find($parentId);
	if($parent)
	{
	    if($parent->role_id == 1)
	    {
	        $check=BlockedUser::where('user_id',$parentId)->first();
	        if($check)
	        {
	            if($request->wantsJson())
	                return response()->json([
	                    'status_code' => 400,
	                    'message' => "Bad Request, this parent is already blocked.",
	                    'data' => []
	                ],200);
	        }
	        else
	        {
	            $blcok = new BlockedUser();
	            $blcok->user_id = $parentId;
	
	            $blcok->save();
	
	            if($request->wantsJson())
	                return response()->json([
	                    'status_code' => 200,
	                    'message' => "Request process succeed.",
	                    'data' => []
	                ],200);
	        }
	    }
	    else
	    {
	        if($request->wantsJson())
	            return response()->json([
	                'status_code' => 400,
	                'message' => "Bad Request, this user isn't parent.",
	                'data' => []
	            ],200);
	    }
	}
	else
	{
	    if($request->wantsJson())
	        return response()->json([
	            'status_code' => 404,
	            'message' => "User not found.",
	            'data' => []
	        ],200);
	}
	return redirect()->back();
	}
	
    protected function validator(Request $re)
    {

        $validationErrors = "";

        $nameValidator = Validator::make($re->only('name'), 
            ['name' => 'required|string|max:255|min:3']);

        if($nameValidator->fails())
        {
            $errors = $nameValidator->errors()->toArray();
            $validationErrors=implode("",$errors['name']);
        }
        /* ============ password ========== */
        $passvalidator = Validator::make($re->only('password'),
            ['password' => 'required|string|min:4']);

        if($passvalidator->fails())
        {
            $errors=$passvalidator->errors()->toArray();
            $validationErrors=implode("",$errors['password']);
        }
        /* ============ Phone ========== */
        $phonevalidator = Validator::make($re->only('phone'), 
            ['phone' => 'digits:14|unique:users,phone,$parent->id']);

        if($phonevalidator->fails())
        {
            $errors=$phonevalidator->errors()->toArray();
            $validationErrors = implode("",$errors['phone']);
        }

        return $validationErrors;


        /*return Validator::make($data, [
            'name' => 'required|string|max:255',
            'phone' => 'required|digits:14|unique:users',
            'password' => 'required|string|min:4',
        ]);*/
    }
    
    public function unBlockParent(Request $request)
    {
        $parentId = $request->pid;
        $parent = User::find($parentId);
        if($parent)
        {
            
            $blocked=BlockedUser::where('user_id',$parentId)->first();
            if($blocked)
            {
                $blocked->delete();
                if($request->wantsJson())
                    return response()->json([
                        'status_code' => 200,
                        'message' => "Request process succeed.",
                        'data' => []
                    ],200);
            }
            else
            {
                if($request->wantsJson())
                return response()->json([
                    'status_code' => 400,
                    'message' => "Bad Request, this parent is already unblocked.",
                    'data' => []
                ],200);
            } 
        }
        else
        {
            if($request->wantsJson())
                return response()->json([
                    'status_code' => 404,
                    'message' => "User not found.",
                    'data' => []
                ],200);
        }
        return redirect()->back();
    }

    public function editAboutUs(Request $request)
    {
        $lang = $request->lang;
        if(!$lang){
            if($request->wantsJson())
                return response()->json([
                    'status_code' => 400,
                    'message' => "حدد اللغة التى تريد التعديل على محتواها.",
                    'data' => []
                ],400);
        }
        $about = About::where('lang',$lang)->first();
        if(!$about){
            if($request->wantsJson())
                return response()->json([
                    'status_code' => 404,
                    'message' => "هذه اللغة غير مدعومة.",
                    'data' => []
                ],404);
        }
        $content = $about->content;
        if($request->content)
            $content = $request->content;
        $about->content = $content;
        $about->update();
        if($request->wantsJson())
            return response()->json([
                'status_code' => 201,
                'message' => "تم التعديل بنجاح.",
                'data' => $about
            ],201);
    }

    public function addAboutUs(Request $request)
    {
        $lang = $request->lang;
        if(!$lang){
            if($request->wantsJson())
                return response()->json([
                    'status_code' => 400,
                    'message' => "حدد اللغة التى تود إضافة محتوى لها.",
                    'data' => []
                ],400);
        }
        $about = About::where('lang',$lang)->first();
        if($about){
            if($request->wantsJson())
                return response()->json([
                    'status_code' => 400,
                    'message' => "هذه اللغة مدعومة بالفعل يمكنك التعديل عليها.",
                    'data' => []
                ],400);
        }
        $content = $request->content;
        if(!$content){
            if($request->wantsJson())
                return response()->json([
                    'status_code' => 400,
                    'message' => "برجاء ادخال المحتوى.",
                    'data' => []
                ],400);
        }

        $newAbout = About::create([
            'lang' => $lang,
            'content' => $content,
        ]);

        if($request->wantsJson())
            return response()->json([
                'status_code' => 201,
                'message' => "تمت الإضافة بنجاح.",
                'data' => $newAbout
            ],201);
    }

}
