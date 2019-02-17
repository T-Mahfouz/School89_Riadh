@extends('layouts.app')
@section('title','Display-Students')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3><b>Welcome Mr.{{$user->name}}</b></h3>
                </div>
                
                <div class="panel-body">
                    <fieldset>
                        <legend>Display Students:</legend>
                        <form action="{{route('teacher.displaystd')}}" method="post">
                            {{csrf_field()}}
                            
                            <div class="form-group">
                                <label for="classes">Classes:</label>
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
                                <input type="submit" value="Display" class="btn btn-primary">
                            </div>
                        </form>
                    </fieldset>
                    

                </div>
            </div>
        </div>
    </div>
</div>
@endsection