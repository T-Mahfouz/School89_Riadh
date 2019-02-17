
<style>
table,td,th{
    text-align: center !important;
}
th{
    width: 20%;
}
table span{
    width: 50px;
    height: 100%;
    color: #fff;
    background-color: #617687;
    padding: 5px;
    margin-bottom: 2px;
}

</style>


@extends('layouts.app')
@section('title','Display-Students')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3><b>Welcome {{Auth::user()->name}}</b></h3>
                </div>

                <div class="panel-body">

                    <fieldset>
                        <legend>  
                            <b style="text-decoration: underline;">
                                {{$teacher->name}}   
                            </b>
                            info.
                        </legend>
                        <table class="table table-bordered">
                            <tr>
                                <th>Name</th>
                                <td>{{$teacher->name}}</td>
                            </tr>

                            <tr>
                                <th>Levels</th>
                                <td>{{$tlevels}}</td>
                            </tr>

                            <tr>
                                <th>Classes</th>
                                <td>{{$tclasses}}</td>
                            </tr>

                            <tr>
                                <th>Subjects</th>
                                <td>{{$tsubjects}}</td>
                            </tr>
                        </table>
                    </fieldset>

                    <fieldset>
                        <legend>
                            Unassign {{$teacher->name}} from class
                        </legend>
                        <table class="table table-bordered">
                            @foreach($editclasses as $class)
                            <tr>
                                <th>Class Name</th>
                                <td>{{$class['name']}}</td>
                                <td>
                                    <a href="{{
                                        route('class.unassign',['tid'=>$teacher->id,
                                            'id'=>$class['id']
                                            
                                        ])
                                    }}">Unassign</a>
                                </td>
                            </tr>
                            @endforeach
                        </table>
                    </fieldset>

                    <fieldset>
                        <legend>
                            Assign {{$teacher->name}} to class
                        </legend>
                        <form action="{{route('admins.edit.teacher',['tid'=>$teacher->id])}}" method="post">

                            {{csrf_field()}}
                                
                            <div class="form-group col-md-3">
                                <label for="Level">Level</label>
                                <select class="form-control" name="level" id="">
                                    @foreach($levels as $level)
                                    <option value="{{$level->id}}">{{$level->name}}</option>
                                    @endforeach
                                </select>
                                
                            </div>

                            <div class="form-group col-md-3">
                                <label for="Class">Class</label>
                                <select class="form-control" name="class" id="">
                                    @foreach($classes as $class)
                                    <option value="{{$class->id}}">{{$class->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="Subject">Subject</label>
                                <select class="form-control" name="subject" id="">
                                    @foreach($subjects as $subject)
                                    <option value="{{$subject->id}}">{{$subject->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-3">
                                <input style="margin-top: 25px;" class="btn btn-primary" type="submit" value="Assign">
                            </div>
                        </form>   
                    </fieldset>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@include('includes.writeMsg')


<script>
    var sendMsgUrl = '{{ route("teacher.send.toparent") }}';
    var token = '{{ Session::token() }}';
</script>