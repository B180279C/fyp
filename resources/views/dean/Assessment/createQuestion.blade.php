<?php
$title = "Course";
$option1 = "id='selected-sidebar'";
?>
@extends('layouts.nav_dean')

@section('content')
<style type="text/css">
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
.dropzone .dz-preview .dz-remove-new{
  text-align: left;
  padding-left: 25px;
  display: inline-block;
  font-size: 14px;
}
#course_list:hover{
    text-decoration: none;
    background-color: #f2f2f2;
}
#show_image_link:hover{
    text-decoration: none;
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
    width: 240px;
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
        margin-left:-28px;
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
  $(document).ready(function(){
        $(document).on('click', '.p_sem_plus', function(){
            $('#previous').slideToggle("slow", function(){
                if($('#previous').is(":visible")){
                    $('#icon').removeClass('fa fa-plus');
                    $('#icon').addClass('fa fa-minus');
                }else{
                    $('#icon').removeClass('fa fa-minus');
                    $('#icon').addClass('fa fa-plus');
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
  function isset(element) {
    return element.length > 0;
  }
  $(document).ready(function(){  
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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

        $(document).on('click', '.edit_button', function(){
          var id = $(this).attr("id");
          var num = id.split("_");
          $.ajax({
            type:'POST',
            url:'/assessment/folderNameEdit',
            data:{value : num[2]},
            success:function(data){
              document.getElementById('ass_id').value = num[2];
              document.getElementById('folder_name').value = data.ass_name;
            } 
          });
          $('#folderNameEdit').modal('show');
          return false;
        });

        $(document).on('click', '.download_button', function(){
          var id = $(this).attr("id");
          var num = id.split("_");
          window.location = "/assessment/download/"+num[2];
        });

        $(document).on('click', '.remove_button', function(){
          var id = $(this).attr("id");
          var num = id.split("_");
          if(confirm('Are you sure you want to remove the it?')) {
            window.location = "/assessment/remove/"+num[2];
          }     
        });
  });
  var i = 0;
  var m = 1;
  Dropzone.options.dropzoneFile =
  {
        acceptedFiles: ".jpg,.jpeg,.png",
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
              $('.submit_button').prop('disabled', true);
              i++;
              var re = /(?:\.([^.]+))?$/;
              var ext = re.exec(file.name)[0];
              var filename = file.name.split(ext);
              file._captionLabel = Dropzone.createElement("<div class='changelabel'><label class='label' style='font-size:13px'>File Name</label></div>")
              file._captionBox = Dropzone.createElement("<div class='changeName'><input id='"+i+"' type='text' name='caption' value='"+filename[0]+"' class='form-control filename'><div id='loader"+i+"' class='loader'></div><span id='loading_word"+i+"' class='loading_word'> Loading OCR </span></div>");
              file.previewElement.appendChild(file._captionLabel);
              file.previewElement.appendChild(file._captionBox);
              $('.dz-remove').css('display','none');
              $('.dz-remove').attr('class', 'dz-remove'+i+"   dz-remove-new");
              //chinese chi_sim
              Tesseract.recognize(
                file,
                'eng',
                { 
                  logger: m => console.log(m)
                }
              ).then(({ data: { text } }) => {
                // console.log(text);
                $('.dz-remove'+m).css('display','block');
                $('#loader'+m).css('display','none');
                $('#loading_word'+m).css('display','none');
                writeInput(m,filename[0],ext,file.upload.filename,text);
                m++;
                if (!isset($('#loader'+m))){
                  if(m>=i){
                    $('.submit_button').prop('disabled', false);
                  }
                }
              });
            });
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
                    document.getElementById("text"+id).value = "";
                }
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                type: 'POST',
                url: '{{ url("/ass_destoryFiles") }}',
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
  function writeInput(num,name,ext,fake,text){
    $(document).ready(function(){  
      $("#writeInput").append("<input type='hidden' id='form"+num+"' name='form"+num+"' value='"+name+"'><input type='hidden' id='ext"+num+"' name='ext"+num+"' value='"+ext+"'><input type='hidden' id='fake"+num+"' name='fake"+num+"' value='"+fake+"'><input type='hidden' id='text"+num+"' name='text"+num+"'>");
      $('#text'+num).val(text);
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
          var question = $('#question').val();
          $.ajax({
              type:'POST',
              url:'/assessment/searchKey/',
              data:{value:value,course_id:course_id,place:place,question:question},
              success:function(data){
                document.getElementById("assessments").innerHTML = data;
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
                      // console.log(image.width());

                      if((image.width() > 380) && (image.width() < 430)){
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
            var question = $('#question').val();
            $.ajax({
               type:'POST',
               url:'/assessment/searchKey/',
               data:{value:value,course_id:course_id,place:place,question:question},
               success:function(data){
                    document.getElementById("assessments").innerHTML = data;
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
                      // console.log(image.width());

                      if((image.width() > 380) && (image.width() < 430)){
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
<div style="background-color:white;">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/course_list">Courses </a>/
            <a href="/course/action/{{$course[0]->course_id}}">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</a>/
            <a href="/assessment/{{$course[0]->course_id}}">Continuous Assessment</a>/
            <span class="now_page">{{$question}}</span>/
        </p>
        <hr style="margin: -10px 10px;">
    </div>
    <div class="row" style="padding: 10px 10px 10px 10px;">
        <div class="col-md-12">
             <p style="display: inline;font-size: 25px;position: relative;top: 5px;left: 10px;color: #0d2f81">{{$question}}</p>
             <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                <div id="action_sidebar" class="w3-animate-right" style="display: none">
                    <div style="text-align: right;padding:10px;">
                        <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                  <ul class="sidebar-action-ul">
                      <a id="open_folder"><li class="sidebar-action-li"><i class="fa fa-folder" style="padding: 0px 10px;" aria-hidden="true"></i>Make a New Folder</li></a>
                      <a id="open_document"><li class="sidebar-action-li"><i class="fa fa-upload" style="padding: 0px 10px;" aria-hidden="true"></i>Upload Files</li></a>
                  </ul>
                </div>
                <br>
                <br>
            <div class="details" style="padding: 0px 5px 5px 5px;">
              <div class="col-md-6 row" style="padding:0px 20px;position: relative;top: -25px;">
                  <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                      <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                          <i class="fa fa-search" aria-hidden="true" style="font-size: 20px;"></i>
                      </p>
                  </div>
                  <div class="col-11" style="padding-left: 20px;">
                      <div class="form-group">
                          <label for="full_name" class="bmd-label-floating">Search</label>
                          <input type="hidden" id="place" value="{{$question}}">
                          <input type="hidden" value="{{$course[0]->course_id}}" id="course_id">
                          <input type="hidden" value="{{$question}}" id="question">
                          <input type="text" name="search" class="form-control search" id="input" style="font-size: 18px;">
                      </div>
                  </div>
              </div>
              
              <div class="row" id="assessments">
              <h5 style="margin-top: -15px;padding-left: 15px;">Current Semester</h5>
              <?php
              $i=0;
              ?>
              @foreach($assessments as $row)
                @if($row->ass_type=="folder")
                <div class="col-12 row align-self-center" id="course_list">
                  <a href="/assessment/folder/{{$row->ass_id}}" id="show_image_link" class="col-9 row align-self-center">
                    <div class="col-12 row" style="padding:10px;color:#0d2f81;">
                      <div class="col-1" style="position: relative;top: -2px;">
                        <img src="{{url('image/folder2.png')}}" width="25px" height="25px"/>
                      </div>
                      <div class="col-10" id="course_name">
                        <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>{{$row->ass_name}}</b></p>
                      </div>
                    </div>
                  </a>
                  <div class="col-3" id="course_action_two">
                    <i class="fa fa-wrench edit_button" aria-hidden="true" id="edit_button_{{$row->ass_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>&nbsp;
                    <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_{{$row->ass_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>
                  </div>
                </div>
                @else
                <div class="col-12 row align-self-center" id="course_list">
                  <a href="/images/assessment/{{$row->ass_document}}" data-toggle="lightbox" data-gallery="example-gallery" class="col-9 row align-self-center" id="show_image_link" data-title="{{$course[0]->semester_name}} : {{$question}} / {{$row->ass_name}} <br> <a href='/assessment/view/whole_paper/{{$row->ass_id}}' class='full_question'>Whole paper</a>">
                    <div class="col-12 row" style="padding:10px;color:#0d2f81;">
                      <div class="col-1" style="position: relative;top: -2px;">
                        <img src="{{url('image/img_icon.png')}}" width="25px" height="20px"/>
                      </div>
                      <div class="col-10" id="course_name">
                        <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;color: black;" id="file_name"> <b>{{$row->ass_name}}</b></p>
                      </div>
                    </div>
                  </a>
                  <div class="col-3" id="course_action_two">
                    <i class="fa fa-download download_button" aria-hidden="true" id="download_button_{{$row->ass_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>&nbsp;
                    <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_{{$row->ass_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>
                  </div>
                </div>
                @endif
              <?php
              $i++;
              ?>
              @endforeach
              @if($i==0)
              <div style="display: block;border:1px solid black;padding: 50px;width: 100%;margin: 10px 20px 0px 20px;">
                  <center>Empty</center>
              </div>
              @endif
              </div>
              <hr>
              <h5 style="padding-left: 3px;" class="p_sem_plus">Previous Semester (<i class="fa fa-plus" aria-hidden="true" id="icon" style="color: #0d2f81;position: relative;top: 2px;"></i>)</h5>
                <div class="row" id="previous" style="display: none;">
                  <?php
                  $p = 0;
                  ?>
                  @foreach($previous_semester as $row_2)
                    @foreach($group_assessments as $row_3)
                    @if($row_2->course_id == $row_3->course_id)
                    <?php
                    $p++;
                    ?>
                    <div class="col-12 row align-self-center" id="course_list">
                        <a href="/assessment/folder/{{$course[0]->course_id}}/previous/{{$row_3->course_id}}/question/{{$question}}/once" id="show_image_link" class="col-9 row align-self-center">
                          <div class="col-12 row" style="padding:10px;color:#0d2f81;">
                            <div class="col-1" style="position: relative;top: -2px;">
                              <img src="{{url('image/folder2.png')}}" width="25px" height="25px"/>
                            </div>
                            <div class="col-10" id="course_name">
                              <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>{{$row_2->semester_name}} : {{$question}} </b></p>
                            </div>
                          </div>
                        </a>
                    </div>
                    @endif
                    @endforeach
                  @endforeach
                
                @if($p==0)
                <div style="display: block;border:1px solid black;padding: 50px;width: 100%;margin: 10px 20px 0px 20px;">
                  <center>Empty</center>
                </div>
                @endif
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
    padding: 10px!important;
    margin: 0px!important;
    border:none!important;
  }
  .title2{
    font-size: 21.6px!important;
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
      <form method="post" action="{{action('AssessmentController@openNewFolder')}}">
        {{csrf_field()}}
      <div class="modal-body body2">
        <div id="message"></div>
        <br>
        <div class="row">
            <div class="col-md-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                    <i class="fa fa-folder" aria-hidden="true" style="font-size: 18px;"></i>
                </p>
            </div>
                <div class="col-11" style="padding-left: 20px;">
                <div class="form-group">
                      <label for="subject_type" class="bmd-label-floating">Folder Name</label>
                      <input type="hidden" name="course_id" value="{{$course[0]->course_id}}">
                      <input type="text" name="folder_name" class="form-control" required/>
                      <input type="hidden" name="assessment" value="{{$question}}">
                      <input type="hidden" name="folder_place" value="{{$question}}">
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
      <form method="post" action="{{action('AssessmentController@updateFolderName')}}">
        {{csrf_field()}}
      <div class="modal-body body2">
        <div id="message"></div>
        <br>
        <div class="row">
            <div class="col-md-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                    <i class="fa fa-folder" aria-hidden="true" style="font-size: 18px;"></i>
                </p>
            </div>
                <div class="col-11" style="padding-left: 20px;">
                <div class="form-group">
                      <label for="subject_type" class="label">Folder Name</label>
                      <input type="hidden" name="ass_id" id="ass_id">
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
        <h5 class="modal-title title2"  id="exampleModalLabel">Upload Files</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="post" action="{{route('assessment.dropzone.uploadFiles')}}" enctype="multipart/form-data"
        class="dropzone" id="dropzoneFile" style="margin: 20px;font-size: 20px;color:#a6a6a6;border-style: double;">
        @csrf

      </form>
      <form method="post" action="{{action('AssessmentController@storeFiles')}}">
        {{csrf_field()}}
        <input type="hidden" name="count" value="0" id="count">
        <input type="hidden" name="file_place" value="{{$question}}">
        <input type="hidden" name="assessment" value="{{$question}}">
        <input type="hidden" value="{{$course[0]->course_id}}" name="course_id">
        <div id="writeInput"></div>
        <div class="modal-footer">
        <button type="button" class="btn btn-raised btn-secondary" data-dismiss="modal">Close</button>
        &nbsp;
        <input type="submit" class="btn btn-raised btn-primary submit_button" style="background-color: #3C5AFF;color: white;margin-right: 13px;" disabled value="Save Changes">
        </div>
      </form>
    </div>
  </div>
</div>
@endsection