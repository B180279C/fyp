<?php
$title = "Course";
$option1 = "id='selected-sidebar'";
?>
@extends('layouts.nav_dean')

@section('content')
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
              url:'/searchTeachCourse',
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
               url:'/searchTeachCourse',
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
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/course_list">Courses </a>/
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
                        <a href="/assign/student/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/student.png')}}" width="150px" height="90px" style="margin-top: 50px;"/>
                            <br>
                            <p style="color: #0d2f81;">Student List</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="/lectureNote/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/note.png')}}" width="80px" height="90px" style="margin-top: 50px;"/>
                            <br>
                            <p style="color: #0d2f81;">Lecture Note</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="/teachingPlan/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
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
            <hr class="row" style="background-color: black;padding: 0px; margin:0px;">
            <p class="page_title">Past Year</p>
            <div class="details" style="padding: 5px 5px 0px 5px;">
                <div class="row">
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="/PastYear/assessment/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/assessment.png')}}" width="80px" height="75px" style="margin-top: 60px;"/>
                            <br>
                            <p style="color: #0d2f81;margin-top: 5px;">Continuous Assessment</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="/PastYear/FinalAssessment/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/final.png')}}" width="75px" height="80px" style="margin-top: 60px;margin-left: 10px;"/>
                            <br>
                            <p style="color: #0d2f81;">Final Assessment</p>
                            </center>
                        </a>
                    </div>

                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="/PastYearNote/{{$id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/note.png')}}" width="75px" height="80px" style="margin-top: 60px;"/>
                            <br>
                            <p style="color: #0d2f81;">Lecture Note</p>
                            </center>
                        </a>
                    </div>

                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/plan.png')}}" width="105px" height="90px" style="margin-top: 50px;"/>
                            <br>
                            <p style="color: #0d2f81;">Teaching Plan</p>
                            </center>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection