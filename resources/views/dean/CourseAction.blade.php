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
      top:50px;
      right: 130px;
    }
  }
  @media only screen and (min-width: 800px) {
    .notification_num{
      float:right;
      position:absolute;
      top:50px;
      right: 80px;
    }
  }
</style>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</p>
        <p class="pass_page">
            <a href="{{$character}}/home" class="first_page"> Home </a>/
            <a href="{{$character}}/course_list">Courses </a>/
            <span class="now_page">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 10px 10px;">
        <div class="col-md-12">
            <p class="page_title">Method</p>
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
            <div class="details" style="padding: 5px 5px 0px 5px;">
                <div class="row">
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="{{$character}}/assign/student/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/student.png')}}" width="150px" height="90px" style="margin-top: 50px;"/>
                            <br>
                            <p style="color: #0d2f81;">Student List</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="{{$character}}/lectureNote/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/note.png')}}" width="80px" height="90px" style="margin-top: 50px;"/>
                            <br>
                            <p style="color: #0d2f81;">Lecture Note</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="{{$character}}/teachingPlan/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/plan.png')}}" width="105px" height="90px" style="margin-top: 50px;"/>
                            <?php
                                $count = App\Http\Controllers\Dean\NotificationController::getTP_Num($id,'course');
                                if($count>0){
                                  echo '<span class="notification_num">';
                                  echo '<img src="'.url('image/notification.png').'" width="25px" height="23px" style="position: relative;top: -12px;left: 3px;">';
                                  echo '<span style="position: absolute;top:-8px;left:3px;font-size: 12px;display: inline-block;width: 25px;text-align: center;color:white;"><b>'.$count.'</b></span>';
                                  echo '</span>';
                                }
                            ?>
                            <br>
                            <p style="color: #0d2f81;">Teaching Plan</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="{{$character}}/assessment/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/assessment.png')}}" width="80px" height="80px" style="margin-top: 60px;"/>
                            <?php
                                $count = App\Http\Controllers\Dean\NotificationController::getCA_Num($id,'course');
                                if($count>0){
                                  echo '<span class="notification_num">';
                                  echo '<img src="'.url('image/notification.png').'" width="25px" height="23px" style="position: relative;top: -12px;left: 3px;">';
                                  echo '<span style="position: absolute;top:-8px;left:3px;font-size: 12px;display: inline-block;width: 25px;text-align: center;color:white;"><b>'.$count.'</b></span>';
                                  echo '</span>';
                                }
                            ?>
                            <br>
                            <p style="color: #0d2f81;">Continuous Assessment</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="{{$character}}/FinalExamination/{{$id}}/" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/final.png')}}" width="75px" height="80px" style="margin-top: 60px;margin-left: 10px;"/>
                            <?php
                                $count = App\Http\Controllers\Dean\NotificationController::getFA_Num($id,'course');
                                if($count>0){
                                  echo '<span class="notification_num">';
                                  echo '<img src="'.url('image/notification.png').'" width="25px" height="23px" style="position: relative;top: -12px;left: 3px;">';
                                  echo '<span style="position: absolute;top:-8px;left:3px;font-size: 12px;display: inline-block;width: 25px;text-align: center;color:white;"><b>'.$count.'</b></span>';
                                  echo '</span>';
                                }
                            ?>
                            <br>
                            <p style="color: #0d2f81;">Final Assessment</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="{{$character}}/Timetable/{{$id}}/" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/timetable.png')}}" width="75px" height="80px" style="margin-top: 60px;margin-left: 10px;"/>
                            <br>
                            <p style="color: #0d2f81;">Timetable</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="{{$character}}/Attendance/{{$id}}/" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/attendance.png')}}" width="90px" height="80px" style="margin-top: 60px;margin-left: 10px;"/>
                            <br>
                            <p style="color: #0d2f81;">Attendance</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="{{$character}}/E_Portfolio/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/portfolio.png')}}" width="75px" height="80px" style="margin-top: 60px;margin-left: 0px;"/>
                            <br>
                            <p style="color: #0d2f81;">E - PortFolio</p>
                            </center>
                        </a>
                    </div>
                </div>
            </div>
            <hr class="row" style="background-color: black;padding: 0px; margin:0px;">
            <p class="page_title">Past Year</p>
            <div class="details" style="padding: 5px 5px 0px 5px;">
                <div class="row">
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="{{$character}}/PastYearNote/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/note.png')}}" width="75px" height="80px" style="margin-top: 60px;"/>
                            <br>
                            <p style="color: #0d2f81;">Lecture Note</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="{{$character}}/PastYearTP/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/plan.png')}}" width="105px" height="90px" style="margin-top: 50px;"/>
                            <br>
                            <p style="color: #0d2f81;">Teaching Plan</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="{{$character}}/PastYear/assessment/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/assessment.png')}}" width="80px" height="75px" style="margin-top: 60px;"/>
                            <br>
                            <p style="color: #0d2f81;margin-top: 5px;">Continuous Assessment</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="{{$character}}/PastYear/FinalAssessment/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/final.png')}}" width="75px" height="80px" style="margin-top: 60px;margin-left: 10px;"/>
                            <br>
                            <p style="color: #0d2f81;">Final Assessment</p>
                            </center>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection