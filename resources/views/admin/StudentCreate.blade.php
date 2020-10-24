<?php
$title = "Add Student";
$option2 = "id='selected-sidebar'";
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
    });
    function myFunction() {
        if(($(".full_name").val()!="")&&($(".student_id").val()!="")){
            $("#form_image").hide();
            $("#dropzoneForm").show();
        }else{
            $("#form_image").show();
            $("#dropzoneForm").hide();
        }
    }
    Dropzone.options.dropzoneForm =
    {
        maxFiles:1,
        acceptedFiles: ".jpeg,.jpg,.png,.gif",
        addRemoveLinks: true,
        timeout: 50000,
        init: function() {
            this.on("maxfilesexceeded", function(file) {
                    this.removeAllFiles();
                    this.addFile(file);
            });
        },
        renameFile: function(file) {
            var re = /(?:\.([^.]+))?$/;
            var ext = re.exec(file.name)[1];
            var filename = new Date().getTime()+"."+ext;
            $("#student_image").val(filename);
            return filename;
        },
        removedfile: function(file)
        {
            var name = file.upload.filename;
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                type: 'POST',
                url: '{{ url("/studentDestoryImage") }}',
                data: {filename: name},
                success: function (data){
                    console.log("File has been successfully removed!!");
                    $("#student_image").val("");
                },
                error: function(e) {
                    console.log(e);
                }
            });
            var fileRef;
            return (fileRef = file.previewElement) != null ? 
            fileRef.parentNode.removeChild(file.previewElement) : void 0;
        },
    };
</script>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">Add New Student</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/student_list">Student </a>/
            <span class="now_page">Add Student</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 0px 10px;">
        <div class="col-md-4">
            <div class="img">
                <h5 style="color: #0d2f81;">Profile Image</h5>
                <hr style="margin: 0px;">
                <center>
                <form method="post" action="{{route('dropzone.uploadStudentImage')}}" enctype="multipart/form-data"
                             class="dropzone" id="dropzoneForm" style="margin: 10px 0px 0px 0px;font-size: 20px;color:#a6a6a6;border-style: double;display: none;">
                    @csrf
                    <input type="hidden" name="student_id" class="dropzone_student_id">
                    <div class="dz-message" data-dz-message><span>Drop a Image in Here. After that click the Submit button to upload<br>(optional)</span></div>
                </form>
                <div id="form_image" style="border-style: double;padding: 50px 20px;font-size: 20px;color:#a6a6a6;">
                    <p class="word">Disable to upload Image.<br>Please fill in the Name and Student ID first.</p>
                </div>
                </center>
            </div>
            <hr>
        </div>
        <div class="col-md-8" >
            <div id="box" class="details" style="padding-bottom: 0px;">
                <h5 style="color: #0d2f81;">Student Details</h5>
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

                    <form method="post" action="{{ route('admin.student.submit') }}" id="details_form">                    {{csrf_field()}}
                    <input type="hidden" name="student_image" id="student_image" value="">
                    <div class="row">
                        <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                            <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                <i class="fa fa-user" aria-hidden="true" style="font-size: 20px;"></i>
                            </p>
                        </div>
                        <div class="col-10" style="padding-left: 20px;">
                            <div class="form-group">
                                <label for="full_name" class="bmd-label-floating">Name</label>
                                <input type="text" name="name" class="form-control full_name" id="input" required onkeyup="myFunction()">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                            <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                <i class="fa fa-briefcase" aria-hidden="true" style="font-size: 17px;"></i>
                            </p>
                        </div>
                        <div class="col-10" style="padding-left: 20px;">
                            <div class="form-group">
                                <label for="Programme" class="label">Programme</label>
                                <select class="selectpicker form-control" name="programme" id="programme" data-width="100%"data-live-search="true" title="Choose One" required >
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
                    
                    <div class="row">
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
                                <div class="col">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1" class="label">Intake</label>
                                        <select class="selectpicker form-control" name="intake" data-width="100%" title="Choose One" required>
                                                <option value="1" class="option">First Year</option>
                                                <option value="2" class="option">Second Year</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                            <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                <i class="fa fa-id-badge" aria-hidden="true" style="font-size: 18px;"></i>
                            </p>
                        </div>
                        <div class="col-10" style="padding-left: 20px;">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1" class="bmd-label-floating">Student ID</label>
                                        <input type="text" name="student_id" class="form-control student_id" placeholder="" id="input" required onkeyup="myFunction()">
                                    </div>
                                </div>
                                <div class="col align-self-end" style="padding: 0px;">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">@sc.edu.my</label></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                            <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                <i class="fa fa-lock" aria-hidden="true"></i>
                            </p>
                        </div>
                        <div class="col-10" style="padding-left: 20px;">
                            <div class="form-group">
                                <label for="exampleInputEmail1" class="bmd-label-floating">Password</label>
                                <input type="password" name="password" class="form-control password" id="input" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                            <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                <i class="fa fa-lock" aria-hidden="true"></i>
                            </p>
                        </div>
                        <div class="col-10" style="padding-left: 20px;">
                            <div class="form-group">
                                <label for="exampleInputEmail1" class="bmd-label-floating">Confirm Password</label>
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" style="color: black;" required onkeyup="check_password()">
                                <!-- <span class="bmd-help">Please enter again your correct pasword.</span> -->
                            </div>
                        </div>
                    </div>
<!--                     <input type="hidden" id="check" value=""> -->
                    <hr>
                    <div class="form-group">
                        <input type="submit" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;float:right;" id="button-submit">
                    </div>
            </form>
        </div>
    </div>
    
    </div>
    <hr>
</div>
@endsection
