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

  function checkORNot(value){
    var input = $('#checkbox_input').val();
    var c_count = parseInt($('#c_count').val());
    var checkbox = $('#checkbox_input').val().split("---");
    $('#checkbox_input').val(input+"---"+value);
    if(input==""){
      $('#checkbox_input').val("---"+value);
    }else{
      input
      var remove = "";
      var data = "";
      for(var m=1;m<=(checkbox.length-1);m++){
        if(checkbox[m]==value){
          for(var c=1;c<=(checkbox.length-1);c++){
            if(checkbox[c]!=value){
              data += '---'+checkbox[c];
            }
          }
          $('#checkbox_input').val(data);
          remove = "true";
        }
      }
    }

    if(remove != "true"){
      $('#checkbox_input').val(input+"---"+value);
      c_count = c_count+1;
      $('#c_count').val(c_count);
      $('.c_count_num').html(c_count);
    }else{
      c_count = c_count-1;
      $('#c_count').val(c_count);
      $('.c_count_num').html(c_count);
    }
  }
  $(document).ready(function(){  
    $(document).on('click', '#open_previous', function(){
      $('#openPreviousModal').modal('show');
    });

    $(document).on('click', '.select_semester', function(){
      var course_id = $('#course_id').val();
      $.ajax({
        type:'POST',
        url:'/lectureNote/SelectPreviousSemester',
        data:{value : course_id},
        success:function(data){
          // console.log(data);
          $('.href_link').html('<a style="padding:0px;margin: 0px;" class="select_semester">Semester</a> &nbsp;/&nbsp;');
          document.getElementById('previous').innerHTML = "";
          for(var i=0;i<=(data.length-1);i++){
            $("#previous").append('<div class="row semester hover" id="semester_'+data[i]['course_id']+'" style="margin:0px; padding: 0px;"><div class="col-1" id="icon_image"><img src="{{url("image/folder2.png")}}" width="25px" height="25px"/></div><div class="col name"><p style="padding: 5px 0px;margin: 0px;">'+data[i]['semester_name']+'</p></div></div>');
          }
        } 
      });
    });

    $(document).on('click', '.semester', function(){
      var id = $(this).attr("id");
      var num = id.split("_");  
      $.ajax({
        type:'POST',
        url:'/lectureNote/SelectFolderSemester',
        data:{value : num[1]},
        success:function(data){
          document.getElementById('previous').innerHTML = "";
          $('.href_link').html('<a style="padding:0px;margin: 0px;" class="select_semester">Semester</a> &nbsp;/&nbsp;<a style="padding:0px;margin: 0px;" class="semester" id="semester_'+data[0]['course_id']+'">'+data[0]['semester_name']+'</a> &nbsp;/&nbsp;');
          for(var i=0;i<=(data.length-1);i++){
            var checked = "";
            var checkbox = $('#checkbox_input').val().split("---");
            for(var m=1;m<=(checkbox.length-1);m++){
              if(checkbox[m]==data[i]['ln_id']){
                checked = 'checked';
              }
            }
            if(data[i]['note_type']=="folder"){
              var type = "folder";
              var image = "<img src='{{url('image/folder2.png')}}' width='25px' height='25px'/>";
              var input = '';
              var name_link = '<div class="col '+type+' name" id="folder_'+data[i]['ln_id']+'"><p style="padding: 5px 0px;margin: 0px;">'+data[i]['note_name']+'</p></div>';
            }else{
              var type = "document";
              var ext = data[i]['note'].split('.');
              if(ext[1]=="pdf"){
                var image = "<img src='{{url('image/pdf.png')}}' width='25px' height='25px'/>";
              }else if(ext[1]=="docx"){
                var image = "<img src='{{url('image/docs.png')}}' width='25px' height='25px'/>";
              }else if(ext[1]=="xlsx"){
                var image = "<img src='{{url('image/excel.png')}}' width='25px' height='25px'/>";
              }else if(ext[1]=="pptx"){
                var image = "<img src='{{url('image/pptx.png')}}' width='25px' height='25px'/>";
              }
              var name_link = "<a href='/lectureNote/download/"+data[i]['ln_id']+"' class='col "+type+" name' id='folder_"+data[i]['ln_id']+"'><p style='padding: 5px 0px;margin: 0px;'>"+data[i]['note_name']+"</p></a>";
              var input = '<div class="col-1" id="checkbox"> <input type="checkbox" value="'+data[i]['ln_id']+'" id="lnId_'+data[i]['ln_id']+'" onchange="checkORNot(this.value)" class="group_'+data[i]['ln_id']+' group_download" '+checked+'></div>';
            }
            $("#previous").append('<div class="row hover" style="margin:0px; padding: 0px;"><div class="col-1" id="icon_image">'+image+'</div>'+name_link+input+'</div>');
          }
        } 
      });
    });

    $(document).on('click', '.folder', function(){
      var id = $(this).attr("id");
      var num = id.split("_");
      $.ajax({
        type:'POST',
        url:'/lectureNote/SelectFolderPlace',
        data:{value : num[1]},
        success:function(data){
          document.getElementById('previous').innerHTML = "";
          var place = data.split("___");
          var name = place[4].split(",,,");
          var folder_id = place[3].split(',,,');
          var link = "";
          for(var m=0;m<=(name.length-1);m++){
            link += '<a style="padding:0px;margin: 0px;" class="folder" id="folder_'+folder_id[m+1]+'">'+name[m]+'</a> &nbsp;/&nbsp;';
          }
          $('.href_link').html('<a style="padding:0px;margin: 0px;" class="select_semester">Semester</a> &nbsp;/&nbsp;<a style="padding:0px;margin: 0px;" class="semester" id="semester_'+place[0]+'">'+place[1]+'</a> &nbsp;/&nbsp;'+link);
          $.ajax({
            type:'POST',
            url:'/lectureNote/SelectFolder',
            data:{value : place[3]},
            success:function(data){
              for(var i=0;i<=(data.length-1);i++){
                var checked = "";
                var checkbox = $('#checkbox_input').val().split("---");
                for(var m=1;m<=(checkbox.length-1);m++){
                  if(checkbox[m]==data[i]['ln_id']){
                    checked = 'checked';
                  }
                }
                if(data[i]['note_type']=="folder"){
                  var type = "folder";
                  var image = "<img src='{{url('image/folder2.png')}}' width='25px' height='25px'/>";
                  var input = '';
                  var name_link = '<div class="col '+type+' name" id="folder_'+data[i]['ln_id']+'"><p style="padding: 5px 0px;margin: 0px;">'+data[i]['note_name']+'</p></div>';
                }else{
                  var type = "document";
                  var ext = data[i]['note'].split('.');
                  if(ext[1]=="pdf"){
                    var image = "<img src='{{url('image/pdf.png')}}' width='25px' height='25px'/>";
                  }else if(ext[1]=="docx"){
                    var image = "<img src='{{url('image/docs.png')}}' width='25px' height='25px'/>";
                  }else if(ext[1]=="xlsx"){
                    var image = "<img src='{{url('image/excel.png')}}' width='25px' height='25px'/>";
                  }else if(ext[1]=="pptx"){
                    var image = "<img src='{{url('image/pptx.png')}}' width='25px' height='25px'/>";
                  }
                  var name_link = "<a href='/lectureNote/download/"+data[i]['ln_id']+"' class='col "+type+" name' id='folder_"+data[i]['ln_id']+"'><p style='padding: 5px 0px;margin: 0px;'>"+data[i]['note_name']+"</p></a>";
                  var input = '<div class="col-1" id="checkbox"> <input type="checkbox" value="'+data[i]['ln_id']+'" id="lnId_'+data[i]['ln_id']+'" onchange="checkORNot(this.value)" class="group_'+data[i]['ln_id']+' group_download" '+checked+'></div>';
                }
                $("#previous").append('<div class="row hover" style="margin:0px; padding: 0px;"><div class="col-1" id="icon_image">'+image+'</div>'+name_link+input+'</div>');
              }
            }
          });          
        } 
      });
    });

    $(document).on('click', '#open_folder', function(){
      $('#openFolderModal').modal('show');
    });

    $(document).on('click', '#open_document', function(){
      $('#openDocumentModal').modal('show');
    });

    $(document).on('keyup', '.filename', function(){  
      var id = $(this).attr("id");
      var value = document.getElementById(id).value;
      $("#form"+id).val(value);
    });

    $(document).on('click', '.edit_button', function(){
      var id = $(this).attr("id");
      var num = id.split("_");
      $.ajax({
        type:'POST',
        url:'/lectureNote/folderNameEdit',
        data:{value : num[2]},
        success:function(data){
          document.getElementById('ln_id').value = num[2];
          document.getElementById('folder_name').value = data.note_name;
          
        } 
      });
      $('#folderNameEdit').modal('show');
      return false;
    });

    $(document).on('click', '.remove_button', function(){
      var id = $(this).attr("id");
      var num = id.split("_");
      if(confirm('Are you sure you want to remove the it?')) {
        window.location = "/lectureNote/remove/"+num[2];
      }
      return false;
    });
  });

  var i = 0;
  Dropzone.options.dropzoneFile =
  {
        acceptedFiles: ".pdf,.xlsx,.docx,.pptx",
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
              file._captionLabel = Dropzone.createElement("<div class='changelabel'><label class='label' style='font-size:13px'>File Name</label></div>")
              file._captionBox = Dropzone.createElement("<div class='changeName'><input id='"+i+"' type='text' name='caption' value='"+filename[0]+"' class='form-control filename'></div>");
              file.previewElement.appendChild(file._captionLabel);
              file.previewElement.appendChild(file._captionBox);
              writeInput(i,filename[0],ext,file.upload.filename);
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
            var name = file.upload.filename;
            var count = $('#count').val();
            for(var i=0;i<=count;i++){
              var fake = $('#fake'+i).val();
                if(fake==name){
                    var id = i;
                    document.getElementById("form"+id).value = "";
                    document.getElementById("ext"+id).value = "";
                    document.getElementById("fake"+id).value = "";
                }
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                type: 'POST',
                url: '{{ url("/note_destoryFiles") }}',
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
  function writeInput(num,name,ext,fake){
    $(document).ready(function(){  
      $("#writeInput").append("<input type='hidden' id='form"+num+"' name='form"+num+"' value='"+name+"'><input type='hidden' id='ext"+num+"' name='ext"+num+"' value='"+ext+"'><input type='hidden' id='fake"+num+"' name='fake"+num+"' value='"+fake+"'>");
    });
  }

  $(function () {
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        if($('.search').val()!=""){
          var value = $('.search').val();
          var place = $('#place').val();
          var course_id = $('#course_id').val();
          $.ajax({
              type:'POST',
              url:'/lectureNote/searchFiles',
              data:{value:value,course_id:course_id,place:place},
              success:function(data){
                document.getElementById("lecture_note").innerHTML = data;
              }
          });
        }
        $(".search").keyup(function(){
            var value = $('.search').val();
            var place = $('#place').val();
            var course_id = $('#course_id').val();
            $.ajax({
               type:'POST',
               url:'/lectureNote/searchFiles',
               data:{value:value,course_id:course_id,place:place},
               success:function(data){
                    document.getElementById("lecture_note").innerHTML = data;
               }
            });
        });
    });

  
</script>
<style type="text/css">
.show_image_link:hover{
  text-decoration: none;
}
.hover:hover{
  background-color: #d9d9d9;
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
.dropzone .dz-preview .dz-remove {
  text-align: left;
  padding-left: 25px;
  display: inline-block;
}
#icon_image{
  padding-top: 3px;border-bottom:1px solid #aaaaaa;
}
#checkbox{
  border-bottom:1px solid #aaaaaa;padding: 5px 0px;margin: 0px; text-align: center;
}
.name:hover{
  text-decoration: none;
  color: #0d2f81;
}
@media only screen and (max-width: 600px) {
  #course_name{
        margin-left:0px;
        padding-top: 5px;
  }
  #course_action_two{
    padding: 0px;
    position: relative;
    right: -19px;
    text-align: right;
  }
  #course_action{
    text-align: right;
    padding: 3px 0px 0px 20px;
  }
  #file_name_two{
    width: 185px;
    margin: 0px;
    padding:0px;
  }
  #file_name{
    width: 240px;
    margin: 0px;
    padding:0px;
  }
  .name{
    border-bottom:1px solid #aaaaaa;
    padding:0px;
    margin: 0px 0px 0px 10px;
    color: black;
  }
}
@media only screen and (min-width: 600px) {
    #course_name{
        margin-left:-55px;
        padding-top: 5px;
    }
    #course_action_two{
      text-align: right;
      padding: 3px 0px 0px 24px;
    }
    #course_action{
      text-align: right;
      padding: 3px 0px 0px 24px;
    }
    .name{
      border-bottom:1px solid #aaaaaa;
      padding:0px;
      margin: 0px 0px 0px -15px;
      color: black;
    }
}
</style>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/course_list">Courses </a>/
            <a href="/course/action/{{$course[0]->course_id}}">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</a>/
            <span class="now_page">Lecture Note</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 10px 10px;">
        <div class="col-md-12">
             <p class="page_title">Lecture Note</p>
             <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                <div id="action_sidebar" class="w3-animate-right" style="display: none">
                    <div style="text-align: right;padding:10px;">
                        <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                  <ul class="sidebar-action-ul">
                      <a id="open_previous"><li class="sidebar-action-li"><i class="fa fa-fast-backward" style="padding: 0px 10px;" aria-hidden="true"></i>Previous semesters of note</li></a>
                      <a id="open_folder"><li class="sidebar-action-li"><i class="fa fa-folder" style="padding: 0px 10px;" aria-hidden="true"></i>Make a new Folder</li></a>
                      <a id="open_document"><li class="sidebar-action-li"><i class="fa fa-upload" style="padding: 0px 10px;" aria-hidden="true"></i>Upload Files</li></a>
                      <p class="title_method">Download</p>
                      <a id="checkDownloadAction"><li class="sidebar-action-li"><i class="fa fa-check-square-o" style="padding: 0px 10px;" aria-hidden="true"></i>Checked Item</li></a>
                      <a href='/lectureNote/download/zipFiles/{{$course[0]->course_id}}'><li class="sidebar-action-li"><i class="fa fa-download" style="padding: 0px 10px;" aria-hidden="true"></i>All Note</li></a>
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
                            <input type="hidden" id="place" value="Note">
                            <input type="hidden" value="{{$course[0]->course_id}}" id="course_id">
                            <input type="text" name="search" class="form-control search" id="input" style="font-size: 18px;">
                        </div>
                    </div>
                </div>
                <div class="row" id="lecture_note" style="margin-top:-25px;">
                      <?php
                      $i=0;
                      ?>
                      @foreach($lecture_note as $row)
                        @if($row->note_type=="folder")
                        <a href="/lectureNote/folder/{{$row->ln_id}}" class="col-md-12 align-self-center" id="course_list">
                          <div class="col-md-12 row" style="padding:10px;color:#0d2f81;">
                            <div class="col-1" style="padding-top: 3px;">
                                <img src="{{url('image/folder2.png')}}" width="25px" height="25px"/>
                            </div>
                          <div class="col" id="course_name">
                              <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>{{$row->note_name}}</b></p>
                          </div>
                          <div class="col-3" id="course_action_two">
                                <i class="fa fa-wrench edit_button" aria-hidden="true" id="edit_button_{{$row->ln_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>&nbsp;
                                <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_{{$row->ln_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>
                            </div>
                          </div>
                        </a>
                        @else
                          <?php
                            $ext = "";
                            if($row->note){
                              $ext = explode(".", $row->note);
                            }
                          ?>
                        <a href="{{ action('Dean\LectureNoteController@downloadLN',$row->ln_id) }}" class="col-md-12 align-self-center" id="course_list">
                          <div class="col-md-12 row" style="padding:10px;color:#0d2f81;">
                            <div class="col-1" style="padding-top: 3px;">
                              @if($ext[1]=="pdf")
                              <img src="{{url('image/pdf.png')}}" width="25px" height="25px"/>
                              @elseif($ext[1]=="docx")
                              <img src="{{url('image/docs.png')}}" width="25px" height="25px"/>
                              @elseif($ext[1]=="xlsx")
                              <img src="{{url('image/excel.png')}}" width="25px" height="25px"/>
                              @elseif($ext[1]=="pptx")
                              <img src="{{url('image/pptx.png')}}" width="25px" height="25px"/>
                              @endif
                            </div>
                            <div class="col" id="course_name">
                              <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>{{$row->note_name}}</b></p>
                            </div>
                            <div class="col-1" id="course_action">
                                <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_{{$row->ln_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>
                            </div>  
                            </div>
                        </a>
                        @endif
                      <?php
                      $i++;
                      ?>
                      @endforeach
                      <?php
                      if($i==0){
                      ?>
                      <div style="display: block;border:1px solid black;padding: 50px;width: 100%;margin: 0px 20px;">
                              <center>Empty</center>
                      </div>
                      <?php
                      }
                      ?>
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
        <h5 class="modal-title" id="exampleModalLabel">Open New Folder</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="post" action="{{action('Dean\LectureNoteController@openNewFolder')}}">
        {{csrf_field()}}
      <div class="modal-body">
        <div id="message"></div>
        <br>
        <div class="row">
            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                    <i class="fa fa-folder" aria-hidden="true" style="font-size: 18px;"></i>
                </p>
            </div>
                <div class="col-11" style="padding-left: 20px;">
                <div class="form-group">
                      <label for="subject_type" class="bmd-label-floating">Folder Name</label>
                      <input type="text" name="folder_name" class="form-control" required/>
                      <input type="hidden" name="folder_place" value="Note">
                      <input type="hidden" value="{{$course[0]->course_id}}" name="course_id">
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
<div class="modal fade bd-example-modal-lg" id="folderNameEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit Folder Name</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="post" action="{{action('Dean\LectureNoteController@updateFolderName')}}">
        {{csrf_field()}}
      <div class="modal-body">
        <div id="message"></div>
        <br>
        <div class="row">
            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                    <i class="fa fa-folder" aria-hidden="true" style="font-size: 18px;"></i>
                </p>
            </div>
                <div class="col-11" style="padding-left: 20px;">
                <div class="form-group">
                      <label for="subject_type" class="label">Folder Name</label>
                      <input type="hidden" name="ln_id" id="ln_id">
                      <input type="text" name="folder_name" class="form-control" id="folder_name" placeholder="Folder" required/>
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
        <h5 class="modal-title" id="exampleModalLabel">Upload Files</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="post" action="{{route('note.dropzone.uploadFiles')}}" enctype="multipart/form-data"
        class="dropzone" id="dropzoneFile" style="margin: 20px;font-size: 20px;color:#a6a6a6;border-style: double;">
        @csrf

      </form>
      <form method="post" action="{{action('Dean\LectureNoteController@storeFiles')}}">
        {{csrf_field()}}
        <input type="hidden" name="count" value="0" id="count">
        <input type="hidden" name="file_place" value="Note">
        <input type="hidden" value="{{$course[0]->course_id}}" name="course_id">
        <div id="writeInput"></div>
        <div class="modal-footer">
        <button type="button" class="btn btn-raised btn-secondary" data-dismiss="modal">Close</button>
        &nbsp;
        <input type="submit" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;margin-right: 13px;" value="Save Changes">
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="openPreviousModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="box-shadow: 1px 1px 2px #aaaaaa;border:0px solid black;padding:15px 8px ;">
        <h5 class="modal-title" id="exampleModalLabel">Select Previous Semester of Notes</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div style="margin:0px; padding: 10px 0px 10px 10px;border-bottom: 1px solid black;" class="href_link">
        <a style="padding:0px;margin: 0px;" class="select_semester">
          Semester
        </a> &nbsp;/&nbsp;
      </div>
      <div id="previous">
      @foreach($previous_semester as $row)
      <div class="row semester hover" id="semester_{{$row->course_id}}" style="margin:0px; padding: 0px;">
        <div class="col-1" id="icon_image">
          <img src="{{url('image/folder2.png')}}" width="25px" height="25px"/>
        </div>
        <div class="col name">
          <p style="padding: 5px 0px;margin: 0px;" class="previous_{{$row->course_id}}">{{$row->semester_name}}</p>
        </div>
      </div>
      @endforeach
      </div>
      <form method="post" action="{{action('Dean\LectureNoteController@storePreviousFiles')}}" style="margin: 5px 0px;">
        {{csrf_field()}}
      <p style="margin: 0px 8px;">Selected Item (Count) : <span class="c_count_num"></span></p>
      <input type="hidden" name="c_count" id="c_count" value="0">
      <input type="hidden" name="checkbox_input" id="checkbox_input">
      <div class="modal-footer" style="border-top:0px solid #aaaaaa;">
        <button type="button" class="btn btn-raised btn-secondary" data-dismiss="modal">Close</button>
        &nbsp;
        <input type="submit" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;margin: 0px!important;" value="Save Changes">
      </div>
      </form>
    </div>
  </div>
</div>
@endsection