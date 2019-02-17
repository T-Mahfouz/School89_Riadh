<?php

namespace App\Http\Controllers;

use App\Like;
use App\Post;
use App\PostImage;
use App\PostPermition;
use App\Teacher;
use App\User;
use App\PushAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class PostController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('block');
        $this->middleware('verified');
    }

    public function createPost(Request $re)
    {
        //echo "<pre>";print_r($re->all());die();
        $user = Auth::user();
        $post = new Post();
        $permition = new PostPermition;

        $check = true;

        if($user->role->name == 'teacher' && $re->classId)
        {
            $teacher=Teacher::where('user_id',$user->id)->get();
            $classesIDs = array();
            foreach ($teacher as $item)
                $classesIDs[] = $item->class_id;
            $classId = $re->classId;
            if(!in_array($classId,$classesIDs))
                $check = false;
        }
        $posted = "";

        if($check)
        {
            if(Auth::user()->role->name == 'teacher' || Auth::user()->role->name == 'principle' || Auth::user()->role->name == 'admin')
            {
                
                if($re->content)
                {
                    $posted = $post->create([
                        'user_id' => Auth::user()->id,
                        'post_id' => 0,
                        'content' => $re->content,
                    ]);     
                }
                else
                {
                    $errmsg = "لا يجب ترك هذا الحقل فارغ.";
                    if($re->wantsJson())
                        return response()->json([
                            'status_code' => 400,
                            'message' => $errmsg,
                            'data' => []
                        ],200);
                }
            } 
        }
        else
        {
            if($re->wantsJson())
                return response()->json([
                    'status_code' => 401,
                    'message' => 'غير مصرح لك بالنشر لاولياء أمور هذا الفصل.',
                    'data' => []
                ],200);
        }

        if($posted)
        {

            $classId = 0;
            $parents = $staff = 1;
            
            switch($re->vtype){
                case 'all':
                $parents = 1;
                $classId = 0;
                $staff = 1;
                break;
                case 'parent':
                $parents = 1;
                $classId = 0;
                $staff = 0;
                break;
                case 'staff':
                $parents = 0;
                $classId = 0;
                $staff = 1;
                break;
                case 'class':
                $parents = 0;
                $classId = $re->classId;
                $staff = 0;
                break;
            }

            $permited = $permition->create([
                'post_id' => $posted->id,
                'parents' => $parents,
                'staff' => $staff,
                'class_id' => $classId,
            ]);
            

            if($re->hasfile('image')){

                $extensions = ['jpeg','jpg','png'];
                $ext = $re->image->extension();

                $destinationPath = public_path('images/posts');
                $file = strtolower(rand(999,99999).uniqid().'.'.$ext);
                $re->image->move($destinationPath, $file);

                $postImg = new PostImage;
                $postImg->post_id = $posted->id;
                $postImg->image = $file;

                $postImg->save();
            }

            $last = Post::where('user_id',Auth::user()->id)
                        ->orderBy('created_at','DESC')->first();

            $last['userName']= Auth::user()->name;
            $last['userImage']= Auth::user()->image;
            $postimg = PostImage::where('post_id',$posted->id)->first();
            if($postimg)
                $last['postImage']= $postimg->image;
            
            $last['permition']= $last->permition;

            if($re->wantsJson())
                return response()->json([
                    'status_code' => 201,
                    'message' => 'تمت الاضافة بنجاح.',
                    'data' => $last
                ],200);

            session()->flash('message','Post Created Successfully'); 
        }
        else
        {
            $errmsg = "غير مصرح لك.";
            if($re->wantsJson())
                return response()->json([
                    'status_code' => 401,
                    'message' => $errmsg,
                    'data' => []
                ],200);
        }

        return redirect()->route('home');
    }

    public function delete(Request $re)
    {
        $postId = $re->post_id;
        $post = Post::where('id',$postId)->first();

        $postPermition = PostPermition::where('post_id',$postId)->first(); 
        $postImage = PostImage::where('post_id',$postId)->first();

        if($post)
        {
            if(Auth::user()->id != $post->user->id)
            {
            $errmsg = "غير مصرح لك.";
            if($re->wantsJson())
                return response()->json([
                    'status_code' => 404,
                    'message' => $errmsg,
                    'data' => []
                ],200);

            return redirect()->back();
            }
        }
        else
        {
            $errmsg = "هذا المنشور غير موجود.";
            if($re->wantsJson())
                return response()->json([
                    'status_code' => 404,
                    'message' => $errmsg,
                    'data' => []
                ],200);

            return redirect()->back();
        }
        
        if($post->delete())
        {
            if($postPermition && $postImage)
            {
                $postPermition->delete();
                $postImage->delete();
                $image = 'images/posts/'.$postImage->image;
                File::delete($image);
            }

            if($re->wantsJson())
                return response()->json([
                    'status_code' => 204,
                    'message' => 'تم الحذف.',
                    'data' => []
                ],200);

            session()->flash('message','Post Deleted Successfully');
        }  

        return redirect()->route('home');
    }

    public function editPost(Request $re)
    {

        $postId = (int)$re['postId'];
        $postBody = $re['body'];

        $post = Post::find($postId);

        if($post)
        {
            if($postBody)
            {
                $post->content = $postBody;
                $updated = $post->update();

                if($re->wantsJson())
                    return response()->json([
                        'status_code' => 201,
                        'message' => 'تم التعديل بنجاح.',
                        'data' => $post
                    ],200);
            }
            else
            {
                $errmsg = "لا يجب ترك هذا الحقل فارغ.";
                if($re->wantsJson())
                    return response()->json([
                        'status_code' => 400,
                        'message' => $errmsg,
                        'data' => []
                    ],200);
            }
        }
        else
        {
            $errmsg = "هذا المنشور غير موجود!";
            if($re->wantsJson())
                return response()->json([
                    'status_code' => 404,
                    'message' => $errmsg,
                    'data' => []
                ],200);
        }

        

        return response()->json([
            'new-body' => $postBody
        ],200);
    }

    public function likePost(Request $request)
    {
        $postId = $request->postId;
        $user = Auth::user();
        $post = Post::find($postId);

        if(!$post)
        {
            if($request->wantsJson())
                return response()->json([
                    'status_code' => 404,
                    'message' => "هذا المنشور غير موجود.",
                    'data' => []
                ],404);
        }
        $isLiked = $allLikes = 0;
        $liked = Like::where([
            ['post_id','=',$post->id],
            ['user_id','=',$user->id]
        ])->first();

        if($liked)
        {
            $liked->delete();
        }
        else
        {
            $like = new Like();
            $like->user_id = $user->id;
            $like->post_id = $post->id;
            $like->save();

            $isLiked = 1;

            $postOwner = User::find($post->user_id);
            if($postOwner)
            {   
                $body = " أُعجب بمنشورِ لك ".$user->name;
                if($postOwner->id != $user->id)
                    $this->shortPush($postOwner,$postId,$body);
            }
        }

        $allLikes = Like::where('post_id',$post->id)->get();
        $likes = count($allLikes);
        if($request->wantsJson())
            return response()->json([
                'status_code' => 201,
                'message' => 'تمت العملية بنجاح.',
                'data' => [
                    'likes' => $likes,
                    'isLiked' => $isLiked
                ]
            ],201);   
    }

    public function commentPost(Request $re)
    { 
        $user = Auth::user();
        $post = new Post();
        $postId = (int)$re->postId;
        $content = $re->content;
        $posted = "";
        
        $parentPost = Post::find($postId);

        if($parentPost)
        {
           if($content)
           {
               $this->Validate($re,[
                'content' => 'required|max:1000'
                ]);

                $posted = $post->create([
                    'user_id' => $user->id,
                    'post_id' => $postId,
                    'content' => $content,
                ]);
                
                $postOwner = User::find($parentPost->user_id);
                if($postOwner)
                {   
                    $body = " علق على منشور لك ".$user->name;
                    $this->shortPush($postOwner,$postId,$body);
                }
            
           }
           else
           {
                $errmsg = "لا يجب ترك هذا الحقل فارغ.";
                if($re->wantsJson())
                    return response()->json([
                        'status_code' => 400,
                        'message' => $errmsg,
                        'data' => []
                    ],200);
           }
        }
        else
        {
            $errmsg = "هذا المنشور غير موجود.";
            if($re->wantsJson())
                return response()->json([
                    'status_code' => 404,
                    'message' => $errmsg,
                    'data' => []
                ],200);
        }
        

        if($posted)
        {
            $last = Post::where([
                ['user_id','=',Auth::user()->id],
                ['post_id','=',$postId]
            ])->orderBy('created_at','DESC')->first();

            if($re->wantsJson())
                return response()->json([
                    'status_code' => 201,
                    'message' => 'تمت الاضافة بنجاح.',
                    'data' => $last
                ],200);

            return response()->json(['feedback'=>'success'],201);
        }
        else
        {
            $errmsg = "error not created.";
            if($re->wantsJson())
                return response()->json([
                    'status_code' => 400,
                    'message' => 'حدث خطأ ما يرجى المحاولة مرة أخرى.',
                    'data' => []
                ],200);

            return response()->json(['feedback'=>'faield'],200);
        }
    }
    
    public function postInfo(Request $request)
    {
        $user = Auth::user();
        $postId = $request->post_id;
        $post = Post::find($postId);

        if(!$post)
        {
            if($request->wantsJson())
                return response()->json([
                    'status_code' => 404,
                    'message' => 'هذا المنشور غير موجود.',
                    'data' => []
                ],404);
        }
        
        $likesCount = count($post->likes);
        $commentsCount = count($post->comments);
        if($post['likes'])
           unset($post['likes']);
        if($post['comments'])
           unset($post['comments']);
           
        $post['likes'] = $likesCount;
        $post['comments'] = $commentsCount;
        $postimg = PostImage::where('post_id',$post->id)->first();
        if($postimg)
            $post['postImage'] = $postimg->image;
        else
            $post['postImage'] = "";
        $auther = User::find($post->user_id);
        if($auther){
            $post['userName'] = $auther->name;
            $post['userImage'] = $auther->image;
        }
        $isLiked = 0;
            $checkLike=Like::where([
                ['post_id','=',$post->id],
                ['user_id','=',$user->id]
            ])->first(); 
            if($checkLike)
               $isLiked = 1;
               
        $post['isLiked'] = $isLiked;
        
        if($request->wantsJson())
            return response()->json([
                'status_code' => 200,
                'message' => 'تمت العملية بنجاح.',
                'data' => $post
            ],200);
        
    }
    
    public function shortPush($user,$postId,$body)
    {   
        $auther = Auth::user();
        $push = new PushAlert();
        $tokens =  $user->firebase_token;
        $title = "إشعار جديد";
        $body  = $body;
        $type  = "notification";
        $data  = ['post_id'   => $postId,];
        
        $push->FCMPush($tokens, $title, $body, $type, $data);
    }
    


}
