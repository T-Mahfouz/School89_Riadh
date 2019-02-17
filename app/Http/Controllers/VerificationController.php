<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VerificationController extends Controller
{

	public function __construct()
    {
        $this->middleware('auth');
        //$this->middleware('block');
    }

    public function activateAccount(Request $request)
    {
        $user = Auth::user();
        $savedVC = $user->verification_code;
        $receivedVC = $request->verification_code;

        if($savedVC != $receivedVC){
            if($request->wantsJson())
                return response()->json([
                    'status_code' => 400,
                    'message' => 'كود التفعيل غير صحيح.',
                    'data' => []
                ],400); 
        }
        $user->status = 1;
        $user->verification_code = null;
        $user->update();

        if($request->wantsJson())
            return response()->json([
                'status_code' => 200,
                'message' => 'تم تفعيل الحساب.',
                'data' => []
            ],200);
    }

    public function resendVC(Request $request)
    {
        $user = Auth::user();
        if($user->status == 1){
            if($request->wantsJson())
                return response()->json([
                    'status_code' => 400,
                    'message' => 'الحساب مُفعل.',
                    'data' => []
                ],400); 
        }

        $digits = 4;
        $vc = rand(pow(10, $digits-1), pow(10, $digits)-1);
        $message = "رمز توثيق المدرسة 89-  ".$vc;

        if($user->verification_code != null){
            $vc = $user->verification_code;
        	$message = "رمز توثيق المدرسة 89-  ".$user->verification_code;
        }

        $user->verification_code = $vc;
        $user->update();

        $check = $user->sendVC($message,$user->phone);
        if($check){
            return response()->json([
                'status_code' => 200,
                'message' => 'تم إرسال كود التفعيل.',
                'data' => []
            ],200);
        }
    }
    
    


}
