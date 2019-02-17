<?php

namespace App\Http\Controllers\userRoles;

use App\About;
use App\BlockedUser;
use App\Chat;
use App\Http\Controllers\Controller;
use App\Level;
use App\Like;
use App\Post;
use App\PostImage;
use App\PostPermition;
use App\Sort;
use App\Student;
use App\StudentSubject;
use App\Subject;
use App\Teacher;
use App\UltraMsgContent;
use App\Url;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	*/
	public function __construct()
	{
		$this->middleware('auth');
		$this->middleware('admin');
	}

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
    */

    public function index()
    {	
    	return view('userRoles.admin.home');
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

            /*if($re->wantsJson())
                return response()->json([
                    'status_code' => 200,
                    'message' => 'Request process succeed.',
                    'data' => $schedule
                ],200);*/

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
                if($lev)
                    $sc['levelName'] = $lev->name;
                if($cla)
                    $sc['className'] = $cla->name;
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
        
    

        return view('userRoles.admin.teachers',with([
            'teachers' => $teachers
        ]));            
    }

    public function editTeacherView(Request $re)
    {
    	$teacherId = $re->tid;

    	$teacher = User::where('id',$teacherId)->first();

    	$teacherClasses = $teacher->classes->toArray();
    	$teacherLevels = $teacher->levels->toArray();
    	$teacherSubjects = $teacher->subjects->toArray();

    	$tclasses = $this->ClassesLevelsToString($teacherClasses);
    	$tlevels = $this->ClassesLevelsToString($teacherLevels);
    	$tsubjects = $this->ClassesLevelsToString($teacherSubjects);

    	$alllevels = Level::all();
    	$allclasses = Sort::orderBy('level_id')->get();
    	$alllsubjects = Subject::all();

        //return $tclasses;    
    	return view('userRoles.admin.editteacher',with([
    		'teacher' => $teacher,
    		'tclasses' => $tclasses,
    		'tlevels' => $tlevels,
    		'tsubjects' => $tsubjects,
    		'editclasses' => $teacherClasses,
    		'editsubjects' => $teacherSubjects,
    		'classes' => $allclasses,
    		'levels' => $alllevels,
    		'subjects' => $alllsubjects

    	]));
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

	public function addTeacherView()
	{

		return view('userRoles.admin.addteacher');
	}

	public function addTeacher(Request $re)
	{
       /* if($re->wantsJson())
            return response()->json([
                'status_code' => 400,
                'message' => $re->all(),
                'data' => 'ads'
            ],200);*/
                       
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
	                ],200);
	        }

		$user = User::create([
			'name' => $re->name,         
			'phone' => $re->phone,
			'image' => 'avatar.jpg',
			'role_id' => 2,
			'password' => bcrypt($re->password),
		]);
        
       $inviteMsg = UltraMsgContent::where('type','inviteTeacher')->first();

        $message = $inviteMsg->content." ".$oldPhone.",  ".$re->password;
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
		

		return redirect()->route('admins.edit.teacher.view',['tid'=>$user->id]);   
	}

	public function parents(Request $re)
	{
		$parents = User::where('role_id',1)->get();

        if($re->wantsJson())
            return response()->json([
                'status_code'=> 200,
                'message'=>'Request process succeed.',
                'data'=>$parents
            ],200);
		

		return view('userRoles.admin.parents',with([
			'parents' => $parents
		]));
	}

	public function viewParent(Request $re)
	{
		$parentId = $re->pid;
		$parent = User::find($parentId);
        $children = array();
        if($parent)
        {
            $children = $parent->children;
            if($re->wantsJson())
                return response()->json([
                    'status_code'=> 200,
                    'message'=>'Request process succeed.',
                    'data'=>$parent
                ],200);
        }
        else
        {
            if($re->wantsJson())
                return response()->json([
                    'status_code'=> 404,
                    'message'=>'User not found.',
                    'data'=>[]
                ],200);

            return redirect()->back();
        }
		
		

		return view('userRoles.admin.viewparent',with([
			'parent' => $parent,
			'children' => $children
		]));
	}

	public function contactus(Request $re)
	{
		$admins = User::whereIn('role_id',[3,4])->pluck('id');
                
        $parentId = Auth::user()->id;
        $msgs = array();
        $allMsgs = Chat::whereIn('to_id',$admins)->get();
        foreach ($allMsgs as $msg) {
            $sender = User::find($msg->from_id);
            if($sender){
                $msg['senderName'] = $sender->name;
                $msg['senderImage'] = $sender->image;
                $msgs[] = $msg;
            }
        }

        if($re->wantsJson())
            return response()->json([
                'status_code'=> 200,
                'message'=>'Request process succeed.',
                'data'=>$msgs
            ],200);

		return view('userRoles.admin.contactus',with([
			'msgs' => $msgs
		]));
	}

	public function schoolUrlsView()
	{
		$urls = Url::first();
		return view('userRoles.admin.schoolurls',compact('urls'));
	}

	public function editSchoolUrls(Request $re)
	{
		$urls = Url::first();

		$images = $urls->images;
		$videos = $urls->videos;
		$location = $urls->location;

		if($re->images)
			$images = $re->images;

		if($re->videos)
					$videos = $re->videos;

		if($re->location)
			$location = $re->location;

		$urls->images = $images;
		$urls->videos = $videos;
		$urls->location = $location;

		$updated = $urls->update();

        unset($urls['id']);
        unset($urls['created_at']);
        unset($urls['updated_at']);

        if($updated)
        {
            if($re->wantsJson())
            return response()->json([
                'status_code'=> 201,
                'message'=>'Update process succeed.',
                'data'=>$urls
            ],200);
        }
        else
        {
           if($re->wantsJson())
            return response()->json([
                'status_code'=> 400,
                'message'=>'Bad Request, not updated.',
                'data'=>[]
            ],200); 
        }


		return redirect()->back();
	}

	public function ClassesLevelsToString($arr)
	{
		$names = array();
		foreach ($arr as $item) {
			$names[] = $item['name'];
		}
		$names = array_unique($names);
		$str = implode(" - ", $names);
		return rtrim($str,", ");
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
    

    public function resetNewYear(Request $request)
    {
        $images = array();

        Like::truncate();
        Post::truncate();
        PostPermition::truncate();

        $postImages = PostImage::all();
        if($postImages){
           foreach ($postImages as $postImage){
                $image = public_path().'/images/posts/'.$postImage->image;
                if(file_exists($image))
                    unlink($image);
            } 
        }
        
        PostImage::truncate();

        Chat::truncate();
        Student::truncate();
        Teacher::truncate();
        StudentSubject::truncate();
        BlockedUser::truncate();

        $parents = User::where('role_id',1)->get();
        foreach ($parents as $parent) {
            if($parent->image != "avatar.jpg")
            {
              $image = public_path().'/images/users/'.$parent->image;
              //$images[] = $image;
              if(file_exists($image))
                unlink($image);
            }
            //return $images;
            
            $parent->delete();
        }

        if($request->wantsJson())
            return response()->json([
                'status_code' => 200,
                'message' => 'Request process succeed.',
                'data' => []
            ],200);

        return redirect()->route('home');
    }


    public function blockedParents(Request $request)
    {
        $parents = User::where('role_id',1)->get();
        $blocked = array();
        foreach ($parents as $parent) {
            if($parent->isBlocked())
                $blocked[] = $parent;
        }
        if($request->wantsJson())
            return response()->json([
                'status_code' => 200,
                'message' => 'Request process succeed.',
                'data' => $blocked
            ],200);  
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
    
    public function addnewlevel(Request $request)
    {
    	$check = $this->validateLevelName($request);
    	$classes = $request->classes;
        if($check)
        {
            if($request->wantsJson())
                return response()->json([
                    'status_code' => 400,
                    'message' => $check,
                    'data' => []
                ],400);
        }

    	$level = new Level();
    	$level->name = $request->name;
    	$added = $level->save();
    	
    	$lastLevel = Level::orderBy('created_at', 'desc')->first();
    	###################################################
    	if($added){
    	   if($classes){
    	        foreach($classes as $class){
    		    $notvalid = $this->validateClassName($class,$lastLevel->id);
    		    if($notvalid){
    		    	if($request->wantsJson())
		                return response()->json([
		                    'status_code' => 400,
		                    'message' => $notvalid,
		                    'data' => []
		                ],200);
    		    }
    		    $newclass = new Sort();
    		    $newclass->name = $class;
    		    $newclass->level_id = $lastLevel->id;
    		    $newclass->save();
    		}
    	   }
    	}
    	###################################################
    	if($request->wantsJson())
            return response()->json([
                'status_code' => 201,
                'message' => "Adding process succeed.",
                'data' => $level
            ],200);   
    }
    
    public function addnewclass(Request $request)
    {
    	$levelId = $request->level_id;
    	$classes = $request->classes;
    	$levelexisted = Level::find($levelId);
    	if($levelexisted)
    	{
    		if($classes)
    		{
	    	        foreach($classes as $class){
			    $notvalid = $this->validateClassName($class,$levelId);
			    if($notvalid){
			    	if($request->wantsJson())
			                return response()->json([
			                    'status_code' => 400,
			                    'message' => $notvalid,
			                    'data' => []
			                ],200);
			    }
			    $newclass = new Sort();
			    $newclass->name = $class;
			    $newclass->level_id = $levelId;
			    $newclass->save();
			}
	        }
    	}
    	else
    	{
    	   if($request->wantsJson())
                return response()->json([
                    'status_code' => 404,
                    'message' => "Level not existed.",
                    'data' => []
                ],200);
    	}
    	
    	if($request->wantsJson())
                return response()->json([
                    'status_code' => 201,
                    'message' => "Adding process succeed.",
                    'data' => $levelexisted->classes
                ],200);
    	
    }
    
    public function editclass(Request $request)
    {
    	$classId = $request->class_id;
    	$levelId = $request->level_id;
    	$className = $request->name;
    	
    	$class = Sort::find($classId);
    	if($class ){
    		$checkLevel = Level::find($levelId);
    		if($checkLevel){
    			if($className){
    				$notvalid = $this->validateClassName($className);
    				if($notvalid){
    				    	if($request->wantsJson())
    				                return response()->json([
    				                    'status_code' => 400,
    				                    'message' => $notvalid,
    				                    'data' => []
    				                ],200);
    				}
    			
    				$class->level_id = $levelId;
    				$class->name = $className;
    				
    				$class->update();
    			}
    			else{
    				if($request->wantsJson())
    			                return response()->json([
    			                    'status_code' => 400,
    			                    'message' => "Class name shouldn't be empty.",
    			                    'data' => []
    			                ],200);
    			}
    			
    		}
    		else{
    			if($request->wantsJson())
    		                return response()->json([
    		                    'status_code' => 404,
    		                    'message' => "Level not found.",
    		                    'data' => []
    		                ],200);
    		}
    	}
    	else{
    		if($request->wantsJson())
    	                return response()->json([
    	                    'status_code' => 404,
    	                    'message' => "Class not found.",
    	                    'data' => []
    	                ],200);
    	}
	
	
	   if($request->wantsJson())
            return response()->json([
                'status_code' => 201,
                'message' => "Edit process succeed.",
                'data' => $class
            ],200);
    }

    public function addNewSubject(Request $request)
    {
        $names = $request->names;
        if(!$names){
            if($request->wantsJson())
                return response()->json([
                    'status_code' => 400,
                    'message' => "يجب إدخال اسم المادة.",
                    'data' => []
                ],400);
        }

        foreach($names as $name){
            $notvalid = $this->validateSubjectName($name);
            if($notvalid){
                if($request->wantsJson())
                    return response()->json([
                        'status_code' => 400,
                        'message' => $notvalid,
                        'data' => []
                    ],400);
            }
            $subject = new Subject;
            $subject->name = $name;
            $subject->save();
        }
        
        $subjects = Subject::all();
        if($request->wantsJson())
            return response()->json([
                'status_code' => 201,
                'message' => "تمت الاضافة بنجاح",
                'data' => $subjects
            ],201);
    }
    
    
    public function validateLevelName(Request $request)
    {
    	$validationErrors = "";
    	$nameValidator = Validator::make($request->only("name"),
            	['name' => 'string|max:255|min:3|unique:levels']);
            
        if($nameValidator->fails())
        {
            $errors = $nameValidator->errors()->toArray();
            $validationErrors= "اسم المستوى موجود من قبل";
            //$validationErrors=implode("",$errors['name']);
        }
        
        return $validationErrors;
    }
    
    public function validateClassName($class,$levelId)
    {
    	$validationErrors = "";
    	$check = Sort::where([
            ['name','=',$class],
            ['level_id','=',$levelId]
        ])->first();

        if($check)   
            $validationErrors = "اسم الفصل موجود بالفعل فى هذا المستوى.";
        
        return $check;
        //return $validationErrors;
    }
    
    public function validateSubjectName($subject)
    {
        $validationErrors = "";
        $check = Subject::where('name',$subject)->first();

        if($check)   
            $validationErrors = "هذه المادة موجودة بالفعل.";
        
        return $check;
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
