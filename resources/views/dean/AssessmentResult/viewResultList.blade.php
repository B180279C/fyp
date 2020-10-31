<?php
$title = "Course";
$option1 = "id='selected-sidebar'";
?>
@extends('layouts.nav_dean')

@section('content')
<style type="text/css">
#show_image_link:hover{
    text-decoration: none;
}
@media only screen and (max-width: 600px) {
  #course_name{
    padding-top: 0px;
  }
  #course_list{
    margin-left: 0px;
    padding: 4px 15px;
  }
  #course_action_two{
    padding: 10px 0px 0px 0px;
    position: relative;
    right: -24px;
    text-align: right;
  }
  #file_name{
    width: 210px;
    margin: 0px;
    padding:0px;
  }
  .show_count{
    display: none;
  }
}
@media only screen and (min-width: 600px) {
    #course_list{
      margin-left: 0px;
      padding: 4px 15px;
    }
    #course_name{
        margin-left:-20px;
        padding-top:0px;
    }
    #course_action_two{
      text-align: right;
      margin-left: 5px;
      padding: 8px 0px 0px 25px;
    }
    .show_count{
      display: block;
    }
}
</style>
<script type="text/javascript">
function w3_open() {
  document.getElementById("action_sidebar").style.display = "block";
  document.getElementById("button_open").style.display = "none";
}
function w3_close() {
  document.getElementById("action_sidebar").style.display = "none";
  document.getElementById("button_open").style.display = "block";
}
$(document).ready(function(){
  	$.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
        $(document).on('click', '.l_plus', function(){
            $('#lecturer').slideToggle("slow", function(){
                if($('#lecturer').is(":visible")){
                    $('#icon_l').removeClass('fa fa-plus');
                    $('#icon_l').addClass('fa fa-minus');
                }else{
                    $('#icon_l').removeClass('fa fa-minus');
                    $('#icon_l').addClass('fa fa-plus');
                }
            });
        });
        $(document).on('click', '.s_plus', function(){
            $('#student').slideToggle("slow", function(){
                if($('#student').is(":visible")){
                    $('#icon_s').removeClass('fa fa-plus');
                    $('#icon_s').addClass('fa fa-minus');
                }else{
                    $('#icon_s').removeClass('fa fa-minus');
                    $('#icon_s').addClass('fa fa-plus');
                }
            });
        });

    $(document).on('click', '.download_button', function(){
          var id = $(this).attr("id");
          var num = id.split("_");
          window.location = "/AssessmentResult/result/"+num[2];
    });

    $(document).on('click', '.remove_button', function(){
        var id = $(this).attr("id");
        var num = id.split("_");
        if(confirm('Are you sure you want to remove the it?')) {
          window.location = "/AssessmentResultStudent/remove/"+num[2];
        }
        return false;
    });
  });
</script>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</p>
        <p class="pass_page">
            <a href="/AssessmentResult/studentResult/{{$assessment_result->ass_rs_id}}/" class="first_page">Back</a>/
            <span class="now_page">{{$assessment_result_student->student_id}} ( {{$assessment_result->submission_name}} )</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 10px 10px;">
        <div class="col-md-12">
            <p class="page_title">{{$assessment_result->submission_name}} ( {{$assessment_result_student->student_id}} )</p>
            @if((count($lecturer_result)!=0)||(count($student_result)!=0))
            <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                <div id="action_sidebar" class="w3-animate-right" style="display: none;width: 250px;">
                    <div style="text-align: right;padding:10px;">
                        <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                    <ul class="sidebar-action-ul">
                        <a href='/AssessmentResult/Student/{{$assessment_result_student->student_id}}/download/zipFiles/{{$assessment_result->ass_rs_id}}'><li class="sidebar-action-li"><i class="fa fa-download" style="padding: 0px 10px;" aria-hidden="true"></i>Download All Result</li></a>
                    </ul>
                </div>
            @endif
            <div class="details" style="padding: 0px 5px 5px 5px;">
              <div class="row" style="margin-top: 20px;">
              	@if(count($lecturer_result)>0)
              	<div class="row col-md-12 l_plus" style="border:0px solid black;margin: 0px 0px 12px 0px;padding:0px;font-size: 20px;">
    			        <div class="col-md-3" style="padding-left: 18px;border:0px solid black" class="col-md-12">
    			           	Submitted By Lecturer (<i class="fa fa-minus" aria-hidden="true" id="icon_l" style="color: #0d2f81;position: relative;top: 2px;"></i>)
    			        </div>
    			        <div class="col-9 show_count" style="border:0px solid black;">
    			            <hr style="display: inline-block; background-color: #cccccc;width: 94%;margin:0px;position: relative;top: -5px;">
    			           	<span style="display: inline-block;border:0px solid black;text-align: right;width:5%;"> ( {{count($lecturer_result)}} ) </span>
    			        </div>
    		        </div>
				  <div class="row col-md-12" id="lecturer" style="margin:0px;padding: 0px;">
		        @foreach($lecturer_result as $row)
		        <?php
              $ext = "";
              if($row->document!=""){
                $ext = explode(".", $row->document);
              }
            ?>
            @if(($ext[1] == "pdf")||($ext[1] == "docx")||($ext[1] == "xlsx")||($ext[1] == "pptx"))
		              <div class="row col-12 align-self-center" id="course_list">
                    <a href="{{ action('AssessmentResultController@downloadDocument',$row->ar_stu_id) }}" class="col-8 row align-self-center" id="show_image_link">
                      <div class="col-12 row" style="padding:10px;color:#0d2f81;">
                        <div class="col-1" style="position: relative;top: -2px;">
                          @if($ext[1]=="pdf")
                            <img src="{{url('image/pdf.png')}}" width="25px" height="25px"/>
                          @elseif($ext[1]=="docx")
                            <img src="{{url('image/docs.png')}}" width="25px" height="25px"/>
                          @elseif($ext[1]=="xlsx")
                            <img src="{{url('image/excel.png')}}" width="25px" height="25px"/>
                          @elseif($ext[1]=="pptx")
                            <img src="{{url('image/pptx.png')}}" width="25px" height="25px"/>
                          @endif 
                        </div>
                        <div class="col-10" id="course_name">
                          <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>{{$row->document_name}}</b></p>
                        </div>
                      </div>
                    </a>
                    <div class="col-4" id="course_action_two">
                      <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_{{$row->ar_stu_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>
                    </div>
                  </div>
            @else
                <div class="col-12 row align-self-center" id="course_list">
                  <a href="/images/AssessmentResult/{{$row->document}}" data-toggle="lightbox" data-gallery="example-gallery" class="col-8 row align-self-center" id="show_image_link" data-title="{{$row->document_name}} <br> <a href='/AssessmentResult/view/whole_paper/{{$row->ar_stu_id}}' class='full_question' target='_blank'>Whole paper</a>">
                    <div class="col-12 row" style="padding:10px;color:#0d2f81;">
                      <div class="col-1" style="position: relative;top: -2px;">
                        <img src="{{url('image/img_icon.png')}}" width="25px" height="20px"/>
                      </div>
                      <div class="col-10" id="course_name">
                        <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>{{$row->document_name}}</b></p>
                      </div>
                    </div>
                  </a>
                  <div class="col-4" id="course_action_two">
                    <i class="fa fa-download download_button" aria-hidden="true" id="download_button_{{$row->ar_stu_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>&nbsp;
                    <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_{{$row->ar_stu_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>
                  </div>
                </div>
            @endif
		        @endforeach
		    	</div>
        @endif

		    	@if(count($student_result)>0)
		    	<div class="row col-md-12 s_plus" style="border:0px solid black;margin: 18px 0px 10px 0px;padding:0px;font-size: 20px;">
			        <div class="col-md-3" style="padding-left: 18px;border:0px solid black" class="col-md-12">
			           	Submitted By Student (<i class="fa fa-minus" aria-hidden="true" id="icon_s" style="color: #0d2f81;position: relative;top: 2px;"></i>)
			        </div>
			        <div class="col-9 show_count" style="border:0px solid black;">
			            <hr style="display: inline-block; background-color: #cccccc;width: 94%;margin:0px;position: relative;top: -5px;">
			           	<span style="display: inline-block;border:0px solid black;text-align: right;width:5%;"> ( {{count($student_result)}} ) </span>
			        </div>
		        </div>
				<div class="row col-md-12" id="student" style="margin:0px;padding: 0px;">
		        @foreach($student_result as $sow)
		        <?php
                    $ext = "";
                   	if($sow->document!=""){
                        $ext = explode(".", $sow->document);
                    }
                ?>
            @if(($ext[1] == "pdf")||($ext[1] == "docx")||($ext[1] == "xlsx")||($ext[1] == "pptx"))
		        <div class="row col-12 align-self-center" id="course_list">
                    <a href='' class="col-8 row align-self-center" id="show_image_link">
                      <div class="col-12 row" style="padding:10px;color:#0d2f81;">
                        <div class="col-1" style="position: relative;top: -2px;">
                          @if($ext[1]=="pdf")
                            <img src="{{url('image/pdf.png')}}" width="25px" height="25px"/>
                          @elseif($ext[1]=="docx")
                            <img src="{{url('image/docs.png')}}" width="25px" height="25px"/>
                          @elseif($ext[1]=="xlsx")
                            <img src="{{url('image/excel.png')}}" width="25px" height="25px"/>
                          @elseif($ext[1]=="pptx")
                            <img src="{{url('image/pptx.png')}}" width="25px" height="25px"/>
                          @endif 
                        </div>
                        <div class="col-10" id="course_name">
                          <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>{{$sow->document_name}}</b></p>
                        </div>
                      </div>
                    </a>
                    <div class="col-4" id="course_action_two">
                      <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_{{$sow->ar_stu_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>
                    </div>
                 </div>
            @else
                <div class="col-12 row align-self-center" id="course_list">
                  <a href="/images/AssessmentResult/{{$sow->document}}" data-toggle="lightbox" data-gallery="example-gallery_student" class="col-8 row align-self-center" id="show_image_link" data-title="{{$sow->document_name}} <br> <a href='/AssessmentResult/view/whole_paper/{{$sow->ar_stu_id}}' class='full_question' target='_blank'>Whole paper</a>">
                    <div class="col-12 row" style="padding:10px;color:#0d2f81;">
                      <div class="col-1" style="position: relative;top: -2px;">
                        <img src="{{url('image/img_icon.png')}}" width="25px" height="20px"/>
                      </div>
                      <div class="col-10" id="course_name">
                        <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>{{$sow->document_name}}</b></p>
                      </div>
                    </div>
                  </a>
                  <div class="col-4" id="course_action_two">
                    <i class="fa fa-download download_button" aria-hidden="true" id="download_button_{{$sow->ar_stu_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>&nbsp;
                    <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_{{$sow->ar_stu_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>
                  </div>
                </div>
            @endif
		        @endforeach
		    	</div>
           @endif
              </div>
            </div>
        </div>
    </div>
</div>
@endsection