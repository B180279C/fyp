<table>
	<thead>
		<tr>
			<th width="5" align="left"><b>No</b></th>
			<th width="10"><b>Staff Id</b></th>
			<th width="20"><b>Name</b></th>
			<th width="20"><b>Email</b></th>
			<th width="10"><b>Position</b></th>
			<th width="50"><b>Faculty</b></th>
			<th width="50"><b>Department</b></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$i = 1;
		?>
		@foreach($staffs as $row)
			<tr>
				<td align="left">{{$i}}</td>
				<td>{{$row->staff_id}}</td>
				<td>{{$row->name}}</td>
				<td>{{$row->email}}</td>
				<td>{{$row->position}}</td>
				<td>{{$row->faculty_name}}</td>
				<td>{{$row->department_name}}</td>
			</tr>
		<?php 
		$i++;
		?>
		@endforeach
	</tbody>
</table>