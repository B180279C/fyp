<?php
function getFullTime($s_hour,$e_hour){
    $s_hour = intval($s_hour);
    $e_hour = intval($e_hour);
    $hour = $e_hour - $s_hour;
    $zero = "0";
    if($s_hour>=1000){
        $zero = "";
    }
    $f_time = "";
    $current = "";
    for($time = 100;$time<=$hour;$time=$time+100){
        if($current==""){
            $current = intval($s_hour+100);
            if($current>=1000){
                $f_time .= $zero.$s_hour."-".($s_hour+100);
            }else{
                $f_time .= $zero.$s_hour."-0".($s_hour+100);
            }
        }else{
            $zero = "0";
            if($current>=1000){
                $zero = "";
            }
            $added_hour = intval($current+100);
            if($added_hour>=1000){
                $f_time .= ",".$zero.$current."-".$added_hour;
            }else{
                $f_time .= ",".$zero.$current."-0".$added_hour;
            }
            $current = $added_hour;
        }
    }
    return $f_time;
}
?>
<table>
	<thead>
		<tr>
			<th colspan="10" style="font-size: 12px;"><b> Subject </b>: {{$course[0]->subject_code}} {{$course[0]->subject_name}}</th>
		</tr>
		<tr>
			<th colspan="10" style="font-size: 12px;"><b> Semester </b>: {{$course[0]->semester_name}}</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td width="30"><b>Name / Date.Time</b></td>
			<?php
	            if($course[0]->semester =='A'){
	                $weeks = 7;
	                $startDate = $course[0]->startDate;
	            }else{
	                $weeks = 14;
	                $startDate = $course[0]->startDate;
	            }
	            $record_attendance = array();
	        ?>
	        @for($i=1;$i<=$weeks;$i++)
				<?php
				$array = array();
				if($i==1){
					foreach($attendance as $att_row){
						if($att_row->A_week==$i){
							$s_hour = explode('-',$att_row->hour);
	                        $e_hour = explode('-',$att_row->hour);
	                        $last_hour = getFullTime($s_hour[0],$e_hour[1]);
	                        $timetable_hour = $att_row->class_hour;
	                        $explode_th = explode(',',$timetable_hour);
	                        $sperate = explode(',',$last_hour);
	                        $less_hour = $att_row->less_hour;
	                        if(count($sperate)>count($explode_th)){
	                        	$less_hour = count($explode_th)-count($sperate);
	                        }
	                        if($less_hour>0){
			                 	echo "<td width='12'><b>".$att_row->A_date ."</b><br/> ( ".$att_row->weekly." ".$s_hour[0]."-".$e_hour[1]." ) Fill up (".$less_hour." Hour)</td>";
			                }else if($less_hour<0){
			                 	echo "<td width='12'><b>".$att_row->A_date ."</b><br/> ( ".$att_row->weekly." ".$s_hour[0]."-".$e_hour[1]." ) Minus On (".$less_hour." Hour)</td>";
			                }else{
			                 	echo "<td width='12'><b>".$att_row->A_date ."</b><br/> ( ".$att_row->weekly." ".$s_hour[0]."-".$e_hour[1]." )</td>";
			                }
			                array_push($array,$att_row->tt_id.'/'.$last_hour);
			                array_push($record_attendance,$att_row->attendance_id);
						}
					}
					foreach($timetable as $row){
						$week = "Next ".$row->week;
		                $hour = explode(',',$row->class_hour);
                        $s_hour = explode('-',$hour[0]);
                        $e_hour = explode('-',$hour[count($hour)-1]);
		                $NewDate = date('Y-m-d', strtotime($startDate . $week));
		                $check = false;
		                $less = "";
		                $less_hour = 0;
		                for($a=0;$a<=(count($array)-1);$a++){
			                $array_tt_id = explode('/',$array[$a]);
			                if($row->tt_id==$array_tt_id[0]){
			                  	if($row->class_hour==$array_tt_id[1]){
									$check = true;
			                  	}else{
				                   	$array_hour_count = explode(',',$array_tt_id[1]);
				                   	$full_hour = count($hour);
				                   	$less_hour = ($full_hour-count($array_hour_count));
									$less = "Fill up (".$less_hour." Hour)";
									if($less_hour<=0){
										$check = true;
									}
	 		                  	}
			                }
		                }
		                if($check==false){
		                	if($row->F_or_H=="Full"){
		                		echo "<td width='12'><b>".$NewDate ."</b><br/> ( ".$row->week." ".$s_hour[0]."-".$e_hour[1]." ) ".$less."</td>";
		                		array_push($record_attendance,"0");
		                	}else{
		                		if ($i % 2) {
		                			echo "<td width='12'><b>".$NewDate ."</b><br/> ( ".$row->week." ".$s_hour[0]."-".$e_hour[1]." ) ".$less."</td>";
		                		array_push($record_attendance,"0");
			                	}
		                	}
		                	
		                }
					}
				}else{
					foreach($attendance as $att_row){
						if($att_row->A_week==$i){
							$s_hour = explode('-',$att_row->hour);
	                        $e_hour = explode('-',$att_row->hour);
	                        $last_hour = getFullTime($s_hour[0],$e_hour[1]);
	                        $timetable_hour = $att_row->class_hour;
	                        $explode_th = explode(',',$timetable_hour);
	                        $sperate = explode(',',$last_hour);
	                        $less_hour = $att_row->less_hour;
	                        if(count($sperate)>count($explode_th)){
	                        	$less_hour = count($explode_th)-count($sperate);
	                        }
	                        if($less_hour>0){
			                 	echo "<td width='12'><b>".$att_row->A_date ."</b><br/> ( ".$att_row->weekly." ".$s_hour[0]."-".$e_hour[1]." ) Fill up (".$less_hour." Hour)</td>";
			                }else if($less_hour<0){
			                 	echo "<td width='12'><b>".$att_row->A_date ."</b><br/> ( ".$att_row->weekly." ".$s_hour[0]."-".$e_hour[1]." ) Minus On (".$less_hour." Hour)</td>";
			                }else{
			                 	echo "<td width='12'><b>".$att_row->A_date ."</b><br/> ( ".$att_row->weekly." ".$s_hour[0]."-".$e_hour[1]." )</td>";
			                }
			                array_push($array,$att_row->tt_id.'/'.$last_hour);
			                array_push($record_attendance,$att_row->attendance_id);
						}
					}
					$startDate = strtotime($course[0]->startDate);
		            $add_date = $startDate+(($i-1)*(86400*7));
		            $add_startDate = date('Y-m-d',$add_date);
					foreach($timetable as $row){
						$week = "Next ".$row->week;
		                $hour = explode(',',$row->class_hour);
                        $s_hour = explode('-',$hour[0]);
                        $e_hour = explode('-',$hour[count($hour)-1]);
		                $NewDate = date('Y-m-d', strtotime($add_startDate . $week));
		                $check = false;
		                $less = "";
		                $less_hour = 0;
		                for($a=0;$a<=(count($array)-1);$a++){
			                $array_tt_id = explode('/',$array[$a]);
			                if($row->tt_id==$array_tt_id[0]){
			                  	if($row->class_hour==$array_tt_id[1]){
									$check = true;
			                  	}else{
				                   	$array_hour_count = explode(',',$array_tt_id[1]);
				                   	$full_hour = count($hour);
				                   	$less_hour = ($full_hour-count($array_hour_count));
									$less = "Fill up (".$less_hour." Hour)";
									if($less_hour<=0){
										$check = true;
									}
	 		                  	}
			                }
		                }
		                if($check==false){
		                	if($row->F_or_H=="Full"){
		                		echo "<td width='12'><b>".$NewDate ."</b><br/> ( ".$row->week." ".$s_hour[0]."-".$e_hour[1]." ) ".$less."</td>";
		                		array_push($record_attendance,"0");
		                	}else{
		                		if ($i % 2) {
		                			echo "<td width='12'><b>".$NewDate ."</b><br/> ( ".$row->week." ".$s_hour[0]."-".$e_hour[1]." ) ".$less."</td>";
		                		array_push($record_attendance,"0");
			                	}
		                	}
		                }
					}
				}
				?>
			@endfor
		</tr>
			<?php
			foreach($assign_student as $row_student){
				$student_id = $row_student->student_id;
				$student_name = $row_student->name;
				echo '<tr><td>'.$student_id." ".$student_name.'</td>';
				for($k=0;$k<=(count($record_attendance)-1);$k++){
					$students_status = "";
					if($record_attendance[$k]!="0"){
						foreach($attendance as $att_row){
							if($att_row->attendance_id==$record_attendance[$k]){
								$students_status = $att_row->students_status;
							}
						}
					}
					$pos = strpos($students_status, $student_id, 0);
		            $status = substr($students_status, ($pos+9),1);
		            if($status=="P"){
		            	echo "<td align='center' style='color:green;'>Present</td>";
		            }else if($status=="L"){
		            	echo "<td align='center' style='color:green;'>Late</td>";
		            }else if($status=="A"){
		            	echo "<td align='center' style='color:red;'>Absent</td>";
		            }else{
		            	echo "<td align='center' style='color:red;'>0</td>";
		            }
				}
				echo '</tr>';
			}
			?>
	</tbody>
</table>