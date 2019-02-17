@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3><i>Admins Dashboard</i></h3>
                    Welcome {{Auth::user()->name}}
                </div>
                
                <div class="panel-body">

                 <div class="teacher-func col-md-3">
                    
                    <a href="{{route('admins.teachers')}}">
                        <button class="btn btn-primary btn-block">
                            Teachers
                        </button>
                    </a>

                    <a href="{{route('admins.addteacher.view')}}">
                        <button class="btn btn-primary btn-block">
                            Add Teacher
                        </button>
                    </a>

                    <a href="{{route('admins.parents')}}">
                        <button class="btn btn-primary btn-block">
                            Parents
                        </button>
                    </a>

                    <a href="{{route('admins.contactus')}}">
                        <button class="btn btn-primary btn-block">
                            Contact Us Messages
                        </button>
                    </a>
                    
                    <a href="{{route('admins.schoolurls.view')}}">
                        <button class="btn btn-primary btn-block">
                            School URLs
                        </button>
                    </a>

                    <a href="{{route('admins.reset')}}">
                        <button class="btn btn-primary btn-block">
                            Reset for new year
                        </button>
                    </a>

                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection