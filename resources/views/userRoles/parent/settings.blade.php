@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <b class="panel-title">Settings</b>
                </div>
                <form action="{{route('parents.settings')}}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    @if(session()->has('updated'))
                    <div class="alert alert-success">
                        <strong>
                            {{session()->get('updated')}}
                        </strong>
                    </div>
                    @endif
                    <div class="panel-body">
                        <div class="col-md-3 profile-pic">
                            <img src="{{asset('images/users/'.$parent->image)}}" alt="">

                            <div class="form-group">
                                <label for="image" class="col-md-12 control-label">Change Image</label>
                                <div class="col-md-12">
                                    <label class="fileContainer">
                                        <input id="image" type="file" class="custom-file-input" name="image" value="avatar.jpg">
                                    </label>
                                    @if ($errors->has('image'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('image') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-9 user-info">

                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="control-label">First Name</label>

                                <input id="name" type="text" class="form-control" name="name" value="{{$parent->name}}" required autofocus>
                                @if ($errors->has('name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                                @endif
                            </div>
                            
                            <div class="form-group{{ $errors->has('bio') ? ' has-error' : '' }}">
                                <label for="name" class="control-label">Brief</label>

                                <textarea id="bio" type="text" class="form-control" name="bio">{{$parent->bio}}</textarea>

                                @if ($errors->has('bio'))
                                <span class="help-block">

                                    <strong>{{ $errors->first('bio') }}</strong>
                                </span>
                                @endif
                            </div>
                            

                            <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                <label for="phone" class="control-label">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-addon">+966</span>
                                    <input id="phone" type="text" class="form-control" name="phone" value="{{substr($parent->phone,-9)}}" required autofocus>
                                </div>

                                @if ($errors->has('phone'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('phone') }}</strong>
                                </span>
                                @endif
                            </div>
                            
                            <fieldset>
                                <legend>Change Password</legend>

                                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label for="Password" class="control-label">New Password:</label>
                                    <input id="pass" type="password" class="form-control{{ $errors->has('password') ? ' has-error' : '' }}" name="password">
                                    @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </fieldset>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    Update
                                </button>
                            </div>
                            
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

<style>

fieldset{
    border: 1px solid #ccc !important;
    border-radius: 7px !important;
    margin-bottom: 20px !important;
    padding: 10 !important;
}

</style>




