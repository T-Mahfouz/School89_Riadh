@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-10 col-md-offset-1">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3><i>Supervisor Dashboard - Edit User</i></h3>
        </div>
        
        @if(session()->has('feedback'))
        <div class="alert alert-danger">
          {{session()->get('feedback')}}
        </div>
        @endif
        
        <div class="panel-body supervisor-editUser">
          <form action="{{route('supervisor.edituser')}}" method="post">
            {{csrf_field()}}
            <input type="hidden" name="uid" value="{{$user->id}}">
            <div class="form-group col-md-8">
              <label for="name">Name:</label>
              <input type="text" name="name" value="{{$user->name}}" class="form-control">
            </div>

            <div class="form-group col-md-8">
              <label for="phone">Phone</label>
              <div class="input-group">
                <span class="input-group-addon">
                  +966
                </span>
                <input type="text" name="phone" value="{{substr($user->phone, 5)}}" class="form-control">
              </div>
            </div>

            <div class="form-group col-md-8">
              <label for="password">Password:(optional)</label>
              <input type="password" name="password" class="form-control">
            </div>
            <div class="form-group col-md-8">
              <input type="submit" value="Edit" class="btn btn-success">
            </div>
          </form>
        </div> 
        

      </div>
    </div>
  </div>
</div>
@endsection