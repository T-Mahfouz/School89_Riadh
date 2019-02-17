@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3><i>Supervisor Dashboard</i></h3>
                    Welcome {{Auth::user()->name}}
                </div>

                <div class="panel-body">
                   <div class="goto col-md-5">
                       <a href="{{route('supervisor.admins')}}">Admins ({{$admins}})</a>
                   </div>
                   <div class="goto col-md-5">
                       <a href="{{route('supervisor.principles')}}">Principles ({{$principles}})</a>
                   </div>
                   <div class="goto col-md-5">
                       <a href="{{route('supervisor.teachers')}}">Teachers ({{$teachers}})</a>
                   </div>
                   <div class="goto col-md-5">
                       <a href="{{route('supervisor.parents')}}">Parents ({{$parents}})</a>
                   </div>
                   <div class="goto col-md-5">
                       <a href="{{route('supervisor.posts')}}">Posts ({{$posts}})</a>
                   </div>
                   <div class="goto col-md-5">
                       <a href="{{route('supervisor.messages')}}">Messages ({{$messages}})</a>
                   </div>
                
            </div>
        </div>
    </div>
</div>
</div>
@endsection