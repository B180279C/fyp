<?php
$title = "CoursePortfolio";
$option5 = "id='selected-sidebar'";
?>
@extends('layouts.layout')

@section('content')
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
        $('#showData').hide();
        $('#errorData').hide();
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        if($('.search').val()!=""){
          var value = $('.search').val();
          $.ajax({
              type:'POST',
              url:'{{$character}}/searchCPCourse',
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
               url:'{{$character}}/searchCPCourse',
               data:{value:value},
               success:function(data){
                    document.getElementById("course").innerHTML = data;
               }
            });
        });
    });
</script>
<style type="text/css">
.dropzoneModel{
  border-bottom: 1px solid black;
  padding-left: 0px;
  padding-top: 10px;
  padding-bottom: 10px!important;
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
  display: inline-block;
}
#syllabus_link:hover{
  text-decoration: none;
}
.InModel{
  padding-left: 25px;
}
.tablebody{
  border-left:1px solid #d9d9d9;
  border-bottom: 1px solid #d9d9d9;
}
.tablebodyCenter{
  border-left:1px solid #d9d9d9;
  border-bottom: 1px solid #d9d9d9;
  text-align: center;
}
.tablehead{
  background-color: #0d2f81!important; color: gold;
}
@media only screen and (max-width: 600px) {
  #showData{
    margin-right: 20px;
  }
}
</style>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">Courses</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <span class="now_page">Courses</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px;">
        <div class="col-md-12">
             @if($character=="")
             <p class="page_title">Courses In {{$faculty->faculty_name}}</p>
             @else
             <p class="page_title">Courses In {{$department->department_name}}</p>
             @endif
             @if($character=="")
             <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                <div id="action_sidebar" class="w3-animate-right" style="display: none">
                    <div style="text-align: right;padding:10px;">
                        <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                  <ul class="sidebar-action-ul">
                      <a href='CourseList/create'><li class="sidebar-action-li"><i class="fa fa-book" style="padding: 0px 10px;" aria-hidden="true"></i>Make a new Course</li></a>
                      <a id="open_document"><li class="sidebar-action-li"><i class="fa fa-upload" style="padding: 0px 10px;" aria-hidden="true"></i>Add Multple Courses</li></a>
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
                            <input type="text" name="search" class="form-control search" id="input" style="font-size: 18px;">
                        </div>
                    </div>
                </div>
                <div class="row" id="course" style="margin-top: -25px;">
                  <div class="col-md-12">
                    <p style="font-size: 18px;margin:0px 0px 0px 10px;">Newest Semester of Courses</p>
                  </div>
                      @foreach($course as $row)
                        <a href="{{$character}}/CourseList/action/{{$row->course_id}}" class="col-md-12 align-self-center" id="course_list">
                          <div class="col-md-12 row" style="padding:10px;color:#0d2f81;">
                            <div class="col-1" style="padding-top: 3px;">
                              <img src="{{url('image/folder2.png')}}" width="25px" height="25px"/>
                            </div>
                            <div class="col" id="course_name">
                              <p style="margin: 0px;"><b>{{$row->semester_name}}</b> : {{$row->short_form_name}} / {{$row->subject_code}} {{$row->subject_name}} ( {{$row->name}} )</p>
                            </div>
                            @if($character=="")
                            <div class="col-1" id="course_action">
                                <i class="fa fa-wrench edit_action" aria-hidden="true" id="edit_button_{{$row->course_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>&nbsp;
                                <i class="fa fa-times remove_action" aria-hidden="true" id="remove_button_{{$row->course_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>
                            </div>
                            @endif
                          </div>
                        </a>
                      @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

