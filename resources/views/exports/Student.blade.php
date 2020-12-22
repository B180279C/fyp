<table>
	<thead>
		<tr>
			<th width="5" align="left"><b>No</b></th>
			<th width="10"><b>Student Id</b></th>
			<th width="20"><b>Name</b></th>
			<th width="20"><b>Email</b></th>
			<th width="15"><b>Batch</b></th>
			<th width="10"><b>Semester</b></th>
			<th width="10"><b>Intake</b></th>
			<th width="50"><b>Programme</b></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$i = 1;
		?>
		@foreach($students as $row)
			<tr>
				<td align="left">{{$i}}</td>
				<td>{{$row->student_id}}</td>
				<td>{{$row->name}}</td>
				<td>{{$row->email}}</td>
				<td>{{$row->batch}}</td>
				<td>{{$row->semester_name}}</td>
				<td>Year {{$row->intake}}</td>
				<td>{{$row->programme_name}}</td>
			</tr>
		<?php 
		$i++;
		?>
		@endforeach
	</tbody>
</table>