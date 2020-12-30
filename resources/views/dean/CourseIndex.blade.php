<?php
$title = "Course";
$option1 = "id='selected-sidebar'";
?>

@extends('layouts.layout')

@section('content')
<style type="text/css">
  @media only screen and (max-width: 800px) {
    .notification_num{
      float:right;
      position:absolute;
      top:10px;
    }
  }
  @media only screen and (min-width: 800px) {
    .notification_num{
      float:right;
      position:absolute;
      top:1px;
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
	$(function () {
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        if($('.search').val()!=""){
          var value = $('.search').val();
          $.ajax({
              type:'POST',
              url:'{{$character}}/searchTeachCourse',
              data:{value:value},
              success:function(data){
                document.getElementById("course").innerHTML = data;
              }
          });
        }
        $(".search").keyup(function(){
            var value = $('.search').val();
            $.ajax({
               type:'POST',
               url:'{{$character}}/searchTeachCourse',
               data:{value:value},
               success:function(data){
                    document.getElementById("course").innerHTML = data;
               }
            });
        });
    });
</script>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">Courses</p>
        <p class="pass_page">
            <a href="{{$character}}/home" class="first_page"> Home </a>/
            <span class="now_page">Courses</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 5px 10px;">
        <div class="col-md-12">
             <p class="page_title">Courses of Teaching</p>
             <!-- <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                <div id="action_sidebar" class="w3-animate-right" style="display: none">
                    <div style="text-align: right;padding:10px;">
                        <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                  <ul class="sidebar-action-ul">
                      <a href='course/create'><li class="sidebar-action-li"><i class="fa fa-book" style="padding: 0px 10px;" aria-hidden="true"></i>Make a new Course</li></a>
                      <a id="open_document"><li class="sidebar-action-li"><i class="fa fa-upload" style="padding: 0px 10px;" aria-hidden="true"></i>Add Multple Courses</li></a>
                  </ul>
            </div> -->
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

            @if(\Session::has('failed'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <?php
              $new_str = str_replace('.', '. <br />', Session::get('failed'));
              echo $new_str;
              ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            <div class="details" style="padding: 0px 5px 0px 5px;">
                <div class="col-md-6 row" style="padding:0px 20px;position: relative;top: -30px;">
                    <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                        <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                            <i class="fa fa-search" aria-hidden="true" style="font-size: 20px;"></i>
                        </p>
                    </div>
                    <div class="col-11" style="padding-left: 20px;">
                        <div class="form-group">
                            <label for="full_name" class="bmd-label-floating">Search</label>
                            <input type="text" name="search" class="form-control search tooltip_hover" id="input" style="font-size: 18px;">
                            <span class="tooltiptext">
                              <span>
                                  <i class="fa fa-info-circle" style="color: #0d2f81;" aria-hidden="true"></i> Important : 
                              </span>
                              <hr style="background-color: #d9d9d9;margin: 3px 0px;">
                              <span>1. Subject Code</span><br/>
                              <span>2. Subject Name</span><br/>
                              <span>3. Semester</span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row" id="course" style="margin-top: -20px;">
                  <div class="col-md-12">
                    <p style="font-size: 18px;margin:0px 0px 0px 10px;display: inline-block;">
                    	Newest Semester of Courses
                    </p>
<!--                     <p id="marking">
                    	<span style="padding:0px 10px;">Plan</span>
                    	<span style="padding:0px 10px;">Note</span>
                    	<span style="padding:0px 10px;">Assessment</span>
                    </p>  -->
                  </div>
                      @foreach($course as $row)
                        <a href="{{$character}}/course/action/{{$row->course_id}}" class="col-md-12 align-self-center" id="course_list">
                          <div class="col-md-12 row" style="padding:10px;color:#0d2f81;">
                            <div class="col-1 align-self-center" style="padding-top: 0px;">
                              <img src="{{url('image/subject.png')}}" width="25px" height="24px"/>
                            </div>
                            <div class="col" id="course_name" style="padding-top: 2px;">
                              <p style="margin: 0px;display: inline-block;"><b>{{$row->semester_name}}</b> : {{$row->subject_code}} {{$row->subject_name}} ( {{$row->short_form_name}} )</p>
                              <?php
                                $count = App\Http\Controllers\Dean\CourseController::getAction($row->course_id);
                                if($count>0){
                                  echo '<span class="notification_num">';
                                  echo '<img src="'.url('image/notification.png').'" width="25px" height="23px" style="position: relative;top: -12px;left: 3px;">';
                                  echo '<span style="position: absolute;top:-8px;left:3px;font-size: 12px;display: inline-block;width: 25px;text-align: center;color:white;"><b>'.$count.'</b></span>';
                                  echo '</span>';
                                }
                              ?>
                            </div>
                          </div>
                        </a>
                      @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection