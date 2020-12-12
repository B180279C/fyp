<?php
$title = "Course";
$option1 = "id='selected-sidebar'";
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
  $(document).ready(function(){  
    $('[data-toggle="lightbox"]').click(function(event) {
             event.preventDefault();
                 $(this).ekkoLightbox({
                   type: 'image',
                   onContentLoaded: function() {
                     var container = $('.ekko-lightbox-container');
                     var content = $('.modal-content');
                     var backdrop = $('.modal-backdrop');
                     var overlay = $('.ekko-lightbox-nav-overlay');
                     var modal = $('.modal');
                     var image = container.find('img');
                     var windowHeight = $(window).height();
                     var dialog = container.parents('.modal-dialog');
                     var data_header = $('.modal-header');
                     var data_title = $('.modal-title');
                     var body = $('.modal-body');
                     console.log(image.width());

                     if((image.width() > 380) && (image.width() < 441)){
                        dialog.css('max-width','700px');
                        image.css('height','900px');
                        image.css('width','700px');
                        overlay.css('height','900px');
                     }else{
                        overlay.css('height','100%');
                     }
                     // backdrop.css('opacity','1');
                     data_header.css('background-color','white');
                     data_header.css('padding','10px');
                     data_header.css('margin','0px 24px');
                     data_header.css('border-bottom','1px solid black');
                     data_title.css('font-size','18px');

                     body.css('padding-top','10px');
                     body.css('padding-bottom','0px');
                     body.css('margin', "0px 24px");
                     body.css('background-color', "white");
                     content.css('background', "none");
                     content.css('-webkit-box-shadow', "0 5px 15px rgba(0,0,0,0)");
                     content.css('-moz-box-shadow', "0 5px 15px rgba(0,0,0,0)");
                     content.css('-o-box-shadow', "0 5px 15px rgba(0,0,0,0)");
                     content.css('box-shadow', "0 5px 15px rgba(0,0,0,0)");
                   }
                 });
           });
    $(document).on('click', '#checkDownloadAction', function(){
      var checkedValue = ""; 
      var inputElements = document.getElementsByClassName('group_download_list');
      for(var i=0; inputElements[i]; i++){
        if(inputElements[i].checked){
          checkedValue += inputElements[i].value+"---";
        }
      }
      if(checkedValue!=""){
        var course_id = $('#course_id').val();
        var id = course_id+"---"+checkedValue;
        window.location = "{{$character}}/lectureNote/download/zipFiles/"+id+"/checked";
      }else{
          alert("Please select the document first.");
      }
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

    $(document).on('click', '.download_button', function(){
        var id = $(this).attr("id");
        var num = id.split("_");
        window.location = "{{$character}}/lectureNote/download/"+num[2];
    });

    $(document).on('click', '.edit_button', function(){
      var id = $(this).attr("id");
      var num = id.split("_");
      $.ajax({
        type:'POST',
        url:'{{$character}}/lectureNote/folderNameEdit',
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
        window.location = "{{$character}}/lectureNote/remove/"+num[2];
      }
      return false;
    });
  });

  var i = 0;
  Dropzone.options.dropzoneFile =
  {
        acceptedFiles: ".pdf,.xlsx,.docx,.pptx,.ppt,.jpg,.jpeg,.png",
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
            $(file.previewElement).find(".dz-image img").css('margin-left','13px');
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
              default:
                $(file.previewElement).find(".dz-image img").attr("src", "{{url('image/file.png')}}").width('80px');
                $(file.previewElement).find(".dz-image img").css('margin-left','20px');
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
                url: '{{ url($character."/note_destoryFiles") }}',
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
              url:'{{$character}}/lectureNote/searchFiles',
              data:{value:value,course_id:course_id,place:place},
              success:function(data){
                document.getElementById("lecture_note").innerHTML = data;
                $('[data-toggle="lightbox"]').click(function(event) {
                   event.preventDefault();
                       $(this).ekkoLightbox({
                         type: 'image',
                         onContentLoaded: function() {
                           var container = $('.ekko-lightbox-container');
                           var content = $('.modal-content');
                           var backdrop = $('.modal-backdrop');
                           var overlay = $('.ekko-lightbox-nav-overlay');
                           var modal = $('.modal');
                           var image = container.find('img');
                           var windowHeight = $(window).height();
                           var dialog = container.parents('.modal-dialog');
                           var data_header = $('.modal-header');
                           var data_title = $('.modal-title');
                           var body = $('.modal-body');
                           console.log(image.width());

                           if((image.width() > 380) && (image.width() < 441)){
                              dialog.css('max-width','700px');
                              image.css('height','900px');
                              image.css('width','700px');
                              overlay.css('height','900px');
                           }else{
                              overlay.css('height','100%');
                           }
                           // backdrop.css('opacity','1');
                           data_header.css('background-color','white');
                           data_header.css('padding','10px');
                           data_header.css('margin','0px 24px');
                           data_header.css('border-bottom','1px solid black');
                           data_title.css('font-size','18px');

                           body.css('padding-top','0px');
                           body.css('padding-bottom','0px');
                           body.css('margin', "0px 24px");
                           body.css('background-color', "white");
                           content.css('background', "none");
                           content.css('-webkit-box-shadow', "0 5px 15px rgba(0,0,0,0)");
                           content.css('-moz-box-shadow', "0 5px 15px rgba(0,0,0,0)");
                           content.css('-o-box-shadow', "0 5px 15px rgba(0,0,0,0)");
                           content.css('box-shadow', "0 5px 15px rgba(0,0,0,0)");
                         }
                       });
                 });
              }
          });
        }
        $(".search").keyup(function(){
            var value = $('.search').val();
            var place = $('#place').val();
            var course_id = $('#course_id').val();
            $.ajax({
               type:'POST',
               url:'{{$character}}/lectureNote/searchFiles',
               data:{value:value,course_id:course_id,place:place},
               success:function(data){
                  document.getElementById("lecture_note").innerHTML = data;
                  $('[data-toggle="lightbox"]').click(function(event) {
                   event.preventDefault();
                       $(this).ekkoLightbox({
                         type: 'image',
                         onContentLoaded: function() {
                           var container = $('.ekko-lightbox-container');
                           var content = $('.modal-content');
                           var backdrop = $('.modal-backdrop');
                           var overlay = $('.ekko-lightbox-nav-overlay');
                           var modal = $('.modal');
                           var image = container.find('img');
                           var windowHeight = $(window).height();
                           var dialog = container.parents('.modal-dialog');
                           var data_header = $('.modal-header');
                           var data_title = $('.modal-title');
                           var body = $('.modal-body');
                           console.log(image.width());

                           if((image.width() > 380) && (image.width() < 441)){
                              dialog.css('max-width','700px');
                              image.css('height','900px');
                              image.css('width','700px');
                              overlay.css('height','900px');
                           }else{
                              overlay.css('height','100%');
                           }
                           // backdrop.css('opacity','1');
                           data_header.css('background-color','white');
                           data_header.css('padding','10px');
                           data_header.css('margin','0px 24px');
                           data_header.css('border-bottom','1px solid black');
                           data_title.css('font-size','18px');

                           body.css('padding-top','0px');
                           body.css('padding-bottom','0px');
                           body.css('margin', "0px 24px");
                           body.css('background-color', "white");
                           content.css('background', "none");
                           content.css('-webkit-box-shadow', "0 5px 15px rgba(0,0,0,0)");
                           content.css('-moz-box-shadow', "0 5px 15px rgba(0,0,0,0)");
                           content.css('-o-box-shadow', "0 5px 15px rgba(0,0,0,0)");
                           content.css('box-shadow', "0 5px 15px rgba(0,0,0,0)");
                         }
                       });
                 });
               }
            });
        });
    });
</script>
<style type="text/css">
.checkbox_group_style{
  border:0px solid black;
  padding: 1px 10px 0px 10px!important;
  margin: 0px!important;
}
.checkbox_style{
  border:0px solid black;
  padding: 0px 5px!important;
  margin: 0px!important;
  display: inline;
  width: 28px;
}
.group{
  margin-top:3px;
  padding-left: 15px;
  border:0px solid black;
  display: inline;
  padding: 0px!important;
  margin: 0px!important;
}
.question_link:hover{
    background-color: #d9d9d9;
    text-decoration: none;
    color: #0d2f81;
}
#show_image_link:hover{
    text-decoration: none;
}
.plus:hover{
    background-color: #f2f2f2;
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
  #assessment_name{
    margin-left: 0px;
    padding-top: 0px;
  }
  #assessment_word{
    margin-left: 0px;
    padding-top: 0px;
  }
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
    width: 240px;
    margin: 0px;
    padding:0px;
  }
  #lecturer_name{
    display: none;
  }
}
@media only screen and (min-width: 600px) {
    #course_list{
      margin-left: 0px;
      padding: 4px 15px;
    }
    #assessment_name{
        margin-left:-53px;
        padding-top:0px;
    }
    #assessment_word{
        margin-left:-48px;
        padding-top:0px;
    }
    #course_name{
        margin-left:-18px;
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
    #lecturer_name{
      text-align: right;
      position:relative;
      top:7px;
      border:0px solid black;
      padding: 0px;
      display: block;
    }
}
</style>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</p>
        <p class="pass_page">
            <a href="{{$character}}/home" class="first_page"> Home </a>/
            <a href="{{$character}}/course_list">Courses </a>/
            <a href="{{$character}}/course/action/{{$course[0]->course_id}}">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</a>/
            <a href="{{$character}}/lectureNote/{{$course[0]->course_id}}">Lecture Note</a>/
            <?php
                $place = explode(',,,',($lecture_note->note_place));
                $place_name = explode(',,,',($data));
                $i=1;
                if($lecture_note->used_by!=""){
                  foreach($all_note as $all_row){
                    if(($lecture_note->used_by)==($all_row->ln_id)){
                      $note_name = $lecture_note->note_name." (".$all_row->semester_name.")";
                    }
                  }
                }else{
                  $note_name = $lecture_note->note_name;
                }
                while(isset($place[$i])!=""){
                  if($lecture_note->used_by!=""){
                    foreach($all_note as $all_row){
                      if(($lecture_note->used_by)==($all_row->ln_id)){
                        echo "<a href='".$character."/lectureNote/folder/$place[$i]'>".$place_name[$i]." (".$all_row->semester_name.")</a>/";
                      }
                    }
                  }else{
                    echo "<a href='".$character."/lectureNote/folder/$place[$i]'>".$place_name[$i]."</a>/";
                  }
                  $i++;
                }
            ?>
            <span class="now_page">{{$note_name}}</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 5px 10px;">
        <div class="col-md-12">
             <p class="page_title">{{$note_name}}</p>
             <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                <div id="action_sidebar" class="w3-animate-right" style="display: none">
                    <div style="text-align: right;padding:10px;">
                        <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                  <ul class="sidebar-action-ul">
                      <a id="open_folder"><li class="sidebar-action-li"><i class="fa fa-folder" style="padding: 0px 10px;" aria-hidden="true"></i>Make a new Folder</li></a>
                      <a id="open_document"><li class="sidebar-action-li"><i class="fa fa-upload" style="padding: 0px 10px;" aria-hidden="true"></i>Upload Files</li></a>
                      <p class="title_method">Download</p>
                      <a id="checkDownloadAction"><li class="sidebar-action-li"><i class="fa fa-check-square-o" style="padding: 0px 10px;" aria-hidden="true"></i>Checked Item</li></a>
                      <a href='{{$character}}/lectureNote/download/zipFiles/{{$lecture_note->ln_id}}/folder'><li class="sidebar-action-li"><i class="fa fa-download" style="padding: 0px 10px;" aria-hidden="true"></i>All Note</li></a>
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
                            <input type="hidden" id="place" value="{{$lecture_note->note_place}},,,{{$lecture_note->ln_id}}">
                            <input type="hidden" value="{{$course[0]->course_id}}" id="course_id">
                            <input type="text" name="search" class="form-control search" id="input" style="font-size: 18px;">
                        </div>
                    </div>
                </div>
                <div class="row" id="lecture_note" style="margin-top:-25px;">
                      <?php
                      $i=0;
                      ?>
                      @foreach($lecture_note_list as $row)
                        @if($row->note_type=="folder")
                        <div class="col-12 row align-self-center" id="course_list">
                          <div class="col-9 row align-self-center">
                            <div class="checkbox_style align-self-center">
                              <input type="checkbox" value="{{$row->ln_id}}" class="group_download_list">
                            </div>
                            <a href="{{$character}}/lectureNote/folder/{{$row->ln_id}}" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">
                              <div class="col-1" style="position: relative;top: -2px;">
                                <img src="{{url('image/folder2.png')}}" width="25px" height="25px"/>
                              </div>
                              <div class="col-10" id="course_name">
                                @if($row->used_by!=null)
                                  @foreach($all_note as $all_row)
                                    @if(($row->used_by)==($all_row->ln_id))
                                      <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>{{$row->note_name}} <span style="color: grey;">( Used In : {{$all_row->semester_name}} )</span></b></p>
                                    @endif
                                  @endforeach
                                @else
                                  <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>{{$row->note_name}}</b></p>
                                @endif  
                              </div>
                            </a>
                          </div>
                          <div class="col-3" id="course_action_two">
                            @if($row->used_by==null)
                                <i class="fa fa-wrench edit_button" aria-hidden="true" id="edit_button_{{$row->ln_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>&nbsp;
                            @endif
                                <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_{{$row->ln_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>
                            </div>
                          </div>
                        @else
                          <?php
                            $ext = "";
                            if($row->note){
                              $ext = explode(".", $row->note);
                            }
                          ?>
                       @if(($ext[1] == "pdf")||($ext[1] == "docx")||($ext[1] == "xlsx")||($ext[1] == "pptx")||($ext[1] == "ppt"))
                        <div class="col-12 row align-self-center" id="course_list">
                          <div class="col-9 row align-self-center">
                            <div class="checkbox_style align-self-center">
                              <input type="checkbox" value="{{$row->ln_id}}" class="group_download_list">
                            </div>
                            <a href="{{$character}}/lectureNote/download/{{$row->ln_id}}" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">
                              <div class="col-1" style="position: relative;top: -2px;">
                               @if($ext[1]=="pdf")
                                <img src="{{url('image/pdf.png')}}" width="25px" height="25px"/>
                                @elseif($ext[1]=="docx")
                                <img src="{{url('image/docs.png')}}" width="25px" height="25px"/>
                                @elseif($ext[1]=="xlsx")
                                <img src="{{url('image/excel.png')}}" width="25px" height="25px"/>
                                @elseif($ext[1]=="pptx")
                                <img src="{{url('image/pptx.png')}}" width="25px" height="25px"/>
                                @elseif($ext[1]=="ppt")
                                <img src="{{url('image/pptx.png')}}" width="25px" height="25px"/>
                               @endif
                              </div>
                              <div class="col-10" id="course_name">
                                @if($row->used_by!=null)
                                  @foreach($all_note as $all_row)
                                    @if(($row->used_by)==($all_row->ln_id))
                                      <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>{{$row->note_name}} <span style="color: grey;">( Used In : {{$all_row->semester_name}} )</span></b></p>
                                    @endif
                                  @endforeach
                                @else
                                  <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>{{$row->note_name}}</b></p>
                                @endif  
                              </div>
                            </a>
                          </div>
                          <div class="col-3" id="course_action_two">
                                <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_{{$row->ln_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>
                          </div>
                        </div>
                          @else
                          @if($row->used_by!=null)
                            @foreach($all_note as $all_row)
                              @if(($row->used_by)==($all_row->ln_id))
                                <?php
                                  $semester_name = "<span style='color: grey;'>( Used In :".$all_row->semester_name.")</span>";
                                ?>
                              @endif
                            @endforeach
                          @else
                            <?php
                              $semester_name = "";
                            ?>
                          @endif
                          <div class="col-12 row align-self-center" id="course_list">
                            <div class="col-9 row align-self-center">
                              <div class="checkbox_style align-self-center">
                                  <input type="checkbox" value="{{$row->ln_id}}" class="group_download_list">
                                </div>
                              <a href="{{$character}}/images/lectureNote/{{$row->ln_id}}/{{$row->note}}" data-toggle="lightbox" data-gallery="example-gallery_student" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="{{$row->note_name}} {{$semester_name}}">
                                <div class="col-1" style="position: relative;top: -2px;">
                                  <img src="{{url('image/img_icon.png')}}" width="25px" height="20px"/>
                                </div>
                                <div class="col-10" id="course_name">
                                   @if($row->used_by!=null)
                                      @foreach($all_note as $all_row)
                                        @if(($row->used_by)==($all_row->ln_id))
                                          <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>{{$row->note_name}} <span style="color: grey;">( Used In : {{$all_row->semester_name}} )</span></b></p>
                                        @endif
                                      @endforeach
                                    @else
                                      <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>{{$row->note_name}}</b></p>
                                    @endif  
                                </div>
                              </a>
                            </div>
                            <div class="col-3" id="course_action_two">
                              <i class="fa fa-download download_button" aria-hidden="true" id="download_button_{{$row->ln_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>&nbsp;
                              <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_{{$row->ln_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>
                            </div>
                          </div>
                          @endif
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

<style type="text/css">
  .content2{
    position:relative;
    background-color:#fff!important;
    background-clip:padding-box;
    border-radius:3px;
    -webkit-box-shadow:0 3px 9px rgba(0,0,0,.5)!important;
    box-shadow:0 3px 9px rgba(0,0,0,.5)!important;
    outline:0;
  }
  .header2{
    padding: 24px 24px 0px 24px!important;
    margin: 0px!important;
    border:none!important;
  }
  .title2{
    font-size: 20px!important;
  }
  .body2{
    padding-top: 20px!important;
  }
</style>

<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="openFolderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content content2">
      <div class="modal-header header2">
        <h5 class="modal-title title2" id="exampleModalLabel">Open New Folder</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="post" action="{{$character}}/lectureNote/openNewFolder">
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
                      <input type="hidden" name="folder_place" value="{{$lecture_note->note_place}},,,{{$lecture_note->ln_id}}">
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
    <div class="modal-content content2">
      <div class="modal-header header2">
        <h5 class="modal-title title2" id="exampleModalLabel">Edit Folder Name</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="post" action="{{$character}}/lectureNote/updateFolderName">
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
    <div class="modal-content content2">
      <div class="modal-header header2">
        <h5 class="modal-title title2" id="exampleModalLabel">Upload Files</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="post" action="{{$character}}/note_uploadFiles" enctype="multipart/form-data"
        class="dropzone" id="dropzoneFile" style="margin: 20px;font-size: 20px;color:#a6a6a6;border-style: double;">
        @csrf

      </form>
      <form method="post" action="{{$character}}/note_storeFiles">
        {{csrf_field()}}
        <input type="hidden" name="count" value="0" id="count">
        <input type="hidden" name="file_place" value="{{$lecture_note->note_place}},,,{{$lecture_note->ln_id}}">
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
@endsection