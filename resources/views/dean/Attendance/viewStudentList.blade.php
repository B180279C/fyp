<?php
$title = "Course";
$option1 = "id='selected-sidebar'";
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

    $('.present').click(function(){
    	var student_list = $('.student_list').val();
    	var student = student_list.split("/");
    	for(var i=0;i<(student.length-1);i++){
    		var $radios = $('input:radio[name=attendance'+student[i]+']');
    		$radios.filter('[value=Present]').prop('checked', true);
    	}
    });

    $('.absent').click(function(){
    	var student_list = $('.student_list').val();
    	var student = student_list.split("/");
    	for(var i=0;i<(student.length-1);i++){
    		var $radios = $('input:radio[name=attendance'+student[i]+']');
    		$radios.filter('[value=Absent]').prop('checked', true);
    	}
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
            <a href="{{$character}}/Attendance/{{$course[0]->course_id}}">Attendance</a>/
            <span class="now_page">List</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 5px 10px;">
        <div class="col-md-12">
             <p class="page_title">List</p>
             <hr style="margin-top:5px;margin-bottom: 0px;padding: 0px;">
            
            @if(\Session::has('success'))
	            <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-top: 25px;">
	                <Strong>{{\Session::get('success')}}</Strong>
	                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                </button>
	            </div>
	        @endif
	        @if(\Session::has('failed'))
	            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-top: 25px;">
	                <Strong>{{\Session::get('failed')}}</Strong>
	                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                </button>
	            </div>
	        @endif
	        <div class="row" style="border: 0px solid black;margin:10px 0px 0px 0px;padding:0px;">
	        	<div class="col-12" style="padding: 0px 12px 5px 12px;">
	        		<span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> The Time of Class : 
                    @if(count($attendance)>0)
	        		<?php
                    $hour = explode(',',$attendance[0]->hour);
                    $s_hour = explode('-',$hour[0]);
                    $e_hour = explode('-',$hour[count($hour)-1]);
                    $last_hour = getFullTime($s_hour[0],$e_hour[1]);
                    $timetable_hour = $timetable->class_hour;
                    $explode_th = explode(',',$timetable_hour);
                    $sperate = explode(',',$last_hour);
                    $less_hour = 0;
                    if(count($sperate)>count($explode_th)){
                        $less_hour = count($explode_th)-count($sperate);
                    }
                    ?>
                        @if($fill_up==0)
                            @if($less_hour<0)
                            <b>{{$attendance[0]->weekly}} ( {{$s_hour[0]}} -  {{$e_hour[1]}} ) Minus On ({{$less_hour}} Hour)</b>
                            @else
                            <b>{{$attendance[0]->weekly}} ( {{$s_hour[0]}} -  {{$e_hour[1]}} )</b>
                            @endif
                        @else
                            <b>{{$attendance[0]->weekly}} ( {{$s_hour[0]}} -  {{$e_hour[1]}} ) Fill Up ({{$fill_up}} Hour)</b>
                        @endif
                    @else
                    <?php
                    $hour = explode(',',$timetable->class_hour);
                    $s_hour = explode('-',$hour[0]);
                    $e_hour = explode('-',$hour[count($hour)-1]);
                    ?>
                        @if($fill_up!=0)
                            <b>{{$timetable->week}} ( {{$s_hour[0]}} -  {{$e_hour[1]}} ) Fill Up ({{$fill_up}} Hour)</b>
                        @else
                            <b>{{$timetable->week}} ( {{$s_hour[0]}} -  {{$e_hour[1]}} )</b>
                        @endif
                    @endif
	        		</span>
	        	</div>
                <div class="col-12" style="padding: 0px 12px 5px 12px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i>
                	<span style="font-size: 17px;">
                		The Date : <b> {{$date}} </b>
                	</span>
            	</div>
                <div class="col-12" style="padding: 0px 12px 0px 12px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i>
                    <span style="font-size: 17px;">
                        The Week : <b> {{$week}} </b>
                    </span>
                </div>
            </div>
             <div class="details" style="padding: 5px 5px 0px 5px;">
             	@if(count($attendance)>0)
             	<form method="post" action="{{$character}}/Attendance/edit/" style="padding: 0px;margin: 0px;">
             		{{csrf_field()}}
             		<div style="overflow-x: auto;">
             		<table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);margin-top: 5px;" id="table" class="table table-hover">
	             		<thead>
	             			<tr style="background-color: #d9d9d9;text-align: center;">
	             				<th style="border-left:1px solid #e6e6e6;color:black;" width="15%"><b>Student ID</b></th>
	             				<th style="border-left:1px solid #e6e6e6;color:black;" width="30%"><b>Student Name</b></th>
	             				<th style="border-left:1px solid #e6e6e6;color:black;" width="10%"><b>Batch</b></th>
	             				<th style="border-left:1px solid #e6e6e6;color:black;"><b>Present</b></th>
	             				<th style="border-left:1px solid #e6e6e6;color:black;"><b>Absent</b></th>
	             				<th style="border-left:1px solid #e6e6e6;color:black;"><b>Late</b></th>
	             			</tr>
	             			</thead>
	             			<?php
	             			$student_list = "";
	             			$students_status = $attendance[0]->students_status;
	             			?>
	             			@foreach($assign_student as $row)
	             				<?php
		             				$pos = strpos($students_status, $row->student_id, 0);
		             				$status = substr($students_status, ($pos+9),1);
		             			?>
	             				<tr>
	             					<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;'>{{$row->student_id}} </td>
	             					<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;'>{{$row->name}}</td>
	             					<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;'>{{$row->batch}}</td>
	             					<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;'><input type="radio" name="attendance{{$row->student_id}}" value="Present" <?php if($status=="P"){ echo "checked";}?>> Present</td>
	             					<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;'><input type="radio" name="attendance{{$row->student_id}}" value="Absent" 
	             					<?php if($status=="A"){ echo "checked";}?>> Absent</td>
	             					<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;'><input type="radio" name="attendance{{$row->student_id}}" value="Late" 
	             					<?php if($status=="L"){ echo "checked";}?>> Late</td>
	             				</tr>
							<?php
	             			$student_list .= $row->student_id."/";
	             			?>
	             			@endforeach
	             			<input type="hidden" name="attendance_id" value="{{$attendance[0]->attendance_id}}">
	             			<input type="hidden" name="student_list" value="{{$student_list}}" class="student_list">
	             	</table>
	             	</div>
	             	<div class="form-group" style="text-align: right;margin: 0px!important;padding:0px 0px 10px 0px;">
	                    <input type="submit" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;margin: 0px!important;" value="Save Change">&nbsp;
	                </div>
             	</form>
             	@else
             	<form method="post" action="{{$character}}/Attendance/store/" style="padding: 0px;margin: 0px;">
             		{{csrf_field()}}
             	<div style="overflow-x: auto;">
             	<table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);margin-top: 5px;" id="table" class="table table-hover">
             		<thead>
             			<tr style="background-color: #d9d9d9;text-align: center;">
             				<th style="border-left:1px solid #e6e6e6;color:black;" width="15%"><b>Student ID</b></th>
             				<th style="border-left:1px solid #e6e6e6;color:black;" width="30%"><b>Student Name</b></th>
             				<th style="border-left:1px solid #e6e6e6;color:black;" width="10%"><b>Batch</b></th>
             				<th style="border-left:1px solid #e6e6e6;color:black;"><b><input type="radio" name="attendance" class="present"> Present</b></th>
             				<th style="border-left:1px solid #e6e6e6;color:black;"><b>Absent</b></th>
             				<th style="border-left:1px solid #e6e6e6;color:black;"><b>Late</b></th>
             			</tr>
             			</thead>
             			<?php
             			$student_list = "";
             			?>
             			@foreach($assign_student as $row)
             				<tr>
             					<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;'>{{$row->student_id}} </td>
             					<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;'>{{$row->name}}</td>
             					<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;'>{{$row->batch}}</td>
             					<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;'><input type="radio" name="attendance{{$row->student_id}}" value="Present"> Present</td>
             					<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;'><input type="radio" name="attendance{{$row->student_id}}" value="Absent" checked> Absent</td>
             					<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;'><input type="radio" name="attendance{{$row->student_id}}" value="Late"> Late</td>
             				</tr>
             				<?php
             				$student_list .= $row->student_id."/";
             				?>
             			@endforeach
             			<input type="hidden" name="student_list" value="{{$student_list}}" class="student_list">
             			<input type="hidden" name="tt_id"  value="{{$tt_id}}">
             			<input type="hidden" name="date"   value="{{$date}}">
                        <input type="hidden" name="week"   value="{{$week}}">
                        <input type="hidden" name="week"   value="{{$week}}">
                        <input type="hidden" name="weekly" value="{{$timetable->week}}">
                        <input type="hidden" name="fill_up" value="{{$fill_up}}">
                        <input type="hidden" name="hour"   value="{{$s_hour[0]}}-{{$e_hour[1]}}">
             	</table>
             	</div>
             	<div class="form-group" style="text-align: right;margin: 0px!important;padding:0px 0px 10px 0px;">
                    <input type="submit" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;margin: 0px!important;" value="Save Change">&nbsp;
                </div>
             	</form>
             	@endif
             </div>
        </div>
    </div>
</div>
@endsection