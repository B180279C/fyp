<?php
$title = "Moderator";
$option3 = "id='selected-sidebar'";
?>
@extends('layouts.nav_dean')

@section('content')
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->semester_name}} : {{$course[0]->short_form_name}} / {{$course[0]->subject_code}} {{$course[0]->subject_name}} ( {{$course[0]->name}} )</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/Moderator">Moderator </a>/
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
            <p class="page_title">Detail</p>
            <div class="details" style="padding: 5px 5px 0px 5px;">
                <div class="row">
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="/Moderator/assign/student/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/student.png')}}" width="150px" height="90px" style="margin-top: 50px;"/>
                            <br>
                            <p style="color: #0d2f81;">Student List</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="/Moderator/lectureNote/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/note.png')}}" width="80px" height="90px" style="margin-top: 50px;"/>
                            <br>
                            <p style="color: #0d2f81;">Lecture Note</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="/Moderator/teachingPlan/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/plan.png')}}" width="105px" height="90px" style="margin-top: 50px;"/>
                            <br>
                            <p style="color: #0d2f81;">Teaching Plan</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="/assessment/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/assessment.png')}}" width="75px" height="70px" style="margin-top: 60px;"/>
                            <br>
                            <p style="color: #0d2f81;">Continuous Assessment</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="/FinalExamination/{{$id}}/" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
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