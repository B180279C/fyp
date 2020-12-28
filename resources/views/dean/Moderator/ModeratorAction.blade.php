<?php
$title = "Moderator";
$option3 = "id='selected-sidebar'";
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
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->semester_name}} : {{$course[0]->short_form_name}} / {{$course[0]->subject_code}} {{$course[0]->subject_name}} ( {{$course[0]->name}} )</p>
        <p class="pass_page">
            <a href="{{$character}}/home" class="first_page"> Home </a>/
            <a href="{{$character}}/Moderator">Moderator </a>/
            <span class="now_page">{{$course[0]->semester_name}} : {{$course[0]->short_form_name}} / {{$course[0]->subject_code}} {{$course[0]->subject_name}} ( {{$course[0]->name}} )</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 10px 10px;">
        <div class="col-md-12">
            <p class="page_title">Status</p>
            <div class="details" style="padding: 5px 5px 10px 5px;">
                <table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);" id="table" class="table table-hover">
                    <thead>
                    <tr style="background-color: #d9d9d9;">
                        <th style="border-left:1px solid #e6e6e6;color:black;text-align: center;"><b>Student</b></th>
                        <th style="border-left:1px solid #e6e6e6;color:black;text-align: center;"><b>Lecture Note</b></th>
                        <th style="border-left:1px solid #e6e6e6;color:black;text-align: center;"><b>Teaching Plan</b></th>
                        <th style="border-left:1px solid #e6e6e6;color:black;text-align: center;"><b>Continuous Assessment</b></th>
                        <th style="border-left:1px solid #e6e6e6;color:black;text-align: center;"><b>Final Assessment</b></th>
                    </tr>
                    <tr>
                        <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">{{count($student)}}</td>
                        <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">
                            @if(count($note)>0)
                            <i class="fa fa-check correct" aria-hidden="true"></i></td>
                            @else
                            <i class="fa fa-times wrong" aria-hidden="true"></i>
                        </td>
                            @endif
                        <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">
                            @if((count($tp)>0)&&(count($tp_ass)>0)&&(count($tp_cqi)>0))
                            <i class="fa fa-check correct" aria-hidden="true"></i></td>
                            @else
                            <i class="fa fa-times wrong" aria-hidden="true"></i></td>
                            @endif
                        </td>
                        <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;"></td>
                        <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;"></td>
                    </tr>
                    </thead>
                </table>
            </div>
            <hr class="row" style="background-color: black;padding: 0px; margin:0px;">
            <p class="page_title">Materials</p>
            <div class="details" style="padding: 5px 5px 0px 5px;">
                <div class="row">
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="{{$character}}/Moderator/assign/student/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/student.png')}}" width="150px" height="90px" style="margin-top: 50px;"/>
                            <br>
                            <p style="color: #0d2f81;">Student List</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="{{$character}}/Moderator/lectureNote/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/note.png')}}" width="80px" height="90px" style="margin-top: 50px;"/>
                            <br>
                            <p style="color: #0d2f81;">Lecture Note</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="{{$character}}/Moderator/teachingPlan/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/plan.png')}}" width="105px" height="90px" style="margin-top: 50px;"/>
                            <?php
                                $count = App\Http\Controllers\Dean\NotificationController::getTP_Num($id,'Moderator');
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
                        <a href="{{$character}}/Moderator/viewAssessment/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/assessment.png')}}" width="75px" height="70px" style="margin-top: 60px;"/>
                            <br>
                            <p style="color: #0d2f81;">Continuous Assessment</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="{{$character}}/Moderator/FinalExam/{{$id}}/" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/final.png')}}" width="75px" height="80px" style="margin-top: 60px;margin-left: 10px;"/>
                            <br>
                            <p style="color: #0d2f81;">Final Assessment</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="{{$character}}/Moderator/timetable/{{$id}}/" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/timetable.png')}}" width="75px" height="80px" style="margin-top: 60px;margin-left: 10px;"/>
                            <br>
                            <p style="color: #0d2f81;">Timetable</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="{{$character}}/Moderator/Attendance/{{$id}}/" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/attendance.png')}}" width="90px" height="80px" style="margin-top: 60px;margin-left: 10px;"/>
                            <br>
                            <p style="color: #0d2f81;">Attendance</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="{{$character}}/Moderator/E_Portfolio/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/portfolio.png')}}" width="75px" height="80px" style="margin-top: 60px;margin-left: 0px;"/>
                            <br>
                            <p style="color: #0d2f81;">E - PortFolio</p>
                            </center>
                        </a>
                    </div>
                </div>
                <hr class="row" style="background-color: black;padding: 0px; margin:0px;">
                <p class="page_title">Moderation</p>
                <div class="row" style="margin-top: 5px;">
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="{{$character}}/Moderator/Assessment/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/assessment.png')}}" width="75px" height="70px" style="margin-top: 60px;"/>
                            <br>
                            <?php
                                $count = App\Http\Controllers\Dean\NotificationController::getCA_Num($id,'Moderator');
                                if($count>0){
                                  echo '<span class="notification_num">';
                                  echo '<img src="'.url('image/notification.png').'" width="25px" height="23px" style="position: relative;top: -12px;left: 3px;">';
                                  echo '<span style="position: absolute;top:-8px;left:3px;font-size: 12px;display: inline-block;width: 25px;text-align: center;color:white;"><b>'.$count.'</b></span>';
                                  echo '</span>';
                                }
                            ?>
                            <p style="color: #0d2f81;">Moderation Form <br/>( Coutinuous Assessment )</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="{{$character}}/Moderator/FinalExamination/{{$id}}/" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/final.png')}}" width="70px" height="70px" style="margin-top: 60px;margin-left: 10px;"/>
                            <br>
                            <?php
                                $count = App\Http\Controllers\Dean\NotificationController::getFA_Num($id,'Moderator');
                                if($count>0){
                                  echo '<span class="notification_num">';
                                  echo '<img src="'.url('image/notification.png').'" width="25px" height="23px" style="position: relative;top: -12px;left: 3px;">';
                                  echo '<span style="position: absolute;top:-8px;left:3px;font-size: 12px;display: inline-block;width: 25px;text-align: center;color:white;"><b>'.$count.'</b></span>';
                                  echo '</span>';
                                }
                            ?>
                            <p style="color: #0d2f81;">Moderation Form <br/>( Final Assessment )</p>
                            </center>
                        </a>
                    </div>
                </div>
                <hr class="row" style="background-color: black;padding: 0px; margin:0px;">
                <p class="page_title">Past Year</p>
                <div class="details" style="padding: 5px 5px 0px 5px;">
                    <div class="row">
                        <div class="col-md-3" style="margin-bottom: 20px">
                            <a href="{{$character}}/Moderator/PastYearNote/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                                <center>
                                <img src="{{url('image/note.png')}}" width="75px" height="80px" style="margin-top: 60px;"/>
                                <br>
                                <p style="color: #0d2f81;">Lecture Note</p>
                                </center>
                            </a>
                        </div>
                        <div class="col-md-3" style="margin-bottom: 20px">
                            <a href="{{$character}}/Moderator/PastYearTP/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                                <center>
                                <img src="{{url('image/plan.png')}}" width="105px" height="90px" style="margin-top: 50px;"/>
                                <br>
                                <p style="color: #0d2f81;">Teaching Plan</p>
                                </center>
                            </a>
                        </div>
                        <div class="col-md-3" style="margin-bottom: 20px">
                            <a href="{{$character}}/Moderator/PastYear/assessment/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                                <center>
                                <img src="{{url('image/assessment.png')}}" width="80px" height="75px" style="margin-top: 60px;"/>
                                <br>
                                <p style="color: #0d2f81;margin-top: 5px;">Continuous Assessment</p>
                                </center>
                            </a>
                        </div>
                        <div class="col-md-3" style="margin-bottom: 20px">
                            <a href="{{$character}}/Moderator/PastYear/FinalAssessment/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
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
</div>
@endsection