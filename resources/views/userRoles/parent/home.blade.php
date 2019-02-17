@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3><i>Parents Dashboard</i></h3>
                    Welcome Mr.{{Auth::user()->name}}
                </div>
                
                <div class="panel-body">

                 <div class="teacher-func col-md-3">
                    
                    <a href="{{route('parents.add.view')}}">
                        <button class="btn btn-primary btn-block">
                            Add Children
                        </button>
                    </a>

                    <a href="{{route('parents.children')}}">
                        <button class="btn btn-primary btn-block">
                            Edit-Remove my Children
                        </button>
                    </a>

                    <a href="{{route('parents.selectTeachers')}}">
                        <button class="btn btn-primary btn-block">
                            My son's teachers
                        </button>
                    </a>
                    
                    <a href="{{route('parents.principles')}}">
                        <button class="btn btn-primary btn-block">
                            Principles
                        </button>
                    </a>
                    
                    <a href="{{route('parent.get.messages')}}">
                        <button class="btn btn-primary btn-block">
                            Comming Messages
                        </button>
                    </a>

                    <a href="{{route('parents.showSettings')}}">
                        <button class="btn btn-primary btn-block">
                            Settings
                        </button>
                    </a>

                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection