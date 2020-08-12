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
        $(".search").keyup(function(){
            var value = $('.search').val();
            // var department_id = $('#department_id').val();
            $.ajax({
               type:'POST',
               url:'/searchLecturerCV',
               data:{value:value},
               success:function(data){
                    document.getElementById("lecturer_CV").innerHTML = data;
               }
            });
        });
    });
</script>
<style type="text/css">
#course_list:hover{
    text-decoration: none;
    background-color: #e6e6e6;
}
</style>
<div style="background-color: #f2f2f2">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$faculty->faculty_name}}</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/FacultyPortFolio"> Faculty PortFolio </a>/
            <span class="now_page">Lecturer CV </span>/
        </p>
        <hr style="margin: -10px 10px;">
    </div>
    <div class="row" style="padding: 10px 10px 10px 10px;">
        <div class="col-md-12">
            <p style="display: inline;font-size: 25px;position: relative;top: 5px;left: 10px;color: #0d2f81;">Lecturer CV</p>
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
                <div class="row" id="lecturer_CV">
                    <div class="col-md-12">
                        <p style="font-size: 18px;margin:0px 0px 0px 10px;">Faculty of Lecturer CV</p>
                    </div>
                    @foreach($faculty_staff as $row)
                            <?php
                                if($row->lecturer_CV!=""){
                                    $ext = explode(".", $row->lecturer_CV);
                                }
                            ?>
                            <a href="{{ asset('staffCV/'.$row->lecturer_CV) }}" class="col-md-12 align-self-center" id="course_list" download>
                              <div class="col-md-12 row" style="padding:10px;color:#0d2f81;">
                                <div class="col-1" style="padding-top: 3px;">
                                    @if($ext[1]=="pdf")
                                    <img src="{{url('image/pdf.png')}}" width="25px" height="25px"/>
                                    @elseif($ext[1]=="docx")
                                    <img src="{{url('image/docs.png')}}" width="25px" height="25px"/>
                                    @elseif($ext[1]=="xlsx")
                                    <img src="{{url('image/excel.png')}}" width="25px" height="25px"/>
                                    @endif
                                </div>
                                <div class="col" id="course_name">
                                  <p style="margin: 0px;"><b>{{$row->lecturer_CV}}</b></p>
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
