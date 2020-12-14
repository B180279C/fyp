<?php
$title = "Course";
$option1 = "id='selected-sidebar'";
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
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</p>
        <p class="pass_page">
            <a href="{{$character}}/home" class="first_page"> Home </a>/
            <a href="{{$character}}/course_list">Courses </a>/
            <a href="{{$character}}/course/action/{{$course[0]->course_id}}">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</a>/
            <span class="now_page">Attendance</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 5px 10px;">
        <div class="col-md-12">
             <p class="page_title">Attendance</p>
             <hr style="margin-top:5px;margin-bottom: 0px;padding: 0px;">
             <div class="details" style="padding: 10px 5px 0px 5px;">
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
	                ?>
	                 @for($i=1;$i<=$weeks;$i++)
	                 	<?php
	                 	$displays = "display:none;";
	                 	if($i==1){
		                    foreach($timetable as $row){
								$week = "Next ".$row->week;
		                   		$NewDate = date('Y-m-d', strtotime($startDate . $week));
		                   		$date = date('Y-m-d');
		                   		$date = date('Y-m-d');
			                	if($NewDate==$date){
			                		$displays = "";
			                	}
		                    }
		                }else{
		                    $startDate = strtotime($course[0]->startDate);
		                   	$add_date = $startDate+(($i-1)*(86400*7));
		                   	$add_startDate = date('Y-m-d',$add_date);
			                foreach($timetable as $row){
			                	$week = "Next ".$row->week;
			                	$NewDate = date('Y-m-d', strtotime($add_startDate . $week));
			                	$date = date('Y-m-d');
			                	if($NewDate==$date){
			                		$displays = "";
			                	}
		                   	}
		                }
	                 	?>
	                    <p class="col-12 align-self-center week" id="{{$i}}" style="padding:10px 10px;font-size: 20px;margin: 0px;">
	                        <i class="fa fa-plus" id="icon_{{$i}}" aria-hidden="true" style="font-size: 20px;color: #0d2f81"></i>
	                        Week {{$i}}
	                   	</p>
	                   	<div class="teachingPlan" style="border-bottom: 1px solid grey;padding:0px 20px;">
		                   	<div class="row plan" id="plan_detail_{{$i}}" style="padding: 0px 20px;{{$displays}}">
		                   		<table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);margin-top: 5px;" id="table" class="table table-hover">
		                   			<thead>
		                   				<tr style="background-color: #d9d9d9;text-align: center;">
			                   				<th style="border-left:1px solid #e6e6e6;color:black;"><b>Date & Time</b></th>
			                   				<th style="border-left:1px solid #e6e6e6;color:black;"><b>QR Code</b></th>
			                   				<th style="border-left:1px solid #e6e6e6;color:black;"><b>Attendance List</b></th>
		                   				</tr>
		                   			</thead>
		                   		<?php
		                   		if($i==1){
		                   			foreach($timetable as $row){
		                   				$week = "Next ".$row->week;
		                   				$hour = explode(',',$row->class_hour);
	                                    $s_hour = explode('-',$hour[0]);
	                                    $e_hour = explode('-',$hour[count($hour)-1]);
			                   			$NewDate = date('Y-m-d', strtotime($startDate . $week));
		                   				if($row->F_or_H=="Full"){
			                   				echo "<tr>";
			                   				echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><b>".$NewDate ."</b> ( ".$row->week." ".$s_hour[0]."-".$e_hour[1]." ) </td>";
			                   				echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><a href></a></td>";
			                   				echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center><a href='' class='show_image_link'>List</a></center></td>";
			                   				echo "</tr>";
			                   			}else{
			                   				if ($i % 2) {
			                   					echo "<tr>";
				                   				echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><b>".$NewDate ."</b> ( ".$row->week." ".$s_hour[0]."-".$e_hour[1]." ) </td>";
				                   				echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><a href></a></td>";
				                   				echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center><a href='' class='show_image_link'>List</a></center></td>";
				                   				echo "</tr>";
			                   				}
			                   			}
		                   			}
		                   		}else{
		                   			$startDate = strtotime($course[0]->startDate);
		                   			$add_date = $startDate+(($i-1)*(86400*7));
		                   			$add_startDate = date('Y-m-d',$add_date);
		                   			foreach($timetable as $row){
		                   				$week = "Next ".$row->week;
		                   				$hour = explode(',',$row->class_hour);
                                        $s_hour = explode('-',$hour[0]);
                                        $e_hour = explode('-',$hour[count($hour)-1]);
		                   				$NewDate = date('Y-m-d', strtotime($add_startDate . $week));
		                   				if($row->F_or_H=="Full"){
			                   				echo "<tr>";
			                   				echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><b>".$NewDate ."</b> ( ".$row->week." ".$s_hour[0]."-".$e_hour[1]." ) </td>";
			                   				echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'></td>";
			                   				echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center><a href='' class='show_image_link'>List</a></center></td>";
			                   				echo "</tr>";
			                   			}else{
			                   				if ($i % 2) {
			                   					echo "<tr>";
				                   				echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><b>".$NewDate ."</b> ( ".$row->week." ".$s_hour[0]."-".$e_hour[1]." ) </td>";
				                   				echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'></td>";
				                   				echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center><a href='' class='show_image_link'>List</a></center></td>";
				                   				echo "</tr>";
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