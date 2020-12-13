<?php
$title = "CoursePortFolio";
$option5 = "id='selected-sidebar'";
?>
@extends('layouts.layout')

@section('content')
<script type="text/javascript">
    $(function () {
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
                    $('#subject').prop('disabled', false);
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
            $.ajax({
                type:'POST',
                url:'/changeModerator',
                data:{value:value},
                success:function(data){
                    $('#moderator').prop('disabled', false);
                    $("#moderator").html(data);
                    $('#moderator').selectpicker('refresh');
                }
            });
        });

        $('#lecturer2').change(function(){
            var value = $('#lecturer2').val();
            document.getElementById('lecturer').value = value;
            $.ajax({
                type:'POST',
                url:'/changeModerator',
                data:{value:value},
                success:function(data){
                    $('#moderator').prop('disabled', false);
                    $("#moderator").html(data);
                    $('#moderator').selectpicker('refresh');
                }
            });
        });
    });

    $(document).ready(function(){
        $('#add').click(function(){
        var count = $('#count').val();
        count++;
        $('#timetable').append('<div id="class'+count+'"><div class="row"><div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;"><p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;"><i class="fa fa-clock-o" aria-hidden="true" style="font-size: 20px;"></i></p></div><div class="col-4" style="padding-left: 20px;"><div class="row"><div class="col"><div class="form-group"><label for="exampleInputEmail1" class="label" style="font-size:12px;">Week</label><select class="selectpicker form-control" name="week'+count+'" data-width="100%" title="Choose One" required><option class="option">Monday</option><option class="option">Tuesday</option><option class="option">Wednesday</option><option class="option">Thursday</option><option class="option">Friday</option><option class="option">Saturday</option><option class="option">Sunday</option></select></div></div></div></div><div class="col-2" style="padding-left: 20px;"><div class="row"><div class="col"><div class="form-group"><label for="exampleInputEmail1" class="label" style="font-size:12px;">Start Hour</label><select class="selectpicker form-control" name="s_hour'+count+'" data-width="100%" title="Choose One" required><option class="option">0700</option><option class="option">0800</option><option class="option">0900</option><option class="option">1000</option><option class="option">1100</option><option class="option">1200</option><option class="option">1300</option><option class="option">1400</option><option class="option">1500</option><option class="option">1600</option><option class="option">1700</option><option class="option">1800</option><option class="option">1900</option><option class="option">2000</option><option class="option">2100</option></select></div></div></div></div><div class="col-2" style="padding-left: 20px;"><div class="row"><div class="col"><div class="form-group"><label for="exampleInputEmail1" class="label" style="font-size:12px;">End Hour</label><select class="selectpicker form-control" name="e_hour'+count+'" data-width="100%" title="Choose One" required><option class="option">0800</option><option class="option">0900</option><option class="option">1000</option><option class="option">1100</option><option class="option">1200</option><option class="option">1300</option><option class="option">1400</option><option class="option">1500</option><option class="option">1600</option><option class="option">1700</option><option class="option">1800</option><option class="option">1900</option><option class="option">2000</option><option class="option">2100</option><option class="option">2200</option></select></div></div></div></div><div class="col-2" style="padding-left: 20px;"><div class="row"><div class="col"><div class="form-group"><label for="exampleInputEmail1" class="label" style="font-size:12px;">H./F.</label><select class="selectpicker form-control" name="hORf'+count+'" data-width="100%" title="Choose One" required><option class="option">Half</option><option class="option">Full</option></select></div></div></div></div><div class="col-1 align-self-center"><button type="button" id="remove_'+count+'" class="btn btn-raised btn-danger btn_remove"><i class="fa fa-times" aria-hidden="true" style="font-size: 18px;position: relative;top: 1px;"></i></button></div>     </div></div>');
        $('.selectpicker').selectpicker();
        $('#count').val(count);
        });

        $(document).on('click', '.btn_remove', function(){  
           var button = $(this).attr("id");
           var id = button.split("remove_");   
           $('#class'+id[1]).remove();
        });
    });
</script>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$faculty_name->faculty_name}}</p>
        <p class="pass_page">
            <a href="{{$character}}/home" class="first_page"> Home </a>/
            <a href="{{$character}}/CourseList">Courses</a>/
            <span class="now_page">Add Course</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="col-md-12">
        <div class="details" style="padding: 10px 0px 5px 0px;">
            <p class="page_title" style="position: relative;left: 5px;">Add New Course Details</p>
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
                      <Strong>{!!\Session::get('failed')!!}</Strong>
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    @endif
                    <div id="error-message"></div>
                        <form method="post" action="{{route('course.submit')}}" id="myForm">
                        {{csrf_field()}}
                        <div class="row">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-briefcase" aria-hidden="true" style="font-size: 17px;"></i>
                                </p>
                            </div>
                            <div class="col-11" style="padding-left: 20px;">
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
                            <div class="col-11" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="Programme" class="label">Subjects </label>
                                    <select class="selectpicker form-control" name="subject" data-width="100%" title="Choose one" data-live-search="true" id="subject" required disabled>

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
                            <div class="col-11" style="padding-left: 20px;">
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

                        <div class="row" id="form_credit">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-usd" aria-hidden="true" style="font-size: 20px;"></i>
                                </p>
                            </div>
                            <div class="col-11" style="padding-left: 20px;">
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1" class="bmd-label-floating">Credit Value</label>
                                            <input type="number" name="credit" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row" id="form_tch" style="margin-top:15px;">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-tags" aria-hidden="true" style="font-size: 18px;"></i>
                                </p>
                            </div>
                            <div class="col-11" style="padding-left: 25px;">
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

                        <div class="row" id="form_lct" style="margin-top:17px;">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-user" aria-hidden="true" style="font-size: 20px;"></i>
                                </p>
                            </div>
                            <div class="col-11" style="padding-left: 20px;">
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


                        <div class="row" id="form_other_lct" style="margin-top:17px;">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-user" aria-hidden="true" style="font-size: 20px;"></i>
                                </p>
                            </div>
                            <div class="col-11" style="padding-left: 20px;">
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


                        <div class="row" id="form_moderator">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-user" aria-hidden="true" style="font-size: 20px;"></i>
                                </p>
                            </div>
                            <div class="col-11" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="full_name" class="label">Moderator</label>
                                    <select class="selectpicker form-control" data-width="100%" title="Choose One" data-live-search="true" name="moderator" id="moderator" required>
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="row" id="form_hod">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-user" aria-hidden="true" style="font-size: 20px;"></i>
                                </p>
                            </div>
                            <div class="col-11" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="full_name" class="label">Verified By</label>
                                    <select class="selectpicker form-control" data-width="100%" title="Choose One" data-live-search="true" name="verified_by" required>
                                        @foreach($faculty as $row_faculty)
                                        <optgroup label="{{ $row_faculty['faculty_name']}}">
                                            @foreach($reviewer as $row)
                                                @if($row_faculty['faculty_id']==$row->faculty_id)
                                                    @if($row->position=="HoD")
                                                        <option value="{{$row->id}}" class="option-group">{{$row->position}} : {{$row->name}} ({{$row->staff_id}})</option>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="form_dean">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-user" aria-hidden="true" style="font-size: 20px;"></i>
                                </p>
                            </div>
                            <div class="col-11" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="full_name" class="label">Approved By</label>
                                    <select class="selectpicker form-control" data-width="100%" title="Choose One" data-live-search="true" name="approved_by" required>
                                        @foreach($faculty as $row_faculty)
                                        <optgroup label="{{ $row_faculty['faculty_name']}}">
                                            @foreach($reviewer as $row)
                                                @if($row_faculty['faculty_id']==$row->faculty_id)
                                                    @if($row->position=="Dean")
                                                        <option value="{{$row->id}}" class="option-group">{{$row->position}} : {{$row->name}} ({{$row->staff_id}})</option>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="lecturer" id="lecturer">
                        <hr style="background-color: black;margin: 0px;">
                        <p class="page_title" style="position: relative;left: 5px;">Timetable <button type="button" id="add" class="btn btn-raised btn-primary" style="margin: 5px 5px 0px 0px;padding:5px 10px;float:right;"><i class="fa fa-plus" aria-hidden="true" style="font-size: 18px;position: relative;top: 3px;"></i></button></p>
                        <div id="timetable">
                            <div id="class1">
                                <div class="row">
                                    <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                        <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                            <i class="fa fa-clock-o" aria-hidden="true" style="font-size: 20px;"></i>
                                        </p>
                                    </div>
                                    <div class="col-4" style="padding-left: 20px;">
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1" class="label">Week</label>
                                                    <select class="selectpicker form-control" name="week1" data-width="100%" title="Choose One" required>
                                                        <option class="option">Monday</option>
                                                        <option class="option">Tuesday</option>
                                                        <option class="option">Wednesday</option>
                                                        <option class="option">Thursday</option>
                                                        <option class="option">Friday</option>
                                                        <option class="option">Saturday</option>
                                                        <option class="option">Sunday</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-2" style="padding-left: 20px;">
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1" class="label">Start Hour</label>
                                                    <select class="selectpicker form-control" name="s_hour1" data-width="100%" title="Choose One" required>
                                                        <option class="option">0700</option>
                                                        <option class="option">0800</option>
                                                        <option class="option">0900</option>
                                                        <option class="option">1000</option>
                                                        <option class="option">1100</option>
                                                        <option class="option">1200</option>
                                                        <option class="option">1300</option>
                                                        <option class="option">1400</option>
                                                        <option class="option">1500</option>
                                                        <option class="option">1600</option>
                                                        <option class="option">1700</option>
                                                        <option class="option">1800</option>
                                                        <option class="option">1900</option>
                                                        <option class="option">2000</option>
                                                        <option class="option">2100</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-2" style="padding-left: 20px;">
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1" class="label">End Hour</label>
                                                    <select class="selectpicker form-control" name="e_hour1" data-width="100%" title="Choose One" required>
                                                        <option class="option">0800</option>
                                                        <option class="option">0900</option>
                                                        <option class="option">1000</option>
                                                        <option class="option">1100</option>
                                                        <option class="option">1200</option>
                                                        <option class="option">1300</option>
                                                        <option class="option">1400</option>
                                                        <option class="option">1500</option>
                                                        <option class="option">1600</option>
                                                        <option class="option">1700</option>
                                                        <option class="option">1800</option>
                                                        <option class="option">1900</option>
                                                        <option class="option">2000</option>
                                                        <option class="option">2100</option>
                                                        <option class="option">2200</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-3" style="padding-left: 20px;">
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1" class="label">Full Or Half</label>
                                                    <select class="selectpicker form-control" name="hORf1" data-width="100%" title="Choose One" required>
                                                        <option class="option">Full</option>
                                                        <option class="option">Half</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="count" name="count" value="1">
                        <div class="form-group" style="text-align: right;margin: 0px!important;">
                            <input type="submit" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;margin: 0px!important;" value="Save Changes">
                        </div>
                    </form>
                </div>
            </div>
</div>
@endsection