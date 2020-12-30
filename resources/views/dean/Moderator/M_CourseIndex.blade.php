<?php
$title = "Moderator";
$option3 = "id='selected-sidebar'";
?>
@extends('layouts.layout')

@section('content')
<style type="text/css">
.view:hover{
    text-decoration:none;
}
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
    text-align: center;
  }
  .tooltiptext{
    width:300px;
    background-color:#e6e6e6;
    color: black;
    text-align: left;
    border-radius: 6px;
    border:1px solid black;
    padding: 5px 10px;
    position: absolute;
    z-index: 1;
    top:-20%;
    left:103%;
  }
}
</style>
<script type="text/javascript">
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
              url:'{{$character}}/searchModeratorCourse',
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
               url:'{{$character}}/searchModeratorCourse',
               data:{value:value},
               success:function(data){
                    document.getElementById("course").innerHTML = data;
               }
            });
        });
    });
    $(document).ready(function() {
        oTable = $('#dtBasicExample').DataTable(
        {
            "bLengthChange" : false,
            "bInfo": false,
            pagingType: 'input',
            pageLength: 8,
            language: {
                oPaginate: {
                   sNext: '<i class="fa fa-forward"></i>',
                   sPrevious: '<i class="fa fa-backward"></i>',
                   sFirst: '<i class="fa fa-step-backward"></i>',
                   sLast: '<i class="fa fa-step-forward"></i>'
                }
            }
        });
        $(document).on("click",".tp_title", function(){
            $('#plan_detail').slideToggle("slow", function(){
                // check paragraph once toggle effect is completed
                if($('#plan_detail').is(":visible")){
                    $('#icon').removeClass('fa fa-plus');
                    $('#icon').addClass('fa fa-minus');
                }else{
                    $('#icon').removeClass('fa fa-minus');
                    $('#icon').addClass('fa fa-plus');
                }
            });
        });
    });
</script>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">Moderator</p>
        <p class="pass_page">
            <a href="{{$character}}/home" class="first_page"> Home </a>/
            <span class="now_page">Moderator</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 5px 10px;">
        <div class="col-md-12">
            <p class="page_title">Courses of Moderating</p>
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
            <h5 style="position:relative;margin-top:10px;left: 10px;">Standard Operating Procedure ( SOP )</h5>
            <div style="overflow-x: auto;padding:0px 10px 5px 10px;">
            <table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);border:none;" id="dtBasicExample">
              <thead>
                <tr style="background-color: #d9d9d9;">
                  <th style="border-left:1px solid #e6e6e6;color:black;border-bottom: 1px solid #d9d9d9;text-align: center;">No.</th>
                  <th style="border-left:1px solid #e6e6e6;color:black;border-bottom: 1px solid #d9d9d9;text-align: center;">Courses Detail</th>
                  <th style="border-left:1px solid #e6e6e6;color:black;border-bottom: 1px solid #d9d9d9;text-align: center;">Type of Materials</th>
                  <th style="border-left:1px solid #e6e6e6;color:black;border-bottom: 1px solid #d9d9d9;text-align: center;">Action</th>
                  <th style="border-left:1px solid #e6e6e6;color:black;border-bottom: 1px solid #d9d9d9;text-align: center;">Responce</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $num = 1;
                ?>
                @foreach($action as $row)
                <tr>
                  <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">{{$num}}</td>
                  <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;">{{$row->subject_code}} {{$row->subject_name}} ( {{$row->name}} )</td>
                  <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">Teaching Plan</td>
                  <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">Verification</td>
                  <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;"><a href="{{$character}}/Moderator/teachingPlan/{{$row->course_id}}" class="view" target='_blank'><i class="fa fa-long-arrow-right" aria-hidden="true"></i> View</a></td>
                </tr>
                <?php
                $num++;
                ?>
                @endforeach
                @foreach($action2 as $row2)
                <tr>
                  <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">{{$num}}</td>
                  <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;">{{$row2->subject_code}} {{$row2->subject_name}} ( {{$row2->name}} )</td>
                  <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">Coutinuous Assessment ( CA )</td>
                  <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">Moderation</td>
                  <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;"><a href="{{$character}}/Moderator/Assessment/{{$row2->course_id}}" class="view" target='_blank'><i class="fa fa-long-arrow-right" aria-hidden="true"></i> View</a></td>
                </tr>
                <?php
                $num++;
                ?>
                @endforeach
                @foreach($action3 as $row3)
                <tr>
                  <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">{{$num}}</td>
                  <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;">{{$row3->subject_code}} {{$row3->subject_name}} ( {{$row3->name}} )</td>
                  <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">Final Assessment ( FA )</td>
                  <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">Moderation</td>
                  <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;"><a href="{{$character}}/Moderator/FinalExamination/{{$row3->course_id}}" class="view" target='_blank'><i class="fa fa-long-arrow-right" aria-hidden="true"></i> View</a></td>
                </tr>
                <?php
                $num++;
                ?>
                @endforeach
              </tbody>
            </table>
            </div>
            <hr style="margin: 15px 5px 5px 5px;background-color:black;">
            <div class="row">
                <h5 style="position: relative;left: 10px;margin-top: 5px;" class="tp_title col-10" id="1">
                    Courses of Materials (<i class="fa fa-plus" aria-hidden="true" id="icon" style="color: #0d2f81;position: relative;top: 2px;"></i>)
                </h5>
            </div>
            <div class="details" style="padding: 0px 5px;display: none;" id="plan_detail">
                <div class="col-md-6 row" style="padding:0px 20px;position: relative;top: -10px;">
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
                              <span>3. Semester</span><br/>
                              <span>4. Lecturer Name</span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row" id="course" style="margin-top: -10px;">
                  <div class="col-md-12">
                    <p style="font-size: 18px;margin:0px 0px 0px 10px;display: inline-block;">
                    	Newest Semester of Courses
                    </p>
                  </div>
                      @foreach($course as $row)
                        <a href="{{$character}}/Moderator/course/{{$row->course_id}}" class="col-md-12 align-self-center" id="course_list">
                          <div class="col-md-12 row" style="padding:13px 10px;color:#0d2f81;">
                            <div class="col-1 align-self-center" style="padding-top: 0px;">
                              <img src="{{url('image/folder2.png')}}" width="25px" height="24px"/>
                            </div>
                            <div class="col" id="course_name" style="padding-top: 2px;">
                              <p style="margin: 0px;display: inline-block;"><b>{{$row->semester_name}}</b> : {{$row->short_form_name}} / {{$row->subject_code}} {{$row->subject_name}} ( {{$row->name}} )</p>
                              <?php
                                $count = App\Http\Controllers\Dean\Moderator\M_CourseController::getAction($row->course_id);
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