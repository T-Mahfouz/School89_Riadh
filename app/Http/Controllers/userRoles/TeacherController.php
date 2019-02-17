<?php

namespace App\Http\Controllers\userRoles;

use App\BlockedUser;
use App\Chat;
use App\Http\Controllers\Controller;
use App\Level;
use App\Sort;
use App\Student;
use App\Subject;
use App\Teacher;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('teacher');
        $this->middleware('verified');   
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    	return view('userRoles.teacher.home');
    }

    public function students()
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id',$user->id)->get();

        $levelsIDs = $classesIDs = $subjectsIDs = array();
        $levels = $classes = $subjects = array();

        foreach ($teacher as $item) {
            $levelsIDs[] = $item->level_id;
            $classesIDs[] = $item->class_id;
            $subjectsIDs[] = $item->subject_id;
        }

        $levels = Level::whereIn('id',$levelsIDs)->get();
        $classes = Sort::whereIn('id',$classesIDs)->get();
        $subjects = Subject::whereIn('id',$subjectsIDs)->get();

        return view('userRoles.teacher.students',with([
            'user'=>$user,
            'levels'=>$levels,
            'classes'=>$classes,
            'subjects'=>$subjects
        ]));
    }

    public function classes(Request $request)
    {
        $tclassses = Auth::user()->classes->toArray();
        $tlevels = Auth::user()->levels->toArray();
        $classes = $this->ClassesLevelsToString($tclassses);
        $levels = $this->ClassesLevelsToString($tlevels);
        if($request->wantsJson())
            return response()->json([
                'status_code' => 200,
                'message' => 'Request process succeed.',
                'data' => $tclassses,
            ],200);
        return view('userRoles.teacher.classes',with([
            'classes' => $classes,
            'levels' => $levels
        ]));
    }

    public function displaystd(Request $request)
    {
        $user = Auth::user();
        $data = $request->all();
        $classId = $data['class'];
        $class = Sort::find($classId);       
        $teacher = Teacher::where('user_id',$user->id)->get();
        $classesIDs = $students = array();
        foreach ($teacher as $item)
            $classesIDs[] = $item->class_id;

        if($classId)
        {
            if($class)
            {
                if(in_array($classId, $classesIDs))
                {
                    $students = Student::where('class_id',$classId)->get();
                }
                else
                {
                    if($request->wantsJson())
                        return response()->json([
                            'status_code' => 403,
                            'message' => "Forbidden you don't teach this class.",
                            'data' => [],
                        ],200);
                }
            }
            else
            {
                if($request->wantsJson())
                    return response()->json([
                        'status_code' => 404,
                        'message' => "class not found.",
                        'data' => [],
                    ],200);
            }
        }
        else
        {
            $students = Student::whereIn('class_id',$classesIDs)->get();
        }
        if($request->wantsJson())
            return response()->json([
                'status_code'=>200,
                'message'=>"Request process succeed.",
                'data' => $students,
            ],200);
        return view('userRoles.teacher.displaystd',compact('students'));       
    }

    public function parents()
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id',$user->id)->get();

        $levelsIDs = $classesIDs = $subjectsIDs = array();
        $levels = $classes = $subjects = array();

        foreach ($teacher as $item) {
            $levelsIDs[] = $item->level_id;
            $classesIDs[] = $item->class_id;
            $subjectsIDs[] = $item->subject_id;
        }

        $levels = Level::whereIn('id',$levelsIDs)->get();
        $classes = Sort::whereIn('id',$classesIDs)->get();
        $subjects = Subject::whereIn('id',$subjectsIDs)->get();

        return view('userRoles.teacher.parents',with([
            'user'=>$user,
            'levels'=>$levels,
            'classes'=>$classes,
            'subjects'=>$subjects
        ]));
    }

    public function displaypts(Request $request)
    {
        $user = Auth::user();
        $data = $request->all();
        $classId = $data['class'];
        $class = Sort::find($classId);
        
        $teacher = Teacher::where('user_id',$user->id)->get();
        $classesIDs = $students = $parents = array();

        foreach ($teacher as $item)
            $classesIDs[] = $item->class_id;

        if($classId)
        {
            if($class)
            {
                if(in_array($classId, $classesIDs))
                {
                    $students = Student::where('class_id',$classId)->get();
                }
                else
                {
                    if($request->wantsJson())
                        return response()->json([
                            'status_code' => 403,
                            'message' => "Forbidden you don't teach this class.",
                            'data' => [],
                        ],200);
                }
            }
            else
            {
                if($request->wantsJson())
                    return response()->json([
                        'status_code' => 404,
                        'message' => "class not found.",
                        'data' => [],
                    ],200);
            }
        }
        else
        {
            $students = Student::whereIn('class_id',$classesIDs)->get();
        }

        foreach ($students as $student) {
            if($student->parent)
                $parents[] = $student->parent;
        }
        $allPts = $ptsIDs = array();
        foreach($parents as $p){
        	$ptsIDs[] = $p->id;
        }
        $ptsIDs = array_unique($ptsIDs);
        foreach($ptsIDs as $pID){
        	$allPts[] = User::find($pID);
        }

        if($request->wantsJson())
            return response()->json([
                'status_code'=>200,
                'message'=>"Request process succeed.",
                'data' => $allPts,
            ],200);

        return view('userRoles.teacher.displaypts',compact('students'));       
    }

    public function colleagues()
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id',$user->id)->get();

        $levels = Level::all();
        $classes = Sort::orderBy('level_id','ASC')->get();

        return view('userRoles.teacher.colleagues',with([
            'user'=>$user,
            'levels'=>$levels,
            'classes'=>$classes
        ]));
    }

    public function displayStf(Request $request)
    {
        $user = Auth::user(); 
        $data = $request->all();
        $levelId = $data['level'];
        $classId = $data['class'];
        $teachers = array();

        if($levelId && $classId == 0)
            $teachers =Teacher::where('level_id',$levelId)->get();
        
        elseif(($classId && $levelId == 0) || ($levelId && $classId))
            $teachers =Teacher::where('class_id',$classId)->get();

        $staffIDs = array();
        foreach ($teachers as $teacher){
            $staffIDs[] = $teacher->user_id;
        }

        if(($key = array_search($user->id, $staffIDs)) !== false) {
            unset($staffIDs[$key]);
        }
        
        $colleagues = User::whereIn('id',$staffIDs)->get();

        if($request->wantsJson())
            return response()->json([
                'status_code'=>200,
                'message'=>"Request process succeed.",
                'data' => $colleagues,
            ],200);

        return view('userRoles.teacher.displayStf',compact('colleagues'));
    }

    public function teacherContactView()
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id',$user->id)->get();

        $levelsIDs = $classesIDs = $subjectsIDs = array();
        $levels = $classes = $subjects = array();

        foreach ($teacher as $item) {
            $levelsIDs[] = $item->level_id;
            $classesIDs[] = $item->class_id;
            $subjectsIDs[] = $item->subject_id;
        }

        $levels = Level::whereIn('id',$levelsIDs)->get();
        $classes = Sort::whereIn('id',$classesIDs)->get();
        $subjects = Subject::whereIn('id',$subjectsIDs)->get();

        return view('userRoles.teacher.tContactView',with([
            'user'=>$user,
            'levels'=>$levels,
            'classes'=>$classes,
            'subjects'=>$subjects
        ]));
    }

    public function addView()
    {
        $user = Auth::user();

        $teacher = Teacher::where('user_id',$user->id)->get();
        $classesIDs = array();

        foreach ($teacher as $item)
            $classesIDs[] = $item->class_id;

        $students = Student::whereIn('class_id',$classesIDs)->get();

        return view('userRoles.teacher.addParent',with([
            'user'=>$user,
            'students'=>$students
        ]));
    }

    public function addpnt(Request $request)
    {
        $studentId = $request->student;
        $user = Auth::user();
        $student = Student::find($studentId);
        $teacher = Teacher::where('user_id',$user->id)->get();
        $classesIDs = array();
        foreach ($teacher as $item)
            $classesIDs[] = $item->class_id;

        if($student)
        {
            if(in_array($student->class_id, $classesIDs))
            {
                if($student->parent)
                {
                    $errmsg = "Bad Request This Student already Assigned to parent";
                    if($request->wantsJson())
                        return response()->json([
                            'status_code'=> 400,
                            'message'=>$errmsg,
                            'data'=>[]
                        ],200);

                    session()->flash('addP-error', $errmsg);
                }
                else
                {
                    $addphone = '00966'.$request->phone;
                    $request->merge(['phone' => $addphone]);

                    $check = $this->validator($request);
                    if($check)
                    {
                        if($request->wantsJson())
                        return response()->json([
                            'status_code' => 400,
                            'message' => "Bad Request.",
                            'data' => $check
                        ],200);
                    }
                    
                    $this->create($request->all());

                    $parent = User::orderBy('created_at', 'desc')->first();
                    $student->parent_id = $parent->id;
                    $student->update();

                    if($request->wantsJson())
                        return response()->json([
                            'status_code'=> 201,
                            'message'=>'Update process succeed.',
                            'data'=>$student
                        ],200);

                    session()->flash('addP-success','Parent has been added successfully.');
                }
            }
            else
            {
                $errmsg = "Forbidden you don't teach this student";
                if($request->wantsJson())
                    return response()->json([
                        'status_code'=> 403,
                        'message'=>$errmsg,
                        'data'=>[]
                    ],200);
            }
        }
        else
        {
            $errmsg = "Student not found"; 
            if($request->wantsJson())
                return response()->json([
                    'status_code'=> 404,
                    'message'=>$errmsg,
                    'data'=>[]
                ],200);

            session()->flash('addP-error', $errmsg);
        }

        return redirect()->route('teacher.add.parent');
    }

    protected function validator(Request $re)
    {

        $validationErrors = array();

        $nameValidator = Validator::make($re->only('name'), 
            ['name' => 'required|string|max:255|min:3']);

        if($nameValidator->fails())
        {
            $errors = $nameValidator->errors()->toArray();
            $validationErrors['name']=$errors['name'];
        }
        /* ============ password ========== */
        $passvalidator = Validator::make($re->only('password'),
            ['password' => 'required|string|min:4']);

        if($passvalidator->fails())
        {
            $errors=$passvalidator->errors()->toArray();
            $validationErrors['password']=$errors['password'];
        }
        /* ============ Phone ========== */
        $phonevalidator = Validator::make($re->only('phone'), 
            ['phone' => 'digits:14|unique:users,phone,$parent->id']);

        if($phonevalidator->fails())
        {
            $errors=$phonevalidator->errors()->toArray();
            $validationErrors['phone']=$errors['phone']; 
        }

        return $validationErrors;

    }

    public function myMsg(Request $request)
    {
        $teacherId = Auth::user()->id;

        $msgs = array();
        $allMsgs = Chat::where('to_id',$teacherId)->orderBy('created_at', 'DESC')->get();
        foreach ($allMsgs as $msg){
               $sender = User::find($msg->from_id); 
               $msg['senderName'] = $sender->name;
               $msg['senderImage'] = $sender->image;

               $msgs[] = $msg;
        }

        if($request->wantsJson())
            return response()->json([
                'status_code'=> 200,
                'message'=>'Request process succeed.',
                'data'=>$msgs
            ],200);

        return view('userRoles.teacher.inbox',with([
            'msgs' => $msgs
        ]));  
    }

    protected function create(array $data)
    {
        $fileName = 'avatar.jpg';
        return User::create([
            'name' => $data['name'],
            'image' => $fileName,
            'phone' => $data['phone'],
            'role_id' => 1,
            'password' => bcrypt($data['password']),
        ]);
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
    
     public function settings(Request $re)
    {
        $validationErrors = array();

        $teacher = Auth::user();

        $nameValidator = Validator::make($re->only('name'),
            ['name' => 'max:1000|min:3']);

        if($nameValidator->fails())
        {
            $errors = $nameValidator->errors()->toArray();
            $validationErrors['name']=$errors['name'];
            if($re->wantsJson())
                return response()->json([
                    'status_code' => 400,
                    'message' => "Bad Request.",
                    'data' => $validationErrors
                ],200);
        }


        $bio = Auth::user()->bio;
        if($re->bio)
            $bio = $re->bio;

        $phone = Auth::user()->phone;
        if($re->phone)
        {
            if(strcmp('00966'.$re->phone, $teacher->phone))
            {
                $phone = '00966'.$re->phone;
                $re->merge(['phone' => $phone]);


                $phonevalidator = Validator::make($re->only('phone'),
                    ['phone' => 'digits:14|unique:users,phone,$parent->id']);

                if($phonevalidator->fails())
                {
                    $errors=$phonevalidator->errors()->toArray();
                    $validationErrors['phone']=$errors['phone'];
                    if($re->wantsJson())
                        return response()->json([
                            'status_code' => 400,
                            'message' => "Bad Request.",
                            'data' => $validationErrors
                        ],200);
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
                $validationErrors['password']=$errors['password'];
                if($re->wantsJson())
                    return response()->json([
                        'status_code' => 400,
                        'message' => "Bad Request.",
                        'data' => $validationErrors
                    ],200);
            }

            $password = bcrypt($re->password);
        }

        $fileName = $teacher->image;

        if($re->hasfile('image'))
        {
            $destination = public_path('images/users');
            $extension = $re->file('image')->getClientOriginalExtension();
            $fileName = strtolower(rand(99999,99999999).uniqid().'.'.$extension);

            $re->file('image')->move($destination, $fileName);
        }

        $teacher->name = $re->name;
        $teacher->phone = $phone;
        $teacher->bio = $bio;
        $teacher->password = $password;
        $teacher->image = $fileName;

        if($teacher->update())
        {
            if($re->wantsJson())
                return response()->json([
                    'status_code' => 201,
                    'message' => "Update process succeed.",
                    'data' => Auth::user()
                ],200);

            session()->flash('updated','Update Succeed.');
        }

        //return redirect()->route('parents.showSettings');
    }




}
