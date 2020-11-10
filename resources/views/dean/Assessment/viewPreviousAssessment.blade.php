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
  #file_name_two{
    width: 185px;
    margin: 0px;
    padding:0px;
  }
  #file_name{
    width: 240px;
    margin: 0px;
    padding:0px;
  }
}
@media only screen and (min-width: 600px) {
    #course_list{
      margin-left: 0px;
      padding: 4px 15px;
    }
    #course_name{
        margin-left:-28px;
        padding-top:0px;
    }
    #course_action_two{
      text-align: right;
      margin-left: 5px;
      padding: 8px 0px 0px 25px;
    }
    #course_action{
      text-align: right;
      padding: 3px 0px 0px 24px;
    }
}
</style>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</p>
        <p class="pass_page">
            <a href="/assessment/{{$course[0]->course_id}}" class="first_page">Go Back</a>/
            <span class="now_page">{{$assessments[0]->semester_name}} : Assessment List</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 10px 10px;">
        <div class="col-md-12">
            <p class="page_title">{{$assessments[0]->semester_name}} : Assessment List</p>
            <hr style="margin: 12px 10px 18px 10px;">
            <div class="details" style="padding: 0px 5px 5px 5px;">
              <div class="row" style="margin-top: -20px;">
              <?php
              $i=0;
              ?>
              @foreach($assessments as $row)
                <div class="col-12 row align-self-center" id="course_list">
                  <a href="/assessment/folder/{{$course[0]->course_id}}/previous/{{$row->course_id}}/question/{{$row->assessment}}/list" id="show_image_link" class="col-9 row align-self-center">
                    <div class="col-12 row" style="padding:10px;color:#0d2f81;">
                      <div class="col-1" style="position: relative;top: -2px;">
                        <img src="{{url('image/folder2.png')}}" width="25px" height="25px"/>
                      </div>
                      <div class="col-10" id="course_name">
                        <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>{{$row->assessment}}</b></p>
                      </div>
                    </div>
                  </a>
                </div>
              <?php
              $i++;
              ?>
              @endforeach
              @if($i==0)
              <div style="display: block;border:1px solid black;padding: 50px;width: 100%;margin: 10px 20px 0px 20px;">
                  <center>Empty</center>
              </div>
              @endif
              </div>
            </div>
        </div>
    </div>
</div>
@endsection