<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    @include('includes.head-resources')
    <style>
    .content a button{
        background-color: #a52a2a;
        margin-bottom: 1%;
    }
    a:hover{
        color: white;
        text-decoration: none;
    }
</style>
</head>

<body>
    
    <div class="container-fluid">
        <div class="row">
            @include('includes.welcome-header')
        </div>
        
        <div class="row">
           <div class="content">
               @include('includes.options')
           </div> 
        </div>
    </div>


</body>
</html>