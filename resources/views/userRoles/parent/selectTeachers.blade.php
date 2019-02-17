@extends('layouts.app')
@section('title','Display-Students')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3><b>Welcome {{Auth::user()->name}}</b></h3>
                </div>
                
                <div class="panel-body">
                    <fieldset>
                        <legend>Teachers of Your son:</legend>

                        <form action="{{route('parents.teachers')}}" method="post">
                            {{csrf_field()}}
                            
                            <div class="form-group">
                                <label for="Sons">
                                    Son:
                                </label>

                                <select name="son" class="form-control">
                                    @foreach($sons as $son)
                                    <option value="{{$son->id}}">
                                        {{$son->name}}
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

