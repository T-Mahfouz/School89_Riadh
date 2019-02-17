
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
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3><b>Welcome Mr.{{Auth::user()->name}}</b></h3>
				</div>

				<div class="panel-body">
					<fieldset>
						<legend>Display Students:</legend>
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>S.N</th>
									<th>Student</th>
									<th>Level-Class</th>
									<th>Parent</th>
								</tr>
							</thead>
							<tbody>
								@foreach($students as $index => $student)
								<tr>
									<td>
										<span class="padge">
											{{$index+1}}
										</span>
									</td>

									<td>
										<b>{{$student['name']}}</b>
									</td>

									<td>
										<b>
											{{
												$student->level->name
												." - ".
												$student->class->name
											}}
										</b>
									</td>

									<td>
										<b>
											@if($student->parent)
											{{
												$student->parent->name
											}}
											@else
												<b style="color: #a52a2a;">Not Set</b>
											@endif
											
										</b>
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
