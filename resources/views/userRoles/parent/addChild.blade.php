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
                    <legend>
                        Add Children
                        <b onclick="childField()" id="addAnother" style="cursor: pointer;">+</b>
                    </legend>

                    @if(session()->has('addC-success')) 
                    <div class="alert alert-success">
                        <strong>
                            {{session()->get('addC-success')}}
                        </strong>
                    </div>
                    @endif



                    <form action="{{route('parents.add')}}" method="post">
                        {{csrf_field()}}
                        <div id="children-list" class="col-md-8">
                            <div class="add-child">
                                <div class="form-group col-md-8">
                                    <label for="childName" class="control-label">
                                        Name:
                                    </label>
                                    <input id="childName" type="text" class="form-control" name="children[childName][]" value="">
                                </div>

                                <div class="form-group col-md-7">
                                    <label for="childLevel" class="control-label">
                                        Level:
                                    </label>
                                    <select class="form-control" id="childLevel" name="children[childLevel][]" >
                                        @foreach($levels as $level)
                                        <option value="{{$level->id}}">{{$level->name}}</option>
                                        @endforeach
                                    </select>  
                                </div>

                                <div class="form-group col-md-7">
                                    <label for="childClass" class="control-label">
                                        Class:
                                    </label>
                                    <select class="form-control" id="childClass" name="children[childClass][]" >
                                        @foreach($classes as $class)
                                        <option value="{{$class->id}}">{{$class->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <hr>
                        </div>
                        <div class="clearfix"></div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" value="Add Children">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>

    function childField()
    {
        $field = '<div class="add-child"><div class="form-group col-md-8"><label for="childName" class="control-label">Name:</label><input id="childName" type="text" class="form-control" name="children[childName][]" value=""></div><div class="form-group col-md-7"><label for="childLevel" class="control-label">Level:</label><select class="form-control" id="childLevel" name="children[childLevel][]" >@foreach($levels as $level)<option value="{{$level->id}}">{{$level->name}}</option>@endforeach</select></div><div class="form-group col-md-7"><label for="childClass" class="control-label">Class:</label><select class="form-control" id="childClass" name="children[childClass][]" >@foreach($classes as $class)<option value="{{$class->id}}">{{$class->name}}</option>@endforeach</select></div></div><div class="clearfix"></div><hr>';

        $('#children-list').append($field);
    }

</script>
