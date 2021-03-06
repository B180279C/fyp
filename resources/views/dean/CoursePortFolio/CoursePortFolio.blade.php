<?php
$title = "CoursePortfolio";
$option5 = "id='selected-sidebar'";
?>
@extends('layouts.layout')

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

    $(document).on('click', '.edit_action', function(){
        var id = $(this).attr("id");
        var num = id.split("_");
        window.location = "/CourseList/"+num[2];
        return false;
    });

    $(document).on('click', '.remove_action', function(){
        var id = $(this).attr("id");
        var num = id.split("_");
        if(confirm('Are you sure want to remove it')){
          window.location = "/CourseList/remove/"+num[2];
        }
        return false;
    });

    $(document).on('click', '#open_document', function(){
      $('#openDocumentModal').modal('show');
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
          $.ajax({
              type:'POST',
              url:'{{$character}}/searchCPCourse',
              data:{value:value},
              success:function(data){
                document.getElementById("course").innerHTML = data;
              }
          });
        }
        $(".search").keyup(function(){
            var value = $('.search').val();
            $.ajax({
               type:'POST',
               url:'{{$character}}/searchCPCourse',
               data:{value:value},
               success:function(data){
                    document.getElementById("course").innerHTML = data;
               }
            });
        });
    });

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
            var table = document.getElementById("table");
            for(var i = 0; i<response.length; i++){
              if((response[i]['subject_code']=="Empty")&&(response[i]['programme']=="Empty")){
                break;
              }
                if((response[i]['subject_code']!=null)&&(response[i]['subject_name']!=null)&&(response[i]['semester']!=null)&&(response[i]['lecturer_staff_id']!=null)&&(response[i]['programme']!=null)&&(response[i]['moderator_staff_id']!=null)&&(response[i]['verified_by_staff_id']!=null)&&(response[i]['approved_by_staff_id']!=null)&&(response[i]['timetable']!=null)){

                  var timetable = response[i]['timetable'];
                  var classList = timetable.split(';');
                  var failed = false;
                  for(var t=0;t<(classList.length-1);t++){
                    var timelist = classList[t].split(',');
                    var week = $.trim(timelist[0]);
                    var time = timelist[1].replace(' ','');
                    var forh = timelist[2].replace(' ','');
                    if(timelist[0]==undefined||timelist[1]==undefined||timelist[2]==undefined){
                      failed = true;

                    }else if((week!="Monday")&&(week!="Tuesday")&&(week!="Wednesday")&&(week!="Thursday")&&(week!="Friday")&&(week!="Saturday")&&(week!="Sunday")){
                      console.log(week);
                      failed = true;
                    }
                    var split_time = time.split("-");
                    if(split_time[0]==undefined||split_time[1]==undefined){
                      console.log('time');
                      failed = true;
                    }
                    if(forh!="Full"&&forh!="Half"){
                      console.log('forh');
                      failed = true;
                    }
                  }

                  if(failed==false){
                    var row = table.insertRow(1+i);
                    var cell = row.insertCell(0);
                    var cell1 = row.insertCell(1);
                    var cell2 = row.insertCell(2);
                    var cell3 = row.insertCell(3);
                    var cell4 = row.insertCell(4);
                    cell.innerHTML  = (i+1);
                    cell1.innerHTML = response[i]['programme_short_form_name']+" / "+response[i]['subject_code'] +" "+ response[i]['subject_name'] +" ( "+response[i]['semester'] +" ) ";
                    cell2.innerHTML = response[i]['credit'];
                    cell3.innerHTML = response[i]['lecturer_staff_id'];
                    cell4.innerHTML = response[i]['moderator_staff_id'];
                    cell.className  = 'tablebody';
                    cell1.className = 'tablebody';
                    cell2.className = 'tablebody';
                    cell3.className = 'tablebody';
                    cell4.className = 'tablebody';
                    $("#writeInput").append("<input type='hidden' id='subject_code"+i+"' name='subject_code"+i+"' value='"+response[i]['subject_code']+"'><input type='hidden' id='subject_name"+i+"' name='subject_name"+i+"' value='"+response[i]['subject_name']+"'><input type='hidden' id='semester"+i+"' name='semester"+i+"' value='"+response[i]['semester']+"'><input type='hidden' id='programme"+i+"' name='programme"+i+"' value='"+response[i]['programme']+"'><input type='hidden' id='credit"+i+"' name='credit"+i+"' value='"+response[i]['credit']+"'><input type='hidden' id='lecturer"+i+"' name='lecturer"+i+"' value='"+response[i]['lecturer_staff_id']+"'><input type='hidden' id='moderator"+i+"' name='moderator"+i+"' value='"+response[i]['moderator_staff_id']+"'><input type='hidden' id='verified_by"+i+"' name='verified_by"+i+"' value='"+response[i]['verified_by_staff_id']+"'><input type='hidden' id='approved_by"+i+"' name='approved_by"+i+"' value='"+response[i]['approved_by_staff_id']+"'><input type='hidden' id='timetable"+i+"' name='timetable"+i+"' value='"+response[i]['timetable']+"'>");
                    $('#showData').show();
                    $('#errorData').hide();
                    $('.submit_button').prop('disabled', false);
                  }else{
                    $('.submit_button').prop('disabled', true);
                    $('#showData').hide();
                    $('#errorData').html("<b>No. "+(i+1)+"</b>, got error the timetable data not complete. <b>Important </b>: the timetable must be in (Week,start_hour-end_hour,Full/Half;)<br/>");
                    $('#errorData').show();
                    break;
                  }
                }else{
                  console.log("BBB");
                  $('.submit_button').prop('disabled', true);
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
  border-left:1px solid #d9d9d9;
  border-bottom: 1px solid #d9d9d9;
}
.tablebodyCenter{
  border-left:1px solid #d9d9d9;
  border-bottom: 1px solid #d9d9d9;
  text-align: center;
}
.tablehead{
  background-color: #0d2f81!important; color: gold;
}
@media only screen and (max-width: 600px) {
  #showData{
    margin-right: 20px;
  }
}
</style>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">Courses</p>
        <p class="pass_page">
            <a href="{{$character}}/home" class="first_page"> Home </a>/
            <span class="now_page">Courses</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 5px 10px;">
        <div class="col-md-12">
             @if($character=="")
             <p class="page_title">Courses In {{$faculty->faculty_name}}</p>
             @else
             <p class="page_title">Courses In {{$department->department_name}}</p>
             @endif
             @if($character=="")
             <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                <div id="action_sidebar" class="w3-animate-right" style="display: none">
                    <div style="text-align: right;padding:10px;">
                        <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                  <ul class="sidebar-action-ul">
                      <a href='CourseList/create'><li class="sidebar-action-li"><i class="fa fa-book" style="padding: 0px 10px;" aria-hidden="true"></i>Make a new Course</li></a>
                      <a id="open_document"><li class="sidebar-action-li"><i class="fa fa-upload" style="padding: 0px 10px;" aria-hidden="true"></i>Add Multple Courses</li></a>
                      <a href="/CourseList/excel/download/"><li class="sidebar-action-li"><i class="fa fa-download" style="padding: 0px 10px;" aria-hidden="true"></i>Export Excel File</li></a>
                  </ul>
            </div>
            @endif
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
              $new_str = Session::get('failed');
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
                            <input type="text" name="search" class="form-control search" id="input" style="font-size: 18px;">
                        </div>
                    </div>
                </div>
                <div class="row" id="course" style="margin-top: -25px;">
                  <div class="col-md-12">
                    <p style="font-size: 18px;margin:0px 0px 0px 10px;">Newest Semester of Courses</p>
                  </div>
                      @foreach($course as $row)
                        <a href="{{$character}}/CourseList/action/{{$row->course_id}}" class="col-md-12 align-self-center" id="course_list">
                          <div class="col-md-12 row" style="padding:10px;color:#0d2f81;">
                            <div class="col-1" style="padding-top: 3px;">
                              <img src="{{url('image/folder2.png')}}" width="25px" height="25px"/>
                            </div>
                            <div class="col" id="course_name">
                              <p style="margin: 0px;"><b>{{$row->semester_name}}</b> : {{$row->short_form_name}} / {{$row->subject_code}} {{$row->subject_name}} ( {{$row->name}} )</p>
                            </div>
                            @if($character=="")
                            <div class="col-1" id="course_action">
                                <i class="fa fa-wrench edit_action" aria-hidden="true" id="edit_button_{{$row->course_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>&nbsp;
                                <i class="fa fa-times remove_action" aria-hidden="true" id="remove_button_{{$row->course_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>
                            </div>
                            @endif
                          </div>
                        </a>
                      @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="openDocumentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Upload Files</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div style="margin: 20px 20px 0px 20px;">
        <p style="color:#0d2f81; "><b>Template:</b></p>
        <p><b>  1. </b>Please download Template by clicking <a href='{{asset("/templete/multiple_courses.xlsx")}}' id="templete_link">Template</a>.</p>
        <p><b>  2. </b>Delete the example data.</p>
        <p><b>  3. </b>Fill in the Subject details and other details in file.</p>
      </div>
      <form method="post" action="{{route('dropzone.uploadCourses')}}" enctype="multipart/form-data"
        class="dropzone" id="dropzoneFile" style="margin: 20px;font-size: 20px;color:#a6a6a6;border-style: double;">
        @csrf
      </form>
      <div id="showData" style="padding: 0px 20px 20px 20px;overflow-x:auto;">
        <table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);" id="table" class="table table-hover">
          <thead>
            <tr style="background-color: #d9d9d9;">
              <th style="border-left:1px solid #e6e6e6;color:black;"><b>No</b></th>
              <th style="border-left:1px solid #e6e6e6;color:black;"><b>Subject</b></th>
              <th style="border-left:1px solid #e6e6e6;color:black;"><b>Credit</b></th>
              <th style="border-left:1px solid #e6e6e6;color:black;"><b>Lecturer</b></th>
              <th style="border-left:1px solid #e6e6e6;color:black;"><b>Moderator</b></th>
            </tr>
          </thead>
        </table>
      </div>
      <div id="errorData" style="padding: 0px 20px 20px 20px;">
        <p><b>Something going wrong. </b>Please Check Again the excel file of data. <br/>(<b>Important : </b>All result cannot be empty cannot be same.)</p>
      </div>
      <form method="post" action="{{action('Dean\CourseController@storeCourses')}}">
        {{csrf_field()}}
        <div id="writeInput"></div>
        <div class="modal-footer">
        <button type="button" class="btn btn-raised btn-secondary" data-dismiss="modal">Close</button>
        &nbsp;
        <input type="submit" class="btn btn-raised btn-primary submit_button" style="background-color: #3C5AFF;color: white;margin-right: 13px;" value="Save Changes">
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

