
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
					<h3><b>Welcome Mr.{{Auth::user()->name}}</b></h3>
				</div>

				<div class="panel-body">
					<fieldset>
						<legend>Parents:</legend>
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>S.N</th>
									<th>Student</th>
									<th>Parent</th>
									<th>Option</th>
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
											@if($student->parent)
											{{
												$student->parent->name
											}}
												
											@else
												<b style="color: #a52a2a;">Not Set</b>
											@endif
										</b>
									</td>
									
									<td>
										@if($student->parent)
										<a href="#" onclick="sendMsg(
											event,
											{{Auth::user()->id}},
											{{$student->parent->id}}
											)">
											Send message
										</a>
										@else
											<b style="color: #a52a2a;">Not Set</b>
										@endif
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