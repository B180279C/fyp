<?php
$title = "Course";
$option1 = "id='selected-sidebar'";
?>
@extends('layouts.layout')

@section('content')
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</p>
        <p class="pass_page">
            <a href="{{$character}}/home" class="first_page"> Home </a>/
            <a href="{{$character}}/course_list">Courses </a>/
            <a href="{{$character}}/course/action/{{$course[0]->course_id}}">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</a>/
            <span class="now_page">Timetable</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 5px 10px;">
        <div class="col-md-12">
             <p class="page_title">Timetable</p>
             <div class="details" style="padding: 10px 5px 0px 5px;">
             	<?php
             	for($w=1;$w<=7;$w++){
             		for($i=1;$i<=15;$i++){
             			${$w.'hour'.$i} = "";
             			${$w.'or'.$i} = "";
	             		${'time'.$i} = (600+($i*100));
	             		${'end'.$i} = (700+($i*100));
	             		if(${'time'.$i}<1000){
	             			${'time'.$i} = "0".${'time'.$i};
	             		}
	             		if(${'end'.$i}<1000){
	             			${'end'.$i} = "0".${'end'.$i};
	             		}
	             	}
             	}
             	// echo $time5;
             	foreach($timetable as $row){
             		$week = $row->week;
             		$forh = $row->F_or_H;
             		$class_hour = explode(',',$row->class_hour);
             		for($t=0;$t<=(count($class_hour)-1);$t++){
             			$hour = explode('-',$class_hour[$t]);
             			$s_hour = $hour[0];
             			$e_hour = $hour[1];
             			for($w=1;$w<=7;$w++){
             				if($week=="Monday"&&$w==1){
             					for($i=1;$i<=15;$i++){
		             				if($s_hour==${'time'.$i}){
		             					${$w.'hour'.$i} = $s_hour."/".$forh;
		             				}
		             			}
             				}
             				if($week=="Tuesday"&&$w==2){
             					for($i=1;$i<=15;$i++){
		             				if($s_hour==${'time'.$i}){
		             					${$w.'hour'.$i} = $s_hour."/".$forh;
		             				}
		             			}
             				}
             				if($week=="Wednesday"&&$w==3){
             					for($i=1;$i<=15;$i++){
		             				if($s_hour==${'time'.$i}){
		             					${$w.'hour'.$i} = $s_hour."/".$forh;
		             				}
		             			}
             				}
             				if($week=="Thursday"&&$w==4){
             					for($i=1;$i<=15;$i++){
		             				if($s_hour==${'time'.$i}){
		             					${$w.'hour'.$i} = $s_hour."/".$forh;
		             				}
		             			}
             				}
             				if($week=="Friday"&&$w==5){
             					for($i=1;$i<=15;$i++){
		             				if($s_hour==${'time'.$i}){
		             					${$w.'hour'.$i} = $s_hour."/".$forh;
		             				}
		             			}
             				}
             				if($week=="Saturday"&&$w==6){
             					for($i=1;$i<=15;$i++){
		             				if($s_hour==${'time'.$i}){
		             					${$w.'hour'.$i} = $s_hour."/".$forh;
		             				}
		             			}
             				}
             				if($week=="Sunday"&&$w==7){
             					for($i=1;$i<=15;$i++){
		             				if($s_hour==${'time'.$i}){
		             					${$w.'hour'.$i} = $s_hour."/".$forh;
		             				}
		             			}
             				}
	             		}
             		}
             	}
				?>
				<table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);" id="table" class="table table-hover">
					<thead>
						<tr style="background-color: #d9d9d9;text-align: center;">
							<th style="border-left:1px solid #e6e6e6;color:black;"><b>Time</b></th>
							<th style="border-left:1px solid #e6e6e6;color:black;" width="13.5%"><b>Monday</b></th>
							<th style="border-left:1px solid #e6e6e6;color:black;" width="13.5%"><b>Tuesday</b></th>
							<th style="border-left:1px solid #e6e6e6;color:black;" width="13.5%"><b>Wednesday</b></th>
							<th style="border-left:1px solid #e6e6e6;color:black;" width="13.5%"><b>Thursday</b></th>
							<th style="border-left:1px solid #e6e6e6;color:black;" width="13.5%"><b>Friday</b></th>
							<th style="border-left:1px solid #e6e6e6;color:black;" width="13.5%"><b>Saturday</b></th>
							<th style="border-left:1px solid #e6e6e6;color:black;" width="13.5%"><b>Sunday</b></th>
						</tr>
					</thead>
					@for($i=1;$i<=15;$i++)
						<tr>
							<td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;font-size: 12px;vertical-align: middle;"><b>{{(${'time'.$i}).' / '.(${'end'.$i})}}</b></td>
	             		@for($w=1;$w<=7;$w++)
	             			@if(${$w.'hour'.$i}!="")
	             			<?php
	             				$or = explode('/',${$w.'hour'.$i});
	             			?>
							<td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;font-size: 12px;">
								<b>{{$course[0]->subject_code}}
								<br/>{{$course[0]->subject_name}}</b>
								<br/>( {{$course[0]->name}} )
								@if($or[1]=="Half")
	             				<br/>
	             				<span style="color: red">Odd / Even Week</span>
	             				@endif
							</td> 
							@else
							<td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;"></td>           	
							@endif		
		             	@endfor
		             	</tr>
		            @endfor
				</table>
             </div>
        </div>
    </div>
</div>
@endsection