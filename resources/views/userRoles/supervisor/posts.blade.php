@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-10 col-md-offset-1">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3><i>Supervisor Dashboard - Posts - <strong style="color: brown;">{{count($posts)}} posts found.</strong></i></h3>
        </div>
        
        @if(session()->has('feedback'))
        <div class="alert alert-danger">
          {{session()->get('feedback')}}
        </div>
        @endif
        <div class="panel-body">
          <table class="table">
            <thead>
              <tr>
                <th>User Name</th>
                <th>User Phone</th>
                <th>User Access</th>
                <th>posts</th>
              </tr>
            </thead>
            <tbody>
              @foreach($users as $user)
              <tr>
                <td>{{$user->name}}</td>
                <td>{{substr($user->phone, 5)}}</td>
                <td>{{$user->role->name}}</td>
                <td>{{count($user->posts)}}</td>
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