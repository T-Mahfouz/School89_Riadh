@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3><i>Teachers Dashboard</i></h3>
                    Welcome Mr.{{Auth::user()->name}}
                </div>
                
                <div class="panel-body">

                 <div class="teacher-func col-md-3">
                    
                    <a href="{{route('teacher.classes')}}">
                        <button class="btn btn-primary btn-block">
                            My Classes
                        </button>
                    </a>

                    <a href="{{route('teacher.students')}}">
                        <button class="btn btn-primary btn-block">
                            Display Students
                        </button>
                    </a>

                    <a href="{{route('teacher.parents')}}">
                        <button class="btn btn-primary btn-block">
                            Send to Specific Parent
                        </button>
                    </a>

                    <a href="{{route('teacher.colleagues')}}">
                        <button class="btn btn-primary btn-block">
                            Colleagues
                        </button>
                    </a>

                    <a href="{{route('teacher.add.parent')}}">
                        <button class="btn btn-primary btn-block">
                            Add a new Parent
                        </button>
                    </a>

                    <a href="{{route('teacher.contact.parents')}}">
                        <button class="btn btn-primary btn-block">
                            Contact Parents
                        </button>
                    </a>

                    <a href="{{route('teacher.get.messages')}}">
                        <button class="btn btn-primary btn-block">
                            Comming Messages
                        </button>
                    </a>

                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection