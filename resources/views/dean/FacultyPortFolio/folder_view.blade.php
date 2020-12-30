<?php
$title = "Department";
$option2 = "id='selected-sidebar'";
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

               if((image.width() > 380) && (image.width() < 441) && (image.width() != 422)){
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

        $(document).on('click', '#checkDownloadAction', function(){
          var checkedValue = ""; 
          var inputElements = document.getElementsByClassName('group_download_list');
          for(var i=0; inputElements[i]; i++){
            if(inputElements[i].checked){
              checkedValue += inputElements[i].value+"---";
            }
          }
          if(checkedValue!=""){
            var id = checkedValue;
            window.location = "/FacultyPortFolio/download/zipFiles/"+id+"/checked";
          }else{
              alert("Please select the document first.");
          }
        });

        $(document).on("click",".tp_title", function(){
            $('#plan_detail').slideToggle("slow", function(){
                // check paragraph once toggle effect is completed
                if($('#plan_detail').is(":visible")){
                    $('#icon').removeClass('fa fa-plus');
                    $('#icon').addClass('fa fa-minus');
                }else{
                    $('#icon').removeClass('fa fa-minus');
                    $('#icon').addClass('fa fa-plus');
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
            url:'/folderNameEdit',
            data:{value : num[2]},
            success:function(data){
              document.getElementById('fp_id').value = num[2];
              document.getElementById('folder_name').value = data.portfolio_name;
            } 
          });
          $('#folderNameEdit').modal('show');
          return false;
        });

        $(document).on('click', '.remove_button', function(){
          var id = $(this).attr("id");
          var num = id.split("_");
          if(confirm('Are you sure you want to remove the it?')) {
            window.location = "/FacultyPortFolio/remove/"+num[2];
          }
          return false;
        });

        $(document).on('click', '.download_button', function(){
            var id = $(this).attr("id");
            var num = id.split("_");
            window.location = "/faculty/portfolio/"+num[2];
        });
    });
    var i = 0;
    var file_up_names = [0];
    Dropzone.options.dropzoneFile =
    {
        acceptedFiles: ".pdf,.xlsx,.docx,.pptx,.ppt,.jpg,.jpeg,.png",
        addRemoveLinks: true,
        timeout: 50000,
        renameFile: function(file) {
            var re = /(?:\.([^.]+))?$/;
            var ext = re.exec(file.name)[1];
            var newName = new Date().getTime()+"."+ext;
            file_up_names.push(newName);
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
                url: '{{ url("/destoryFiles") }}',
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
          $.ajax({
              type:'POST',
              url:'/searchFiles',
              data:{value:value,place:place},
              success:function(data){
                document.getElementById("course").innerHTML = data;
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

                           if((image.width() > 380) && (image.width() < 441) && (image.width() != 422)){
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
            $.ajax({
               type:'POST',
               url:'/searchFiles',
               data:{value:value,place:place},
               success:function(data){
                  document.getElementById("course").innerHTML = data;
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

                           if((image.width() > 380) && (image.width() < 441) && (image.width() != 422)){
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
  .name{
    border-bottom:1px solid #aaaaaa;
    margin: 0px;
    color: #0d2f81;
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
    .name{
      border-bottom:1px solid #aaaaaa;
      margin: 0px 0px 0px -30px;
      color: #0d2f81;
    }
}

@media only screen and (min-width: 600px) {
  .tooltiptext{
    width:300px;
    background-color:#e6e6e6;
    color: black;
    text-align: left;
    border-radius: 6px;
    border:1px solid black;
    padding: 5px 10px;
    position: absolute;
    z-index: 1;
    top:38%;
    left:103%;
  }
}
</style>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$faculty->faculty_name}}</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/FacultyPortFolio"> Faculty ( Portfolio )</a>/
            @if($faculty_portfolio->portfolio_place=="Faculty")
                <span class="now_page">{{$faculty_portfolio->portfolio_name}}</span>/
            @else
                <?php
                    $place = explode(',,,',($faculty_portfolio->portfolio_place));
                    $place_name = explode(',,,',($data));
                    $i=1;
                    while(isset($place[$i])!=""){
                        echo "<a href='/faculty_portfolio/folder/$place[$i]'>".$place_name[$i]."</a>/";
                        $i++;
                    }
                ?>
                <span class="now_page">{{$faculty_portfolio->portfolio_name}}</span>/
            @endif
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 5px 10px;">
        <div class="col-md-12">
          <p class="page_title">{{$faculty_portfolio->portfolio_name}}</p>
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
                            <input type="hidden" id="place" value="{{$portfolio_place}}">
                            <input type="text" name="search" class="form-control search tooltip_hover" id="input" style="font-size: 18px;">
                            <span class="tooltiptext">
                              <span>
                                  <i class="fa fa-info-circle" style="color: #0d2f81;" aria-hidden="true"></i> Important : 
                              </span>
                              <hr style="background-color: #d9d9d9;margin: 3px 0px;">
                              <span>1. All PortFolio in Faculty</span><br/>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row" id="course" style="margin-top:-25px;">
                    @foreach($faculty_portfolio_list as $row)
                        @if($row->portfolio_type=="folder")
                        <div class="col-12 row align-self-center" id="course_list">
                          <div class="col-9 row align-self-center">
                            <div class="checkbox_style align-self-center">
                              <input type="checkbox" value="{{$row->fp_id}}" class="group_download_list">
                            </div>
                            <a href="/faculty_portfolio/folder/{{$row->fp_id}}" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">
                              <div class="col-1" style="position: relative;top: -2px;">
                                <img src="{{url('image/folder2.png')}}" width="25px" height="25px"/>
                              </div>
                              <div class="col-10" id="course_name">
                                  <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>{{$row->portfolio_name}}</b></p>
                              </div>
                            </a>
                          </div>
                          <div class="col-3" id="course_action_two">
                                <i class="fa fa-wrench edit_button" aria-hidden="true" id="edit_button_{{$row->fp_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>&nbsp;
                                <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_{{$row->fp_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>
                            </div>
                          </div>
                        @else
                        <?php
                          $ext = "";
                          if($row->portfolio_file!=""){
                            $ext = explode(".", $row->portfolio_file);
                          }
                        ?>
                        @if(($ext[1] == "pdf")||($ext[1] == "docx")||($ext[1] == "xlsx")||($ext[1] == "pptx")||($ext[1] == "ppt"))
                        <div class="col-12 row align-self-center" id="course_list">
                          <div class="col-9 row align-self-center">
                            <div class="checkbox_style align-self-center">
                              <input type="checkbox" value="{{$row->fp_id}}" class="group_download_list">
                            </div>
                            <a href="/faculty/portfolio/{{$row->fp_id}}" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">
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
                                  <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>{{$row->portfolio_name}}</b></p> 
                              </div>
                            </a>
                          </div>
                          <div class="col-3" id="course_action_two">
                                <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_{{$row->fp_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>
                          </div>
                        </div>
                        @else
                        <div class="col-12 row align-self-center" id="course_list">
                            <div class="col-9 row align-self-center">
                              <div class="checkbox_style align-self-center">
                                  <input type="checkbox" value="{{$row->fp_id}}" class="group_download_list">
                                </div>
                              <a href="/images/faculty_portfolio/{{$row->fp_id}}/{{$row->portfolio_file}}" data-toggle="lightbox" data-gallery="example-gallery_student" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="{{$row->portfolio_name}}">
                                <div class="col-1" style="position: relative;top: -3px;">
                                  <img src="{{url('image/img_icon.png')}}" width="25px" height="20px"/>
                                </div>
                                <div class="col-10" id="course_name">
                                      <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>{{$row->portfolio_name}}</b></p>
                                </div>
                              </a>
                            </div>
                            <div class="col-3" id="course_action_two">
                              <i class="fa fa-download download_button" aria-hidden="true" id="download_button_{{$row->fp_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>&nbsp;
                              <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_{{$row->fp_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>
                            </div>
                          </div>
                        @endif
                        @endif
                      @endforeach
                      <?php
                      if(count($faculty_portfolio_list)==0){
                      ?>
                      <div style="display: block;border:1px solid black;padding: 50px;width: 100%;margin: 0px 20px 10px 20px;">
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
      <form method="post" action="{{action('Dean\F_PortFolioController@openNewFolder')}}">
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
                      <input type="hidden" name="folder_place" value="{{$faculty_portfolio->portfolio_place}},,,{{$faculty_portfolio->fp_id}}">
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
      <form method="post" action="{{action('Dean\F_PortFolioController@updateFolderName')}}">
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
                      <input type="hidden" name="fp_id" id="fp_id" value="">
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
      <form method="post" action="{{route('dropzone.uploadFiles')}}" enctype="multipart/form-data"
        class="dropzone" id="dropzoneFile" style="margin: 20px;font-size: 20px;color:#a6a6a6;border-style: double;">
        @csrf

      </form>
      <form method="post" action="{{action('Dean\F_PortFolioController@storeFiles')}}">
        {{csrf_field()}}
        <input type="hidden" name="count" value="0" id="count">
        <input type="hidden" name="file_place" value="{{$faculty_portfolio->portfolio_place}},,,{{$faculty_portfolio->fp_id}}">
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
