<table>
	<thead>
		<tr>
			<th width="5" align="left"><b>No</b></th>
			<th width="50"><b>Name</b></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$i = 1;
		?>
		@foreach($facultys as $row)
			<tr>
				<td align="left">{{$i}}</td>
				<td>{{$row['faculty_name']}}</td>
			</tr>
		<?php 
		$i++;
		?>
		@endforeach
	</tbody>
</table>