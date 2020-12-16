<?php
$title = "Reviewer";
$option4 = "id='selected-sidebar'";
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
@extends('layouts.layout')

@section('content')

<script type="text/javascript">
$(document).ready(function(){
    $('.week').click(function(){
        var id = $(this).attr("id");
        $('#plan_detail_'+id).slideToggle("slow", function(){
            // check paragraph once toggle effect is completed
            if($('#plan_detail_'+id).is(":visible")){
                $('#icon_'+id).removeClass('fa fa-plus');
                $('#icon_'+id).addClass('fa fa-minus');
            }else{
                $('#icon_'+id).removeClass('fa fa-minus');
                $('#icon_'+id).addClass('fa fa-plus');
            }
        });
    });
});
</script>
<style type="text/css">
.show_image_link:hover{
    text-decoration: none;
}
</style>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->semester_name}} : {{$course[0]->short_form_name}} / {{$course[0]->subject_code}} {{$course[0]->subject_name}} ( {{$course[0]->name}} )</p>
        <p class="pass_page">
            <a href="{{$character}}/home" class="first_page"> Home </a>/
            <a href="{{$character}}/Reviewer">{{$cha}} </a>/
            <a href="{{$character}}/Reviewer/course/{{$course[0]->course_id}}">{{$course[0]->semester_name}} : {{$course[0]->short_form_name}} / {{$course[0]->subject_code}} {{$course[0]->subject_name}} ( {{$course[0]->name}} )</a>/
            <span class="now_page">Attendance</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 5px 10px;">
        <div class="col-md-12">
             <p class="page_title">Attendance</p>
             <hr style="margin-top:5px;margin-bottom: 0px;padding: 0px;">
             <div class="row" style="border: 0px solid black;margin:10px 0px 0px 0px;padding:0px;">
             	<div class="col-12" style="padding: 0px 12px 0px 12px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i>
                	<span style="font-size: 17px;">
                		Completed of Attendance Marked : <b>{{round($completed)}} %</b>
                	</span>
            	</div>
	        	<div class="col-12" style="padding: 0px 12px 5px 12px;">
	        		<span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> The Percentage of average of student attendance (Marked) : 
	        		@if($average==100)
	        		<b> {{round($average)}} % </b>
	        		@else
	        		<b> {{round(100-$average)}} % </b>
	        		@endif
	        		</span>
	        	</div>
            </div>
             <div class="details" style="padding: 5px 5px 0px 5px;">
             	<div class="row" style="padding:0px;"> 
                    <div class="col-md-12" style="padding:0px;">
	             	<?php
	                    if($course[0]->semester =='A'){
	                        $weeks = 7;
	                        $startDate = $course[0]->startDate;
	                    }else{
	                        $weeks = 14;
	                        $startDate = $course[0]->startDate;
	                    }
	                    $absent_person = 0;
	                    $take_hour = 0;
	                ?>
	                 @for($i=1;$i<=$weeks;$i++)
	                 <?php
	                 	$displays = "display:none;";
	                 	$array = array();
	                 	$count_hour = 0;
	                 	$check = '<i class="fa fa-times wrong" aria-hidden="true" style="float:right;padding-top:5px;margin-right:15px;"></i>';
	                 	if($i==1){
		                    foreach($timetable as $row){
								$week = "Next ".$row->week;
		                   		$NewDate = date('Y-m-d', strtotime($startDate . $week));
		                   		$hour = explode(',',$row->class_hour);
		                   		$count_hour = $count_hour + count($hour);
		                   		$take_hour = 0;
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
	                                        if($less_hour==0){
	                                        	for($s=0;$s<=count($sperate)-1;$s++){
		                                        	$take_hour++;
		                                        }
	                                        }else if($less_hour<0){
	                                        	for($s=0;$s<=count($explode_th)-1;$s++){
		                                        	$take_hour++;
		                                        }
	                                    	}else{
	                                        	$take_hour= $take_hour + $less_hour;
	                                        }
	                                }
	                            }
		                    }
		                    if($take_hour>=$count_hour&&$count_hour!=0){
		                    	$check = '<i class="fa fa-check correct" aria-hidden="true" style="float:right;padding-top:5px;margin-right:13px;"></i>';
		                    }

		                }else{
		                    $startDate = strtotime($course[0]->startDate);
		                   	$add_date = $startDate+(($i-1)*(86400*7));
		                   	$add_startDate = date('Y-m-d',$add_date);
		                    foreach($timetable as $row){
								$week = "Next ".$row->week;
		                   		$NewDate = date('Y-m-d', strtotime($add_startDate . $week));
		                   		$hour = explode(',',$row->class_hour);
		                   		$take_hour = 0;
		                   		if($row->F_or_H=="Full"){
			            			$count_hour = $count_hour + count($hour);
			            		}else{
			            			if ($i % 2) {
			            				$count_hour = $count_hour + count($hour);
			            			}
			            		}
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
	                                        if($less_hour==0){
	                                        	for($s=0;$s<=count($sperate)-1;$s++){
		                                        	$take_hour++;
		                                        }
	                                        }else if($less_hour<0){
	                                        	for($s=0;$s<=count($explode_th)-1;$s++){
		                                        	$take_hour++;
		                                        }
	                                    	}else{
	                                        	$take_hour= $take_hour + $less_hour;
	                                        }
	                                }
	                            }
		                    }
		                    if($take_hour>=$count_hour&&$count_hour!=0){
		                    	$check = '<i class="fa fa-check correct" aria-hidden="true" style="float:right;padding-top:5px;margin-right:13px;"></i>';
		                    }
		                }
	                 	?>
	                    <p class="col-12 align-self-center week" id="{{$i}}" style="padding:10px 10px;font-size: 20px;margin: 0px;">
	                        <i class="fa fa-plus" id="icon_{{$i}}" aria-hidden="true" style="font-size: 20px;color: #0d2f81"></i>
	                        Week {{$i}}
	                        {!!$check!!}
	                   	</p>
	                   	<div class="teachingPlan" style="border-bottom: 1px solid grey;padding:0px 20px;">
		                   	<div class="row plan" id="plan_detail_{{$i}}" style="padding: 0px 20px;{{$displays}}">
		                   		<table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);margin-top: 5px;" id="table" class="table table-hover">
		                   			<thead>
		                   				<tr style="background-color: #d9d9d9;text-align: center;">
			                   				<th style="border-left:1px solid #e6e6e6;color:black;"><b>Date & Time</b></th>
			                   				<th style="border-left:1px solid #e6e6e6;color:black;"><b>Attendance List</b></th>
			                   				<th style="border-left:1px solid #e6e6e6;color:black;"><b>Average(Attend %)</b></th>
			                   				<th style="border-left:1px solid #e6e6e6;color:black;"><b>Status</b></th>
		                   				</tr>
		                   			</thead>
		                   			<?php
		                   		if($i==1){
		                   			$take_hour = 0;
		                   			foreach($attendance as $att_row){
		                   				$absent_person = 0;
			                   			if($att_row->A_week==$i){
			                   				$students_status = $att_row->students_status;
					                        $substr_count = substr_count($students_status, 'Absent');
					                        $absent_person = $absent_person+$substr_count;
					                        $average = (($count_student-$absent_person)/$count_student)*100; 
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
	                                        if($less_hour==0){
	                                        	for($s=0;$s<=count($sperate)-1;$s++){
		                                        	$take_hour++;
		                                        }
	                                        }else if($less_hour<0){
	                                        	for($s=0;$s<=count($explode_th)-1;$s++){
		                                        	$take_hour++;
		                                        }
	                                    	}else{
	                                        	$take_hour= $take_hour + $less_hour;
	                                        }
			                   				echo "<tr>";
			                   				if($less_hour>0){
			                   					echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><b>".$att_row->A_date ."</b> ( ".$att_row->week." ".$s_hour[0]."-".$e_hour[1]." ) Fill up (".$less_hour." Hour)</td>";
			                   				}else if($less_hour<0){
			                   					echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><b>".$att_row->A_date ."</b> ( ".$att_row->week." ".$s_hour[0]."-".$e_hour[1]." ) Minus on (".$less_hour." Hour)</td>";
			                   				}else{
			                   					echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><b>".$att_row->A_date ."</b> ( ".$att_row->week." ".$s_hour[0]."-".$e_hour[1]." ) </td>";
			                   				}

			                   				if($less_hour<0){
			                   					$less_hour=0;
			                   				}
			                   				echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center><a href='".$character."/Reviewer/Attendance/".$att_row->tt_id."-".$i."-".$less_hour."/student_list/".$att_row->A_date."' class='show_image_link'>List</a></center></td>";
			                   				echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center>".$average."% </center></td>";
			                   				echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center><i class='fa fa-check correct' aria-hidden='true'></i></center></td>";
			                   				echo "</tr>";
			                   				array_push($array,$att_row->tt_id.'/'.$last_hour);
			                   			}
			                   		}
			                   		$average = 0;
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
					                   			echo "<tr>";
					                   			echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><b>".$NewDate ."</b> ( ".$row->week." ".$s_hour[0]."-".$e_hour[1]." ) ".$less."</td>";
					                   			if($take_hour<$count_hour){
			                   						echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center>Empty</center></td>";
			                   						echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center>".$average."% </center></td>";
					                   				echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center><i class='fa fa-times wrong' aria-hidden='true'></i></center></td>";
			                   					}else{
			                   						echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center>Skip</center></td>";
			                   						echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center>".$average."% </center></td>";
					                   				echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center><i class='fa fa-ban wrong' aria-hidden='true'></i></center></td>";
			                   					}
					                   			echo "</tr>";
					                   		}else{
					                   			if ($i % 2) {
					                   				echo "<tr>";
						                   			echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><b>".$NewDate ."</b> ( ".$row->week." ".$s_hour[0]."-".$e_hour[1]." ) ".$less."</td>";
						                   			if($take_hour<$count_hour){
				                   						echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center>Empty</center></td>";
				                   						echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center>".$average."% </center></td>";
						                   				echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center><i class='fa fa-times wrong' aria-hidden='true'></i></center></td>";
				                   					}else{
				                   						echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center>Skip</center></td>";
				                   						echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center>".$average."% </center></td>";
						                   				echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center><i class='fa fa-ban wrong' aria-hidden='true'></i></center></td>";
				                   					}
						                   			echo "</tr>";
					                   			}
					                   		}
				                   		}
		                   			}
		                   		}else{
		                   			$take_hour = 0;
		                   			foreach($attendance as $att_row){
		                   				$absent_person = 0;
			                   			if($att_row->A_week==$i){
			                   				$students_status = $att_row->students_status;
					                        $substr_count = substr_count($students_status, 'Absent');
					                        $absent_person = $absent_person+$substr_count;
					                        $average = (($count_student-$absent_person)/$count_student)*100; 
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
	                                        if($less_hour==0){
	                                        	for($s=0;$s<=count($sperate)-1;$s++){
		                                        	$take_hour++;
		                                        }
	                                        }else if($less_hour<0){
	                                        	for($s=0;$s<=count($explode_th)-1;$s++){
		                                        	$take_hour++;
		                                        }
	                                    	}else{
	                                        	$take_hour= $take_hour + $less_hour;
	                                        }
			                   				echo "<tr>";
			                   				if($less_hour>0){
			                   					echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><b>".$att_row->A_date ."</b> ( ".$att_row->week." ".$s_hour[0]."-".$e_hour[1]." ) Fill up (".$less_hour." Hour)</td>";
			                   				}else if($less_hour<0){
			                   					echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><b>".$att_row->A_date ."</b> ( ".$att_row->week." ".$s_hour[0]."-".$e_hour[1]." ) Minus on (".$less_hour." Hour)</td>";
			                   				}else{
			                   					echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><b>".$att_row->A_date ."</b> ( ".$att_row->week." ".$s_hour[0]."-".$e_hour[1]." ) </td>";
			                   				}
			                   				echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center><a href='".$character."/Reviewer/Attendance/".$att_row->tt_id."-".$i."-".$less_hour."/student_list/".$att_row->A_date."' class='show_image_link'>List</a></center></td>";
			                   				echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center>".$average."% </center></td>";
			                   				echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center><i class='fa fa-check correct' aria-hidden='true'></i></center></td>";
			                   				echo "</tr>";
			                   				array_push($array,$att_row->tt_id.'/'.$last_hour);
			                   			}
			                   		}
			                   		$startDate = strtotime($course[0]->startDate);
		                   			$add_date = $startDate+(($i-1)*(86400*7));
		                   			$add_startDate = date('Y-m-d',$add_date);
		                   			$average = 0;
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
					                   			echo "<tr>";
					                   			echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><b>".$NewDate ."</b> ( ".$row->week." ".$s_hour[0]."-".$e_hour[1]." ) ".$less."</td>";
					                   			if($take_hour<$count_hour){
			                   						echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center>Empty</center></td>";
			                   						echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center>".$average."% </center></td>";
					                   				echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center><i class='fa fa-times wrong' aria-hidden='true'></i></center></td>";
			                   					}else{
			                   						echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center>Skip</center></td>";
			                   						echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center>".$average."% </center></td>";
					                   				echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center><i class='fa fa-ban wrong' aria-hidden='true'></i></center></td>";
			                   					}
					                   			echo "</tr>";
					                   		}else{
					                   			if ($i % 2) {
					                   				echo "<tr>";
						                   			echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><b>".$NewDate ."</b> ( ".$row->week." ".$s_hour[0]."-".$e_hour[1]." ) ".$less."</td>";
						                   			if($take_hour<$count_hour){
				                   						echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center>Empty</center></td>";
				                   						echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center>".$average."% </center></td>";
						                   				echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center><i class='fa fa-times wrong' aria-hidden='true'></i></center></td>";
				                   					}else{
				                   						echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center>Skip</center></td>";
				                   						echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center>".$average."% </center></td>";
						                   				echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center><i class='fa fa-ban wrong' aria-hidden='true'></i></center></td>";
				                   					}
						                   			echo "</tr>";
					                   			}
					                   		}
				                   		}
		                   			}
			                   	}
		                   		?>
		                   		</table>
                   			</div>
	                    </div>
	                @endfor
		            </div>
		        </div>
             </div>
        </div>
    </div>
</div>
@endsection