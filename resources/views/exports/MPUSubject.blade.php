<table>
	<thead>
		<tr>
			<th width="5" align="left"><b>No</b></th>
			<th width="20"><b>Level</b></th>
			<th width="20"><b>Subject Code</b></th>
			<th width="50"><b>Subject Name</b></th>
			<th width="50"><b>Subject Type</b></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$i = 1;
		?>
		@foreach($subjects as $row)
			<tr>
				<td align="left">{{$i}}</td>
				<td>{{$row->level}}</td>
				<td>{{$row->subject_code}}</td>
				<td>{{$row->subject_name}}</td>
				<td>{{$row->subject_type}}</td>
			</tr>
		<?php 
		$i++;
		?>
		@endforeach
	</tbody>
</table>