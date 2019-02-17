<?php

namespace App;

use App\BlockedUser;
use App\Chat;
use App\Level;
use App\Like;
use App\Post;
use App\Role;
use App\Sort;
use App\Student;
use App\Subject;
use App\Teacher;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Keygen\Keygen;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{

    use Notifiable,HasApiTokens;

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    

    public function subjectsNames()
    {
        $subjects = $this->subjects->toArray();
        $names = array();
        foreach ($subjects as $item) {
            $names[] = $item['name'];
        }
        $names = array_unique($names);
        $allsubjs = array();
        foreach($names as $item)
            $allsubjs[] = $item;

        $subnames = implode(" - ", $allsubjs);
        return $subnames;
    }
    
    public function messagesTo()
    {
        return $this->hasMany(Chat::class,'to_id');
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function isOnline()
    {
        return Cache::has('user-online-'.$this->id);
    }

    public function isBlocked()
    {
        
        return BlockedUser::where('user_id',$this->id)->first();
    }
    public function amIBlocked()
    {
        $isBlocked = 0;
        $blocked =  BlockedUser::where('user_id',$this->id)->first();
        if($blocked)
            $isBlocked = 1;

        return $isBlocked;
    }

    /*public function followers($id)
    {
        $followers = 0;
        $user = $this->find($id);
        $userId = (int)$id;
        if($user)
        {
            if($user->role_id == 1)
            {
                $children = $user->children;
                $classes = array();
                foreach ($children as $child){
                    $classes[] = $child->class->id;
                }
                $classes = array_unique($classes);
                $teachers = Teacher::distinct('user_id')
                                ->whereIn('class_id',$classes)
                                ->get();
                $followers = count($teachers);
            }
            elseif($user->role_id == 2)
            {
                $colleagues = $this->whereIn('role_id',[2,3])->get();

                $classes=Teacher::distinct('class_id')
                    ->where('user_id',$userId)->pluck('class_id')->toArray();
                $students = $parents = array();
                $students = Student::whereIn('class_id',$classes)->get();

                foreach ($students as $student) {
                    if($student->parent)
                        $parents[] = $student->parent->id;
                }
                $parents = array_unique($parents);

                //-1 because function count user itself;
                $followers = count($colleagues)+count($parents)-1;
            }
            elseif($user->role_id == 3)
            {
                $users=$this->whereIn('role_id',[1,2,3])->get();
                //-1 because function count user itself;
                $followers = count($users)-1;
            }
            elseif($user->role_id == 3)
            {
                $followers = 0;
            }
            
        }

        return $followers;
    }*/
    public function followers($id)
    {
        $followers = 0;
        $user = $this->find($id);
        $userId = (int)$id;
        if($user)
        {
            if($user->role_id == 1)
            {
                $children = $user->children;
                $classes = array();
                foreach ($children as $child){
                    if($child->class)
                        $classes[] = $child->class->id;
                }
                $classes = array_unique($classes);
                
                $teachers = Teacher::distinct('user_id')
                                ->whereIn('class_id',$classes)
                                ->get();
                $followers = count($teachers);
                
            }
            elseif($user->role_id == 2)
            {
                $colleagues = $this->whereIn('role_id',[2,3])->get();

                $classes=Teacher::distinct('class_id')
                ->where('user_id',$userId)->pluck('class_id')->toArray();
                
                $students = $parents = array();
                $students = Student::whereIn('class_id',$classes)->get();

                foreach ($students as $student) {
                    if($student->parent)
                        $parents[] = $student->parent->id;
                }
                $parents = array_unique($parents);

                    //-1 because function count user itself;
                $followers = count($colleagues)+count($parents)-1;
                
            }
            elseif($user->role_id == 3)
            {
                $users=$this->whereIn('role_id',[1,2,3])->get();
                //-1 because function count user itself;
                $followers = count($users)-1;
            }
            elseif($user->role_id == 3)
            {
                $followers = 0;
            }
            
        }

        return $followers;
    }

    # @token = array
    # @title = string
    # @body = string
    # @data = array

    #"title":"message receved",
    #"body":"mohamed ali sent you a message",
    #"type":"message",

    /*function send_notification ($tokens, $title, $body, $type, $data = []  )
    {

        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = array(
            'registration_ids' =>$tokens,
            'notification' => [
                "title" => $title, 
                "body" => $body,
                "type" => $type,
            ],
        );
        if(count($data) > 0 ){
            $fields['data'] =  $data;
        }
        $headers = array(
            'Authorization:key= AAAAoGYIwQs:APA91bH4SIsPdzlcADnP5u0T4tbwl4JJCylkHtOb9bve_NJtJ-gQShDVG-jK6netrXUUcBICh2qSPTAMuHrR45My1d4B6_M3r9dpbCQ7SqXNcJiI2Cajis1gcwjswktm901Nn2TjEuAa',
            'Content-Type:application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($fields));

        $result = curl_exec($ch);
        if($result === false)
            die('cUrl faild: '.curl_error($ch));
        curl_close($ch);

        return $result;    
    }*/
    public function send_notification($tokens,$title,$body,$type,$data=[])
    {

        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = array(
            'to' =>$tokens,
            'data' => [
                "title" =>"message receved",
                "body"  =>"mohamed ali sent you a message",
                "type"  =>"message",
                "data"  =>$data
            ]
        );
        /*if(count($data) > 0 ){
            $fields['data'] =  $data;
        }*/
        $headers = array(
            'Authorization:key= AAAAoGYIwQs:APA91bH4SIsPdzlcADnP5u0T4tbwl4JJCylkHtOb9bve_NJtJ-gQShDVG-jK6netrXUUcBICh2qSPTAMuHrR45My1d4B6_M3r9dpbCQ7SqXNcJiI2Cajis1gcwjswktm901Nn2TjEuAa',
            'Content-Type:application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($fields));

        $result = curl_exec($ch);
        if($result === false)
            die('cUrl faild: '.curl_error($ch));
        curl_close($ch);

        return $result;    
    }

    public function sendVC($message,$phone)
    {
        $feedback = 1;
        $phon = substr($phone, 2);

        $client = new \GuzzleHttp\Client();
        $username = USER_NAME;
        $password = PASSWORD;
        $sender = SENDER;

        $sendUrl = "http://ultramsg.com/api.php?send_sms&username=".$username."&password=".$password."&numbers=".$phon."&sender=".$sender."&message=".$message."";
        $res = $client->request('GET', $sendUrl);
        
        if($res)
            $feedback = 1;

        return $feedback;
    }


    public function ApiLogout()
    {
        Auth::guard( 'api' )->logout();
    }
    

}
