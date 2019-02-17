
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
					
					<fieldset>
						<legend>Contact Us Messages:</legend>
						@foreach($msgs as $msg)
						<a href="{{route('private.chat',[
							'from' => $msg->from->id
						])}}">
						<div class="msg-container col-md-12">
							
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
						</a>
						@endforeach
						
					</fieldset>

				</div>
			</div>
		</div>
	</div>
</div>
@endsection

