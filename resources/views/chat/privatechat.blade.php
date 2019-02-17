
<style>
.msg-container{
    border-left: 2px solid #479a28;
    margin-bottom: 30px;
}

.sender-img{
    height: 30px;
    width: 30px;
}

.sender-img img{
    max-height: 100%;
    max-width: 100%;
}

.inbox{
    max-height: 500px;
    overflow-y: scroll; 
}

</style>


@extends('layouts.app')
@section('title','Display-Students')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3><b>Welcome {{Auth::user()->name}}</b></h3>
                </div>

                <div class="panel-body">
                    
                    <fieldset class="inbox">
                        <legend>Private Inbox:</legend>
                        @foreach($msgs as $msg)
                        <div style="border-left:{{($msg->from_id == Auth::user()->id)?'3px solid #219ddf':'3px solid #449928'}}" class="msg-container col-md-12"> 
                            <div class="owner">
                                <span class="owner-img">
                                    <img src="{{
                                        asset('images/users/'.$msg->from->image)
                                    }}" alt="">
                                </span>
                                <span class="owner-name">
                                    <b>{{$msg->from->name}}</b>  
                                </span>
                            </div>

                            <div class="clearfix"></div>

                            <div class="col-md-8 post-content">
                                <b>{{$msg->content}}</b> 
                            </div>  
                            
                            <div class="clearfix"></div>

                            <div class="info">

                                Sent by {{$msg->from->name}} on 
                                {{ \Carbon\Carbon::parse($msg->created_at)->format('d M,Y')}}
                                at {{ \Carbon\Carbon::parse($msg->created_at)->format('h:i A')}}
                            </div>
                        </div>
                        @endforeach
                    </fieldset>
                    <hr>
                    <form action="{{route('send.back')}}" method="post">
                        {{csrf_field()}}
                        <input type="hidden" name="msgTo" readonly value="{{$sendto}}">
                        <div class="form-group">
                            <textarea class="form-control" name="msg" rows="5" placeholder="what do you want to say?"></textarea>
                        </div>
                        <div class="form-group">
                            <div class="col-md-3 col-md-offset-4">
                                <input class="btn btn-primary btn-block" type="submit" value="send">
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@include('includes.writeMsg')
