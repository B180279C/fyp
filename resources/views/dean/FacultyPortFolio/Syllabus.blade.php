<?php
$title = "Department";
$option2 = "id='selected-sidebar'";
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
              url:'/searchSyllabus',
              data:{value:value},
              success:function(data){
                document.getElementById("Syllabus").innerHTML = data;
              }
          });
        }
        $(".search").keyup(function(){
            var value = $('.search').val();
            $.ajax({
               type:'POST',
               url:'/searchSyllabus',
               data:{value:value},
               success:function(data){
                    document.getElementById("Syllabus").innerHTML = data;
               }
            });
        });
    });
</script>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$faculty->faculty_name}}</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/FacultyPortFolio"> Faculty PortFolio </a>/
            <span class="now_page">Syllabus</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 10px 10px;">
        <div class="col-md-12">
            <p class="page_title">Syllabus</p>
            <div class="details" style="padding: 0px 5px 5px 5px;">
<!--                 <h5 style="color: #0d2f81;">List of Lecturer CV</h5> -->
                <div class="col-md-6 row" style="padding:0px 20px;position: relative;top: -10px;">
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
                <!-- <hr style="margin: 0px 0px 15px 0px;"> -->
                <div class="row" id="Syllabus">
                    <div class="col-md-12">
                        <p style="font-size: 18px;margin:0px 0px 0px 10px;">Syllabus In Faculty</p>
                    </div>
                    @foreach($subjects as $row)
                            <a href="{{ action('Dean\F_PortFolioController@downloadSyllabus',$row->subject_id) }}" class="col-md-12 align-self-center" id="course_list" download="{{$row->syllabus_name}}.xlsx">
                                <div class="col-md-12 row" style="padding:10px;color:#0d2f81;">
                                    <div class="col-1" style="padding-top: 3px;">
                                        <img src="{{url('image/excel.png')}}" width="25px" height="25px"/>
                                    </div>
                                    <div class="col" id="course_name">
                                        <p style="margin: 0px;"><b>{{$row->syllabus_name}}</b></p>
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