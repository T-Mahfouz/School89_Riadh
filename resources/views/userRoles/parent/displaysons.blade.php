
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
@section('title','Display-children')
@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">

			<div class="panel panel-default">
				<div class="panel-heading">
					<h3><b>Welcome Mr.{{Auth::user()->name}}</b></h3>
				</div>

				<div class="panel-body">
					<fieldset>
						@if(session()->has('deleted')) 
						<div class="alert alert-success">
							<strong>
								{{session()->get('deleted')}}
							</strong>
						</div>
						@elseif(session()->has('edited'))
						<div class="alert alert-success">
							<strong>
								{{session()->get('edited')}}
							</strong>
						</div>
						@endif
						
						<legend>Parents:</legend>
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>S.N</th>
									<th>Son</th>
									<th>Level</th>
									<th>Class</th>
									<th>Options</th>
								</tr>
							</thead>
							<tbody>
								@foreach($children as $index => $child)
								<tr>
									<td>
										<span class="padge">
											{{$index+1}}
										</span>
									</td>

									<td>
										<b>{{$child->name}}</b>
									</td>

									<td>
										<b>
											{{$child->level->name}}
										</b>
									</td>
									
									<td>
										<b>
											{{$child->class->name}}
										</b>
									</td>
									<td>
										<a href="{{route('parent.editSon.view',['id'=>$child->id])}}" >
											Edit
										</a>
										|
										<a href="{{route('parent.removeChild',['id'=>$child->id])}}">
											Remove
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

