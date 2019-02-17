@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-10 col-md-offset-1">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3><i>Supervisor Dashboard - Admins</i></h3>
        </div>
        
        @if(session()->has('feedback'))
        <div class="alert alert-danger">
          {{session()->get('feedback')}}
        </div>
        @endif
        <div class="panel-body">
          <a class="btn btn-default" href="{{route('supervisor.addview',['urole'=>'admin'])}}">Add Admin</a>
          <table class="table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Phone</th>
                <th>Options</th>
              </tr>
            </thead>
            <tbody>
              @foreach($admins as $admin)
              <tr>
                <td>{{$admin->name}}</td>
                <td>{{substr($admin->phone, 5)}}</td>
                <td>
                  <a href="{{route('supervisor.editview',['uid'=>$admin->id,'urole'=>'admin'])}}">Edit</a> | 
                  <a href="{{route('supervisor.deleteuser',['uid'=>$admin->id])}}">Delete</a>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
       </div>
     </div>
   </div>
 </div>
</div>
@endsection