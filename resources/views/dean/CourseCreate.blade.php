<?php
$title = "Department";
$option4 = "id='selected-sidebar'";
?>
@extends('layouts.nav_dean')

@section('content')
<script type="text/javascript">
    $(function () {
        $("#form_sub").hide();
        $("#form_year").hide();
        $("#form_tch").hide();
        $("#form_lct").hide();
        $("#form_other_lct").hide();

        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        
        $('#programme').change(function(){
            var value = $('#programme').val();
            $.ajax({
               type:'POST',
               url:'/courseSubject',
               data:{value:value},

               success:function(data){
                    $("#subject").html(data);
                    $('#subject').selectpicker('refresh');
                    $("#form_sub").show();
               }
            });
        });
        $('#inlineRadio1').change(function(){
            $("#form_other_lct").hide();
            $("#form_lct").show();
            var value = $('#lecturer1').val();
            document.getElementById('lecturer').value = value;
        });

        $('#inlineRadio2').change(function(){
            $("#form_other_lct").show();
            $("#form_lct").hide();
            var value = $('#lecturer2').val();
            document.getElementById('lecturer').value = value;
        });

        $('#lecturer1').change(function(){
            var value = $('#lecturer1').val();
            document.getElementById('lecturer').value = value;
        });

        $('#lecturer2').change(function(){
            var value = $('#lecturer2').val();
            document.getElementById('lecturer').value = value;
        });

        $('#subject').change(function(){
            $("#form_year").show();
            $("#form_tch").show();
            $("#form_lct").show();
        });
    });
</script>
<div style="background-color:white;">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$faculty_name->faculty_name}}</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/CoursePortFolio">Course PortFolio </a>/
            <span class="now_page">Add Course</span>/
        </p>
        <hr style="margin: 0px 10px;">
    </div>
    <div class="col-md-12">
        <div class="details" style="padding: 10px 5px 5px 5px;">
            <h5 style="color: #0d2f81;">Add New Course Portfolio Details</h5>
            <hr style="margin: 0px;">
                    @if(count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{$error}}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
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
                      <Strong>{{\Session::get('failed')}}</Strong>
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    @endif

                        <form method="post" action="{{route('course.submit')}}">
                        {{csrf_field()}}
                        <div class="row">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-briefcase" aria-hidden="true" style="font-size: 17px;"></i>
                                </p>
                            </div>
                            <div class="col-10" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="Programme" class="label">Programme</label>
                                    <select class="selectpicker form-control" name="programme" id="programme" data-width="100%"data-live-search="true" title="Choose One" required>
                                        @foreach($faculty as $row_faculty)
                                        <optgroup label="{{ $row_faculty['faculty_name']}}">
                                            @foreach($programme as $row)
                                                @if($row_faculty['faculty_id']==$row->faculty_id)
                                                    <option value="{{ $row->programme_id }}" class="option-group">{{$row->short_form_name}} : {{$row->programme_name}}</option>
                                                @endif
                                            @endforeach
                                        </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="row" id="form_sub">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-book" aria-hidden="true" style="font-size: 17px;"></i>
                                </p>
                            </div>
                            <div class="col-10" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="Programme" class="label">Subjects </label>
                                    <select class="selectpicker form-control" name="subject" data-width="100%" title="Choose one" data-live-search="true" id="subject" required>

                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="form_year">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-calendar" aria-hidden="true" style="font-size: 20px;"></i>
                                </p>
                            </div>
                            <div class="col-10" style="padding-left: 20px;">
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1" class="label">Semester</label>
                                            <select class="selectpicker form-control" name="semester" data-width="100%" title="Choose One" required>
                                                    @foreach($semester as $row_semester)
                                                        <option value="{{ $row_semester->semester_id }}" class="option">{{$row_semester->semester_name}}</option>
                                                    @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row" id="form_tch">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-tags" aria-hidden="true" style="font-size: 18px;"></i>
                                </p>
                            </div>
                            <div class="col-10" style="padding-left: 20px;">
                                <div class="row">
                                    <div class="col" style="padding: 18px 0px 0px 0px;margin: 0px;">
                                        <div class="form-group" style="margin: 0px;">
                                            <label for="exampleInputEmail1" class="label">&nbsp;</label>
                                                <div class="form-check form-check-inline">
                                                  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1" checked>
                                                  <label class="form-check-label" for="inlineRadio1">Own Faculty</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
                                                  <label class="form-check-label" for="inlineRadio2">Other Faculty</label>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="form_lct">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-user" aria-hidden="true" style="font-size: 20px;"></i>
                                </p>
                            </div>
                            <div class="col-10" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="full_name" class="label">Lecturer</label>
                                    <select class="selectpicker form-control" id="lecturer1" data-width="100%" title="Choose One" data-live-search="true">
                                    @foreach($staffs as $row_staff)
                                        <option  value="{{$row_staff->id}}" class="option">{{$row_staff->name}} ({{$row_staff->staff_id}})</option>
                                    @endforeach
                                </select>
                                </div>
                            </div>
                        </div>


                        <div class="row" id="form_other_lct">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-user" aria-hidden="true" style="font-size: 20px;"></i>
                                </p>
                            </div>
                            <div class="col-10" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="full_name" class="label">Lecturer</label>
                                    <select class="selectpicker form-control" id="lecturer2" data-width="100%" title="Choose One" data-live-search="true">
                                        @foreach($faculty as $row_faculty)
                                        <optgroup label="{{ $row_faculty['faculty_name']}}">
                                            @foreach($lct as $row)
                                                @if($row_faculty['faculty_id']==$row->faculty_id)
                                                    <option  value="{{$row->id}}" class="option-group">{{$row->name}} ({{$row->staff_id}})</option>
                                                @endif
                                            @endforeach
                                        </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="lecturer" id="lecturer">
                        <hr>
                        <div class="form-group" style="text-align: right;margin: 0px!important;">
                            <input type="submit" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;margin: 0px!important;">
                        </div>
                    </form>
                </div>
            </div>
</div>
@endsection