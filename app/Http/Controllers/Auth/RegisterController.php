<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use App\ValidPhone;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Client;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {

        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'phone' => 'required|digits:14|unique:users',
            'password' => 'required|string|min:4',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {

        $fileName = 'avatar.jpg';
        //echo $data['image']->getClientOriginalExtension();

        if (isset($data['image'])) {

            $destinationPath = public_path('images/users');
            $extension = $data['image']->getClientOriginalExtension();
            $fileName = strtolower(rand(99999,99999999).uniqid().'.'.$extension);

            $data['image']->move($destinationPath, $fileName);
        }



        $user =  User::create([
            'name' => $data['name'],
            'image' => $fileName,
            'phone' => $data['phone'],
            'role_id' => 1,
            'password' => bcrypt($data['password']),
            'firebase_token' => $data['firebase_token'],
        ]);

        $digits = 4;
        $vc = rand(pow(10, $digits-1), pow(10, $digits)-1);
        $message = 'رمز توثيق المدرسة 89-  '.$vc;

        $user->verification_code = $vc;
        $user->update();
        $user->sendVC($message,$user->phone);

        return $user;

    }


    /*=========== API REGISTER ============== */
    
    public function apiRegister(Request $request)
    {
        $phone = '00966'.$request->phone;
        $request->merge(['phone' => $phone]);
        $validate = $this->apiValidator($request);
        if($validate)
        {
            if($request->wantsJson())
                return response()->json([
                    'status_code'=> 400,
                    'message'=>$validate,
                    'data'=>[]
                ],401);
        }
        $data = $request->all();
        event(new Registered($user = $this->create($request->all())));
        
        // ======== //Tarek Mahfouz// Add new Child ========= 
        if($request->children)
            $this->ApiAddChild($user->id,$data);

        $this->guard()->login($user);

        return $this->apiRegistered($request, $user)
                        ?: redirect($this->redirectPath());
    }

    protected function apiRegistered($request, $user)
    {
        $token = $user->createToken(null,['*'])->accessToken;
        
        $user['followers'] = $user->followers($user->id);
        $user['numberOfPosts'] = 0;
        $user['subjects'] = [];
        
        return response()->json([
            'status_code' => 201,
            'message' => "Adding process succeed.",
            'access_token' => $token,
            'data' => $user
        ],201);
    }
    
    
    protected function apiValidator(Request $re)
    {
        $validationErrors = "";
        $nameValidator = Validator::make($re->only('name'), 
            ['name' => 'required|string|max:255|min:2']);

        if($nameValidator->fails())
        {
            $errors = $nameValidator->errors()->toArray();
            $validationErrors="برجاء ادخال اسم المستخدم بطريقة صحيحة";
            //$validationErrors=implode("",$errors['name']);
        }
        /* ============ password ========== */
        $passvalidator = Validator::make($re->only('password'),
            ['password' => 'required|string|min:4']);

        if($passvalidator->fails())
        {
            $errors=$passvalidator->errors()->toArray();
            $validationErrors="كلمه السر يجب أن تكون أربعة أحرف على الأقل";
            //$validationErrors=implode("",$errors['password']);
        }
        /* ============ Phone ========== */
        $phonevalidator = Validator::make($re->only('phone'), 
            ['phone' => 'digits:14|unique:users,phone,$parent->id']);
        if($phonevalidator->fails())
        {
            $errors=$phonevalidator->errors()->toArray();
            $validationErrors="هذا الرقم غير صالح أو لديه حساب بالفعل"; 
            //$validationErrors=implode("",$errors['phone']); 
        }
        return $validationErrors;
    }





}
