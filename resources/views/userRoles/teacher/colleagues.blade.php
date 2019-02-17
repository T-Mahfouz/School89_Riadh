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
                        <legend>Your Colleagues In:</legend>
                        <form action="{{route('teacher.displayStf')}}" method="post">
                            {{csrf_field()}}
                            
                            <div class="form-group">
                                <label for="Levels">select by:</label>
                                <select id="by" onchange="show()" class="form-control">
                                    <option></option>
                                    <option>Level</option>
                                    <option>Class</option>
                                </select>
                            </div>

                            <div id="level-group" class="form-group">
                                <label for="Levels">Levels:</label>
                                <select class="form-control" name="level" id="level">
                                    <option value="0"></option>
                                    @foreach($levels as $level)
                                    <option value="{{$level->id}}">
                                        {{$level->name}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="class-group" class="form-group">
                                <label for="classes">Classes:</label>
                                <select class="form-control" name="class" id="class">
                                    <option value="0"></option>
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

<style>
    #class-group,#level-group
    {
        display: none;
    }
</style>

<script>

    function show(){
       sort = $('#by').val();
       if(sort == "Level"){
            $('#level-group').css('display','block');
            $('#level select').val('0');
            $('#class-group').css('display','none');
       }
       else if(sort == "Class"){
            $('#class-group').css('display','block');
            $('#class select').val('0');
            $('#level-group').css('display','none');
       }
       else{
            $('#class-group').css('display','none');
            $('#level-group').css('display','none');
            $('#level select').val('0');
            $('#class select').val('0');
       }

    }

</script>