<?php
$title = "Courses";
$option8 = "id='selected-sidebar'";
?>
@extends('layouts.nav')

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
               url:'/courses/courseSubject',
               data:{value:value},

               success:function(data){
                    $("#subject").html(data);
                    $('#subject').selectpicker('refresh');
                    $("#form_sub").show();
               }
            });
        });

        $('#lecturer2').change(function(){
            var value = $('#lecturer2').val();
            document.getElementById('lecturer').value = value;
            $.ajax({
                type:'POST',
                url:'/courses/changeModerator',
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
        $('#timetable').append('<input type="hidden" name="tt_id'+count+'" value="0"><div id="class'+count+'"><div class="row"><div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;"><p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;"><i class="fa fa-clock-o" aria-hidden="true" style="font-size: 20px;"></i></p></div><div class="col-4" style="padding-left: 20px;"><div class="row"><div class="col"><div class="form-group"><label for="exampleInputEmail1" class="label" style="font-size:12px;">Week</label><select class="selectpicker form-control" name="week'+count+'" data-width="100%" title="Choose One" required><option class="option">Monday</option><option class="option">Tuesday</option><option class="option">Wednesday</option><option class="option">Thursday</option><option class="option">Friday</option><option class="option">Saturday</option><option class="option">Sunday</option></select></div></div></div></div><div class="col-2" style="padding-left: 20px;"><div class="row"><div class="col"><div class="form-group"><label for="exampleInputEmail1" class="label" style="font-size:12px;">Start Hour</label><select class="selectpicker form-control" name="s_hour'+count+'" data-width="100%" title="Choose One" required><option class="option">0700</option><option class="option">0800</option><option class="option">0900</option><option class="option">1000</option><option class="option">1100</option><option class="option">1200</option><option class="option">1300</option><option class="option">1400</option><option class="option">1500</option><option class="option">1600</option><option class="option">1700</option><option class="option">1800</option><option class="option">1900</option><option class="option">2000</option><option class="option">2100</option></select></div></div></div></div><div class="col-2" style="padding-left: 20px;"><div class="row"><div class="col"><div class="form-group"><label for="exampleInputEmail1" class="label" style="font-size:12px;">End Hour</label><select class="selectpicker form-control" name="e_hour'+count+'" data-width="100%" title="Choose One" required><option class="option">0800</option><option class="option">0900</option><option class="option">1000</option><option class="option">1100</option><option class="option">1200</option><option class="option">1300</option><option class="option">1400</option><option class="option">1500</option><option class="option">1600</option><option class="option">1700</option><option class="option">1800</option><option class="option">1900</option><option class="option">2000</option><option class="option">2100</option><option class="option">2200</option></select></div></div></div></div><div class="col-2" style="padding-left: 20px;"><div class="row"><div class="col"><div class="form-group"><label for="exampleInputEmail1" class="label" style="font-size:12px;">H./F.</label><select class="selectpicker form-control" name="hORf'+count+'" data-width="100%" title="Choose One" required><option class="option">Half</option><option class="option">Full</option></select></div></div></div></div><div class="col-1 align-self-center"><button type="button" id="remove_'+count+'" class="btn btn-raised btn-danger btn_remove"><i class="fa fa-times" aria-hidden="true" style="font-size: 18px;position: relative;top: 1px;"></i></button></div>     </div></div>');
        $('.selectpicker').selectpicker();
        $('#count').val(count);
        });

        $(document).on('click', '.btn_remove', function(){  
           var button = $(this).attr("id");
           var id = button.split("remove_");   
           $('#class'+id[1]).remove();
        });

        $(document).on('click', '.remove_action', function(){
          var id = $(this).attr("id");
          var num = id.split("_");
          if(confirm('Are you sure you want to remove the it?')) {
            window.location = "/courses/timetable/remove/"+num[1];
          }
          return false;
        });
    });
</script>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">Edit Course Information</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/courses">Courses </a>/
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
                      <Strong>{!!\Session::get('failed')!!}</Strong>
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    @endif

                    <div id="error-message"></div>
                        <form method="post" action="/courses/{{$id}}" id="myForm">
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
                        
                        <div class="row" id="form_other_lct">
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
                                                    @if($row->id!=$course[0]->lecturer)
                                                        <option <?php if($row->id==$course[0]->moderator){ echo "selected"; }?> value="{{$row->id}}" class="option-group">{{$row->name}} ({{$row->staff_id}})</option>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </optgroup>
                                        @endforeach
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
                                    <select class="selectpicker form-control" data-width="100%" title="Choose One" data-live-search="true" name="verified_by" id="reviewer">
                                        @foreach($faculty as $row_faculty)
                                        <optgroup label="{{ $row_faculty['faculty_name']}}">
                                            @foreach($reviewer as $row)
                                                @if($row_faculty['faculty_id']==$row->faculty_id)
                                                    @if($row->position=="HoD")
                                                        <option <?php if($row->id==$course[0]->verified_by){ echo "selected"; }?> value="{{$row->id}}" class="option-group">{{$row->position}} : {{$row->name}} ({{$row->staff_id}})</option>
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
                        </div>
                        <input type="hidden" name="lecturer" id="lecturer" value="{{$course[0]->lecturer}}">
                        <hr style="background-color: black;margin: 0px;">
                        <p class="page_title" style="position: relative;left: 5px;">Timetable <button type="button" id="add" class="btn btn-raised btn-primary" style="margin: 5px 5px 0px 0px;padding:5px 10px;float:right;"><i class="fa fa-plus" aria-hidden="true" style="font-size: 18px;position: relative;top: 3px;"></i></button></p>
                        <div id="timetable">
                            <?php
                            $num = 1;
                            ?>
                            @foreach($timetable as $row)
                            <input type="hidden" name="tt_id{{$num}}" value="{{$row->tt_id}}">
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
                                                    <select class="selectpicker form-control" name="week{{$num}}" data-width="100%" title="Choose One" required>
                                                        <option class="option" <?php if($row->week=="Monday"){ echo "selected";}?>>Monday</option>
                                                        <option class="option" <?php if($row->week=="Tuesday"){ echo "selected";}?>>Tuesday</option>
                                                        <option class="option" <?php if($row->week=="Wednesday"){ echo "selected";}?>>Wednesday</option>
                                                        <option class="option" <?php if($row->week=="Thursday"){ echo "selected";}?>>Thursday</option>
                                                        <option class="option" <?php if($row->week=="Friday"){ echo "selected";}?>>Friday</option>
                                                        <option class="option" <?php if($row->week=="Saturday"){ echo "selected";}?>>Saturday</option>
                                                        <option class="option" <?php if($row->week=="Sunday"){ echo "selected";}?>>Sunday</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-2" style="padding-left: 20px;">
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <?php
                                                        $hour = explode(',',$row->class_hour);
                                                        $s_hour = explode('-',$hour[0]);
                                                    ?>
                                                    <label for="exampleInputEmail1" class="label">Start Hour</label>
                                                    <select class="selectpicker form-control" name="s_hour{{$num}}" data-width="100%" title="Choose One" required>
                                                        <option class="option" <?php if($s_hour[0]=="0700"){ echo "selected";}?>>0700</option>
                                                        <option class="option" <?php if($s_hour[0]=="0800"){ echo "selected";}?>>0800</option>
                                                        <option class="option" <?php if($s_hour[0]=="0900"){ echo "selected";}?>>0900</option>
                                                        <option class="option" <?php if($s_hour[0]=="1000"){ echo "selected";}?>>1000</option>
                                                        <option class="option" <?php if($s_hour[0]=="1100"){ echo "selected";}?>>1100</option>
                                                        <option class="option" <?php if($s_hour[0]=="1200"){ echo "selected";}?>>1200</option>
                                                        <option class="option" <?php if($s_hour[0]=="1300"){ echo "selected";}?>>1300</option>
                                                        <option class="option" <?php if($s_hour[0]=="1400"){ echo "selected";}?>>1400</option>
                                                        <option class="option" <?php if($s_hour[0]=="1500"){ echo "selected";}?>>1500</option>
                                                        <option class="option" <?php if($s_hour[0]=="1600"){ echo "selected";}?>>1600</option>
                                                        <option class="option" <?php if($s_hour[0]=="1700"){ echo "selected";}?>>1700</option>
                                                        <option class="option" <?php if($s_hour[0]=="1800"){ echo "selected";}?>>1800</option>
                                                        <option class="option" <?php if($s_hour[0]=="1900"){ echo "selected";}?>>1900</option>
                                                        <option class="option" <?php if($s_hour[0]=="2000"){ echo "selected";}?>>2000</option>
                                                        <option class="option" <?php if($s_hour[0]=="2100"){ echo "selected";}?>>2100</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-2" style="padding-left: 20px;">
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <?php
                                                        $hour = explode(',',$row->class_hour);
                                                        $e_hour = explode('-',$hour[count($hour)-1]);
                                                    ?>
                                                    <label for="exampleInputEmail1" class="label">End Hour</label>
                                                    <select class="selectpicker form-control" name="e_hour{{$num}}" data-width="100%" title="Choose One" required>
                                                        <option class="option" <?php if($e_hour[1]=="0800"){ echo "selected";}?>>0800</option>
                                                        <option class="option" <?php if($e_hour[1]=="0900"){ echo "selected";}?>>0900</option>
                                                        <option class="option" <?php if($e_hour[1]=="1000"){ echo "selected";}?>>1000</option>
                                                        <option class="option" <?php if($e_hour[1]=="1100"){ echo "selected";}?>>1100</option>
                                                        <option class="option" <?php if($e_hour[1]=="1200"){ echo "selected";}?>>1200</option>
                                                        <option class="option" <?php if($e_hour[1]=="1300"){ echo "selected";}?>>1300</option>
                                                        <option class="option" <?php if($e_hour[1]=="1400"){ echo "selected";}?>>1400</option>
                                                        <option class="option" <?php if($e_hour[1]=="1500"){ echo "selected";}?>>1500</option>
                                                        <option class="option" <?php if($e_hour[1]=="1600"){ echo "selected";}?>>1600</option>
                                                        <option class="option" <?php if($e_hour[1]=="1700"){ echo "selected";}?>>1700</option>
                                                        <option class="option" <?php if($e_hour[1]=="1800"){ echo "selected";}?>>1800</option>
                                                        <option class="option" <?php if($e_hour[1]=="1900"){ echo "selected";}?>>1900</option>
                                                        <option class="option" <?php if($e_hour[1]=="2000"){ echo "selected";}?>>2000</option>
                                                        <option class="option" <?php if($e_hour[1]=="2100"){ echo "selected";}?>>2100</option>
                                                        <option class="option" <?php if($e_hour[1]=="2200"){ echo "selected";}?>>2200</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-2" style="padding-left: 20px;">
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1" class="label">Full Or Half</label>
                                                    <select class="selectpicker form-control" name="hORf{{$num}}" data-width="100%" title="Choose One" required>
                                                        <option class="option" <?php if($row->F_or_H=="Full"){ echo "selected";}?>>Full</option>
                                                        <option class="option" <?php if($row->F_or_H=="Half"){ echo "selected";}?>>Half</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-1" style="margin-top: 25px;"><button type="button" id="remove_{{$row->tt_id}}" class="btn btn-raised btn-danger remove_action"><i class="fa fa-times" aria-hidden="true" style="font-size: 18px;position: relative;top: 1px;"></i></button></div>
                                </div>
                            </div>
                            <?php
                            $num++;
                            ?>
                            @endforeach
                            @if($num==1)
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
                            
                            @endif
                            @if($num==1)
                            <input type="hidden" id="count" name="count" value="1">
                            @else
                            <input type="hidden" id="count" name="count" value="{{$num-1}}">
                            @endif
                        </div>
                        <div class="form-group" style="text-align: right;margin: 0px!important;">
                            <input type="submit" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;margin: 0px!important;" value="Save Changes">
                        </div>
                    </form>
                </div>
            </div>
</div>
@endsection