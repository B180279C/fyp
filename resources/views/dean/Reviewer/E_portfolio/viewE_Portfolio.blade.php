<?php
$title = "Reviewer";
$option4 = "id='selected-sidebar'";
?>
@extends('layouts.layout')

@section('content')

<script type="text/javascript">
function w3_open() {
    document.getElementById("action_sidebar").style.display = "block";
    document.getElementById("button_open").style.display = "none";
}
function w3_close() {
    document.getElementById("action_sidebar").style.display = "none";
    document.getElementById("button_open").style.display = "block";
}
$(document).ready(function() {
	// var quill_editor = new Quill('#remarks', {
	// 	theme: 'snow'
	// });
});
</script>
<style type="text/css">
.editor{
  height: 100px;
  display: block;
}
.th{
	border-left:1px solid #e6e6e6;
	color:black!important;
	border-bottom: 1px solid #d9d9d9;
	text-align: center;
	font-weight: bold!important;
}
.td{
	border-left:1px solid #d9d9d9;
	border-bottom: 1px solid #d9d9d9;
	text-align: left;
}
</style>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->semester_name}} : {{$course[0]->short_form_name}} / {{$course[0]->subject_code}} {{$course[0]->subject_name}} ( {{$course[0]->name}} )</p>
        <p class="pass_page">
            <a href="{{$character}}/home" class="first_page"> Home </a>/
            <a href="{{$character}}/Reviewer">Dean </a>/
            <a href="{{$character}}/Reviewer/course/{{$course[0]->course_id}}">{{$course[0]->semester_name}} : {{$course[0]->short_form_name}} / {{$course[0]->subject_code}} {{$course[0]->subject_name}} ( {{$course[0]->name}} )</a>/
            <span class="now_page">E - Portfolio</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 0px 10px;">
        <div class="col-md-12">
            <p class="page_title">E - Portfolio</p>
            <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                <div id="action_sidebar" class="w3-animate-right" style="display: none">
                    <div style="text-align: right;padding:10px;">
                        <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                  <ul class="sidebar-action-ul">
                      <p class="title_method">Download</p>
                      <a href='{{$character}}/Reviewer/E_Portfolio/report/{{$course[0]->course_id}}'><li class="sidebar-action-li"><i class="fa fa-download" style="padding: 0px 10px;" aria-hidden="true"></i>E-Portfolio Report</li></a>
                  </ul>
            </div>
            <br>
            <br>
            <div class="details" style="padding: 0px 5px 0px 5px;">
            	<div style="overflow-x: auto;padding:0px 5px 5px 5px;margin-top: -5px;">
            		<table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);padding: 0px;" id="table" class="table">
              			<thead>
              				<tr style="background-color: #d9d9d9;">
              					<th rowspan="2" width="20%" class="th" style="border-bottom:1px solid #d9d9d9;"><b>Folder Name</b></th>
              					<th rowspan="2" width="30%" class="th" style="border-bottom:1px solid #d9d9d9;"><b>Documents</b></th>
              					<th colspan="3" class="th" style="border-bottom:1px solid white;"><center><b>Softcopy</b></center></th>
              				</tr>
              				<tr style="background-color: #d9d9d9;">
              					<th class="th" style="border-bottom:1px solid #d9d9d9;">Course Coordinator</th>
              					<th class="th" style="border-bottom:1px solid #d9d9d9;">Moderator</th>
              					<th class="th" style="border-bottom:1px solid #d9d9d9;">Audit Commitee</th>
              				</tr>
              			</thead>
              			<tbody>
              				<tr>
              					<td rowspan="9" class="td" style="border-bottom: 1px solid grey;"><b>1. Course Information</b></td>
              					<td class="td">a) Syllabus</td>
              					<td class="td">
              						@if($syllabus!="")
              							<center><i class="fa fa-check correct"></i></center>
              						@endif
              					</td>
              					<td class="td"></td>
              					<td class="td"></td>
              				</tr>
              				<tr>
              					<td class="td">b) Teaching Plan</td>
              					<td class="td">
              						@if(count($action)>0)
              						<center><i class="fa fa-check correct"></i></center>
              						@endif
              					</td>
              					<td class="td"></td>
              					<td class="td"></td>
              				</tr>
              				<tr>
              					<td class="td">c) Internal Moderation of Continuous Assessment</td>
              					<td class="td">
              						@if(count($ca_action)>0)
              						<center><i class="fa fa-check correct"></i></center>
              						@endif
              					</td>
              					<td class="td"></td>
              					<td class="td"></td>
              				</tr>
              				<tr>
              					<td class="td">d) Internal Moderation of Final Examination Paper</td>
              					<td class="td">
              						@if(count($fa_action)>0)
              						<center><i class="fa fa-check correct"></i></center>
              						@endif
              					</td>
              					<td class="td"></td>
              					<td class="td"></td>
              				</tr>
              				<tr>
              					<td class="td">e) External Moderation of Final Examination Paper ( if application )</td>
              					<td class="td"></td>
              					<td class="td"></td>
              					<td class="td"></td>
              				</tr>
              				<tr>
              					<td class="td">f) CA Marks with Excel Format (by programme)</td>
              					<td class="td"></td>
              					<td class="td"></td>
              					<td class="td"></td>
              				</tr>
              				<tr>
              					<td class="td">g) Final Marks with Excel Format (by programme)</td>
              					<td class="td"></td>
              					<td class="td"></td>
              					<td class="td"></td>
              				</tr>
              				<tr>
                        <td class="td">h) Timetable</td>
                        <td class="td">
                          @if(count($timetable)>0)
                          <center><i class="fa fa-check correct"></i></center>
                          @endif     
                        </td>
                        <td class="td"></td>
                        <td class="td"></td>
                      </tr>
                      <tr>
                        <td class="td" style="border-bottom: 1px solid grey;">i) Attendance</td>
                        <td class="td" style="border-bottom: 1px solid grey;">
                          @if(round($attendance)>80)
                          <center><i class="fa fa-check correct"></i></center>
                          @endif     
                        </td>
                        <td class="td" style="border-bottom: 1px solid grey;"></td>
                        <td class="td" style="border-bottom: 1px solid grey;"></td>
                      </tr>
              				<tr>
              					<td class="td" style="border-bottom: 1px solid grey;"><b>2. Teaching Material</b></td>
              					<td class="td" style="border-bottom: 1px solid grey;">Lecture Slides, documents and other teaching materials</td>
              					<td class="td" style="border-bottom: 1px solid grey;">
              						@if(count($lecture_note)>0)
              						<center><i class="fa fa-check correct"></i></center>
              						@endif
              					</td>
              					<td class="td" style="border-bottom: 1px solid grey;"></td>
              					<td class="td" style="border-bottom: 1px solid grey;"></td>
              				</tr>
              				<?php
              				$num = 3;
              				?>
              				@foreach($assessments as $row)
              					<tr>
              						<td rowspan="3" class="td" style="border-bottom: 1px solid grey;">
              							<b>{{$num}}. {{$row->assessment_name}}</b>
              						</td>
              						<td class="td">a) Moderated Question(s)</td>
              						<td class="td">
              							<?php
              								$question = 0;
              							?>
              							@foreach($assessment_list as $row_list)
              								@if($row->ass_id == $row_list->ass_id)
              									@if($row_list->ass_type=="Question")
              										<?php
              											$question++;
              										?>
              									@endif
              								@endif
              							@endforeach
              							@if($question>0)
              								<center><i class="fa fa-check correct"></i></center>
              							@endif
              						</td>
	              					<td class="td"></td>
	              					<td class="td"></td>
              					</tr>
              					<tr>
              						<td class="td">b) Moderated Marking Scheme(s) / Solution(s)</td>
              						<td class="td">
              							<?php
              								$solution = 0;
              							?>
              							@foreach($assessment_list as $row_list)
              								@if($row->ass_id == $row_list->ass_id)
              									@if($row_list->ass_type=="Solution")
              										<?php
              											$solution++;
              										?>
              									@endif
              								@endif
              							@endforeach
              							@if($solution>0)
              								<center><i class="fa fa-check correct"></i></center>
              							@endif</td>
	              					<td class="td"></td>
	              					<td class="td"></td>
              					</tr>
              					<tr>
              						<td class="td" style="border-bottom: 1px solid grey;">c) Samples ( 9 samples : 3 Good; 3 Average; 3 poor )</td>
              						<?php
              							$result = 0;
              						?>
              						<td class="td" style="border-bottom: 1px solid grey;">
              							@foreach($lecturer_result as $row_result)
              								@if($row->ass_id == $row_result->ass_id)
              									<?php
              									$result++;
              									?>
              								@endif
              							@endforeach
              							@if($result>=9)
              								<center><i class="fa fa-check correct"></i></center>
              							@endif
              						</td>
	              					<td class="td" style="border-bottom: 1px solid grey;"></td>
	              					<td class="td" style="border-bottom: 1px solid grey;"></td>
              					</tr>
              					<?php
              					$num++;
              					?>
              				@endforeach
              				@if(count($ass_final)>0)
              					<tr>
              						<td rowspan="4" class="td" style="border-bottom: 1px solid grey;">
              							<b>{{$num}}. Final Exam</b>
              						</td>
              						<td class="td">a) Moderated Examination Paper</td>
              						<td class="td">
              							<?php
              								$f_q = 0;
              							?>
              							@if(count($assessment_final)>0)
              								@foreach($assessment_final as $row_final)
              									@if($row_final->ass_fx_type=="Question")
              										<?php
              											$f_q++;
              										?>
              									@endif
              								@endforeach
              								@if($f_q>0)
	              								<center><i class="fa fa-check correct"></i></center>
	              							@endif
              							@endif
              						</td>
	              					<td class="td"></td>
	              					<td class="td"></td>
              					</tr>
              					<tr>
              						<td class="td">b) Moderated Examination Paper Marking Scheme</td>
              						<td class="td">
              							<?php
              								$f_s = 0;
              							?>
              							@if(count($assessment_final)>0)
              								@foreach($assessment_final as $row_final)
              									@if($row_final->ass_fx_type=="Solution")
              										<?php
              											$f_s++;
              										?>
              									@endif
              								@endforeach
              								@if($f_s>0)
	              								<center><i class="fa fa-check correct"></i></center>
	              							@endif
              							@endif
              						</td>
	              					<td class="td"></td>
	              					<td class="td"></td>
              					</tr>
              					<tr>
              						<td class="td">c) Final Exam Scripts Moderation for Course File Form</td>
              						<td class="td">
              							@if(count($fa_action)>0)
		              						<center><i class="fa fa-check correct"></i></center>
		              					@endif
              						</td>
	              					<td class="td"></td>
	              					<td class="td"></td>
              					</tr>
              					<tr>
              						<td class="td" style="border-bottom: 1px solid grey;">d) Samples ( 9 samples : 3 Good; 3 Average; 3 poor )</td>
              						<td class="td" style="border-bottom: 1px solid grey;">
              							@if(count($lecturer_fx_result)>=9)
              								<center><i class="fa fa-check correct"></i></center>
              							@endif
              						</td>
	              					<td class="td" style="border-bottom: 1px solid grey;"></td>
	              					<td class="td" style="border-bottom: 1px solid grey;"></td>
              					</tr>
              					<?php
              					$num++;
              					?>
              					@endif
              					<tr>
              						<td class="td" style="border-bottom: 1px solid grey;">
              							<b>{{$num}}. Course Review Report</b>
              						</td>
              						<td class="td" style="border-bottom: 1px solid grey;">
              							a) Completed Course Review Report
              						</td>
              						<td class="td" style="border-bottom: 1px solid grey;"></td>
	              					<td class="td" style="border-bottom: 1px solid grey;"></td>
	              					<td class="td" style="border-bottom: 1px solid grey;"></td>
              					</tr>
              			</tbody>
              		</table>
            	</div>
            	<!-- <hr style="margin:6px 5px 0px 5px;background-color:black;"> -->
            	<!-- <div class="row" style="height: auto;margin: 5px -10px 0px -10px;">
                  <form id="myForm" method="post" action="{{action('Dean\Dean\D_AssessmentController@D_Ass_Verify_Action')}}" style="width: 100%;margin: 0px;">
                      {{csrf_field()}}
                      <input type="hidden" name="verify" id="verify">
                      <input type="hidden" name="course_id" value="{{$course[0]->course_id}}">
                      <input type="hidden" name="result" id="result">
                      <div class="col-12" style="border: 0px solid black;margin: 0px;padding: 0px 20px;font-size: 16px;">
                          Remarks : 
                      </div>
                      <div class="col-12" style="border: 0px solid black;margin-top:5px;margin-bottom: 0px;">
                          <div id="remarks" class="editor">
                          </div>
                          <textarea style="display:none" id="remarks_data" name="remarks"></textarea>
                      </div>
                  </form>
                  <div class="col-12" style="text-align: right;margin: 0px!important;padding-top: 10px;padding-right: 10px;">
                      <input type="button" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;margin: 0px!important;" value="Submit to ( HOD ) For Verify" onclick="Submit_Action('Verify')">&nbsp;
                  </div>
                </div> -->
            </div>
        </div>
    </div>
</div>
@endsection
