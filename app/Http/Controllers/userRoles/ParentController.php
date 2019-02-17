<?php

namespace App\Http\Controllers\userRoles;

use App\Chat;
use App\Http\Controllers\Controller;
use App\Level;
use App\Sort;
use App\Student;
use App\StudentSubject;
use App\Teacher;
use App\User;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ParentController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('parent');
        $this->middleware('block');
        $this->middleware('verified');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    	return view('userRoles.parent.home');
    }

    public function addView()
    {
        $levels = Level::all();
        $classes = Sort::orderBy('level_id')->get();
        
        return view('userRoles.parent.addChild',with([
            'levels'=>$levels,
            'classes'=>$classes
        ]));
    }

    public function addChild(Request $re)
    {
    	$data = $re->all();
        $parent = Auth::user();
        $children = $data['children'];
        $newStudents = array();
        
        if(count($children['childName']) > 0)
        {
            for ($i = 0; $i < count($children['childName']); $i++) 
            {
                if($children['childName'][$i] &&
                    $children['childLevel'][$i] &&
                    $children['childClass'][$i])
                {
                    $student = new Student();
                    $student->name = $children['childName'][$i];
                    $student->level_id = $children['childLevel'][$i];
                    $student->class_id = $children['childClass'][$i];
                    $student->parent_id = $parent->id;

                    $student->save();

                    session()->flash('addC-success','Children Added Successfully.');
                }
                
            }
        }
        
        
        return redirect()->route('parents.add.view');
    }

    public function ApiAddChild(Request $re)
    {
    	$data = $re->all();
        $parent = Auth::user();
    	$children = $data['children'];
        $parentId = $parent->id;

        if(count($children) > 0){
            foreach ($children as $child) {
                if($child['childName'])
                {
                    $student = new Student();
                    $student->name = $child['childName'];
                    $student->level_id = $child['childLevel'];
                    $student->class_id = $child['childClass'];
                    $student->parent_id = $parentId;

                    $student->save();
                } 
            }
        }

    	$sons = $parent->children;
        return response()->json([
            'status_code' => 201,
            'message' => "Adding process succeed.",
            'data' => $sons
        ],200);
    }

    public function children(Request $re)
    {
        $children = Auth::user()->children;
        if($re->wantsJson())
            return response()->json([
                'status_code' => 200,
                'message' => "Request completed successfully.",
                'data' => $children
            ],200);
        return view('userRoles.parent.displaysons',with([
            'children' => $children
        ]));
    }

    public function removeChild(Request $re)
    {
        $childId = $re->id;
        $parentId = Auth::user()->id;
        $child = Student::find($childId);

        $stdSubjects= StudentSubject::where('student_id',$childId)->get();        

        if($child)
        {
            if($child->parent_id == $parentId){
                $child->delete();
                if($stdSubjects){
                    foreach ($stdSubjects as $stdSubject){
                        $stdSubject->delete();
                    }
                }
            }

            if($re->wantsJson())
                return response()->json([
                    'status_code' => 204,
                    'message' => "Deleting Process succeed.",
                    'data' => []
                ],200);
        }
        else
        {
            if($re->wantsJson())
                return response()->json([
                    'status_code' => 404,
                    'message' => "Children not found.",
                    'data' => []
                ],200);
        }
        
        session()->flash('deleted','Son Deleted Successfully.');  
        return redirect()->route('parents.children');
    }

    public function editChild(Request $re)
    {
        $parentId = Auth::user()->id;
        $childId = $re->id;
        $son = Student::where('id',$childId)->first();

        if($son->parent_id != $parentId)
            return redirect()->back();

        $levels = Level::all();
        $classes = Sort::orderBy('level_id')->get();

        return view('userRoles.parent.editSon',with([
            'son' => $son,
            'levels' => $levels,
            'classes' => $classes
        ]));
    }

    public function editSon(Request $re)
    {
        $childId = $re->id;
        $parentId = Auth::user()->id;
        $childName = $re->childName;
        $childLevel = $re->childLevel;
        $childClass = $re->childClass;

        $student = Student::find($childId);
        if($student)
        {
            if($student->parent_id == $parentId)
            {
                $student->name = $childName;
                $student->level_id = $childLevel;
                $student->class_id = $childClass;
                $student->update();
                if($re->wantsJson())
                    return response()->json([
                        'status_code' => 201,
                        'message' => "Update Process succeed.",
                        'data' => $student
                    ],200);
                session()->flash('edited','Your son info. modified successfully.');
            }
            else
            {
                if($re->wantsJson())
                    return response()->json([
                        'status_code' => 403,
                        'message' => "Forbidden this student isn't your son.",
                        'data' => []
                    ],200);
            }
        }
        else
        {
            if($re->wantsJson())
                return response()->json([
                    'status_code' => 404,
                    'message' => "Child not found.",
                    'data' => []
                ],200); 
        }      
        return redirect()->route('parents.children');
    }

    public function selectTeachers()
    {
        $sons = Auth::user()->children;

        return view('userRoles.parent.selectTeachers',with([
            'sons' => $sons
        ]));
    }

    public function teachers(Request $re)
    {
        $student = new Student();
        $son = Student::find($re->son);
        $teachers = array();
        if($son)
        {
            if($son->parent_id == Auth::user()->id)
            {
                $teachers = $student->studentTeachers($re->son);
                if($re->wantsJson())
                    return response()->json([
                        'status_code' => 200,
                        'message' => "Request process succeed.",
                        'data' => $teachers
                    ],200);
            }
            else
            {
                return response()->json([
                    'status_code' => 403,
                    'message' => "Forbidden this student isn't your son.",
                    'data' => []
                ],200);
            }     
        }
        else
        {
            if($re->wantsJson())
                return response()->json([
                    'status_code' => 404,
                    'message' => "Student not found.",
                    'data' => []
                ],200);
        }
        
        return view('userRoles.parent.teachers',with([
            'teachers' => $teachers
        ]));
    }

    public function principles(Request $re)
    {
        $users = User::all();
        $principles = array();
        foreach ($users as $user){
            if($user->role->name == 'principle')
                $principles[] = $user;
        }
        if($re->wantsJson())
            return response()->json([
                'status_code' => 200,
                'message' => "Request process succeed.",
                'data' => $principles
            ],200);

        return view('userRoles.parent.principles',with([
            'principles' => $principles
        ]));
    }

    public function showSettings()
    {
        $parent = Auth::user();
        return view('userRoles.parent.settings',with([
            'parent' => $parent
        ]));
    }

    public function settings(Request $re)
    {
        $parent = Auth::user();
        
        $nameValidator = Validator::make($re->only('name'), 
            ['name' => 'max:1000|min:2']);

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
            if(strcmp('00966'.$re->phone, $parent->phone))
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

        $fileName = $parent->image;

        if($re->hasfile('image'))
        {
            $destination = public_path('images/users');
            $extension = $re->file('image')->getClientOriginalExtension();
            $fileName = strtolower(rand(99999,99999999).uniqid().'.'.$extension);

            $re->file('image')->move($destination, $fileName);
        }

        $parent->name = $re->name;
        $parent->phone = $phone;
        $parent->bio = $bio;
        $parent->password = $password;
        $parent->image = $fileName;

        if($parent->update())
        {
            if($re->wantsJson())
                return response()->json([
                    'status_code' => 201,
                    'message' => "Update process succeed.",
                    'data' => Auth::user()
                ],200);

            session()->flash('updated','Update Succeed.');
        }

        return redirect()->route('parents.showSettings');
    }

    public function myMsg(Request $re)
    {
        $parentId = Auth::user()->id;
        $msgs = array();
        $allMsgs = Chat::where('to_id',$parentId)->get();
        foreach ($allMsgs as $msg) {
            $sender = User::find($msg->from_id); 
            $msg['senderName'] = $sender->name;
            $msg['senderImage'] = $sender->image;

            $msgs[] = $msg;
        }
        if($re->wantsJson())
            return response()->json([
                'status_code' => 200,
                'message' => "Request process succeed.",
                'data' => $msgs
            ],200);

        return view('userRoles.parent.inbox',with([
            'msgs' => $msgs
        ]));  
    }



}
