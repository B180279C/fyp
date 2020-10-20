<?php
$title = "Edit Student";
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
        $('#edit_image').click(function(){
            if(confirm("Do you sure want to remove this?")){
                var value = $('#id').val();
                var image = $('#image').val();
                $.ajax({
                   type:'POST',
                   url:'/studentRemoveImage',
                   data:{value:value,image:image},
                   success:function(data){
                        document.getElementById('form_image').style.display = "none";      
                        document.getElementById('dropzoneForm').style.display = "block";
                   }
                });
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
<div style="background-color: white;">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">Edit Student Information</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/student_list">Student </a>/
            <span class="now_page">Edit Student</span>/
        </p>
        <hr style="margin: 0px 10px;">
    </div>
    <div class="row" style="padding: 10px 10px 0px 10px;">
        <div class="col-md-4">
            <div class="img">
                <h5 style="color: #0d2f81;">Profile Image</h5>
                <hr style="margin: 0px;">
                <center>
                @if($student->student_image == "")
                    <form method="post" action="{{route('dropzone.uploadStudentImage')}}" enctype="multipart/form-data" class="dropzone" id="dropzoneForm" style="margin: 10px 0px 0px 0px;font-size: 20px;color:#a6a6a6;border-style: double;">
                    @csrf
                    <input type="hidden" name="student_id" class="dropzone_student_id">
                    <div class="dz-message" data-dz-message><span>Drop a Image in Here. After that click the Submit button to upload<br>(optional)</span></div>
                    </form>
                @else
                    <div style="margin: 50px 0px 20px 0px;" id="form_image">
                        <input type="hidden" id="image" value="{{$student->student_image}}">
                        <img src="{{ action('StudentController@show',$student->student_image) }}" width="auto" height="100px" style="border-radius:10%;" />
                        <br>
                        <p id="edit_image" style="font-size: 14px;color: #009697;">Remove file</a>
                    </div>
                    <form method="post" action="{{route('dropzone.uploadStudentImage')}}" enctype="multipart/form-data" class="dropzone" id="dropzoneForm" style="margin: 10px 0px 0px 0px;font-size: 20px;color:#a6a6a6;border-style: double;display: none;">
                    @csrf
                    <input type="hidden" name="student_id" class="dropzone_student_id">
                    <div class="dz-message" data-dz-message><span>Drop a Image in Here. After that click the Submit button to upload<br>(optional)</span></div>
                    </form>
                @endif
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

                    <form method="post" action="{{action('StudentController@update', $id)}}" id="details_form">{{csrf_field()}}
                    <input type="hidden" name="student_image" id="student_image" value="">
                    <input type="hidden" name="id" value="{{$id}}" id="id">

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
                                        <input type="text" name="student_id" class="form-control student_id" placeholder="" id="input" value="{{$student->student_id}}" required>
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
                                <i class="fa fa-user" aria-hidden="true" style="font-size: 20px;"></i>
                            </p>
                        </div>
                        <div class="col-10" style="padding-left: 20px;">
                            <div class="form-group">
                                <label for="full_name" class="bmd-label-floating">Name</label>
                                <input type="text" name="name" class="form-control full_name" id="input" required value="{{$user->name}}">
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
                                                @if($student->programme_id == $row->programme_id)
                                                    <option selected value="{{ $row->programme_id }}" class="option-group">{{$row->short_form_name}} : {{$row->programme_name}}</option>
                                                @else
                                                    <option value="{{ $row->programme_id }}" class="option-group">{{$row->short_form_name}} : {{$row->programme_name}}</option>
                                                @endif
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
                                                    <option <?php if($student->semester==$row_semester->semester_id){ echo "selected";}?> value="{{ $row_semester->semester_id }}" class="option">{{$row_semester->semester_name}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1" class="label">Intake</label>
                                        <select class="selectpicker form-control" name="intake" id="programme" data-width="100%" title="Choose One" required>
                                                <option <?php if($student->intake=="1"){ echo "selected";}?> value="1" class="option">First Year</option>
                                                <option <?php if($student->intake=="2"){ echo "selected";}?> value="2" class="option">Second Year</option>
                                        </select>
                                    </div>
                                </div>
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
   <!--  <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card">
                <div class="card-header">{{ __('Edit Student Information') }}</div>
                <div class="card-body">
                    @if(count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                   @if($error=="The programme must be a string.")
                                        <li>The programme cannot be empty.</li>
                                    @elseif($error=="The year must be a string.")
                                        <li>The year cannot be empty.</li>
                                    @elseif($error=="The semester must be a string.")
                                        <li>The semester cannot be empty.</li>
                                    @elseif($error=="The intake must be a string.")
                                        <li>The intake cannot be empty.</li>
                                    @else
                                        <li>{{$error}}</li>
                                    @endif
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

                        <form method="post" action="{{action('StudentController@update', $id)}}">
                        {{csrf_field()}}
                        <div class="form-group row">
                            <label for="student_id" class="col-md-4 col-form-label text-md-right">{{ __('Email : ') }}</label>
                            <div class="col-md-4">
                                <input type="text" name="student_id" class="form-control" placeholder="Student ID" value="{{$student->student_id}}">
                            </div>
                            <span class="col-md-4 col-form-label text-md-left">@sc.edu.my</span>
                        </div>
                        <hr>
                        <input type="hidden" name="_method" value="post" />
                        <div class="form-group row">
                            <label for="full_name" class="col-md-4 col-form-label text-md-right">{{ __('Name : ') }}</label>
                            <div class="col-md-6">
                                <input type="text" name="name" value="{{$user->name}}" class="form-control" placeholder="Full Name">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="programme" class="col-md-4 col-form-label text-md-right">{{ __('Programme : ') }}</label>
                            <div class="col-md-6">
                                <select class="selectpicker" name="programme" id="programme" data-width="100%" title="Choose one" data-live-search="true">
                                    @foreach($faculty as $row_faculty)
                                    <optgroup label="{{ $row_faculty['faculty_name']}}">
                                        @foreach($programme as $row)
                                            @if($row_faculty['faculty_id']==$row->faculty_id)
                                                @if($student->programme_id == $row->programme_id)
                                                    <option selected value="{{ $row->programme_id }}">{{$row->short_form_name}} : {{$row->programme_name}}</option>
                                                @else
                                                    <option value="{{ $row->programme_id }}">{{$row->short_form_name}} : {{$row->programme_name}}</option>
                                                @endif
                                            @endif
                                        @endforeach
                                    </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="full_name" class="col-md-4 col-form-label text-md-right">{{ __('Year/Intake : ') }}</label>
                            <div class="col-md-2">
                                <select class="selectpicker" name="year" id="programme" data-width="100%" title="Year">
                                    <option <?php if($student->year==(date('y')-5)){ echo "selected";}?> value="<?php echo date('y')-5?>"><?php echo date('Y')-5?></option>
                                    <option <?php if($student->year==(date('y')-4)){ echo "selected";}?> value="<?php echo date('y')-4?>"><?php echo date('Y')-4?></option>
                                    <option <?php if($student->year==(date('y')-3)){ echo "selected";}?> value="<?php echo date('y')-3?>"><?php echo date('Y')-3?></option>
                                    <option <?php if($student->year==(date('y')-2)){ echo "selected";}?> value="<?php echo date('y')-2?>"><?php echo date('Y')-2?></option>
                                    <option <?php if($student->year==(date('y')-1)){ echo "selected";}?> value="<?php echo date('y')-1?>"><?php echo date('Y')-1?></option>
                                    <option <?php if($student->year==date('y')){ echo "selected";}?> value="<?php echo date('y')?>"><?php echo date('Y')?></option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="selectpicker" name="semester" id="programme" data-width="100%" title="Semester">
                                    <option <?php if($student->semester=="A"){ echo "selected";}?> value="A">Semester 1</option>
                                    <option <?php if($student->semester=="B"){ echo "selected";}?> value="B">Semester 2</option>
                                    <option <?php if($student->semester=="C"){ echo "selected";}?> value="C">Semester 3</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="selectpicker" name="intake" id="programme" data-width="100%" title="Intake">
                                    <option <?php if($student->intake=="1"){ echo "selected";}?> value="1">First Year</option>
                                    <option <?php if($student->intake=="2"){ echo "selected";}?> value="2">Second Year</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-8 offset-md-4">
                                <input type="submit" class="btn btn-warning" value="Edit">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> -->
@endsection
