@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-10 col-md-offset-1">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3><i>Supervisor Dashboard - Messages - <strong style="color: brown;">{{count($messages)}} messages found.</strong></i></h3>
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
                <th>From</th>
                <th>To</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              @foreach($messages as $message)
              <tr>
                <td>{{($message->from)? $message->from->name:"User is no longer existed"}} <i style="color: brown;">{{($message->from)?$message->from->role->name:""}}</i></td>
                <td>{{($message->to)?$message->to->name:"User is no longer existed"}} <i style="color: brown;">{{($message->to)?$message->to->role->name:""}}</i></td>
                <td>{{$message->created_at->diffForHumans()}}</td>
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