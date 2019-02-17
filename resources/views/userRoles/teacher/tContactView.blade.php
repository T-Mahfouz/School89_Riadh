@extends('layouts.app')
@section('title','Display-Students')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">

            @if(session()->has('chat-feedback-success'))
            <div class="alert alert-success">
                <strong>
                    {{session()->get('chat-feedback-success')}}
                </strong>
            </div>
            @elseif(session()->has('chat-feedback-error'))
            <div class="alert alert-danger">
                <strong>
                    {{session()->get('chat-feedback-error')}}
                </strong>
            </div>
            @endif

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3><b>Welcome Mr.{{$user->name}}</b></h3>
                </div>

                <div class="panel-body">
                    <fieldset>
                        <legend>Contact All Parents Of Students in:</legend>
                        <form action="{{route('teacher.toparents')}}" method="post">
                            {{csrf_field()}}

                            <div class="form-group">
                                <label for="classes">Students in Class:</label>
                                <select class="form-control" name="class" id="class">
                                    <option value="0">All</option>
                                    @foreach($classes as $class)
                                    <option value="{{$class->id}}">
                                        {{$class->name}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="classes">Message:</label>
                                <textarea class="form-control" name="msg" id="t_p_msg" rows="5" placeholder="Write your message here..."></textarea>
                            </div>
                            <div class="form-group">
                                <input type="submit" value="Send" class="btn btn-primary">
                            </div>
                        </form>
                    </fieldset>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection