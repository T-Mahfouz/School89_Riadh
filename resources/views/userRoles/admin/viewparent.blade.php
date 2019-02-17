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
						<legend>Parent:</legend>
						<a style="margin-bottom: 5px;" class="btn btn-danger" href="{{route('block.parent',[
								'pid' => $parent->id
							])}}">
							Block this Parent
						</a>
						<table class="table table-bordered">
							<tr>
								<th>Name</th>
								<td>
									{{$parent->name}}	
								</td>
							</tr>

							<tr>
								<th>Phone</th>
								<td>{{$parent->phone}}</td>
							</tr>
						</table>
					</fieldset>

					<fieldset>
						<legend>Children:</legend>
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>Name</th>
									<th>Level</th>
									<th>Class</th>
								</tr>
							</thead>

							<tbody>
								@foreach($children as $child)
								<tr>
									<td>
										{{$child->name}}	
									</td>
									<td>
										{{$child->level->name}}
									</td>
									<td>
										{{$child->class->name}}
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
