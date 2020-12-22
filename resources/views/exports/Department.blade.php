<table>
	<thead>
		<tr>
			<th width="5" align="left"><b>No</b></th>
			<th width="50"><b>Faculty Name</b></th>
			<th width="50"><b>Department Name</b></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$i = 1;
		?>
		@foreach($departments as $row)
			<tr>
				<td align="left">{{$i}}</td>
				<td>{{$row->faculty_name}}</td>
				<td>{{$row->department_name}}</td>
			</tr>
		<?php 
		$i++;
		?>
		@endforeach
	</tbody>
</table>