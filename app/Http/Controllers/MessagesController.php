<?php

namespace App\Http\Controllers;

use App\Chat;
use App\Like;
use App\Post;
use App\Sort;
use App\Student;
use App\Teacher;
use App\User;
use App\PushAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessagesController extends Controller
{

	public function __construct()
	{
		$this->middleware('auth');
		$this->middleware('block');
		$this->middleware('verified');
	}

	public function sendMsg(Request $request)
	{
		$msg = new Chat;
		$user = Auth::user();
		$userId = $user->id;
		$recieptient = User::find($request->msgTo);
		if($request->msg)
		{
			if($recieptient)
			{
				$msg->from_id = $userId;
				$msg->to_id = $request->msgTo;
				$msg->content = $request->msg;
				$save = $msg->save();
			}
			else
			{
				$errmsg = "هذا الحساب غير موجود.";
				if($request->wantsJson())
					return response()->json([
						'status_code'=> 404,
						'message'=>$errmsg,
						'data'=>[]
					],200);
			}	
		}
		else
		{
			$errmsg = "Bad Request, You should write a message.";
			if($request->wantsJson())
				return response()->json([
					'status_code'=> 400,
					'message'=>$errmsg,
					'data'=>[]
				],200);
			return response()->json(['data' => false],200);
		}
		

		$last = Chat::where('from_id',$userId)->orderBy('created_at','DESC')->first();
		$last['senderName'] = $user->name;
		$last['senderImage'] = $user->image;
		
		$this->shortPush($recieptient);

		if($request->wantsJson())
			return response()->json([
				'status_code'=> 201,
				'message'=>'Adding process succeed.',
				'data'=>$last
			],200);

		return response()->json(['data' => $save],200);
	}

	public function sendBack(Request $request)
	{
		$msg = new Chat;
		$msg->from_id = Auth::user()->id ;
		$msg->to_id = $request->msgTo ;
		$msg->content = $request->msg ;

		$msg->save();
		/*session()->flash('chat-feedback','Message has been sent successfully.');*/
		$last = Chat::where('from_id',Auth::user()->id)
		->orderBy('created_at','DESC')
		->first();
		
		if($request->wantsJson())
			return response()->json([
				'status_code' => 201,
				'message' => 'Adding process succeed.',
				'data' => $last
			],200);

		return redirect()->back();
		//return response()->json(['data' => $msg->save()],200);
	}

	public function tContactPts(Request $request)
	{
		if(Auth::user()->role_id != 1)
		{
			$user = Auth::user();
			$teacher = Teacher::where('user_id',$user->id)->get();
			$classesIDs = array();
			foreach ($teacher as $item)
				$classesIDs[] = $item->class_id;

			$userId = Auth::user()->id;
			$classId = $request->class;
			$class = Sort::find($classId);
			$Messages = $students = array();

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
								'status_code'=> 403,
								'message'=>"Forbidden, you don't teach this class.",
								'data'=>[]
							],200);
					}
				}
				else
				{
					if($request->wantsJson())
						return response()->json([
							'status_code'=> 404,
							'message'=>'Class not found.',
							'data'=>[]
						],200);
				}
			}
			else
				$students = Student::whereIn('class_id',$classesIDs)->get();

			if($request->msg)
			{
				$parentIDs = array();
				foreach ($students as $student)
				{
					if($student->parent)
					{
					     $parentIDs[] = $student->parent->id;
					}
				}
				$parentIDs = array_unique($parentIDs);
				foreach($parentIDs as $parentID)
				{
					$msg = new Chat;
					$msg->to_id = $parentID;
					$msg->from_id = $userId;
					$msg->content = $request->msg;

					$Messages[] = $msg->save();
					$msgto = User::find($parentID);
					$this->shortPush($msgto);
					
				}
				session()->flash('chat-feedback-success','Messages have been sent successfully.');	
			}
			else
			{
				$errmsg = "Bad Request, You should write a message.";
				if($request->wantsJson())
					return response()->json([
						'status_code'=> 400,
						'message'=>$errmsg,
						'data'=>[]
					],200);

				session()->flash('chat-feedback-error',$errmsg);
			}

			$last = Chat::select('from_id','content','created_at','updated_at')
			->where('from_id',$userId)
			->orderBy('created_at','DESC')
			->first();
			$last['senderName'] = $user->name;
			$last['senderImage'] = $user->image;

			if($request->wantsJson())
				return response()->json([
					'status_code'=> 201,
					'message'=>'Adding process succeed.',
					'data'=>$last
				],200);
		}

		else
		{
			if($request->wantsJson())
				return response()->json([
					'status_code'=> 401,
					'message'=>"Unauthorized to use this feature.",
					'data'=>[]
				],200);
		}

		return redirect()->route('teacher.contact.parents');
	}

	public function principleSend(Request $re)
	{
		
		$user = Auth::user();
		$userId = $user->id;
		$teachers = User::where('role_id',2)->get();
		$parents  = User::where('role_id',1)->get();
		$allUsers = User::whereIn('role_id',[1,2])->get();
		$Messages = array();

		$from_id = Auth::user()->id;

		if($user->role->name == "principle")
		{
			if($re->msg)
			{
				if($re->type && !$re->msgTo)
				{
					switch ($re->type) 
					{
						case "all":
						foreach ($allUsers as $user) {
							
							
							$msg = new Chat;
							$msg->to_id = $user->id;
							$msg->from_id = $from_id;
							$msg->content = $re->msg;

							$Messages[] = $msg->save();
							$this->shortPush($user);
						}
						break;

						case "parents":
						foreach ($parents as $parent) {
							
							
							$msg = new Chat;
							$msg->to_id = $parent->id;
							$msg->from_id = $from_id;
							$msg->content = $re->msg;

							$Messages[] = $msg->save();
							$this->shortPush($parent);
						}
						break;

						case "teachers":
						foreach ($teachers as $teacher) {
						
							$msg = new Chat;
							$msg->to_id = $teacher->id;
							$msg->from_id = $from_id;
							$msg->content = $re->msg;

							$Messages[] = $msg->save();
							$this->shortPush($teacher);
						}
						break;

						default:
						if($re->wantsJson())
							return response()->json([
								'status_code' => 400,
								'message' => 'Bad Request, You should define who will recieve your message.',
								'data' => []
							],200);
						return redirect()->back();
						break;
					}
				}
				elseif($re->msgTo && !$re->type)
				{
					$msg = new Chat;
					$recieptient = User::find($re->msgTo);
					if($recieptient)
					{
						$msg->from_id = $userId;
						$msg->to_id = $re->msgTo;
						$msg->content = $re->msg;
						$save = $msg->save();
						
						$msgTo = User::find($re->msgTo);
						$this->shortPush($msgTo);
					}
					else
					{
						$errmsg = "هذا الحساب غير موجود.";
						if($re->wantsJson())
							return response()->json([
								'status_code'=> 404,
								'message'=>$errmsg,
								'data'=>[]
							],200);
					}
				}
				else
				{
					$errmsg = "Bad Request.";
						if($request->wantsJson())
							return response()->json([
								'status_code'=> 400,
								'message'=>$errmsg,
								'data'=>[]
							],200);
				}


				$last = Chat::select('from_id','content','created_at','updated_at')
				->where('from_id',$userId)
				->orderBy('created_at','DESC')
				->first();
		
				if($re->wantsJson())
					return response()->json([
						'status_code' => 201,
						'message' => 'Adding process succeed.',
						'data' => $last
					],200);	

				session()->flash('chat-feedback-success','Messages have been sent successfully.');	
			}
			else
			{
				$errMsg ="Bad Request,You should write a message!";
				if($re->wantsJson())
					return response()->json([
						'status_code' => 400,
						'message' => $errMsg,
						'data' => []
					],200);

				session()->flash('chat-feedback-error',$errMsg);
			}
		}
		else
		{
			$errMsg = "Unauthorized to use this feature.";
			if($re->wantsJson())
				return response()->json([
					'status_code' => 403,
					'message' => $errMsg,
					'data' => []
				],200);
			session()->flash('chat-feedback-error',$errMsg);
		}


		return redirect()->route('principles.send.view');
	}

	public function lastCommingMsg(Request $request)
	{
		$user = Auth::user();
		$userId = $user->id;

		$msg = Chat::select('chats.*','u.name as userName','u.image as userImage')
			->leftJoin('users as u', 'u.id', '=', 'chats.from_id')
			->where('to_id',$userId)->orderBy('created_at', 'desc')->first();

		if($user)
		{
			if($msg)
			{
				if($request->wantsJson())
					return response()->json([
						'status_code' => 200,
						'message' => 'Request process succeed.',
						'data' => $msg
					],200);
			}
			else
			{
				$errmsg = "No Messages came";
				if($request->wantsJson())
					return response()->json([
						'status_code' => 404,
						'message' => $errmsg,
						'data' => []
					],200);
			}
		}
		else
		{
			$errmsg = "404 هذا الحساب غير موجود";
			if($request->wantsJson())
				return response()->json([
					'status_code' => 404,
					'message' => $errmsg,
					'data' => []
				],200);	
		}
	}

	public function privateChat(Request $request)
	{
		$from = $request->from;
		$msgs = array();
		$allMsgs = Chat::select('chats.*','u.name as senderName','u.image as senderImage')
		->leftJoin('users as u', 'u.id', '=', 'chats.from_id')
		->where([
			['to_id','=',Auth::user()->id],
			['from_id','=',$from]
		])->orWhere([
			['to_id','=',$from],
			['from_id','=',Auth::user()->id]
		])->orderBy('created_at', 'DESC')->get();

		foreach ($allMsgs as $item) {
			$client = User::find($item->to_id);
			$item['receiverName'] = "";
			$item['receiverImage'] = "";
			if($client){
				$item['receiverName'] = $client->name;
				$item['receiverImage'] = $client->image;
			}

			$msgs[] = $item;
		}
		
		$msgs = array_reverse($msgs);
		if($request->wantsJson())
			return response()->json([
				'status_code' => 200,
				'message' => 'Request process succeed.',
				'data' => $msgs
			],200);

		return view('chat.privatechat',with([
			'msgs' => $msgs,
			'sendto' => $from
		]));
	}


	public function contactSpecificParents(Request $request)
	{
		if(Auth::user()->role_id == 2)
		{
		
			$user = Auth::user();
			$userId = $user->id;
			$clients = $request->msgTo;

			$classes=Teacher::distinct('class_id')
			->where('user_id',$userId)->pluck('class_id')->toArray();
			$students = $parents = array();
			$students = Student::whereIn('class_id',$classes)->get();

			foreach ($students as $student) {
				if($student->parent)
					$parents[] = $student->parent->id;
			}
			$parents = array_unique($parents);
			
			if($request->msg)
			{
				foreach ($clients as $client) {
					if(in_array($client, $parents))
					{
						$recieptions[] = User::find($client);
						$msg = new Chat;
						$msg->from_id = $userId;
						$msg->to_id = $client;
						$msg->content = $request->msg;
						$save = $msg->save();
						
						$msgTo = User::find($client);
						$this->shortPush($msgTo);
					}
					else
					{
						$errmsg = "هذا الحساب غير موجود.";
						if($request->wantsJson())
							return response()->json([
								'status_code'=> 404,
								'message'=>$errmsg,
								'data'=>[]
							],200);
					}
				}
			}
			else
			{
				$errmsg = "يجب كتابة شئ فى الرسالة.";
				if($request->wantsJson())
					return response()->json([
						'status_code'=> 400,
						'message'=>$errmsg,
						'data'=>[]
					],200);
			}

			$last = Chat::select('from_id','content','created_at','updated_at')
			->where('from_id',$userId)
			->orderBy('created_at','DESC')
			->first();
				
			

			if($request->wantsJson())
				return response()->json([
					'status_code' => 201,
					'message' => 'تمت الإضافة بنجاح.',
					'data' => $last
				],200);
		}
		else
		{
			if($request->wantsJson())
				return response()->json([
					'status_code'=> 401,
					'message'=>"غير مصرح لك باستخدام هذه الخاصية.",
					'data'=>[]
				],200);
		}
	}
	
	
	public function notifications(Request $request)
	{
		$user = Auth::user();
		$userPostsIds=Post::where('user_id',$user->id)->pluck('id');

		$alert = $notSeenChat = $notSeenComment = $notSeenLike = array();
		
		$messages = Chat::select('chats.*','u.name as fromName','u.image as fromImage')
		->leftJoin('users as u', 'u.id', '=', 'chats.from_id')
		->where([
			['to_id','=',$user->id]
		])->orderBy('created_at','DESC')->get();

		foreach ($messages as $msg) {
			$msg['notificationType'] = "message";
			$notSeenChat[] = $msg;
		}

		$comments = Post::select('posts.*','u.name as fromName','u.image as fromImage')
		->leftJoin('users as u', 'u.id', '=', 'posts.user_id')
		->where([
			['user_id','!=',0]
		])->whereIn('post_id',$userPostsIds)->orderBy('created_at','DESC')->get();

		foreach ($comments as $comment) {

			if($comment['created_at'] && $comment['updated_at'])
			{
				$comment['createdAt'] =$comment->created_at->diffForHumans();
				$comment['updatedAt'] =$comment->updated_at->diffForHumans();
				
				unset($comment['created_at']);	
				unset($comment['updated_at']);
			}
			else
			{
				unset($comment['created_at']);	
				unset($comment['updated_at']);
				$comment['createdAt'] = "";
				$comment['updatedAt'] = "";
			}

			$comment['notificationType'] = "comment";
			$comment['targetPost'] = Post::find($comment->post_id);
			if($comment['targetPost']['created_at'] && $comment['targetPost']['updated_at'])
			{

				$comment['targetPost']['createdAt'] =$comment['targetPost']->created_at->diffForHumans();
				$comment['targetPost']['updatedAt'] =$comment['targetPost']->updated_at->diffForHumans();

				unset($comment['targetPost']['created_at']);	
				unset($comment['targetPost']['updated_at']);
			}
			else
			{
				unset($comment['targetPost']['created_at']);	
				unset($comment['targetPost']['updated_at']);
				$comment['targetPost']['createdAt'] = "";
				$comment['targetPost']['updatedAt'] = "";
			}
			
			unset($comment['targetPost']['seen']);
			$notSeenComment[] = $comment;
		}

		$likes = Like::select('likes.*','u.name as fromName','u.image as fromImage')
		->leftJoin('users as u', 'u.id', '=', 'likes.user_id')
		->where([
			
		])->whereIn('post_id',$userPostsIds)->orderBy('created_at','DESC')->get();

		foreach ($likes as $like) {

			if($like['created_at'] && $like['updated_at']){

				$like['createdAt'] =$like->created_at->diffForHumans();
				$like['updatedAt'] =$like->updated_at->diffForHumans();

				unset($like['created_at']);	
				unset($like['updated_at']);
			}
			else
			{
				unset($like['created_at']);	
				unset($like['updated_at']);
				$like['createdAt'] = "";
				$like['updatedAt'] = "";
			}

			$like['notificationType'] = "like";
			$like['targetPost'] = Post::find($like->post_id);
			if($like['targetPost']['created_at'] && $like['targetPost']['updated_at'])
			{

				$like['targetPost']['createdAt'] =$like['targetPost']->created_at->diffForHumans();
				$like['targetPost']['updatedAt'] =$like['targetPost']->updated_at->diffForHumans();

				unset($like['targetPost']['created_at']);	
				unset($like['targetPost']['updated_at']);
			}
			else
			{
				unset($like['targetPost']['created_at']);	
				unset($like['targetPost']['updated_at']);
				$like['targetPost']['createdAt'] = "";
				$like['targetPost']['updatedAt'] = "";
			}

			unset($like['targetPost']['seen']);
			$notSeenLike[] = $like;
		}



		$alert = array_merge($notSeenComment,$notSeenLike);
		$flag = array();
		foreach ($alert as $key => $row)
			$flag[$key] = $row['createdAt'];

		array_multisort($flag, SORT_DESC, $alert);

		if($request->wantsJson())
			return response()->json([
				'status_code' => 200,
				'message' => 'تمت العملية بنجاح.',
				'data' => $alert
			],200);
	}

	public function markAsRead(Request $request)
	{
		$auther = Auth::user();
		$msgId = $request->mid;
		$message = Chat::find($msgId);
		if($message)
		{
			if($auther->id == $message->to_id)
			{
			
				if($message->seen == 0)
				{
					$message->seen = 1;
					$message->update();
					
					$sender = User::find($message->from_id);
					
					$message['senderName'] = $sender->name;
					$message['senderImage'] = $sender->image;
					$message['senderRole'] = $sender->role->name;
	
					if($request->wantsJson())
					return response()->json([
						'status_code' => 200,
						'message' => 'تمت العملية بنجاح.',
						'data' => $message
					],200);
				}
				else
				{
					if($request->wantsJson())
					return response()->json([
						'status_code' => 400,
						'message' => 'تم قراءة الرسالة من قبل.',
						'data' => []
					],200);
				}
			}
			else
			{
				if($request->wantsJson())
					return response()->json([
						'status_code' => 401,
						'message' => 'غير مصرح لك بقراءة هذه الرسالة.',
						'data' => []
					],200);
			}
		}
		else
		{
			if($request->wantsJson())
			return response()->json([
				'status_code' => 404,
				'message' => 'هذه الرسالة غير موجودة.',
				'data' => []
			],200);
		}
	}
	
	public function contactAnyUser(Request $request)
	{
		$msg = new Chat;
		$user = Auth::user();
		$userId = $user->id;
		$recieptient = User::find($request->msgTo);
		if($user->role_id == 3 || $user->role_id == 4)
		{
			if($request->msg)
			{
				if($recieptient)
				{
					
					$msg->from_id = $userId;
					$msg->to_id = $request->msgTo;
					$msg->content = $request->msg;
					$save = $msg->save();
					
					$this->shortPush($recieptient);
				}
				else
				{
					$errmsg = "هذا الحساب غير موجود.";
					if($request->wantsJson())
						return response()->json([
							'status_code'=> 404,
							'message'=>$errmsg,
							'data'=>[]
						],200);
				}	
			}
			else
			{
				$errmsg = "يجب كتابة شئ فى الرسالة.";
				if($request->wantsJson())
					return response()->json([
						'status_code'=> 400,
						'message'=>$errmsg,
						'data'=>[]
					],200);
				return response()->json(['data' => false],200);
			}	
		}
		else
		{
			$errmsg = "هذا الحساب غير موجود.";
			if($request->wantsJson())
				return response()->json([
					'status_code'=> 401,
					'message'=>'غير مسموح لك باستخدام هذه الخاصية.',
					'data'=>[]
				],200);
		}
		

		$last = Chat::where('from_id',$userId)->orderBy('created_at','DESC')->first();
		
		

		if($request->wantsJson())
			return response()->json([
				'status_code'=> 201,
				'message'=>'تمت الاضافة بنجاح.',
				'data'=>$last
			],200);

		return response()->json(['data' => $save],200);
	}
	
	public function lastMessages(Request $request)
	{

		$user = Auth::user();
		$userId = $user->id;
		$senders = $msgs = array();


		$lastMsgs = Chat::select('chats.*','u.name as senderName','u.image as senderImage')
		->leftJoin('users as u', 'u.id', '=', 'chats.from_id')
		->where('to_id',$userId)->orderBy('created_at', 'desc')->get();

		foreach ($lastMsgs as $item) {
			$senders[] = $item->from_id;
		}

		$senders = array_unique($senders);
		foreach ($senders as $sender) {
			$msgs[] = Chat::select('chats.*','u.name as senderName','u.image as senderImage')
		->leftJoin('users as u', 'u.id', '=', 'chats.from_id')
		->where([
			['to_id','=',$userId],
			['from_id','=',$sender]

		    ])->orderBy('created_at', 'desc')->first();
		}
		
		

		if($lastMsgs)
		{
			if($request->wantsJson())
				return response()->json([
					'status_code' => 200,
					'message' => 'تمت العملية بنجاح.',
					'data' => $msgs
				],200);
		}
		else
		{
			$errmsg = "لا توجد رسائل";
			if($request->wantsJson())
				return response()->json([
					'status_code' => 404,
					'message' => $errmsg,
					'data' => []
				],200);
		}	
	}
	
	public function shortPush($user)
	{	
		$auther = Auth::user();
		$push = new PushAlert();
		$tokens =  $user->firebase_token;
		$title = "رسالة جديدة";
		$body  = $auther->name." أرسل إليك رسالة ";
		$type  = "message";
		$data  = [
			'from_id'=>$auther->id,
			'userImage' => $auther->image,
		];
		$push->FCMPush($tokens, $title, $body, $type, $data);
	}

	
	


}
