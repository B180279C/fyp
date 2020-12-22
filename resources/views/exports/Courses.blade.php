<table>
	<thead>
		<tr>
			<th width="5" align="left"><b>No</b></th>
			<th width="50"><b>Programme Name</b></th>
			<th width="20"><b>Short Form Name</b></th>
			<th width="20"><b>Subject Code</b></th>
			<th width="50"><b>Subject Name</b></th>
			<th width="20"><b>Semester</b></th>
			<th width="20"><b>Credit</b></th>
			<th width="30"><b>Lecturer (Staff ID)</b></th>
			<th width="30"><b>Moderator (Staff ID)</b></th>
			<th width="30"><b>Verified By (Staff ID)</b></th>
			<th width="30"><b>Approved By (Staff ID)</b></th>
			<th width="100"><b>Timetable</b></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$i = 1;
		?>
		@foreach($courses as $row)
			<?php
			$tt_data = "";
			?>
			<tr>
				<td align="left">{{$i}}</td>
				<td>{{$row->programme_name}}</td>
				<td>{{$row->short_form_name}}</td>
				<td>{{$row->subject_code}}</td>
				<td>{{$row->subject_name}}</td>
				<td>{{$row->semester_name}}</td>
				<td align="left">{{$row->credit}}</td>
				<td>{{$row->name}} ({{$row->staff_id}})</td>
				@if($row->moderator!="")
					@foreach($staffs as $other_row)
						@if($row->moderator==$other_row->id)
							<td>{{$other_row->name}} ({{$other_row->staff_id}})</td>
						@endif
					@endforeach
				@endif
				@if($row->verified_by!="")
					@foreach($staffs as $other_row)
						@if($row->verified_by==$other_row->id)
							<td>{{$other_row->name}} ({{$other_row->staff_id}})</td>
						@endif
					@endforeach
				@endif
				@if($row->approved_by!="")
					@foreach($staffs as $other_row)
						@if($row->approved_by==$other_row->id)
							<td>{{$other_row->name}} ({{$other_row->staff_id}})</td>
						@endif
					@endforeach
				@endif

				@foreach($timetable as $tt_row)
					@if($tt_row->course_id==$row->course_id)
						<?php
							$week = $tt_row->week;
							$hour = explode(',',$tt_row->class_hour);
							$s_hour = explode('-',$hour[0]);
	            			$e_hour = explode('-',$hour[count($hour)-1]);
	            			$f_or_h = $tt_row->F_or_H;
	            			$tt_data .= $week.",".$s_hour[0]."-".$e_hour[1].",".$f_or_h."; ";
						?>
					@endif
				@endforeach 
				<td>{{$tt_data}}</td>
			</tr>
		<?php 
		$i++;
		?>
		@endforeach
	</tbody>
</table>