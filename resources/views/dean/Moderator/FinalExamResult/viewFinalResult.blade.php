<?php
$title = "Moderator";
$option3 = "id='selected-sidebar'";
?>
@extends('layouts.layout')

@section('content')
<style type="text/css">
#show_image_link:hover{
    text-decoration: none;
}
.dropzone .dz-preview{
  border-bottom: 1px solid black;
  padding-left: 10px;
  padding-top: 10px;
  padding-bottom: -30px!important;
  width: 95%;
}
.dropzone .dz-preview .dz-filename {
  display: none;
}
.dropzone .dz-preview .dz-size {
  display: none;
}
.dropzone .dz-preview .dz-remove{
  text-align: left;
  padding-left: 25px;
  display: inline-block;
}
@media only screen and (max-width: 600px) {
  .show_count{
    display: none;
  }
}
@media only screen and (min-width: 600px) {
  .show_count{
    display: block;
  }
}

.checkbox_group_style{
  border:0px solid black;
  padding: 3px 10px 0px 10px!important;
  margin: 0px!important;
}
.checkbox_style{
  border:0px solid black;
  padding: 0px 10px!important;
  margin: 0px!important;
  display: inline;
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
  });
  function w3_open() {
      document.getElementById("action_sidebar").style.display = "block";
      document.getElementById("button_open").style.display = "none";
  }
  function w3_close() {
      document.getElementById("action_sidebar").style.display = "none";
      document.getElementById("button_open").style.display = "block";
  }

  $(document).on('click', '#checkDownloadAction', function(){
    var checkedValue = ""; 
    var inputElements = document.getElementsByClassName('group_download');
    for(var i=0; inputElements[i]; i++){
      if(inputElements[i].checked){
        checkedValue += inputElements[i].value+"---";
      }
    }
    if(checkedValue!=""){
      var course_id = $('#course_id').val();
      var id = course_id+"---"+checkedValue;
      window.location = "{{$character}}/FinalResult/download/zipFiles/"+id+"/checked";
    }else{
      alert("Please select the document first.");
    }
  });

  $(function () {
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    if($('.search').val()!=""){
      var value = $('.search').val();
      var course_id = $('#course_id').val();
      $.ajax({
          type:'POST',
          url: "{{$character}}/Moderator/FinalResult/searchStudentList/",
          data:{value:value,course_id:course_id},
          success:function(data){
            document.getElementById("student_list").innerHTML = data;
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
          }
      });
    }
    $(".search").keyup(function(){
        var value = $('.search').val();
        var course_id = $('#course_id').val();
        $.ajax({
           type:'POST',
           url: "{{$character}}/Moderator/FinalResult/searchStudentList/",
           data:{value:value,course_id:course_id},
           success:function(data){
              document.getElementById("student_list").innerHTML = data;
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
           }
        });
    });
  });
 </script>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->semester_name}} : {{$course[0]->short_form_name}} / {{$course[0]->subject_code}} {{$course[0]->subject_name}} ( {{$course[0]->name}} )</p>
        <p class="pass_page">
            <a href="{{$character}}/home" class="first_page"> Home </a>/
            <a href="{{$character}}/Moderator">Moderator </a>/
            <a href="{{$character}}/Moderator/course/{{$course[0]->course_id}}">{{$course[0]->semester_name}} : {{$course[0]->short_form_name}} / {{$course[0]->subject_code}} {{$course[0]->subject_name}} ( {{$course[0]->name}} )</a>/
            <a href="{{$character}}/Moderator/FinalExam/{{$course[0]->course_id}}/">Final Assessment</a>/
            <span class="now_page">Final ( R )</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 5px 10px;">
        <div class="col-md-12">
            <p class="page_title">Final ( R )</p>
            @if((count($lecturer_result)!=0)||(count($student_result)!=0))
            <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                <div id="action_sidebar" class="w3-animate-right" style="display: none;width: 250px;">
                    <div style="text-align: right;padding:10px;">
                        <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                    <ul class="sidebar-action-ul">
                      
                      <p class="title_method">Download</p>
                        <a id="checkDownloadAction"><li class="sidebar-action-li"><i class="fa fa-check-square-o" style="padding: 0px 10px;" aria-hidden="true"></i>Checked Item</li></a>
                        <a href='{{$character}}/FinalResult/download/zipFiles/{{$course[0]->course_id}}/All'><li class="sidebar-action-li"><i class="fa fa-download" style="padding: 0px 10px;" aria-hidden="true"></i>All Result</li></a>
                      
                    </ul>
                </div>
                @endif
                <br>
                <br>
                @if(\Session::has('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <Strong>{{\Session::get('success')}}</Strong>
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              @endif
              <div class="details" style="padding: 0px 5px 0px 5px;">
                <div class="col-md-6 row" style="padding:0px 20px;position: relative;top: -25px;">
                    <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                        <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                              <i class="fa fa-search" aria-hidden="true" style="font-size: 20px;"></i>
                        </p>
                    </div>
                    <div class="col-11" style="padding-left: 20px;">
                        <div class="form-group">
                            <label for="full_name" class="bmd-label-floating">Search</label>
                            <input type="hidden" value="{{$course[0]->course_id}}" id="course_id">
                            <input type="text" name="search" class="form-control search" id="input" style="font-size: 18px;">
                        </div>
                    </div>
                </div>
                
                <div id="student_list" class="row" style="margin-top: -15px;">
                  @if(count($lecturer_result)>0)
                  <div class="col-12 row" style="padding: 0px 10px;margin: 0px;">
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
                  <div class="row col-md-12" id="lecturer" style="margin:12px 0px 0px 0px;padding: 0px 0px 5px 0px;border-bottom:1px solid black;">
                    @foreach($lecturer_result as $lr_row)
                    <div class="row col-md-4 align-self-center" id="course_list" style="margin:0px 0px 5px 0px;">
                          <div class="checkbox_style align-self-center">
                            <input type="checkbox" name="group{{$lr_row->fxr_id}}" value="{{$lr_row->student_id}}_Lecturer" class="group_lecturer group_download">
                          </div>
                          <a href='{{$character}}/Moderator/FinalResult/view/student/{{$lr_row->fxr_id}}/' class="col-11 row align-self-center" id="show_image_link" style="margin-left:0px;border:0px solid black;">
                            <div class="col-12 row" style="padding:10px 10px 10px 0px;color:#0d2f81;">
                              <div class="col-1" style="position: relative;top: -2px;padding-left: 2px;">
                                <img src="{{url('image/folder2.png')}}" width="25px" height="25px"/>
                              </div>
                              <div class="col-10">
                                <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;width:280px;"><b>{{$lr_row->student_id}} ( {{$lr_row->name}} ) </b></p>
                              </div>
                            </div>
                          </a>
                      </div>
                      @endforeach
                    </div>
                    @endif
                    @if(count($student_result)>0)
                    <div class="col-12 row" style="padding: 0px 10px;margin: 10px 0px 0px 0px;">
                      <div class="checkbox_group_style align-self-center">
                        <input type="checkbox" name="group_student" id='group_student' class="group_checkbox">
                      </div>
                      <div class="s_plus row col" style="border:0px solid black;margin:0px;padding:0px;font-size: 20px;">
                        <div class="col-md-3 row" style="padding-left: 18px;border:0px solid black;margin: 0px;padding:0px;">
                          Submitted By Students (<i class="fa fa-minus" aria-hidden="true" id="icon_s" style="color: #0d2f81;position: relative;top: 7px;"></i>)
                        </div>
                        <div class="col-9 show_count" style="border:0px solid black;">
                          <hr style="display: inline-block; background-color: #cccccc;width: 94%;margin:0px;position: relative;top: -5px;">
                          <span style="display: inline-block;border:0px solid black;text-align: right;width:5%;"> ( {{count($student_result)}} ) </span>
                        </div>
                      </div>
                    </div>
                    <div class="row col-md-12" id="student" style="margin:12px 0px 0px 0px;padding: 0px  0px 10px 0px;border-bottom:1px solid black;">
                      @foreach($student_result as $sr_row)
                    <div class="row col-md-4 align-self-center" id="course_list" style="margin:0px;">
                          <div class="checkbox_style align-self-center">
                            <input type="checkbox" name="group{{$sr_row->fxr_id}}" value="{{$sr_row->student_id}}_Students" class="group_student group_download">
                          </div>
                          <a href='{{$character}}/Moderator/FinalResult/view/student/{{$sr_row->fxr_id}}/' class="col-11 row align-self-center" id="show_image_link" style="margin-left:0px;border:0px solid black;">
                            <div class="col-12 row" style="padding:10px 10px 10px 0px;color:#0d2f81;">
                              <div class="col-1" style="position: relative;top: -2px; padding-left: 2px;">
                                <img src="{{url('image/folder2.png')}}" width="25px" height="25px"/>
                              </div>
                              <div class="col-10">
                                <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;width:280px;"><b>{{$sr_row->student_id}} ( {{$sr_row->name}} ) </b></p>
                              </div>
                            </div>
                          </a>
                      </div>
                      @endforeach
                  @endif
                  @if((count($lecturer_result)==0)&&(count($student_result)==0))
                        <div style="display: block;border:1px solid black;padding: 50px;width: 100%;margin:5px 20px;">
                          <center>Empty</center>
                        </div>
                      @endif
                    </div>
                </div>
              </div>
        </div>
    </div>
</div>
@endsection