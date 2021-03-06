<?php
$title = "Reviewer";
$option4 = "id='selected-sidebar'";
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
            <a href="{{$character}}/Reviewer">{{$cha}} </a>/
            <span class="now_page">{{$course[0]->semester_name}} : {{$course[0]->short_form_name}} / {{$course[0]->subject_code}} {{$course[0]->subject_name}} ( {{$course[0]->name}} )</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 10px 10px;">
        <div class="col-md-12">
            <p class="page_title">Status</p>
            <div style="padding: 5px;overflow-x:auto;">
                <table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);" id="table" class="table table-hover">
                    <thead>
                    <tr style="background-color: #d9d9d9;">
                        <th style="border-left:1px solid #e6e6e6;color:black;text-align: center;"><b>Student</b></th>
                        <th style="border-left:1px solid #e6e6e6;color:black;text-align: center;"><b>Lecture Note</b></th>
                        <th style="border-left:1px solid #e6e6e6;color:black;text-align: center;"><b>Timetable</b></th>
                        <th style="border-left:1px solid #e6e6e6;color:black;text-align: center;"><b>Attendance</b></th>
                        <th style="border-left:1px solid #e6e6e6;color:black;text-align: center;"><b>Teaching Plan</b></th>
                        <th style="border-left:1px solid #e6e6e6;color:black;text-align: center;"><b>Continuous Assessment</b></th>
                        <th style="border-left:1px solid #e6e6e6;color:black;text-align: center;"><b>Final Assessment</b></th>
                    </tr>
                    <tr>
                        <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">
                            <b>{{count($student)}}</b>
                        </td>
                        <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">
                            @if(count($note)>0)
                            <i class="fa fa-check correct" aria-hidden="true"></i></td>
                            @else
                            <i class="fa fa-times wrong" aria-hidden="true"></i>
                            @endif
                        </td>
                        <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">
                            @if(count($timetable)>0)
                            <i class="fa fa-check correct" aria-hidden="true"></i></td>
                            @else
                            <i class="fa fa-times wrong" aria-hidden="true"></i>
                            @endif
                        </td>
                        <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">
                            <b>{{round($attendance)}}%</b>
                        </td>
                        <?php
                        if($status_TP=="Rejected"){
                            $color_TP = "red";
                        }else if($status_TP=="Pending"){
                            $color_TP = "grey";
                        }else if($status_TP=="Approved"){
                            $color_TP = "green";
                        }else{
                            $color_TP = "blue";
                        }
                        ?>
                        <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;color:<?php echo $color_TP;?>;"><b>
                            {{$status_TP}}</b>
                        </td>
                        <?php
                        if($status_CA=="Rejected"){
                            $color_CA = "red";
                        }else if($status_CA=="Pending"){
                            $color_CA = "grey";
                        }else if($status_CA=="Verified"){
                            $color_CA = "green";
                        }else{
                            $color_CA = "blue";
                        }
                        ?>
                        <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;color:<?php echo $color_CA;?>;"><b>
                            {{$status_CA}}</b>
                        </td>
                        <?php
                        if($status_FA=="Rejected"){
                            $color_FA = "red";
                        }else if($status_FA=="Pending"){
                            $color_FA = "grey";
                        }else if($status_FA=="Approved"){
                            $color_FA = "green";
                        }else{
                            $color_FA = "blue";
                        }
                        ?>
                        <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;color:<?php echo $color_FA;?>;"><b>
                            {{$status_FA}}</b>
                        </td>
                    </tr>
                    </thead>
                </table>
            </div>
            <hr class="row" style="background-color: black;padding: 0px; margin:0px;">
            <p class="page_title">Detail</p>
            <div class="details" style="padding: 5px 5px 0px 5px;">
                <div class="row">
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="{{$character}}/Reviewer/assign/student/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/student.png')}}" width="150px" height="90px" style="margin-top: 50px;"/>
                            <br>
                            <p style="color: #0d2f81;">Student List</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="{{$character}}/Reviewer/lectureNote/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/note.png')}}" width="80px" height="90px" style="margin-top: 50px;"/>
                            <br>
                            <p style="color: #0d2f81;">Lecture Note</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="{{$character}}/Reviewer/teachingPlan/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/plan.png')}}" width="105px" height="90px" style="margin-top: 50px;"/>
                            <?php
                                $count = App\Http\Controllers\Dean\NotificationController::getTP_Num($id,'Reviewer');
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
                        <a href="{{$character}}/Reviewer/viewAssessment/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/assessment.png')}}" width="75px" height="70px" style="margin-top: 60px;"/>
                            <br>
                            <p style="color: #0d2f81;">Continuous Assessment</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="{{$character}}/Reviewer/FinalExam/{{$id}}/" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/final.png')}}" width="75px" height="80px" style="margin-top: 60px;margin-left: 10px;"/>
                            <br>
                            <p style="color: #0d2f81;">Final Assessment</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="{{$character}}/Reviewer/timetable/{{$id}}/" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/timetable.png')}}" width="75px" height="80px" style="margin-top: 60px;margin-left: 10px;"/>
                            <br>
                            <p style="color: #0d2f81;">Timetable</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="{{$character}}/Reviewer/Attendance/{{$id}}/" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/attendance.png')}}" width="90px" height="80px" style="margin-top: 60px;margin-left: 10px;"/>
                            <br>
                            <p style="color: #0d2f81;">Attendance</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="{{$character}}/Reviewer/E_Portfolio/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
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
                        <a href="{{$character}}/Reviewer/Assessment/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/assessment.png')}}" width="75px" height="70px" style="margin-top: 60px;"/>
                            <?php
                                $count = App\Http\Controllers\Dean\NotificationController::getCA_Num($id,'Reviewer');
                                if($count>0){
                                  echo '<span class="notification_num">';
                                  echo '<img src="'.url('image/notification.png').'" width="25px" height="23px" style="position: relative;top: -12px;left: 3px;">';
                                  echo '<span style="position: absolute;top:-8px;left:3px;font-size: 12px;display: inline-block;width: 25px;text-align: center;color:white;"><b>'.$count.'</b></span>';
                                  echo '</span>';
                                }
                            ?>
                            <br>
                            <p style="color: #0d2f81;">Moderation Form <br/>( Coutinuous Assessment )</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="{{$character}}/Reviewer/FinalExamination/{{$id}}/" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/final.png')}}" width="70px" height="70px" style="margin-top: 60px;margin-left: 10px;"/>
                            <?php
                                $count = App\Http\Controllers\Dean\NotificationController::getFA_Num($id,'Reviewer');
                                if($count>0){
                                  echo '<span class="notification_num">';
                                  echo '<img src="'.url('image/notification.png').'" width="25px" height="23px" style="position: relative;top: -12px;left: 3px;">';
                                  echo '<span style="position: absolute;top:-8px;left:3px;font-size: 12px;display: inline-block;width: 25px;text-align: center;color:white;"><b>'.$count.'</b></span>';
                                  echo '</span>';
                                }
                            ?>
                            <br>
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
                            <a href="{{$character}}/Reviewer/PastYearNote/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                                <center>
                                <img src="{{url('image/note.png')}}" width="75px" height="80px" style="margin-top: 60px;"/>
                                <br>
                                <p style="color: #0d2f81;">Lecture Note</p>
                                </center>
                            </a>
                        </div>
                        <div class="col-md-3" style="margin-bottom: 20px">
                            <a href="{{$character}}/Reviewer/PastYearTP/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                                <center>
                                <img src="{{url('image/plan.png')}}" width="105px" height="90px" style="margin-top: 50px;"/>
                                <br>
                                <p style="color: #0d2f81;">Teaching Plan</p>
                                </center>
                            </a>
                        </div>
                        <div class="col-md-3" style="margin-bottom: 20px">
                            <a href="{{$character}}/Reviewer/PastYear/assessment/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                                <center>
                                <img src="{{url('image/assessment.png')}}" width="80px" height="75px" style="margin-top: 60px;"/>
                                <br>
                                <p style="color: #0d2f81;margin-top: 5px;">Continuous Assessment</p>
                                </center>
                            </a>
                        </div>
                        <div class="col-md-3" style="margin-bottom: 20px">
                            <a href="{{$character}}/Reviewer/PastYear/FinalAssessment/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
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