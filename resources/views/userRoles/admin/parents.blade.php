
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
						<legend>Parents:</legend>
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>S.N</th>
									<th>Name</th>
									<th>Option</th>
								</tr>
							</thead>
							<tbody>
								@foreach($parents as $index => $parent)
								<tr>
									<td>
										<span class="padge">
											{{$index+1}}
										</span>
									</td>

									<td>
										<b>{{$parent->name}}</b>
									</td>

									<td>
										<a href="{{route('admin.parent.view',['pid'=>$parent->id])}}">
											View Parent Info.
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
