@extends('layouts.app')

@section('content')



<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">

                <div class="panel-body">

                    @if(Auth::user()->role->name != 'parent'
                    && Auth::user()->role->name != 'admin')
                    <section class="row new-post">
                        <div class="col-md-8">
                            <header>
                                <h3>What do you have to say?</h3>
                            </header>
                            @if(session()->has('message'))
                            <div class="alert alert-success" role="alert">
                                <strong class="alert-link">{{session()->get('message')}}</strong>
                            </div>
                            @endif
                            <form action="{{route('create.post')}}" method="post" enctype="multipart/form-data">
                                {{csrf_field()}}

                                <div class="form-group">

                                    <textarea class="form-control" name="content" id="new-post" rows="5" placeholder="Your post"></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="Image">Add Image:</label>
                                    <input type="file" name="image">
                                </div>

                                <div class="form-group">

                                 <label for="Image">Visible To:</label>

                                 <select onchange="show()" class="" name="vtype" id="vtype">
                                     <option value="all">All</option>
                                     <option value="parent">Parents only</option>
                                     <option value="staff">Staff only</option>
                                     <option value="class">Class</option>
                                 </select>

                                 <select class="" name="classId" id="classId">
                                     @foreach($classes as $class)
                                     <option value=" {{$class->id}}">
                                        Class {{$class->name}}
                                    </option>
                                    @endforeach

                                </select> 


                            </div>
                            <div class="clearfix"></div>
                            <button type="submit" class="btn btn-primary">Create Post</button>
                        </form>
                    </div>
                </section>
                @endif

                <section class="row posts">
                    <div class="col-md-8">
                        <header>
                            <h3>What other people say...</h3>
                        </header>

                        @foreach($posts as $post)

                        <article class="post">

                            <div class="owner">
                                <span class="owner-img">
                                    <img src="{{
                                        asset('images/users/'.$post->user->image)
                                    }}" alt="">
                                </span>
                                <span class="owner-name">
                                    <b>
                                        {{$post->user->name}}
                                    </b>  
                                </span>
                            </div>

                            @if($post->image)
                            <div class="col-md-8 post-image">
                                <img src="{{
                                    asset('images/posts/'.$post->image->image)
                                }}" alt="">
                            </div>
                            @endif

                            <div class="clearfix"></div>

                            <div class="col-md-8 post-content">
                                <b>{{$post->content}}</b> 
                            </div>

                            <div class="clearfix"></div>

                            <div class="info">
                                <p>
                                    <b class="allLikes">
                                        {{count($post->likes)." "}}
                                    </b> Likes  
                                </p>
                                posted by {{$post->user->name}} on 
                                {{ \Carbon\Carbon::parse($post->created_at)->format('d M,Y')}}
                                at {{ \Carbon\Carbon::parse($post->created_at)->format('h:i A')}}
                            </div>

                            <div class="interaction">
                                <a class="like" data-postId="{{$post->id}}" href="#" data-val="{{Auth::user()->likes()->where('post_id',$post->id)->first()? Auth::user()->likes()->where('post_id',$post->id)->first()->like == 1 ? 'd': 'l':'l'}}">
 
                                    {{Auth::user()->likes()->where('post_id',$post->id)->first()? Auth::user()->likes()->where('post_id',$post->id)->first()->like == 1 ? 'Dislike': 'Like':'Like' }}
                                </a> |
                                <a class="comment" data-postId="{{$post->id}}" href="#">Comment</a>
                                @if(Auth::user()->id == $post->user_id)
                                | <a class="edit" data-postId="{{$post->id}}" href="#">Edit</a> |
                                <a href="{{route('delete.post',['post_id'=>$post->id])}}">Delete</a> 
                                @endif
                            </div>

                            <div class="clearfix"></div>

                            <div class="comments">
                                @foreach($post->comments as $comment)
                                @if($comment->user_id == Auth::user()->id || $comment->user_id == $post->user_id || $post->user_id == Auth::user()->id)
                                <div class="singleCom">
                                    <div class="owner">
                                        <span class="owner-img">
                                            <img src="{{
                                                asset('images/users/'.$comment->user->image)
                                            }}" alt="">
                                        </span>
                                        <span class="owner-name">
                                            <b>
                                                {{
                                                    $comment->user->name
                                                }}
                                            </b>  
                                        </span>
                                    </div>
                                   <div class="clearfix"></div>
                                   <div class="post-content">
                                    <b>{{$comment->content}}</b> 
                                    </div>

                                    <div class="info"> 
                                        posted by {{$comment->user->name}} on 
                                        {{ \Carbon\Carbon::parse($comment->created_at)->format('d M,Y')}}
                                        at {{ \Carbon\Carbon::parse($comment->created_at)->format('h:i A')}}
                                    </div>

                                    <div class="interaction">
                                        @if(Auth::user()->id == $comment->user_id)
                                        
                                        <a href="{{route('delete.post',['post_id'=>$comment->id])}}">Delete</a> 
                                        @endif
                                    </div>

                                    <div class="clearfix"></div> 
                                </div>
                                @endif
                                @endforeach
                            </div>

                        </article>

                        @endforeach
                    </div>
                </section>

            </div>
        </div>
    </div>
</div>
</div>
@endsection

@include('includes.editPostModal')
@include('includes.commentPostModal')

<style>
#classId{
    display: none;
}
</style>

<script>
    var token = '{{ Session::token() }}';
    var urlEditPost = '{{ route("edit.post") }}';
    var urlLikePost = '{{ route("like.post") }}';
    var urlWriteComment = '{{ route("comment.post") }}';

    function show()
    {
        visible = $('#vtype').val();
        if(visible == "class"){
            $('#classId').css('display','inline-block');
        }
        else if(visible != "class"){
           $('#classId').css('display','none');
       }
    }

</script>











