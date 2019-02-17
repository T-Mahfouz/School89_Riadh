@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3><i>Principles Dashboard</i></h3>
                    Welcome {{Auth::user()->name}}
                </div>
                
                <div class="panel-body">

                 <div class="teacher-func col-md-3">
                    
                    <a href="{{route('principles.teachers')}}">
                        <button class="btn btn-primary btn-block">
                            Teachers
                        </button>
                    </a>

                     <a href="{{route('principles.send.view')}}">
                        <button class="btn btn-primary btn-block">
                            Send Messages
                        </button>
                    </a>
                    
                    <a href="{{route('principles.contactus')}}">
                        <button class="btn btn-primary btn-block">
                            Contact Us Messages
                        </button>
                    </a>

                   
                    

                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection