@extends('layouts.app')
@section('title','Display-Students')
@section('content')

<style>
.form-group{
    margin-bottom: 10px !important;
}
</style>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">

                <div class="panel-heading">
                    <h3><b>Welcome {{$user->name}}</b></h3>
                </div>
                
                <div class="panel-body">
                    <legend>Assign Parent To a student</legend>

                    @if(session()->has('addP-error'))
                    <div class="alert alert-danger">
                        <strong>
                            {{session()->get('addP-error')}}
                        </strong>
                    </div>
                    @elseif(session()->has('addP-success'))
                    <div class="alert alert-success">
                        <strong>
                            {{session()->get('addP-success')}}
                        </strong>
                    </div>
                    @endif

                    <form action="{{route('teacher.addParent')}}" method="post">
                        {{csrf_field()}}

                        <div class="form-group">
                            <label for="students">Student:</label>
                            <select class="form-control" name="student" id="student">
                                @foreach($students as $student)
                                <option value="{{$student->id}}">
                                    {{$student->name}}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="control-label">   Name
                            </label>
                            <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>

                            @if ($errors->has('name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                            @endif
                        </div>

                        

                        <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                            <label for="phone" class="control-label">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-addon">+966</span>
                                <input id="phone" type="text" class="form-control" name="phone" value="{{ old('phone') }}" required autofocus>
                            </div>

                            @if ($errors->has('phone'))
                            <span class="help-block">
                                <strong>{{ $errors->first('phone') }}</strong>
                            </span>
                            @endif
                        </div>
 
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="control-label">Password</label>
                            <input id="password" type="password" class="form-control" name="password" required>

                            @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                            @endif
                        </div>
                        
                        <div class="form-group">

                            <input type="submit" value="Add" class="btn btn-primary">
                        </div>
                        
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
