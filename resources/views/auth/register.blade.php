@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Register</div>

                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        

                        <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                            <label for="image" class="col-md-4 control-label">Choose Image</label>

                            <div class="col-md-6">
                                <label class="fileContainer">
                                    <input id="image" type="file" class="custom-file-input" name="image" value="avatar.jpg">
                                </label>

                                @if ($errors->has('image'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('image') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Name</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>

                                @if ($errors->has('name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        

                        <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                            <label for="phone" class="col-md-4 control-label">Phone Number</label>

                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-addon">+966</span>
                                    <input id="phone" type="text" class="form-control" name="phone" value="{{ substr(old('phone'), 5) }}" required autofocus>
                                </div>

                                @if ($errors->has('phone'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('phone') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        
                        <fieldset class="col-md-6 col-md-offset-3" style="border-bottom: 1px solid;margin-bottom: 10px;">
                            <legend>
                                Add Child
                                <b onclick="childField()" id="addAnother" style="cursor: pointer;">+</b>
                            </legend>

                            <div id="children-list">
                                <div class="add-child">
                                    <div class="form-group">
                                        <label for="childName" class="col-md-4 control-label">
                                            Name
                                        </label>

                                        <div class="col-md-8">
                                            <input id="childName" type="text" class="form-control" name="children[childName][]" value="">  
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="childLevel" class="col-md-4 control-label">
                                            Level
                                        </label>

                                        <div class="col-md-8">
                                            <select id="childLevel" name="children[childLevel][]" >
                                                @foreach($levels as $level)
                                                <option value="{{$level->id}}">{{$level->name}}</option>
                                                @endforeach
                                            </select>  
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="childClass" class="col-md-4 control-label">
                                            Class
                                        </label>

                                        <div class="col-md-8">
                                            <select id="childClass" name="children[childClass][]" >
                                                @foreach($classes as $class)
                                                <option value="{{$class->id}}">{{$class->name}}</option>
                                                @endforeach
                                            </select>  
                                        </div>
                                    </div>
                                </div>
                                <hr>
                            </div>
                        </fieldset>
                        
                        
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Sign Up
                                </button>
                                already have an <a href="{{route('login')}}">
                                    account
                                </a>
                                ?
                            </div>
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
        $field = '<div class="add-child"><div class="form-group"><label for="childName" class="col-md-4 control-label">Name</label><div class="col-md-8"><input id="childName" type="text" class="form-control" name="children[childName][]" value=""></div></div><div class="form-group"><label for="childLevel" class="col-md-4 control-label">Level</label><div class="col-md-8"><select id="childLevel" name="children[childLevel][]" >@foreach($levels as $level)<option value="{{$level->id}}">{{$level->name}}</option>@endforeach</select></div></div><div class="form-group"><label for="childClass" class="col-md-4 control-label">Class</label><div class="col-md-8"><select id="childClass" name="children[childClass][]">@foreach($classes as $class)<option value="{{$class->id}}">{{$class->name}}</option>@endforeach</select></div></div></div><hr>';
        $('#children-list').append($field);
    }

</script>




