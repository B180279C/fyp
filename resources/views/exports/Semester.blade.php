<table>
	<thead>
		<tr>
			<th width="5" align="left"><b>No</b></th>
			<th width="10"><b>Name</b></th>
			<th width="12"><b>Start Date</b></th>
			<th width="12"><b>End Date</b></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$i = 1;
		?>
		@foreach($semester as $row)
			<tr>
				<td align="left">{{$i}}</td>
				<td>{{$row->semester_name}}</td>
				<td>{{$row->startDate}}</td>
				<td>{{$row->endDate}}</td>
			</tr>
		<?php 
		$i++;
		?>
		@endforeach
	</tbody>
</table>