<?php
    $title = "Home";
    if(auth()->user()->position=="student"){
        $layout = 'layouts.nav_student';
        $cha2 = "student.";
    }
?>
@extends($layout)

@section('content')
<script type="text/javascript">
    $(function () {
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });

        $('#edit_image').click(function(){
            if(confirm("Do you sure want to remove this?")){
            	$('#form_image').hide();
            	$('#dropzoneForm').show();
            }
        });
        $('#edit_CV').click(function(){
            if(confirm("Do you sure want to remove this?")){
            	$('#form_CV').hide();
            	$('#dropzoneCV').show();
            }
        });
        $('#edit_Sign').click(function(){
            if(confirm("Do you sure want to remove this?")){
            	$('#form_sign').hide();
            	$('#dropzoneSign').show();
            }
        });
    });
    
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
                    url: '{{ url($character."/staffDestoryImage") }}',
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
</script>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">Profile</p>
        <p class="pass_page">
            <a href="{{$character}}/home" class="first_page"> Home </a>/
            <span class="now_page">Profile</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 0px 10px;">
        <div class="col-md-4">
            <div class="img">
                <p class="page_title" style="position: relative;left: 0px ;top: -5px;">Profile Image</p>
                <center>
                    @if($student->student_image == "")
                    <form method="post" action="{{route($cha2.'dropzone.uploadStaffImage')}}" enctype="multipart/form-data" class="dropzone" id="dropzoneForm" style="margin: 10px 0px 0px 0px;font-size: 20px;color:#a6a6a6;border-style: double;">
                    {{csrf_field()}}
                    <div class="dz-message" data-dz-message><span>Drop a Image in Here. After that click the Submit button to upload<br>(optional)</span></div>
                    </form>
                    @else
                        <div style="margin:20px 0px 20px 0px;" id="form_image">
                            <input type="hidden" id="image" value="{{$student->student_image}}">
                            <img src="{{$character}}/images/profile/{{$student->student_image}}" width="auto" height="100px" style="border-radius:10%;" />
                            <br>
                            <p id="edit_image" style="font-size: 14px;color: #009697;">Remove file</a>
                        </div>
                        <form method="post" action="{{route($cha2.'dropzone.uploadStaffImage')}}" enctype="multipart/form-data" class="dropzone" id="dropzoneForm" style="margin: 10px 0px 0px 0px;font-size: 20px;color:#a6a6a6;border-style: double;display: none;">
                        {{csrf_field()}}
                        <div class="dz-message" data-dz-message><span>Drop a Image in Here. After that click the Submit button to upload<br>(optional)</span></div>
                        </form>
                    @endif
                </center>
            </div>
            <hr>
        </div>
        <div class="col-md-8" >
            <div id="box" class="details" style="padding-bottom: 0px;">
                <p class="page_title" style="position: relative;left: -5px ;top: 0px;">Details</p>
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

                    <form method="post" action="{{$character}}/profile/store" id="form">
                        <input type="hidden" name="student_image" id="staff_image" value="">
                        {{csrf_field()}}
                        <input type="hidden" name="id" value="{{$student->student_id}}">
						<div class="row">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-briefcase" aria-hidden="true" style="font-size: 17px;"></i>
                                </p>
                            </div>
                            <div class="col-10" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="Programme" class="label">Programme</label>
                                    <input type="text" name="programme" value="{{$programme->programme_name}}" class="form-control" readonly>
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
                                            <input type="text" name="semester" value="{{$semester->semester_name}}" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1" class="label">Intake</label>
                                            <?php
                                            if($student->intake=="1"){
                                                $intake = "First Year";
                                            }else{
                                                $intake = "Second Year";
                                            }
                                            ?>
                                            <input type="text" name="semester" value="{{$intake}}" class="form-control" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr style="margin: 0px;">
                        <br/>
                        <p class="page_title" style="position: relative;left: -5px ;top: 0px;">Change Email & Password</p>
                        <div class="row">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-id-badge" aria-hidden="true" style="font-size: 18px;"></i>
                                </p>
                            </div>
                            <div class="col-11" style="padding-left: 20px;">
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1" class="bmd-label-floating">Student ID & Email</label>
                                            <input type="text" name="student_id" class="form-control" value="{{$student->student_id}}" required>
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
                        <input type="hidden" name="_method" value="post" />
                        <div class="row">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-user" aria-hidden="true" style="font-size: 20px;"></i>
                                </p>
                            </div>
                            <div class="col-11" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="full_name" class="bmd-label-floating">Name</label>
                                    <input type="text" name="name" value="{{$user->name}}" class="form-control full_name" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-key" aria-hidden="true" style="font-size: 20px;"></i>
                                </p>
                            </div>
                            <div class="col-11" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="full_name" class="bmd-label-floating">Current Password</label>
                                    <input type="password" name="current_password" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-key" aria-hidden="true" style="font-size: 20px;"></i>
                                </p>
                            </div>
                            <div class="col-11" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="full_name" class="bmd-label-floating">New Password</label>
                                    <input type="password" name="password" class="form-control password">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-key" aria-hidden="true" style="font-size: 20px;"></i>
                                </p>
                            </div>
                            <div class="col-11" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="full_name" class="bmd-label-floating">Confirm Password</label>
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation">
                                </div>
                            </div>
                        </div>
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