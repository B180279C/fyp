<?php
$title = "CoursePortFolio";
$option5 = "id='selected-sidebar'";
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
    });

    function checkLCT_MOD(){
        var lecturer = $('#lecturer').val();
        var moderator = $('#moderator').val();
        if(lecturer==moderator){
            document.getElementById('error-message').innerHTML = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><Strong>The lecturer and moderator cannot be same.</Strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }else{
            document.getElementById("myForm").submit();
        }
    }
</script>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$faculty_name->faculty_name}}</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/CourseList">Courses </a>/
            <span class="now_page">Edit Course</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="col-md-12">
        <div class="details" style="padding: 10px 0px 5px 0px;">
            <p class="page_title" style="position: relative;left: 5px;">Edit Course Information</p>
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

                    <div id="error-message"></div>
                        <form method="post" action="{{action('Dean\CourseController@update', $id)}}" id="myForm">
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
                                                    <option <?php if($row->programme_id==$course[0]->programme_id){echo "selected";} ?> value="{{ $row->programme_id }}" class="option-group">{{$row->short_form_name}} : {{$row->programme_name}}</option>
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
                                    <select class="selectpicker form-control" name="subject" data-width="100%" title="Choose one" data-live-search="true" id="subject" required>
                                        @foreach($group as $row_group)
                                            <optgroup label='{{$row_group->subject_type}}'>
                                                @foreach($subjects as $row)
                                                    @if($row_group->subject_type == $row->subject_type)
                                                        <option <?php if($row->subject_id==$course[0]->subject_id){echo "selected";} ?> value="{{$row->subject_id}}" class='option-group'>{{$row->subject_code}} : {{$row->subject_name}}</option>
                                                    @endif
                                                @endforeach
                                            </optgroup>
                                        @endforeach
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
                                                        <option <?php if($row_semester->semester_id==$course[0]->semester){echo "Selected";} ?> value="{{ $row_semester->semester_id }}" class="option">{{$row_semester->semester_name}}</option>
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
                                    <i class="fa fa-clock-o" aria-hidden="true" style="font-size: 20px;"></i>
                                </p>
                            </div>
                            <div class="col-11" style="padding-left: 20px;">
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1" class="bmd-label-floating">Credit Value</label>
                                            <input type="number" name="credit" class="form-control" value="{{$course[0]->credit}}">
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
                                                  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1" <?php if($count_staff_InFaculty>0){ echo "checked";}?>>
                                                  <label class="form-check-label" for="inlineRadio1">Own Faculty</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2" <?php if($count_staff_InFaculty===0){ echo "checked";}?>>
                                                  <label class="form-check-label" for="inlineRadio2">Other Faculty</label>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($count_staff_InFaculty>0)
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
                                        <option <?php if($row_staff->id==$course[0]->lecturer){ echo "selected"; }?> value="{{$row_staff->id}}" class="option">{{$row_staff->name}} ({{$row_staff->staff_id}})</option>
                                    @endforeach
                                </select>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="form_other_lct" style="display: none;margin-top:17px;">
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
                                                    <option <?php if($row->id==$course[0]->lecturer){ echo "selected"; }?> value="{{$row->id}}" class="option-group">{{$row->name}} ({{$row->staff_id}})</option>
                                                @endif
                                            @endforeach
                                        </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        @else
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
                                                    <option <?php if($row->id==$course[0]->lecturer){ echo "selected"; }?> value="{{$row->id}}" class="option-group">{{$row->name}} ({{$row->staff_id}})</option>
                                                @endif
                                            @endforeach
                                        </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="form_lct" style="display: none;margin-top:17px;">
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
                                        <option <?php if($row_staff->id==$course[0]->lecturer){ echo "selected"; }?> value="{{$row_staff->id}}" class="option">{{$row_staff->name}} ({{$row_staff->staff_id}})</option>
                                    @endforeach
                                </select>
                                </div>
                            </div>
                        </div>

                        @endif

                        <div class="row" id="form_moderator">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-user" aria-hidden="true" style="font-size: 20px;"></i>
                                </p>
                            </div>
                            <div class="col-11" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="full_name" class="label">Moderator</label>
                                    <select class="selectpicker form-control" data-width="100%" title="Choose One" data-live-search="true" name="moderator" id="moderator">
                                        @foreach($faculty as $row_faculty)
                                        <optgroup label="{{ $row_faculty['faculty_name']}}">
                                            @foreach($moderator as $row)
                                                @if($row_faculty['faculty_id']==$row->faculty_id)
                                                    <option <?php if($row->id==$course[0]->moderator){ echo "selected"; }?> value="{{$row->id}}" class="option-group">{{$row->name}} ({{$row->staff_id}})</option>
                                                @endif
                                            @endforeach
                                        </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="row" id="form_hod">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-user" aria-hidden="true" style="font-size: 20px;"></i>
                                </p>
                            </div>
                            <div class="col-11" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="full_name" class="label">Verified By</label>
                                    <select class="selectpicker form-control" data-width="100%" title="Choose One" data-live-search="true" name="verified_by" id="reviewer">
                                        @foreach($faculty as $row_faculty)
                                        <optgroup label="{{ $row_faculty['faculty_name']}}">
                                            @foreach($reviewer as $row)
                                                @if($row_faculty['faculty_id']==$row->faculty_id)
                                                    <option <?php if($row->id==$course[0]->verified_by){ echo "selected"; }?> value="{{$row->id}}" class="option-group">{{$row->position}} : {{$row->name}} ({{$row->staff_id}})</option>
                                                @endif
                                            @endforeach
                                        </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div> -->

                        <!-- <div class="row" id="form_dean">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-user" aria-hidden="true" style="font-size: 20px;"></i>
                                </p>
                            </div>
                            <div class="col-11" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="full_name" class="label">Approved By</label>
                                    <select class="selectpicker form-control" data-width="100%" title="Choose One" data-live-search="true" name="approved_by" id="reviewer">
                                        @foreach($faculty as $row_faculty)
                                        <optgroup label="{{ $row_faculty['faculty_name']}}">
                                            @foreach($reviewer as $row)
                                                @if($row_faculty['faculty_id']==$row->faculty_id)
                                                    @if($row->position=="Dean")
                                                        <option <?php if($row->id==$course[0]->approved_by){ echo "selected"; }?> value="{{$row->id}}" class="option-group">{{$row->position}} : {{$row->name}} ({{$row->staff_id}})</option>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div> -->
                        <input type="hidden" name="lecturer" id="lecturer" value="{{$course[0]->lecturer}}">
                        <hr>
                        <div class="form-group" style="text-align: right;margin: 0px!important;">
                            <input type="button" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;margin: 0px!important;" onclick="checkLCT_MOD();" value="Save Changes">
                        </div>
                    </form>
                </div>
            </div>
</div>
@endsection