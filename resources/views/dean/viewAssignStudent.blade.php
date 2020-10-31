<?php
$title = "Course";
$option1 = "id='selected-sidebar'";
?>
@extends('layouts.nav_dean')

@section('content')
<script type="text/javascript">
  function w3_open() {
      document.getElementById("action_sidebar").style.display = "block";
      document.getElementById("button_open").style.display = "none";
  }
  function w3_close() {
      document.getElementById("action_sidebar").style.display = "none";
      document.getElementById("button_open").style.display = "block";
  }
  $(document).ready(function(){
    $(document).on('click', '#open_model', function(){
              $('#openFolderModal').modal('show');
    });
    $(document).on('click', '#open_document', function(){   
      $('#openDocumentModal').modal('show');
    });

    $(document).on('click', '.remove_button', function(){
      var id = $(this).attr("id");
      var num = id.split("_");
      if(confirm('Are you sure you want to remove the it?')) {
        window.location = "/assignStudent/remove/"+num[2];
      }
      return false;
    });
  });

  $(function () {
        $('#showData').hide();
        $('#errorData').hide();
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        if($('.search').val()!=""){
          var value = $('.search').val();
          var course_id = $('#course_id').val();
          $.ajax({
              type:'POST',
              url:'/searchAssignStudent',
              data:{value:value,course_id:course_id},
              success:function(data){
                document.getElementById("assign_student").innerHTML = data;
              }
          });
        }
        $(".search").keyup(function(){
            var value = $('.search').val();
            var course_id = $('#course_id').val();
            $.ajax({
               type:'POST',
               url:'/searchAssignStudent',
               data:{value:value,course_id:course_id},
               success:function(data){
                    document.getElementById("assign_student").innerHTML = data;
               }
            });
        });
    });
    function showStudent(){
        var programme = $('#programme').val();
        var semester = $('#semester').val();
        var intake = $('#intake').val();
        if(programme!=""&&semester!=""&&intake!=""){
          $.ajax({
              type:'POST',
              url:'/showStudent',
              data:{programme:programme,semester:semester,intake:intake},
              success:function(data){
                $("#student").html(data);
                $('#student').selectpicker('refresh');
              }
          });
        }
      }

  Dropzone.options.dropzoneFile =
    {
        acceptedFiles: ".xlsx,xls",
        maxFiles:1,
        timeout: 50000,
        renameFile: function(file) {
            var re = /(?:\.([^.]+))?$/;
            var ext = re.exec(file.name)[1];
            var newName = new Date().getTime() +"___"+file.name;
            return newName;
        },
        init: function() {
            this.on("maxfilesexceeded", function(file) {
                    this.removeAllFiles();
                    this.addFile(file);
                    $(".tablebody").remove(); 
            });
            this.on("addedfile", function(file){
              var re = /(?:\.([^.]+))?$/;
              var ext = re.exec(file.upload.filename)[0];
              var filename = file.upload.filename.split(ext);
              var name_without_time = filename[0].split("___");
              file._captionLabel = Dropzone.createElement("<div class='changelabel'><label class='label' style='font-size:13px'>File Name</label></div>")
                  file._captionBox = Dropzone.createElement("<div class='changeName'><input id='syllabus' type='text' name='syllabus' value='"+name_without_time[1]+"' class='form-control filename'></div>");
              file.previewElement.appendChild(file._captionLabel);
              file.previewElement.appendChild(file._captionBox);
              $(".dz-remove").addClass("InModel");
              $(".dz-preview").addClass("dropzoneModel");
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
        success: function(file, response) {
            console.log(response);
            var table = document.getElementById("dtBasicExample");
            for(var i = 0; i<response.length; i++){
                if((response[i]['student_id']!=null)&&(response[i]['student_name']!=null)){
                  var row = table.insertRow(1+i);
                  var cell = row.insertCell(0);
                  var cell1 = row.insertCell(1);
                  var cell2 = row.insertCell(2);
                  cell.innerHTML  = (i+1);
                  cell1.innerHTML = response[i]['student_id'];
                  cell2.innerHTML = response[i]['student_name'];
                  cell.className  = 'tablebody';
                  cell1.className = 'tablebody';
                  cell2.className = 'tablebody';
                  $("#writeInput").append("<input type='hidden' id='student_id"+i+"' name='student_id"+i+"' value='"+response[i]['student_id']+"'><input type='hidden' id='student_name"+i+"' name='student_name"+i+"' value='"+response[i]['student_name']+"'>");
                  $('#showData').show();
                  $('#errorData').hide();
                }else{
                  $('#showData').hide();
                  $('#errorData').show();
                  break;
                }
          }
          $("#writeInput").append("<input type='hidden' name='count' value='"+(i-1)+"'>");
        },
        error: function(file, response) {
            console.log(response);
        }
    };
</script>
<style type="text/css">
.dropzoneModel{
  border-bottom: 1px solid black;
  padding-left: 0px;
  padding-top: 10px;
  padding-bottom: 10px!important;
  width: 95%;
}
.dropzone .dz-preview .dz-filename {
  display: none;
}
.dropzone .dz-preview .dz-size {
  display: none;
}
.dropzone .dz-preview .dz-remove{
  text-align: left;
  display: inline-block;
}
#syllabus_link:hover{
  text-decoration: none;
}
.InModel{
  padding-left: 25px;
}
.tablebody{
  background-color: white!important;
  color: black;
  height: 60px;
  padding-left: 10px;
}
.tablehead{
  background-color: #0d2f81!important; color: gold;
}
</style>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/course_list">Courses </a>/
            <a href="/course/action/{{$course[0]->course_id}}">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</a>/
            <span class="now_page">Student List</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 10px 10px;">
        <div class="col-md-12">
             <p class="page_title">Student List ( {{count($assign_student)}} )</p>
             <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                <div id="action_sidebar" class="w3-animate-right" style="display: none">
                    <div style="text-align: right;padding:10px;">
                        <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                  <ul class="sidebar-action-ul">
                      <a id="open_model"><li class="sidebar-action-li"><i class="fa fa-graduation-cap" style="padding: 0px 10px;" aria-hidden="true"></i>Assign Student</li></a>
                      <a id="open_document"><li class="sidebar-action-li"><i class="fa fa-upload" style="padding: 0px 10px;" aria-hidden="true"></i>Upload a File</li></a>
                  </ul>
            </div>
            <br>
            <br>
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
                <?php
                $new_str = str_replace('.', '. <br />', Session::get('failed'));
                echo $new_str;
                ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            <div class="details" style="padding: 0px 5px 0px 5px;">
                <div class="col-md-6 row" style="padding:0px 20px;position: relative;top: -30px;">
                    <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                        <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                            <i class="fa fa-search" aria-hidden="true" style="font-size: 20px;"></i>
                        </p>
                    </div>
                    <div class="col-11" style="padding-left: 20px;">
                        <div class="form-group">
                            <label for="full_name" class="bmd-label-floating">Search</label>
                            <input type="hidden" value="{{$course[0]->course_id}}" id="course_id">
                            <input type="text" name="search" class="form-control search" id="input" style="font-size: 18px;">
                        </div>
                    </div>
                </div>
                <div class="row" id="assign_student" style="position: relative;top: -20px;padding: 0px 20px;">
                      @foreach($assign_student as $row)
                      <div class="col-md-4" style="margin: 0px;padding:2px;">
                        <a href="" class="row" id="course_list" style="border: 1px solid #cccccc;border-radius: 10px;color: black;font-weight: bold;margin: 0px;">
                            <div class="col-10" style="color: #0d2f81;padding: 10px;">
                              <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;"><b>{{$row->name}} ( {{$row->student_id}})</b>
                              </p>
                            </div>
                            <div class="col-1" style="padding: 10px 20px;">
                              <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_{{$row->asc_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>
                            </div>
                        </a>
                      </div>
                      @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="openFolderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Assign Student to Course</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="post" action="{{action('AssignStudentController@storeStudent')}}">
        {{csrf_field()}}
      <div class="modal-body">
        <div id="message"></div>
        <br>
        <input type="hidden" name="course_id" value="{{$course[0]->course_id}}">
        <div class="row">
          <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
            <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
              <i class="fa fa-briefcase" aria-hidden="true" style="font-size: 17px;"></i>
            </p>
          </div>
          <div class="col-11" style="padding-left: 20px;">
            <div class="form-group">
                <label for="Programme" class="label">Programme</label>
                <select class="selectpicker form-control" name="programme" id="programme" data-width="100%"data-live-search="true" title="Choose One" required onchange="showStudent()">
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
          <div class="col-11" style="padding-left: 20px;">
              <div class="row">
                  <div class="col">
                      <div class="form-group">
                          <label for="exampleInputEmail1" class="label">Semester</label>
                          <select class="selectpicker form-control" name="semester" id="semester" data-width="100%" title="Choose One" required onchange="showStudent()">
                                  @foreach($semester as $row_semester)
                                      <option value="{{ $row_semester->semester_id }}" class="option">{{$row_semester->semester_name}}</option>
                                  @endforeach
                          </select>
                      </div>
                  </div>
                  <div class="col">
                      <div class="form-group">
                          <label for="exampleInputEmail1" class="label">Intake</label>
                          <select class="selectpicker form-control" name="intake" id="intake" data-width="100%" title="Choose One" required onchange="showStudent()">
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
              <i class="fa fa-user" aria-hidden="true" style="font-size: 20px;"></i>
          </p>
        </div>
        <div class="col-11" style="padding-left: 20px;">
            <div class="form-group">
              <label for="full_name" class="label">Student ID (Optional)</label>
              <select class="selectpicker form-control" name="student" id="student" data-width="100%" title="Choose One">
              </select>
            </div>
        </div>
      </div>
      <br>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-raised btn-secondary" data-dismiss="modal">Close</button>
        &nbsp;
        <input type="submit" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;margin: 0px!important;" value="Save Changes">
      </div>
      </form>
    </div>
  </div>
</div>


<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="openDocumentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Upload a File</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div style="margin: 20px 20px 0px 20px;">
        <p style="color:#0d2f81; "><b>Template:</b></p>
        <p><b>  1. </b>Please download template by clicking <a href='{{asset("/templete/assign_student.xlsx")}}' id="templete_link">Template</a>.</p>
        <p><b>  2. </b>Delete the example data.</p>
        <p><b>  3. </b>Fill in the student ID and other details in file.</p>
      </div>
      <form method="post" action="{{route('dropzone.uploadAssignStudent')}}" enctype="multipart/form-data"
        class="dropzone" id="dropzoneFile" style="margin: 20px;font-size: 20px;color:#a6a6a6;border-style: double;">
        @csrf
      </form>
      <div id="showData" style="padding: 0px 20px 20px 20px;overflow-x:auto;">
        <table id="dtBasicExample" style="box-shadow: 0px 2px 5px #aaaaaa;border:none;width:100%;">
          <thead class="tablehead">
            <tr style="height: 60px;text-align: left;">
              <th style="padding-left: 10px;">No</th>
              <th style="padding-left: 10px;">Student ID</th>
              <th style="padding-left: 10px;">Student Name</th>
            </tr>
          </thead>
        </table>
        <form method="post" action="{{action('AssignStudentController@storeAssignStudent')}}">
        {{csrf_field()}}
          <input type="hidden" name="course_id" value="{{$course[0]->course_id}}">
          <div id="writeInput"></div>
          <br>
          <div style="text-align: right;">
          <button type="button" class="btn btn-raised btn-secondary" data-dismiss="modal">Close</button>
          &nbsp;
          <input type="submit" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;margin:0px;" value="Save Changes">
          </div>
        </form>
      </div>
      <div id="errorData" style="padding: 0px 20px 20px 20px;">
        <p>The Input Data are not completed. Please Check Again the excel file of data.</p>
      </div>
    </div>
  </div>
</div>
@endsection