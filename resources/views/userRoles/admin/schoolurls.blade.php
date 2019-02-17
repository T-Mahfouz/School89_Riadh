
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
                            Update School URLs
                        </legend>
                        <form action="{{route('admins.schoolurls')}}" method="post">

                            {{csrf_field()}}
                                
                            <div class="form-group col-md-7">
                                <label for="Images">Images:</label>
                                <div class="input-group">
                                    <span class="input-group-addon">https://</span>
                                    <input class="form-control" type="text" name="images" value="{{substr($urls->images, 8)}}" placeholder="">
                                </div>

                            </div>

                            <div class="form-group col-md-7">
                                <label for="Videos">Videos:</label>
                                <div class="input-group">
                                    <span class="input-group-addon">https://</span>
                                    <input class="form-control" type="text" name="videos" value="{{substr($urls->videos, 8)}}" placeholder="">
                                </div>
                                
                            </div>

                            <div class="form-group col-md-7">
                                <label for="Location">Location:</label>
                                <div class="input-group">
                                    <span class="input-group-addon">https://</span>
                                    <input class="form-control" type="text" name="location" value="{{substr($urls->location, 8)}}" placeholder="">
                                </div>
                                
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group col-md-3">
                                <input style="margin-top: 25px;" class="btn btn-primary" type="submit" value="Update">
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
