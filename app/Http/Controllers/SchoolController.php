<?php

namespace App\Http\Controllers;

use App\About;
use App\Level;
use App\Mail\SendContactUs;
use App\Sort;
use App\Subject;
use App\Url;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class SchoolController extends Controller
{

    public function classes(Request $request)
    {
        $classes = Sort::all();
        if($request->wantsJson())
            return response()->json([
                'status_code' => 200,
                'message' => 'تمت العملية بنجاح.',
                'data' => $classes
            ],200);
    } 
    public function levels(Request $request)
    {
        $levels = Level::all();
        if($request->wantsJson())
            return response()->json([
                'status_code' => 200,
                'message' => 'تمت العملية بنجاح.',
                'data' => $levels
            ],200);
    }
    
    
    public function subjects(Request $re)
    {
        $subjects = Subject::all();
        if($subjects)
            foreach ($subjects as $subject){
                unset($subject['created_at']);
                unset($subject['updated_at']);
            }
        if($re->wantsJson())
            return response()->json([
                'status_code' => 200,
                'message' => "تمت العملية بنجاح.",
                'data' => $subjects
            ],200);
    }
    
    public function classesOfLevel(Request $re)
    {
        $levelId = $re->level;
        $check = Level::find($levelId);
        if($check)
        {
           $classes = Sort::where('level_id',$levelId)->get();
           if($re->wantsJson())
            return response()->json([
                'status_code' => 200,
                'message' => 'تمت العملية بنجاح.',
                'data' => $classes
            ],200);
        }
        else
        {
             if($re->wantsJson())
            return response()->json([
                'status_code' => 404,
                'message' => 'هذا المستوى غير موجود.',
                'data' => []
            ],404);
        }  
        
    }
    
    public function schoolUrls(Request $request)
    {
        $urls = Url::first();
        unset($urls['id']);
        unset($urls['created_at']);
        unset($urls['updated_at']);

        if($request->wantsJson())
            return response()->json([
                'status_code' => 200,
                'message' => 'تمت العملية بنجاح.',
                'data' => $urls
            ],200);
    }
    
    public function forgetPassword(Request $request)
    {
        $digits = 4;
        $vc = rand(pow(10, $digits-1), pow(10, $digits)-1);
        $message = 'رمز توثيق المدرسة 89 - '.$vc;

        $phone = $request->phone;
        $user = User::where('phone',$phone)->first();
        if(!$phone){
            return response()->json([
                'status_code' => 400,
                'message' => 'يجب إدخال رقم الهاتف.',
                'data' => []
            ],400);
        }
        if(!$user){
            return response()->json([
                'status_code' => 404,
                'message' => 'هذا الحساب غير موجود.',
                'data' => []
            ],404);
        }

        $user->verification_code = $vc;
        $user->update();

        $check = $user->sendVC($message,$phone);
        if($check){
            return response()->json([
                'status_code' => 200,
                'message' => 'تم إرسال كود التفعيل.',
                'data' => []
            ],200);
        }
    }
    
    public function TestVC(Request $request)
    {
        $digits = 4;
        $vc = rand(pow(10, $digits-1), pow(10, $digits)-1);
        $message = "كود تفعيل الممدرسة 89 ".$vc;

        $phone = $request->phone;
        $user = User::where('phone',$phone)->first();
        if(!$phone){
            return response()->json([
                'status_code' => 400,
                'message' => 'يجب إدخال رقم الهاتف.',
                'data' => []
            ],400);
        }
        if(!$user){
            return response()->json([
                'status_code' => 404,
                'message' => 'هذا الحساب غير موجود.',
                'data' => []
            ],404);
        }

        $user->verification_code = $vc;
        $user->update();

        return response()->json([
            'status_code' => 200,
            'message' => 'Test verification code.',
            'data' => $vc
        ],200);
    }

    public function changePassword(Request $request)
    {

        $phone = $request->phone;
        if(!$phone){
            return response()->json([
                'status_code' => 400,
                'message' => 'يجب إدخال رقم الهاتف.',
                'data' => []
            ],400);
        }
        $user = User::where('phone',$phone)->first();
        if(!$user){
            return response()->json([
                'status_code' => 404,
                'message' => 'هذا الحساب غير موجود.',
                'data' => []
            ],404);
        }

        $savedVC = $user->verification_code;
        $receivedVC = $request->verification_code;
        $password = bcrypt($request->password);

        $notvalid = $this->verifyPassword($request);
        if($notvalid){
            if($request->wantsJson())
                return response()->json([
                    'status_code' => 400,
                    'message' => $notvalid,
                    'data' => []
                ],400);
        }
        if($savedVC != $receivedVC){
            if($request->wantsJson())
                return response()->json([
                    'status_code' => 400,
                    'message' => 'الكود غير صحيح.',
                    'data' => []
                ],400); 
        }
        $user->status = 1;
        $user->verification_code = null;
        $user->password = $password;
        $user->update();

        if($request->wantsJson())
            return response()->json([
                'status_code' => 200,
                'message' => 'تم تغيير كلمة المرور.',
                'data' => []
            ],200);
    }
    
    public function verifyPassword(Request $request)
    {
        $validationErrors = "";
        $passvalidator = Validator::make($request->only('password'),
            ['password' => 'required|string|min:4']);

        if($passvalidator->fails())
        {
            $errors=$passvalidator->errors()->toArray();
            $validationErrors = implode("", $errors['password']);
        }
        return $validationErrors;
    }

    public function sendContactUs()
    {
        Mail::send(new SendContactUs());
    }
    
    public function aboutUs(Request $request)
    {
        $lang = $request->lang;
        $about = "";
        if($lang){
            $about = About::where('lang',$lang)->first();
            if(!$about){
                if($request->wantsJson())
                    return response()->json([
                        'status_code' => 404,
                        'message' => "لا يوجد محتوى مدعوم بهذه اللغة.",
                        'data' => []
                    ],404);
            }
        }
        else
            $about = About::where('lang','en')->first();

        if($request->wantsJson())
            return response()->json([
                'status_code' => 200,
                'message' => "تمت العملية بنجاح.",
                'data' => $about
            ],200);
    }  


    public function testpush(Request $request)
    {
        $tokens = $request->token;;

        $title = "Test School";
        $body = "Is it working ?";
        $type = "Test"; 
        $data = array("user_phone" => "");

        $url = 'https://fcm.googleapis.com/fcm/send';
        $data = array(
            "title" => $title,
            "body"  => $body,
            "type"  => $type,
            "data"  => $data,
            'content_available'=> true,
            'vibrate' => 1,
            'sound' => true,
            'priority'=> 'high'
        );
        $fields = array(
            'to' =>$tokens,
            'notification' => $data
        );
        $headers = array(
            'Authorization:key= AAAAoGYIwQs:APA91bH4SIsPdzlcADnP5u0T4tbwl4JJCylkHtOb9bve_NJtJ-gQShDVG-jK6netrXUUcBICh2qSPTAMuHrR45My1d4B6_M3r9dpbCQ7SqXNcJiI2Cajis1gcwjswktm901Nn2TjEuAa',
            'Content-Type:application/json'
        );
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL,$url);
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        if($result === false)
            die('cUrl faild: '.curl_error($ch));
        curl_close($ch);
        return $result;    
    }
      
}
