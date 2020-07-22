<?php
$title = "Add Staff";
$option1 = "id='selected-sidebar'";
?>
@extends('layouts.nav')

@section('content')
<script type="text/javascript">
    $(function () {
        $("#form_dep").hide();
        $('#department').prop('disabled', true);
        $('#department').selectpicker('refresh');
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        
        $('#faculty').change(function(){
            var value = $('#faculty').val();
            $.ajax({
               type:'POST',
               url:'/staffFaculty',
               data:{value:value},

               success:function(data){
                    if(data!="null"){
                        $("#department").html(data);
                        $('#department').prop('disabled', false);
                        $('#department').selectpicker('refresh');
                        $("#form_dep").show();
                    }else{
                        $("#form_dep").hide();
                    }
               }
            });
        });
        // $('.staff_id').keyup(function(){
        //     var value = $('.staff_id').val();
        //     $.ajax({
        //        type:'POST',
        //        url:'/checkStaffID',
        //        data:{value:value},
        //        success:function(data){
        //             if(data=="true"){
        //                 $("#check").val("true");
        //             }else{
        //                 $("#check").val("false");
        //             }
        //        }
        //     });
        // });
    });

    function myFunction() {
        if(($(".full_name").val()!="")&&($(".staff_id").val()!="")){
            $("#form_image").hide();
            $("#form_CV").hide();
            $("#dropzoneForm").show();
            $("#dropzoneCV").show();
        }else{
            $("#form_image").show();
            $("#form_CV").show();
            $("#dropzoneForm").hide();
            $("#dropzoneCV").hide();
        }
    }
    // function check_password(){
    //     var c_password =  $("#password-confirm").val();
    //     var password = $(".password").val();
    //     if(password == c_password){
    //         $("#check").val("true");
    //     }else{
    //         $("#check").val("false");
    //     }
    // }
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
            var name = $('.full_name').val();
            var staff_id = $('.staff_id').val();
            var re = /(?:\.([^.]+))?$/;
            var ext = re.exec(file.name)[1];
            var filename = name+"_"+staff_id+"_Image"+"."+ext;
            $("#staff_image").val(filename);
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
                url: '{{ url("/staffDestoryImage") }}',
                data: {filename: name},
                success: function (data){
                    console.log("File has been successfully removed!!");
                    $("#staff_image").val("");
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
    Dropzone.options.dropzoneCV =
    {
        maxFiles:1,
        acceptedFiles: ".pdf,.xlsx,.docx",
        addRemoveLinks: true,
        timeout: 50000,
        init: function() {
            this.on("maxfilesexceeded", function(file) {
                    this.removeAllFiles();
                    this.addFile(file);
            });
        },
        accept: function(file, done) {
            switch (file.type) {
              case 'application/pdf':
                $(file.previewElement).find(".dz-image img").attr("src", "{{url('image/pdf.png')}}");
                break;
              case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                $(file.previewElement).find(".dz-image img").attr("src", "{{url('image/docs.png')}}");
                break;
              case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                $(file.previewElement).find(".dz-image img").attr("src", "{{url('image/excel.png')}}");
                 break;
            }
            done();
        },
        renameFile: function(file) {
            var name = $('.full_name').val();
            var staff_id = $('.staff_id').val();
            var re = /(?:\.([^.]+))?$/;
            var ext = re.exec(file.name)[1];
            var filename = name+"_"+staff_id+"_CV"+"."+ext;
            $("#staff_CV").val(filename);
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
                url: '{{ url("/staffDestoryCV") }}',
                data: {filename: name},
                success: function (data){
                    console.log("File has been successfully removed!!");
                    $("#staff_CV").val("");
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

<div style="background-color: #f2f2f2">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">Add New Staff</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/staff_list">Staff </a>/
            <span class="now_page">Add Staff</span>/
        </p>
        <hr style="margin: 0px 10px;">
    </div>
    <div class="row" style="padding: 10px 10px 0px 10px;">
        <div class="col-md-4">
            <div class="img">
                <h5 style="color: #0d2f81;">Profile Image</h5>
                <hr style="margin: 0px;">
                <center>
                <form method="post" action="{{route('dropzone.uploadStaffImage')}}" enctype="multipart/form-data"
                             class="dropzone" id="dropzoneForm" style="margin: 10px 0px 0px 0px;font-size: 20px;color:#a6a6a6;border-style: double;display: none;">
                    @csrf
                    <input type="hidden" name="staff_id" class="dropzone_staff_id">
                    <div class="dz-message" data-dz-message><span>Drop a Image in Here to Upload<br>(optional)</span></div>
                </form>
                <div id="form_image" style="border-style: double;padding: 50px 20px;font-size: 20px;color:#a6a6a6;">
                    <p class="word">Disable to upload Image.<br>Please fill in the Name and Staff ID first.</p>
                </div>
                </center>
            </div>
            <hr>
            <div class="CV">
                <h5 style="color: #0d2f81;">Staff CV</h5>
                <hr style="margin: 0px;">
                <center>
                <form method="post" action="{{route('dropzone.uploadStaffCV')}}" enctype="multipart/form-data"
                                class="dropzone" id="dropzoneCV" style="margin: 10px 0px 0px 0px;font-size: 20px;color:#a6a6a6;border-style: double;display:none;">
                    @csrf
                    <input type="hidden" name="staff_id" class="dropzone_staff_id">
                    <div class="dz-message" data-dz-message><span>Drop a File in Here to Upload<br>(optional)</span></div>
                </form>
                <div id="form_CV" style="border-style: double;padding: 50px 20px;font-size: 20px;color:#a6a6a6;">
                    <p class="word">Disable to upload CV.<br>Please fill in the Name and Staff ID first.</p>
                </div>
                </center>
            </div>
            <hr>
        </div>
        <div class="col-md-8" >
            <div id="box" class="details" style="padding-bottom: 0px;">
                <h5 style="color: #0d2f81;">Staff Details</h5>
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

                    <form method="post" action="{{route('staff.submit')}}" id="details_form">                    {{csrf_field()}}
                    <input type="hidden" name="staff_image" id="staff_image" value="">
                    <input type="hidden" name="staff_CV" id="staff_CV" value="">
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
                                <label for="Position" class="label">Position</label>
                                <select class="selectpicker form-control" name="position" id="position" data-width="100%" title="Choose one" required>
                                    <option value="Teacher" class="option">Teacher</option>
                                    <option value="HoD" class="option">Head of Department</option>
                                    <option value="Dean" class="option">Dean</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                            <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                <i class="fa fa-home" aria-hidden="true" style="font-size: 20px;"></i>
                            </p>
                        </div>
                        <div class="col-10" style="padding-left: 20px;">
                            <div class="form-group">
                                <label for="faculty" class="label">{{ __('Faculty') }}</label>
                                <select class="selectpicker form-control" name="faculty" id="faculty" data-width="100%" title="Choose one" required>
                                    @foreach($faculty as $row)
                                        <option value="{{ $row['faculty_id'] }}" class="option">{{$row['faculty_name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    
                    

                    <div class="row">
                        <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                            <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                <i class="fa fa-address-book" aria-hidden="true" style="font-size: 17px;padding-left: 1px;"></i>
                            </p>
                        </div>
                        <div class="col-10" style="padding-left: 20px;">
                            <div class="form-group">
                                <label for="department" class="label">{{ __('Department ') }}</label>
                                <select class="selectpicker form-control" name="department" data-width="100%" title="Choose one" data-live-search="true" id="department" required>
                                </select>
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
                                        <label for="exampleInputEmail1" class="bmd-label-floating">Staff ID</label>
                                        <input type="text" name="staff_id" class="form-control staff_id" placeholder="" id="input" required onkeyup="myFunction()">
                                        
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