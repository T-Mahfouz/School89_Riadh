
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
th{
    width: 20%;
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
                                <tr>
                                    <th>Levels</th>
                                    <td>
                                       {{$levels}} 
                                    </td>
                                </tr>
                                <tr>
                                    <th>Classes</th>
                                    <td>
                                       {{$classes}} 
                                    </td>
                                </tr>
                        </table>
                        
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@include('includes.writeMsg')
