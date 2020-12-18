<?php
$title = "CoursePortFolio";
$option5 = "id='selected-sidebar'";
?>
@extends('layouts.layout')

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

.checkbox_group_style{
  border:0px solid black;
  padding: 1px 10px 0px 20px!important;
  margin: 0px!important;
}
.checkbox_style{
  border:0px solid black;
  padding: 0px 10px 0px 20px!important;
  margin: 0px!important;
  display: inline;
  width: 28px;
}
.group{
  margin-top:3px;
  padding-left: 15px;
  border:0px solid black;
  display: inline;
  padding: 0px!important;
  margin: 0px!important;
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
  $('[data-toggle="lightbox"]').click(function(event) {
             event.preventDefault();
                 $(this).ekkoLightbox({
                   type: 'image',
                   onContentLoaded: function() {
                     var container = $('.ekko-lightbox-container');
                     var content = $('.modal-content');
                     var backdrop = $('.modal-backdrop');
                     var overlay = $('.ekko-lightbox-nav-overlay');
                     var modal = $('.modal');
                     var image = container.find('img');
                     var windowHeight = $(window).height();
                     var dialog = container.parents('.modal-dialog');
                     var data_header = $('.modal-header');
                     var data_title = $('.modal-title');
                     var body = $('.modal-body');
                     console.log(image.width());

                     if((image.width() > 380) && (image.width() < 441)){
                        dialog.css('max-width','700px');
                        image.css('height','900px');
                        image.css('width','700px');
                        overlay.css('height','900px');
                     }else{
                        overlay.css('height','100%');
                     }
                     // backdrop.css('opacity','1');
                     data_header.css('background-color','white');
                     data_header.css('padding','10px');
                     data_header.css('margin','0px 24px');
                     data_header.css('border-bottom','1px solid black');
                     data_title.css('font-size','18px');

                     body.css('padding-top','0px');
                     content.css('background', "none");
                     content.css('-webkit-box-shadow', "0 5px 15px rgba(0,0,0,0)");
                     content.css('-moz-box-shadow', "0 5px 15px rgba(0,0,0,0)");
                     content.css('-o-box-shadow', "0 5px 15px rgba(0,0,0,0)");
                     content.css('box-shadow', "0 5px 15px rgba(0,0,0,0)");
                   }
                 });
        });
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
          var original_id = $('#original_id').val();
          window.location = "{{$character}}/CourseList/PastYear/FinalSampleResult/download/"+original_id+"-"+num[2];
    });
  });

$(document).ready(function(){
    $('.group_checkbox').click(function(){
      var id = $(this).attr("id");
      var type = id.split("group_");

      if($(this).prop("checked") == true){
        $('.group_'+type[1]).prop("checked", true);
      }
      else if($(this).prop("checked") == false){
        $('.group_'+type[1]).prop("checked", false);
      }
    });
  });

$(document).on('click', '#checkDownloadAction', function(){
  var checkedValue = ""; 
  var inputElements = document.getElementsByClassName('group_download');
  for(var i=0; inputElements[i]; i++){
    if(inputElements[i].checked){
      checkedValue += inputElements[i].value+"_";
    }
  }
  if(checkedValue!=""){
    var course_id = $('#course_id').val();
    var student_id = $('#student_id').val();
    var id = course_id+"_"+checkedValue;
    window.location = "{{$character}}/PastYear/FinalSampleResult/Student/"+student_id+"/download/zipFiles/"+id+"/checked";
  }else{
    alert("Please select the document first.");
  }
});
</script>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->subject_code}} {{$course[0]->subject_name}}</p>
        <p class="pass_page">
            <a href="{{$character}}/CourseList/action/{{$id}}" class="first_page">Past Year</a>/
            <a href="{{$character}}/CourseList/PastYear/FinalAssessment/{{$id}}">Final Assessment</a>/
            <a href="{{$character}}/CourseList/PastYear/FinalSampleResult/{{$id}}/previous/{{$course[0]->course_id}}/All">{{$course[0]->semester_name}}</a>/
            <span class="now_page">{{$assessment_final_result->student_id}}</span>
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 5px 10px;">
        <div class="col-md-12">
            <p class="page_title">{{$assessment_final_result->student_id}}</p>
            @if((count($lecturer_result)!=0)||(count($student_result)!=0))
            <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                <div id="action_sidebar" class="w3-animate-right" style="display: none;width: 250px;">
                    <div style="text-align: right;padding:10px;">
                        <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                    <ul class="sidebar-action-ul">
                      <p class="title_method">Download</p>
                      <input type="hidden" id="course_id" value="{{$course[0]->course_id}}">
                      <input type="hidden" id="student_id" value="{{$assessment_final_result->student_id}}">
                      <input type="hidden" id="original_id" value="{{$id}}">
                        <a id="checkDownloadAction"><li class="sidebar-action-li"><i class="fa fa-check-square-o" style="padding: 0px 10px;" aria-hidden="true"></i>Checked Item</li></a>
                        <a href='{{$character}}/PastYear/FinalSampleResult/Student/{{$assessment_final_result->student_id}}/download/zipFiles/{{$course[0]->course_id}}/All'><li class="sidebar-action-li"><i class="fa fa-download" style="padding: 0px 10px;" aria-hidden="true"></i>All Result</li></a>
                    </ul>
                </div>
            @endif
           @if(\Session::has('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-top:20px;margin-bottom: 0px;">
                  <Strong>{{\Session::get('success')}}</Strong>
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
            @endif
            <div class="details" style="padding: 5px 5px 0px 5px;">
              <div class="row" style="margin-top: 15px;">
                @if(count($lecturer_result)>0)
                <div class="col-12 row" style="padding: 0px;margin: 0px;">
                    <div class="checkbox_group_style align-self-center">
                      <input type="checkbox" name="group_lecturer" id='group_lecturer' class="group_checkbox">
                    </div>
                    <div class="l_plus row col" style="border:0px solid black;margin: 0px;padding:0px;font-size: 20px;">
                      <div class="col-md-3 row" style="padding-left: 18px;border:0px solid black;margin: 0px;padding:0px;">
                        Submitted By Lecturer (<i class="fa fa-minus" aria-hidden="true" id="icon_l" style="color: #0d2f81;position: relative;top: 7px;"></i>)
                      </div>
                      <div class="col-9 show_count" style="border:0px solid black;">
                        <hr style="display: inline-block; background-color: #cccccc;width: 94%;margin:0px;position: relative;top: -5px;">
                        <span style="display: inline-block;border:0px solid black;text-align: right;width:5%;"> ( {{count($lecturer_result)}} ) </span>
                      </div>
                    </div>
                  </div>
          <div class="row col-md-12" id="lecturer" style="margin:0px;padding: 10px  0px 10px 0px;border-bottom:1px solid black;">
            @foreach($lecturer_result as $row)
            <?php
              $ext = "";
              if($row->document!=""){
                $ext = explode(".", $row->document);
              }
            ?>
            @if(($ext[1] == "pdf")||($ext[1] == "docx")||($ext[1] == "xlsx")||($ext[1] == "pptx"))
                  <div class="col-12 row align-self-center" id="course_list">
                    <div class="col-9 row align-self-center">
                      <div class="checkbox_style align-self-center">
                        <input type="checkbox" value="{{$row->fxr_id}}" class="group_lecturer group_download">
                      </div>
                      <a href="{{$character}}/CourseList/PastYear/FinalSampleResult/download/{{$id}}-{{$row->fxr_id}}" class="col-11 row" style="padding:10px 0px;margin-left:5px;color:#0d2f81;border:0px solid black;" id="show_image_link">
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
                      </a>
                    </div>
                  </div>
            @else
                <div class="col-12 row align-self-center" id="course_list">
                  <div class="col-9 row align-self-center" >
                    <div class="checkbox_style align-self-center">
                      <input type="checkbox" value="{{$row->fxr_id}}" class="group_lecturer group_download">
                    </div>
                    <a href="{{$character}}/CourseList/PastYear/images/FinalResult/{{$id}}-{{$row->document}}" data-toggle="lightbox" data-gallery="example-gallery" class="col-11 row" style="padding:10px 0px;margin-left:5px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="{{$row->document_name}} <br> <a href='{{$character}}/CourseList/PastYear/FinalResult/view/whole_paper/{{$id}}-{{$row->fxr_id}}' class='full_question' target='_blank'>Whole paper</a>">
                      <div class="col-1" style="position: relative;top: -2px;">
                        <img src="{{url('image/img_icon.png')}}" width="25px" height="20px"/>
                      </div>
                      <div class="col-10" id="course_name">
                        <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>{{$row->document_name}}</b></p>
                      </div>
                    </a>
                  </div>
                  <div class="col-3" id="course_action_two">
                    <i class="fa fa-download download_button" aria-hidden="true" id="download_button_{{$row->fxr_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>&nbsp;
                  </div>
                </div>
            @endif
            @endforeach
          </div>
        @endif

          @if(count($student_result)>0)
          <div class="col-12 row" style="padding: 0px;margin: 10px 0px 10px 0px;">
                    <div class="checkbox_group_style align-self-center">
                      <input type="checkbox" name="group_student" id='group_student' class="group_checkbox">
                    </div>
                    <div class="s_plus row col" style="border:0px solid black;margin: 0px;padding:0px;font-size: 20px;">
                      <div class="col-md-3 row" style="padding-left: 18px;border:0px solid black;margin: 0px;padding:0px;">
                        Submitted By Student (<i class="fa fa-minus" aria-hidden="true" id="icon_s" style="color: #0d2f81;position: relative;top: 7px;"></i>)
                      </div>
                      <div class="col-9 show_count" style="border:0px solid black;">
                        <hr style="display: inline-block; background-color: #cccccc;width: 94%;margin:0px;position: relative;top: -5px;">
                        <span style="display: inline-block;border:0px solid black;text-align: right;width:5%;"> ( {{count($student_result)}} ) </span>
                      </div>
                    </div>
                  </div>
        <div class="row col-md-12" id="student" style="margin:0px;padding:0px 0px 10px 0px;border-bottom:1px solid black;">
            @foreach($student_result as $sow)
            <?php
                    $ext = "";
                    if($sow->document!=""){
                        $ext = explode(".", $sow->document);
                    }
                ?>
            @if(($ext[1] == "pdf")||($ext[1] == "docx")||($ext[1] == "xlsx")||($ext[1] == "pptx"))
            <div class="row col-12 align-self-center" id="course_list">
                    <div class="col-9 row align-self-center">
                      <div class="checkbox_style align-self-center">
                        <input type="checkbox" value="{{$sow->fxr_id}}" class="group_student group_download">
                      </div>
                      <a href="{{$character}}/CourseList/PastYear/FinalSampleResult/download/{{$id}}-{{$sow->fxr_id}}" class="col-11 row" style="padding:10px 0px;margin-left:5px;color:#0d2f81;border:0px solid black;" id="show_image_link">
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
                      </a>
                    </div>
                 </div>
            @else
                <div class="col-12 row align-self-center" id="course_list">
                  <div class="col-9 row align-self-center">
                    <div class="checkbox_style align-self-center">
                        <input type="checkbox" value="{{$sow->fxr_id}}" class="group_student group_download">
                      </div>
                    <a href="{{$character}}/CourseList/PastYear/images/FinalResult/{{$id}}-{{$sow->document}}" data-toggle="lightbox" data-gallery="example-gallery_student" class="col-11 row" style="padding:10px 0px;margin-left:5px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="{{$sow->document_name}} <br> <a href='{{$character}}/CourseList/PastYear/AssessmentResult/view/whole_paper/{{$id}}-{{$sow->fxr_id}}' class='full_question' target='_blank'>Whole paper</a>">
                      <div class="col-1" style="position: relative;top: -2px;">
                        <img src="{{url('image/img_icon.png')}}" width="25px" height="20px"/>
                      </div>
                      <div class="col-10" id="course_name">
                        <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>{{$sow->document_name}}</b></p>
                      </div>
                    </a>
                  </div>
                  <div class="col-3" id="course_action_two">
                    <i class="fa fa-download download_button" aria-hidden="true" id="download_button_{{$sow->fxr_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>&nbsp;
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