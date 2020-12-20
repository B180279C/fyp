<?php
    $title = "Home";
    // $option = "id='selected-sidebar'";
    if(auth()->user()->position=="Dean"){
        $layout = 'layouts.nav_dean';
        $cha2 = "";
    }else if(auth()->user()->position=="HoD"){
        $layout = 'layouts.nav_hod';
        $cha2 = "hod.";
    }else if(auth()->user()->position=="Lecturer"){
        $layout = 'layouts.nav_lecturer';
        $cha2 = "lecturer.";
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
            renameFile: function(file) {
                var re = /(?:\.([^.]+))?$/;
                var ext = re.exec(file.name)[1];
                var filename = new Date().getTime()+"."+ext;
                $("#staff_CV").val(filename);
                return filename;
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
            removedfile: function(file)
            {
                var name = file.upload.filename;
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: 'POST',
                    url: '{{ url($character."/staffDestoryCV") }}',
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
    Dropzone.options.dropzoneSign =
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
                $("#staff_Sign").val(filename);
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
                    url: '{{ url($character."/staffDestorySign") }}',
                    data: {filename: name},
                    success: function (data){
                        console.log("File has been successfully removed!!");
                        $("#staff_Sign").val("");
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
                    @if($staff->staff_image == "")
                    <form method="post" action="{{route($cha2.'dropzone.uploadStaffImage')}}" enctype="multipart/form-data" class="dropzone" id="dropzoneForm" style="margin: 10px 0px 0px 0px;font-size: 20px;color:#a6a6a6;border-style: double;">
                    {{csrf_field()}}
                    <div class="dz-message" data-dz-message><span>Drop a Image in Here. After that click the Submit button to upload<br>(optional)</span></div>
                    </form>
                    @else
                        <div style="margin:20px 0px 20px 0px;" id="form_image">
                            <input type="hidden" id="image" value="{{$staff->staff_image}}">
                            <img src="{{$character}}/images/profile/{{$staff->staff_image}}" width="auto" height="100px" style="border-radius:10%;" />
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
            <div class="CV">
                <p class="page_title" style="position: relative;left: 0px ;top: -10px;">Staff CV</p>
                <center>
                @if($staff->lecturer_CV == "")
                <form method="post" action="{{route($cha2.'dropzone.uploadStaffCV')}}" enctype="multipart/form-data"
                                class="dropzone" id="dropzoneCV" style="margin: 10px 0px 0px 0px;font-size: 20px;color:#a6a6a6;border-style: double;">
                    @csrf
                    <div class="dz-message" data-dz-message><span>Drop a File in Here. After that click the Submit button to upload<br>(optional)</span></div>
                </form>
                @else
                    <div style="margin: 0px 0px 20px 0px;" id="form_CV">
                            <input type="hidden" id="CV" value="{{$staff->lecturer_CV}}">
                            <?php
                            $ext = "";
                            if($staff->lecturer_CV!=""){
                                $ext = explode(".", $staff->lecturer_CV);
                            }
                            ?>
                            <a href="{{$character}}/profile/CV/{{$staff->staff_id}}" id="download_link">
                            <div id="download">
                            @if($ext[1]=="pdf")
                            <img src="{{url('image/pdf.png')}}" width="100px" height="100px" style="border-radius:10%;"/>
                            @elseif($ext[1]=="docx")
                            <img src="{{url('image/docs.png')}}" width="100px" height="100px" style="border-radius:10%;"/>
                            @else
                            <img src="{{url('image/excel.png')}}" width="100px" height="100px" style="border-radius:10%;"/>
                            @endif
                            <br>
                            <p style="text-align: center;">CV({{$staff->staff_id}})</p>
                            </div>
                            </a>
                            <p id="edit_CV" style="font-size: 14px;color: #009697;">Remove file</a>
                    </div>
                    <form method="post" action="{{route($cha2.'dropzone.uploadStaffCV')}}" enctype="multipart/form-data"
                                class="dropzone" id="dropzoneCV" style="margin: 10px 0px 0px 0px;font-size: 20px;color:#a6a6a6;border-style: double;display:none;">
                        @csrf
                        <!-- <input type="hidden" name="staff_id" value=""> -->
                        <div class="dz-message" data-dz-message><span>Drop a File in Here. After that click the Submit button to upload<br>(optional)</span></div>
                    </form>
                @endif
                </center>
            </div>
            <hr>
            <div class="img">
                <p class="page_title" style="position: relative;left: 0px ;top: -15px;">Signature</p>
                <center>
              		@if($staff->staff_sign == "")
                    <form method="post" action="{{route($cha2.'dropzone.uploadStaffSign')}}" enctype="multipart/form-data" class="dropzone" id="dropzoneSign" style="margin: 10px 0px 0px 0px;font-size: 20px;color:#a6a6a6;border-style: double;">
                    {{csrf_field()}}
                    <div class="dz-message" data-dz-message><span>Drop a Image in Here. After that click the Submit button to upload<br>(optional)</span></div>
                    </form>
                    @else
                        <div style="margin: 0px 0px 20px 0px;" id="form_sign">
                            <input type="hidden" id="image" value="{{$staff->staff_sign}}">
                            <img src="{{$character}}/sign/profile/{{$staff->staff_sign}}" width="auto" height="100px" style="border-radius:10%;" />
                            <br>
                            <p id="edit_Sign" style="font-size: 14px;color: #009697;">Remove file</a>
                        </div>
                        <form method="post" action="{{route($cha2.'dropzone.uploadStaffSign')}}" enctype="multipart/form-data" class="dropzone" id="dropzoneSign" style="margin: 10px 0px 0px 0px;font-size: 20px;color:#a6a6a6;border-style: double;display: none;">
                        {{csrf_field()}}
                        <div class="dz-message" data-dz-message><span>Drop a Image in Here. After that click the Submit button to upload<br>(optional)</span></div>
                        </form>
                    @endif
                </center>
            </div>
            <hr>
        </div>
        <div class="col-md-8">
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
                        <input type="hidden" name="staff_image" id="staff_image" value="">
                        <input type="hidden" name="staff_CV" id="staff_CV" value="">
                        <input type="hidden" name="staff_Sign" id="staff_Sign" value="">
                        {{csrf_field()}}
                        <input type="hidden" name="id" value="{{$staff->staff_id}}">
							<div class="row">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-briefcase" aria-hidden="true" style="font-size: 17px;"></i>
                                </p>
                            </div>
                            <div class="col-11" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="Position" class="label">Position</label>
                                    <?php
                                    $position = "";
                                    if($user->position=="HoD"){
                                        $position = "Head Of Department";
                                    }else{
                                        $position = $user->position;
                                    }
                                    ?>
                                    <input type="text" name="position" value="{{$position}}" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-home" aria-hidden="true" style="font-size: 20px;"></i>
                                </p>
                            </div>
                            <div class="col-11" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="faculty" class="label">{{ __('Faculty') }}</label>
                                    <input type="text" name="faculty" value="{{$faculty->faculty_name}}" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-address-book" aria-hidden="true" style="font-size: 17px;padding-left: 1px;"></i>
                                </p>
                            </div>
                            <div class="col-11" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="department" class="label">{{ __('Department ') }}</label>
                                    <input type="text" name="department" value="{{$department->department_name}}" class="form-control" readonly>
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
                                            <label for="exampleInputEmail1" class="bmd-label-floating">Staff ID & Email</label>
                                            <input type="text" name="staff_id" class="form-control" value="{{$staff->staff_id}}" required>
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