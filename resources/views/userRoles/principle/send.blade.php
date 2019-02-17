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
                    <h3><b>Welcome Mr.{{Auth::user()->name}}</b></h3>
                </div>

                <div class="panel-body">
                    <fieldset>
                        <legend>Contact To:</legend>
                        <form action="{{route('principles.send')}}" method="post">
                            {{csrf_field()}}

                            <div class="form-group">
                                <label for="classes">What would you like to contact to?</label>
                                <select class="form-control" name="type" id="class">
                                    <option value="all">All</option>
                                    
                                    <option value="parents">
                                        Parents Only
                                    </option>

                                    <option value="teachers">
                                        Teachers Only
                                    </option>
                                    
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