
<style>
table,td,th{
    text-align: center !important;
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
                        <legend>Teachers:</legend>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>S.N</th>
                                    <th>Teacher</th>
                                    <th>Option</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($teachers as $index => $teacher)
                                <tr>
                                    <td>
                                        <span class="padge">
                                            {{$index+1}}
                                        </span>
                                    </td>

                                    <td>
                                        <b>{{$teacher->name}}</b>
                                    </td>

                                    <td>
                                        <a href="{{route('admins.edit.teacher.view',[
                                            'tid'=> $teacher->id
                                        ])}}">
                                            View and Edit
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
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