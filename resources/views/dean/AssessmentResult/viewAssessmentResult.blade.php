<?php
$title = "Course";
$option1 = "id='selected-sidebar'";
?>
@extends('layouts.nav_dean')

@section('content')
<style type="text/css">
#show_image_link:hover{
    text-decoration: none;
}
.dropzone .dz-preview{
  border-bottom: 1px solid black;
  padding-left: 10px;
  padding-top: 10px;
  padding-bottom: -30px!important;
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
  padding-left: 25px;
  display: inline-block;
}
@media only screen and (max-width: 600px) {
  #course_name{
    padding-top: 0px;
  }
  #course_list{
    margin-left: 0px;
    padding: 4px 15px;
  }
  #course_action_two{
    padding: 10px 0px 0px 0px;
    position: relative;
    right: -24px;
    text-align: right;
  }
  #file_name_two{
    width: 185px;
    margin: 0px;
    padding:0px;
  }
  #file_name{
    width: 210px;
    margin: 0px;
    padding:0px;
  }
}
@media only screen and (min-width: 600px) {
    #course_list{
      margin-left: 0px;
      padding: 4px 15px;
    }
    #course_name{
        margin-left:-20px;
        padding-top:0px;
    }
    #course_action_two{
      text-align: right;
      margin-left: 5px;
      padding: 8px 0px 0px 25px;
    }
    #course_action{
      text-align: right;
      padding: 3px 0px 0px 24px;
    }
}
</style>
<script type="text/javascript">
  function w3_open() {
    document.getElementById("action_sidebar").style.display = "block";
    document.getElementById("button_open").style.display = "none";
  }
  function w3_close() {
    document.getElementById("action_sidebar").style.display = "none";
    document.getElementById("button_open").style.display = "block";
  }
  Dropzone.autoDiscover = false;
  $(document).ready(function(){  
      $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });

      $(document).on('keyup', '.filename', function(){  
           var id = $(this).attr("id");
           var f_id = id.split("file_name");
           var value = document.getElementById("file_name"+f_id[1]).value;
           var model_id = $('#model_id').val();
           $("#"+model_id+"form"+f_id[1]).val(value);
      });

      $(document).on('click', '#open_submission', function(){
        $('#openSubmissionModal').modal('show');
        getOption(0);
      });



      $(document).on('click', '.edit_button', function(){
        var id = $(this).attr("id");
        var num = id.split("_");
        $.ajax({
          type:'POST',
          url:'/AssessmentResult/submissionFormEdit',
          data:{value : num[2]},
          success:function(data){
            document.getElementById('ass_rs_id').value = num[2];
            document.getElementById('submission_name').value = data.submission_name;
            getOption(data.assessment);
          } 
        });
        $('#submissionFormEdit').modal('show');
        return false;
      });
    
    function getOption(name){
      var course_id = $('#course_id').val();
      $.ajax({
        type:'POST',
        url:'/assessment/getSyllabusData',
        data:{course_id:course_id},
        success:function(response){
            var count = 0;
            var new_count = 0;
            var option = "";
            for(var i = 0;i<=(response.length-1);i++){
              if((response[i][1]==null)&&(response[i][2]!=null)&&(response[i][3]!=null)&&(response[i][9]!=null)){
                if(count == 0){
                  count = response[i][2];
                }else{
                  new_count = response[i][2];
                  if(new_count>count){
                    count = new_count;
                  }else{
                    break;
                  }
                }
                option += "<option class='option' value="+response[i][3]+">"+response[i][3]+"</option>";
              }
            }
            if(name == 0){
              $("#assessment").html(option);
              $('#assessment').selectpicker('refresh');
            }else{
              $("#assessment_model").html(option);
              $('#assessment_model').selectpicker('refresh');
              $('#assessment_model').selectpicker('val', name);
            }
        }
      });
    }
  });

  $(document).on('click', '.open_modal', function(){
    var upload_button_id = $(this).attr("id");
    var num = upload_button_id.split("_");
    $('#model_id').val(num[2]);
    $('#openDocumentModal').modal('show');
    $('.dropzone').css('display','none');
    $('.append_input').css('display','none');
    $('.count').css('display','none');
    $('#dropzoneFile'+num[2]).css('display',"block");
    $('#writeInput'+num[2]).css('display',"block");
    $('#count'+num[2]).css('display',"block");
  });

  $(document).on('click', '.dropzone', function(){
      var idName = $(this).attr("id");
      var getIdNum = idName.split("dropzoneFile");
      lastNum = getIdNum[1];
      var i = 0;
      $("#dropzoneFile"+lastNum).dropzone({
          url: "{{action('AssessmentResultController@uploadFiles')}}",
          acceptedFiles: ".pdf,.xlsx,.docx,.pptx,.jpg,.jpeg,.png",
          addRemoveLinks: true,
          timeout: 50000,
          headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
          },
          renameFile: function(file) {
              var re = /(?:\.([^.]+))?$/;
              var ext = re.exec(file.name)[1];
              var newName = new Date().getTime()+"."+ext;
              return newName;
          },
          init: function() {
              this.on("maxfilesexceeded", function(file) {
                    this.removeAllFiles();
                    this.addFile(file);
              });
              this.on("addedfile", function(file){
                i++;
                var re = /(?:\.([^.]+))?$/;
                var ext = re.exec(file.name)[0];
                var filename = file.name.split(ext);
                var filename_without_ext = file.name.split(".");
                var student_id = checkStudentID(filename[0]);
                file._captionLabel = Dropzone.createElement("<div class='changelabel'><label class='label' style='font-size:13px'>Student ID</label></div>")
                file._captionBox = Dropzone.createElement("<div class='changeName'><input id='file_name"+i+"' type='text' name='caption' value='"+student_id+"' class='form-control filename'></div>");
                file.previewElement.appendChild(file._captionLabel);
                file.previewElement.appendChild(file._captionBox);
                writeInput(lastNum,i,student_id,ext,file.upload.filename);
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
                case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
                  $(file.previewElement).find(".dz-image img").attr("src", "{{url('image/pptx.png')}}");
                   break;
              }
              done();
          },
          removedfile: function(file)
          {
            var model_id = $('#model_id').val();
              var name = file.upload.filename;
              var count = $('#count'+model_id).val();
              for(var i=1;i<=count;i++){
                var fake = $('#'+model_id+'fake'+i).val();
                  if(fake==name){
                      var id = i;
                      document.getElementById(model_id+"form"+id).value = "";
                      document.getElementById(model_id+"ext"+id).value = "";
                      document.getElementById(model_id+"fake"+id).value = "";
                  }
              }        
              $.ajax({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                  },
                  type: 'POST',
                  url: '{{ url("/ass_rs_destoryFiles") }}',
                  data: {filename: name},
                  success: function (data){
                      console.log("File has been successfully removed!!");
                  },
                  error: function(e) {
                      console.log(e);
                  }
              });
              var fileRef;
              return (fileRef = file.previewElement) != null ? 
              fileRef.parentNode.removeChild(file.previewElement) : void 0;
          },
          success: function(file, response) {
              document.getElementById('count'+lastNum).value = i;
          },
          error: function(file, response) {
              alert(response);
          }
      });
  });

  function writeInput(id,num,name,ext,fake){
    $(document).ready(function(){  
      $("#writeInput"+id).append("<input type='hidden' id='"+id+"form"+num+"' name='"+id+"form"+num+"' value='"+name+"'><input type='hidden' id='"+id+"ext"+num+"' name='"+id+"ext"+num+"' value='"+ext+"'><input type='hidden' id='"+id+"fake"+num+"' name='"+id+"fake"+num+"' value='"+fake+"'>");
    });
  }

  function checkStudentID(name){
    $('.submit_button').prop('disabled', true);
    var num = name.length;
    var result = "";
    var student_id = false;
    var getID = "";
    for(var w = 0; w<name.length; w++){
      var char = name.charAt(w);
      if(isNumber(char)){
        if(isNumber(name.charAt(w+1))){
          if(isNumber(name.charAt(w+2))){
            if(isNumber(name.charAt(w+3))){
              if(isNumber(name.charAt(w+4))){
                if(isNumber(name.charAt(w+5))){
                  student_id = true;
                  getID = name.charAt(w-1)+name.charAt(w)+name.charAt(w+1)+name.charAt(w+2)+name.charAt(w+3)+name.charAt(w+4)+name.charAt(w+5)+name.charAt(w+6);
                  if(isNumber(name.charAt(w+6))){
                    student_id = false;
                    w = w+6;
                  }
                }
              }
            }
          }
        }
      }
    }
    if(student_id == false){
      getID = "Error : 0";
    }
    return getID;
  }

  function isNumber(n) { return /^-?[\d.]+(?:e-?\d+)?$/.test(n); } 

  function checkAllStudentID(){
    var model_id = $('#model_id').val();
    var count = $('#count'+model_id).val();
    var run = false;
    for(var i=1;i<=count;i++){
      var form = $('#'+model_id+'form'+i).val();
      if((form.length!=8)&&(form.length!=0)){
        run = false;
        break;
      }else{
        run = true;
      }
    }
    if(run == true){
      document.getElementById("myForm").submit();
    }else{
      document.getElementById('error-message').innerHTML = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><Strong>Someone of Student ID got error!!!</Strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
    }
  }
</script>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/course_list">Courses </a>/
            <a href="/course/action/{{$course[0]->course_id}}">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</a>/
            <span class="now_page">Assessment Result</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 10px 10px;">
        <div class="col-md-12">
             <p style="display: inline;font-size: 25px;position: relative;top: 5px;left:8px;color: #0d2f81">Assessment Result</p>
              <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                <div id="action_sidebar" class="w3-animate-right" style="display: none;width: 250px;">
                    <div style="text-align: right;padding:10px;">
                        <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                  <ul class="sidebar-action-ul">
                      <a id="open_submission"><li class="sidebar-action-li"><i class="fa fa-folder" style="padding: 0px 10px;" aria-hidden="true"></i>Make New Submission Form</li></a>
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
            <div class="details" style="padding: 0px 5px 0px 5px;">
              <div class="col-md-6 row" style="padding:0px 20px;position: relative;top: -25px;">
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
              <div id="submission" class="row" style="margin-top: -10px;">
                <?php 
                $i = 0;
                ?>
                @foreach($group as $row_group)
                  <h5 style="margin-top:3px;padding-left: 15px;">{{$row_group->assessment}}</h5>
                  @foreach($assessment_results as $row)
                  @if($row_group->assessment == $row->assessment)
                  <div class="col-12 row align-self-center" id="course_list">
                    <a href='' class="col-8 row align-self-center" id="show_image_link">
                      <div class="col-12 row" style="padding:10px;color:#0d2f81;">
                        <div class="col-1" style="position: relative;top: -2px;">
                          <img src="{{url('image/folder_submit.png')}}" width="25px" height="25px"/>
                        </div>
                        <div class="col-10" id="course_name">
                          <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>{{$row->submission_name}}</b></p>
                        </div>
                      </div>
                    </a>
                    <div class="col-4" id="course_action_two">
                      <i class="fa fa-upload upload_button open_modal" aria-hidden="true" id="upload_button_{{$row->ass_rs_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>&nbsp;
                      <i class="fa fa-wrench edit_button" aria-hidden="true" id="edit_button_{{$row->ass_rs_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>&nbsp;
                      <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_{{$row->ass_rs_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>
                    </div>
                  </div>
                  @endif
                  <?php
                  $i++;
                  ?>
                  @endforeach
                @endforeach
                @if($i==0)
                <div style="display: block;border:1px solid black;padding: 50px;width: 100%;margin: 0px 20px 0px 20px;">
                  <center>Empty</center>
                </div>
                <br>
                @endif
              </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="openSubmissionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Open New Submission Form</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="post" action="{{action('AssessmentResultController@openSubmissionForm')}}">
        {{csrf_field()}}
      <div class="modal-body">
        <div id="message"></div>
        <input type="hidden" name="course_id" value="{{$course[0]->course_id}}">
        <div class="row">
            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                    <i class="fa fa-list" aria-hidden="true" style="font-size: 18px;"></i>
                </p>
            </div>
                <div class="col-11" style="padding-left: 20px;">
                <div class="form-group">
                      <label class="label" style="font-size: 13px;">Assessment</label>
                      <select class="selectpicker form-control" id="assessment" name="assessment" data-width="100%" title="Choose One" required></select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                    <i class="fa fa-users" aria-hidden="true" style="font-size: 18px;"></i>
                </p>
            </div>
                <div class="col-11" style="padding-left: 20px;">
                <div class="form-group">
                      <label for="subject_type" class="bmd-label-floating">Submission Name</label>
                      <input type="text" name="submission_name" class="form-control" required/>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-raised btn-secondary" data-dismiss="modal">Close</button>
        &nbsp;
        <input type="submit" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;margin-right: 13px;margin-top: 0px;" value="Save Changes">
      </div>
      </form>
    </div>
  </div>
</div>


<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="submissionFormEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content content2">
      <div class="modal-header header2">
        <h5 class="modal-title title2" id="exampleModalLabel">Edit Submission Form</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="post" action="{{action('AssessmentResultController@updateSubmissionForm')}}">
        {{csrf_field()}}
      <div class="modal-body body2">
        <div id="message"></div>
        <br>

        <div class="row">
            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                    <i class="fa fa-list" aria-hidden="true" style="font-size: 18px;"></i>
                </p>
            </div>
                <div class="col-11" style="padding-left: 20px;">
                <div class="form-group">
                      <label class="label" style="font-size: 13px;">Assessment</label>
                      <select class="selectpicker form-control" id="assessment_model" name="assessment_model" data-width="100%" title="Choose One" required>
                      </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                    <i class="fa fa-folder" aria-hidden="true" style="font-size: 18px;"></i>
                </p>
            </div>
                <div class="col-11" style="padding-left: 20px;">
                <div class="form-group">
                      <label for="subject_type" class="label">Submission Name</label>
                      <input type="hidden" name="ass_rs_id" id="ass_rs_id">
                      <input type="text" name="submission_name" class="form-control" id="submission_name" placeholder="Folder" required/>
                </div>
            </div>
        </div>
        <br>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-raised btn-secondary" data-dismiss="modal">Close</button>
        &nbsp;
        <input type="submit" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;margin-right: 13px;margin-top: 0px;" value="Save Changes">
      </div>
      </form>
    </div>
  </div>
</div>


<!-- Modal -->
<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="openDocumentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content content2">
      <div class="modal-header header2">
        <h5 class="modal-title title2"  id="exampleModalLabel">Upload Files</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div style="padding: 20px;">
      <div id="error-message"></div>
      <form method="post" action="{{action('AssessmentResultController@storeFiles')}}" id="myForm">
      {{csrf_field()}}
      @foreach($assessment_results as $row_model)
      <div class="dropzone" id="dropzoneFile{{$row_model->ass_rs_id}}" style="padding:25px;display: none;font-size: 20px;color:#a6a6a6;border-style: double;">
        <div class="dz-message" data-dz-message><span>Drop Files in Here to Upload</span></div>
      </div>
      <div id="writeInput{{$row_model->ass_rs_id}}" class="append_input" style="display: none;"></div>
      <input type="hidden" name="count{{$row_model->ass_rs_id}}" class="count" id="count{{$row_model->ass_rs_id}}">
      @endforeach
      </div>
        <input type="hidden" value="{{$course[0]->course_id}}" name="course_id">
        <input type="hidden" name="ass_rs_id" id="model_id">
        <div class="modal-footer">
        <button type="button" class="btn btn-raised btn-secondary" data-dismiss="modal">Close</button>
        &nbsp;
        <input type="button" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;margin-right: 13px;margin-top: 0px;" value="Save Changes" onclick="checkAllStudentID();">
        </div>
      </form>
    </div>
  </div>
</div>

@endsection