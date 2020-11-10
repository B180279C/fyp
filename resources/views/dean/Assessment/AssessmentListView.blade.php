<?php
$title = "Course";
$option1 = "id='selected-sidebar'";
?>
@extends('layouts.nav_dean')

@section('content')
<style type="text/css">
.checkbox_group_style{
  border:0px solid black;
  padding: 1px 10px 0px 10px!important;
  margin: 0px!important;
}
.checkbox_style{
  border:0px solid black;
  padding: 0px 10px!important;
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
.dropzone .dz-preview .dz-remove-new {
  text-align: left;
  padding-left: 25px;
  display: inline-block;
  font-size: 14px;
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

  function setType(value){
    $('#ass_type').val(value);
  }
  $(document).ready(function(){  

        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });

        $('.group_checkbox').click(function(){
            var id = $(this).attr("id");
            var type = id.split("group_");
            // console.log(type[1]);
            if($(this).prop("checked") == true){
              $('.group_'+type[1]).prop("checked", true);
            }
            else if($(this).prop("checked") == false){
              $('.group_'+type[1]).prop("checked", false);
            }
        });

        $(document).on('click', '.plus', function(){
            var id = $(this).attr("id"); 
            $('#assessment_list_'+id).slideToggle("slow", function(){
              if($('#assessment_list_'+id).is(":visible")){
                $('#icon_'+id).removeClass('fa fa-plus');
                $('#icon_'+id).addClass('fa fa-minus');
              }else{
                $('#icon_'+id).removeClass('fa fa-minus');
                $('#icon_'+id).addClass('fa fa-plus');
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


        $(document).on('click', '.download_button', function(){
          var id = $(this).attr("id");
          var num = id.split("_");
          window.location = "/assessment/download/"+num[2];
        });

        $(document).on('click', '.remove_button', function(){
          var id = $(this).attr("id");
          var num = id.split("_");
          if(confirm('Are you sure you want to remove the it?')) {
            window.location = "/assessment/remove/list/"+num[2];
          }
          return false;
        });

        $(document).on('click', '#checkDownloadAction', function(){
          var checkedValue = ""; 
          var inputElements = document.getElementsByClassName('group_download');
          for(var i=0; inputElements[i]; i++){
            if(inputElements[i].checked){
              checkedValue += inputElements[i].value+"---";
            }
          }
          if(checkedValue!=""){
            var ass_id = $('#ass_id').val();
            var id = ass_id+"---"+checkedValue;
            window.location = "/assessment/download/zipFiles/"+id+"/checked";
          }else{
            alert("Please select the document first.");
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
              Tesseract.recognize(
                file,
                'eng',
                { logger: m => console.log(m) }
              ).then(({ data: { text } }) => {
                for(var c=0;c<=i;c++){
                  var checkfile = $('#'+c).val();
                  if(filename[0]==checkfile){
                    writeInput(c,filename[0],ext,file.upload.filename,text);
                    $('.dz-remove'+c).css('display','block');
                    $('#loader'+c).css('display','none');
                    $('#loading_word'+c).css('display','none');
                  }
                }
                
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
            var detect_m = $('#detect_m').val();
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
          var ass_id = $('#ass_id').val();
          $.ajax({
              type:'POST',
              url: "/assessment/searchKey/",
              data:{value:value,ass_id:ass_id},
              success:function(data){
                document.getElementById("assessments").innerHTML = data;
                $('.group_checkbox').click(function(){
                    var id = $(this).attr("id");
                    var type = id.split("group_");
                    console.log(type[1]);
                    if($(this).prop("checked") == true){
                      $('.group_'+type[1]).prop("checked", true);
                      // console.log(ass_rs_id[1]+" Checkbox is checked.");
                    }
                    else if($(this).prop("checked") == false){
                      $('.group_'+type[1]).prop("checked", false);
                      // console.log(ass_rs_id[1]+" Checkbox is unchecked.");
                    }
                });
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
            var ass_id = $('#ass_id').val();
            $.ajax({
               type:'POST',
               url: "/assessment/searchKey/",
               data:{value:value,ass_id:ass_id},
               success:function(data){
                    document.getElementById("assessments").innerHTML = data;
                  $('.group_checkbox').click(function(){
                      var id = $(this).attr("id");
                      var type = id.split("group_");
                      console.log(type[1]);
                      if($(this).prop("checked") == true){
                        $('.group_'+type[1]).prop("checked", true);
                        // console.log(ass_rs_id[1]+" Checkbox is checked.");
                      }
                      else if($(this).prop("checked") == false){
                        $('.group_'+type[1]).prop("checked", false);
                        // console.log(ass_rs_id[1]+" Checkbox is unchecked.");
                      }
                  });
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
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/course_list">Courses </a>/
            <a href="/course/action/{{$course[0]->course_id}}">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</a>/
            <a href="/assessment/{{$course[0]->course_id}}">Continuous Assessment</a>/
            <a href="/assessment/create/{{$course[0]->course_id}}/question/{{$question}}">{{$question}} ( Q & S )</a>/
            <span class="now_page">{{$assessments->assessment_name}}</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 10px 10px;">
        <div class="col-md-12">
             <p class="page_title">{{$assessments->assessment_name}}</p>
             <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                <div id="action_sidebar" class="w3-animate-right" style="display: none">
                    <div style="text-align: right;padding:10px;">
                        <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                  <ul class="sidebar-action-ul">
                      <!-- <a id="open_folder"><li class="sidebar-action-li"><i class="fa fa-folder" style="padding: 0px 10px;" aria-hidden="true"></i>Make a New Folder</li></a> -->
                      <a id="open_document"><li class="sidebar-action-li"><i class="fa fa-upload" style="padding: 0px 10px;" aria-hidden="true"></i>Upload Files</li></a>
                      @if((count($group_list)!=0))
                      <p class="title_method">Download</p>
                      <input type="hidden" id="ass_id" value="{{$assessments->ass_id}}">
                        <a id="checkDownloadAction"><li class="sidebar-action-li"><i class="fa fa-check-square-o" style="padding: 0px 10px;" aria-hidden="true"></i>Checked Item</li></a>
                        <a href='/assessment/download/zipFiles/{{$assessments->ass_id}}/All'><li class="sidebar-action-li"><i class="fa fa-download" style="padding: 0px 10px;" aria-hidden="true"></i>All Result</li></a>
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
                          <input type="hidden" value="{{$assessments->ass_id}}" id="ass_id">
                          <input type="text" name="search" class="form-control search" id="input" style="font-size: 18px;">
                      </div>
                  </div>
              </div>
              <div class="row" id="assessments" style="margin-top: -25px;">
              <?php
                $i=0;
              ?>
              @foreach($group_list as $row_group)
                <div style="border-bottom:1px solid black;padding:0px;" class="col-md-12">
                  <div class="col-12 row" style="padding:10px;margin: 0px;">
                    <div class="checkbox_group_style">
                      <input type="checkbox" id='group_{{$row_group->ass_type}}' class="group_checkbox">
                    </div>
                    <h5 class="group plus" id="{{$i}}">{{$row_group->ass_type}} (<i class="fa fa-minus" aria-hidden="true" id="icon_{{$i}}" style="color: #0d2f81;position: relative;top: 2px;"></i>)</h5>
                  </div>
                  <div id="assessment_list_{{$i}}" class="col-12 row align-self-center list" style="margin-left:0px;padding:0px;">
                @foreach($assessment_list as $row)
                @if($row_group->ass_type == $row->ass_type)
                  <div class="col-12 row align-self-center" id="course_list">
                    <div class="col-9 row align-self-center" >
                      <div class="checkbox_style align-self-center">
                        <input type="checkbox" value="{{$row->ass_li_id}}_{{$row->ass_type}}" class="group_{{$row_group->ass_type}} group_download">
                      </div>
                      <a href="/images/assessment/{{$row->ass_document}}" data-toggle="lightbox" data-gallery="example-gallery" class="col-11 row" style="padding:10px 0px;margin-left:5px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="{{$course[0]->semester_name}} : {{$assessments->assessment_name}} / {{$row_group->ass_type}} / {{$row->ass_name}} <br> <a href='/assessment/view/whole_paper/{{$row->ass_id}}' class='full_question' target='_blank'>Whole paper</a>">
                        <div class="col-1" style="position: relative;top: -2px;">
                          <img src="{{url('image/img_icon.png')}}" width="25px" height="20px"/>
                        </div>
                        <div class="col-10" id="course_name">
                          <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>{{$row->ass_name}}</b></p>
                        </div>
                      </a>
                    </div>
                    <div class="col-3" id="course_action_two">
                      <i class="fa fa-download download_button" aria-hidden="true" id="download_button_{{$row->ass_li_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>&nbsp;
                      <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_{{$row->ass_li_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>
                    </div>
                  </div>
                @endif
                @endforeach
                <?php
                $i++;
                ?>
                </div>
              </div>
              @endforeach
              @if($i==0)
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
<div class="modal fade bd-example-modal-lg" id="openDocumentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content content2">
      <div class="modal-header header2">
        <h5 class="modal-title title2" id="exampleModalLabel">Upload Files</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="row" style="padding: 15px 22px 0px 22px;">
            <div class="col-md-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                    <i class="fa fa-file" aria-hidden="true" style="font-size: 18px;"></i>
                </p>
            </div>
                <div class="col-11" style="padding-left: 20px;">
                <div class="form-group">
                      <label for="subject_type" class="label">Assessment Type</label>
                      <select class="form-control selectpicker" onchange="setType(this.value)">
                          <option class="option" value="Question">Question</option>
                          <option class="option" value="Solution">Solution</option>
                      </select>
                </div>
            </div>
      </div>
      <hr style="background-color: #d9d9d9;padding: 0px;margin:0px 20px;" class="row">
      <form method="post" action="{{route('assessment.dropzone.uploadFiles')}}" enctype="multipart/form-data"
        class="dropzone" id="dropzoneFile" style="margin: 20px;font-size: 20px;color:#a6a6a6;border-style: double;">
        @csrf
      </form>
      <form method="post" action="{{action('AssessmentController@storeFiles')}}">
        {{csrf_field()}}
        <input type="hidden" name="count" value="0" id="count">
        <input type="hidden" value="{{$assessments->ass_id}}" name="ass_id">
        <input type="hidden" name="ass_type" id="ass_type" value="Question">
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