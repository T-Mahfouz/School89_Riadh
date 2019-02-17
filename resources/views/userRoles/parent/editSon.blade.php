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
                    <h3><b>Welcome Mr.</b></h3>
                </div>
                
                <div class="panel-body">
                    <legend>Edit Children</legend>

                    @if(session()->has('edited')) 
                    <div class="alert alert-success">
                        <strong>
                            {{session()->get('edited')}}
                        </strong>
                    </div>
                    @endif



                    <form action="{{route('parent.editSon')}}" method="post">
                        {{csrf_field()}}
                        <input type="hidden" name="id" value="{{$son->id}}">
                        <div id="children-list" class="col-md-8">
                            <div class="add-child">
                                <div class="form-group col-md-8">
                                    <label for="childName" class="control-label">
                                        Name:
                                    </label>
                                    <input id="childName" type="text" class="form-control" name="childName" value="{{$son->name}}">
                                </div>

                                <div class="form-group col-md-7">
                                    <label for="childLevel" class="control-label">
                                        Level:
                                    </label>
                                    <select class="form-control" id="childLevel" name="childLevel" >
                                        @foreach($levels as $level)
                                        <option {{($son->level->id == $level->id)?"selected":""}} value="{{$level->id}}">{{$level->name}}</option>
                                        @endforeach
                                    </select>  
                                </div>

                                <div class="form-group col-md-7">
                                    <label for="childClass" class="control-label">
                                        Class:
                                    </label>
                                    <select class="form-control" id="childClass" name="childClass" >
                                        @foreach($classes as $class)
                                        <option {{($son->class->id == $class->id)?"selected":""}} value="{{$class->id}}">{{$class->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <hr>
                        </div>
                        <div class="clearfix"></div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" value="Edit Son">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

