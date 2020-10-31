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
	.show_count{
		display: none;
	}
}
@media only screen and (min-width: 600px) {
	.show_count{
		display: block;
	}
}
</style>
<script type="text/javascript">
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

        $(document).on('click', '.l_plus', function(){
            $('#lecturer').slideToggle("slow", function(){
                if($('#lecturer').is(":visible")){
                    $('#icon_l').removeClass('fa fa-plus');
                    $('#icon_l').addClass('fa fa-minus');
                }else{
                    $('#icon_l').removeClass('fa fa-minus');
                    $('#icon_l').addClass('fa fa-plus');
                }
            });
        });
        $(document).on('click', '.s_plus', function(){
            $('#student').slideToggle("slow", function(){
                if($('#student').is(":visible")){
                    $('#icon_s').removeClass('fa fa-plus');
                    $('#icon_s').addClass('fa fa-minus');
                }else{
                    $('#icon_s').removeClass('fa fa-minus');
                    $('#icon_s').addClass('fa fa-plus');
                }
            });
        });
  });
  function w3_open() {
      document.getElementById("action_sidebar").style.display = "block";
      document.getElementById("button_open").style.display = "none";
  }
  function w3_close() {
      document.getElementById("action_sidebar").style.display = "none";
      document.getElementById("button_open").style.display = "block";
  }

  $(document).on('click', '.open_modal', function(){
    $('#openDocumentModal').modal('show');
  });

  
  var i = 0;
  Dropzone.options.dropzoneFile =
  {
        acceptedFiles: ".pdf,.xlsx,.docx,.pptx,.jpg,.jpeg,.png",
        addRemoveLinks: true,
        timeout: 50000,
        renameFile: function(file) {
            var re = /(?:\.([^.]+))?$/;
            var ext = re.exec(file.name)[1];
            var newName = new Date().getTime()+"."+ext;
            return newName;
        },
        init: function() {
            this.on("addedfile", function(file){
              i++;
                var re = /(?:\.([^.]+))?$/;
                var ext = re.exec(file.name)[0];
                var filename = file.name.split(ext);
                var filename_without_ext = file.name.split(".");
                var student_id = checkStudentID(filename[0]);
                var model_id = $('#model_id').val();
                file._captionLabel = Dropzone.createElement("<div class='changelabel'><label class='label' style='font-size:13px'>Student ID</label></div>")
                file._captionBox = Dropzone.createElement("<div class='changeName'><input id='file_name"+i+"' type='text' name='caption' value='"+student_id+"' class='form-control filename'></div>");
                file.previewElement.appendChild(file._captionLabel);
                file.previewElement.appendChild(file._captionBox);
                writeInput(model_id,i,student_id,ext,file.upload.filename);
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
            var count = $('#count').val();
            for(var i=1;i<=count;i++){
                var fake = $('#'+model_id+'fake'+i).val();
                  if(fake==name){
                      var id = i;
                      document.getElementById(model_id+"form"+id).value = "";
                      document.getElementById(model_id+"ext"+id).value = "";
                      document.getElementById(model_id+"fake"+id).value = "";
                  }
            } 
            var name = file.upload.filename;
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
            document.getElementById('count').value = i;
        },
        error: function(file, response) {
            alert(response);
        }
  };  

  function writeInput(id,num,name,ext,fake){
    $(document).ready(function(){  
      $("#writeInput").append("<input type='hidden' id='"+id+"form"+num+"' name='"+id+"form"+num+"' value='"+name+"'><input type='hidden' id='"+id+"ext"+num+"' name='"+id+"ext"+num+"' value='"+ext+"'><input type='hidden' id='"+id+"fake"+num+"' name='"+id+"fake"+num+"' value='"+fake+"'>");
    });
  }

  function checkStudentID(name){
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
    var count = $('#count').val();
    var run = false;
    for(var i=1;i<=count;i++){
      var form = $('#'+model_id+'form'+i).val();
      if(form!=""){
        var check = checkStudentID(form);
        if(check=="Error : 0"){
          run = false;
          break;
        }else{
          run = true;
        }
      }
    }
    if(run == true){
      document.getElementById("myForm").submit();
    }else{
      document.getElementById('error-message').innerHTML = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><Strong>Someone of Student ID got error!!!</Strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
    }
  }


  $(function () {
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    if($('.search').val()!=""){
      var value = $('.search').val();
      var course_id = $('#course_id').val();
      var ass_rs_id = $('#ass_rs_id').val();
      $.ajax({
          type:'POST',
          url: "/AssessmentResult/searchStudentList/",
          data:{value:value,course_id:course_id,ass_rs_id:ass_rs_id},
          success:function(data){
            document.getElementById("student_list").innerHTML = data;
          }
      });
    }
    $(".search").keyup(function(){
        var value = $('.search').val();
        var course_id = $('#course_id').val();
        var ass_rs_id = $('#ass_rs_id').val();
        $.ajax({
           type:'POST',
           url: "/AssessmentResult/searchStudentList/",
           data:{value:value,course_id:course_id,ass_rs_id:ass_rs_id},
           success:function(data){
              document.getElementById("student_list").innerHTML = data;
           }
        });
    });
  });
 </script>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/course_list">Courses </a>/
            <a href="/course/action/{{$course[0]->course_id}}">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</a>/
            <a href="/AssessmentResult/{{$course[0]->course_id}}">Assessment Result</a>/
            <span class="now_page">{{$assessment_result->submission_name}}</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 10px 10px;">
        <div class="col-md-12">
            <p class="page_title">{{$assessment_result->submission_name}}</p>
            <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                <div id="action_sidebar" class="w3-animate-right" style="display: none;width: 250px;">
                    <div style="text-align: right;padding:10px;">
                        <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                  	<ul class="sidebar-action-ul">
                      <a class="open_modal"><li class="sidebar-action-li"><i class="fa fa-upload" style="padding: 0px 10px;" aria-hidden="true"></i>Upload Files</li></a>
                      @if((count($lecturer_result)!=0)||(count($student_result)!=0))
                        <a href='/AssessmentResult/download/zipFiles/{{$assessment_result->ass_rs_id}}'><li class="sidebar-action-li"><i class="fa fa-download" style="padding: 0px 10px;" aria-hidden="true"></i>Download All Result</li></a>
                      @endif
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
                            <input type="hidden" value="{{$assessment_result->ass_rs_id}}" id="ass_rs_id">
		                        <input type="text" name="search" class="form-control search" id="input" style="font-size: 18px;">
		                    </div>
		                </div>
		            </div>
		            <div id="student_list" class="row" style="margin-top: -10px;">
                  @if(count($lecturer_result)>0)
		            	<div class="row col-md-12 l_plus" style="border:0px solid black;margin: 0px;padding:0px;font-size: 20px;">
			            	<div class="col-md-3" style="padding-left: 18px;border:0px solid black" class="col-md-12">
			            		Submitted By Lecturer (<i class="fa fa-minus" aria-hidden="true" id="icon_l" style="color: #0d2f81;position: relative;top: 2px;"></i>)
			            	</div>
			            	<div class="col-9 show_count" style="border:0px solid black;">
			            		<hr style="display: inline-block; background-color: #cccccc;width: 94%;margin:0px;position: relative;top: -5px;">
			            		<span style="display: inline-block;border:0px solid black;text-align: right;width:5%;"> ( {{count($lecturer_result)}} ) </span>
			            	</div>
		            	</div>
                  
		            	<div class="row col-md-12" id="lecturer" style="margin:12px 0px 0px 0px;padding: 0px;">
		            		@foreach($lecturer_result as $lr_row)
			            	<div class="row col-md-4 align-self-center" id="course_list" style="margin:0px 0px 5px 0px;">
			                    <a href='/AssessmentResult/view/student/{{$lr_row->ar_stu_id}}/' class="col-12 row align-self-center" id="show_image_link">
			                      <div class="col-12 row" style="padding:10px;color:#0d2f81;">
			                        <div class="col-1" style="position: relative;top: -2px;">
			                          <img src="{{url('image/folder2.png')}}" width="25px" height="25px"/>
			                        </div>
			                        <div class="col-10">
			                          <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;width:280px;"><b>{{$lr_row->student_id}} ( {{$lr_row->name}} ) </b></p>
			                        </div>
			                      </div>
			                    </a>
			                </div>
			                @endforeach
		                </div>
                    @endif
                    @if(count($student_result)>0)
		                <div class="row col-md-12 s_plus" style="border:0px solid black;margin: 20px 0px 0px 0px;padding:0px;font-size: 20px;">
  			            	<div class="col-md-3" style="padding-left: 18px;border:0px solid black" class="col-md-12">
  			            		Submitted By Students (<i class="fa fa-minus" aria-hidden="true" id="icon_s" style="color: #0d2f81;position: relative;top: 2px;"></i>)
  			            	</div>
  			            	<div class="col-9 show_count" style="border:0px solid black;">
  			            		<hr style="display: inline-block; background-color: #cccccc;width: 94%;margin:0px;position: relative;top: -5px;">
  			            		<span style="display: inline-block;border:0px solid black;text-align: right;width:5%;"> ( {{count($student_result)}} ) </span>
  			            	</div>
		            	  </div>
		                <div class="row col-md-12" id="student" style="margin:12px 0px 0px 0px;padding: 0px;">
		                	@foreach($student_result as $sr_row)
			            	<div class="row col-md-4 align-self-center" id="course_list" style="margin:0px;">
			                    <a href='/AssessmentResult/view/student/{{$sr_row->ar_stu_id}}/' class="col-12 row align-self-center" id="show_image_link">
			                      <div class="col-12 row" style="padding:10px;color:#0d2f81;">
			                        <div class="col-1" style="position: relative;top: -2px;">
			                          <img src="{{url('image/folder2.png')}}" width="25px" height="25px"/>
			                        </div>
			                        <div class="col-10">
			                          <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;width:280px;"><b>{{$sr_row->student_id}} ( {{$sr_row->name}} ) </b></p>
			                        </div>
			                      </div>
			                    </a>
			                </div>
			                @endforeach
                  @endif
                  @if((count($lecturer_result)==0)&&(count($student_result)==0))
                        <div style="display: block;border:1px solid black;padding: 50px;width: 100%;margin:5px 20px;">
                          <center>Empty</center>
                        </div>
                      @endif
		                </div>
		            </div>
	           	</div>
        </div>
    </div>
</div>


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
      <div id="error-message" style="margin: 5px 20px;"></div>
      <form method="post" action="{{action('AssessmentResultController@uploadFiles')}}" enctype="multipart/form-data"
        class="dropzone" id="dropzoneFile" style="margin:0px 20px;font-size: 20px;color:#a6a6a6;border-style: double;">
        @csrf
      </form>
      <form method="post" action="{{action('AssessmentResultController@storeFiles')}}" id="myForm">
        {{csrf_field()}}
        <input type="hidden" name="count{{$assessment_result->ass_rs_id}}" value="0" id="count">
        <input type="hidden" value="{{$course[0]->course_id}}" name="course_id">
        <input type="hidden" name="ass_rs_id" value="{{$assessment_result->ass_rs_id}}" id="model_id">
        <div id="writeInput"></div>
        <br>
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