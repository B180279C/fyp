<?php
$title = "Moderator";
$option3 = "id='selected-sidebar'";
?>
@extends('layouts.layout')

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
.dropzone .dz-preview .dz-remove-new{
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
    oTable = $('#dtBasicExample').DataTable(
        {
            "bLengthChange" : false,
            "bInfo": false,
            pagingType: 'input',
            pageLength: 8,
            language: {
                oPaginate: {
                   sNext: '<i class="fa fa-forward"></i>',
                   sPrevious: '<i class="fa fa-backward"></i>',
                   sFirst: '<i class="fa fa-step-backward"></i>',
                   sLast: '<i class="fa fa-step-forward"></i>'
                }
            }
    }); 
    $('#input').keyup(function(){
        oTable.search($(this).val()).draw();
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

        $(document).on('click', '.download_button', function(){
          var id = $(this).attr("id");
          var num = id.split("_");
          window.location = '{{$character}}/FinalExamination/download/'+num[2];
        });

        $(document).on('click', '.edit_button', function(){
          var id = $(this).attr("id");
          var num = id.split("_");
          $.ajax({
            type:'POST',
            url:'{{$character}}/FinalExamination/AssessmentNameEdit',
            data:{value : num[2]},
            success:function(data){
              var clo = data[0].CLO;
              var clo_list = clo.split(",");
              var option = "";
              for(var c = 0;c<=(data[2].length-1);c++){
                var assessment_list = data[2][c].assessment.split('///');
                var markdown = data[2][c].markdown.split(',');
                var assessment = assessment_list[0].split(',');
                for(var i = 0; i<=assessment.length-1;i++){
                  var assessment_rep = assessment[i].replace(' ','');
                  if(assessment_rep=="FinalExamination"){
                    if(markdown[i]=="yes"){
                      var selected = false;
                      for(var d = 0;d<=(clo_list.length-1);d++){
                        if(clo_list[d]==("CLO"+(c+1))){
                          var selected = true;
                        }
                      }
                      if(selected==true){
                        option += "<option title='CLO "+(c+1)+"' class='option' value='CLO"+(c+1)+"' selected>CLO "+(c+1)+" : "+data[2][c].CLO+" ( "+data[2][c].domain_level+" , "+data[2][c].PO+" ) </option>";
                      }else{
                        option += "<option title='CLO "+(c+1)+"' class='option' value='CLO"+(c+1)+"'>CLO "+(c+1)+" : "+data[2][c].CLO+" ( "+data[2][c].domain_level+" , "+data[2][c].PO+" ) </option>";
                      }
                    }
                  }
                }
              }
              $("#CLO").html(option);
              $('#CLO').selectpicker('refresh');

              var topic = data[0].topic;
              var topic_selected = topic.split(',');
              var option = "";
              for(var c = 0;c<=(data[3].length-1);c++){
                var sel = false;
                var topic_num = (data[3][c].lecture_topic).split('///');
                // var title = "Topic "+topic_num[0];
                for(var d = 0;d<=(topic_selected.length-1);d++){
                  var selected = topic_selected[d];
                  if(selected==("Topic"+topic_num[0])){
                    sel = true;
                  }
                } 
                if(sel == true){
                  option += "<option class='option' title='Topic "+topic_num[0]+"' value='Topic"+topic_num[0]+"' selected>Topic "+topic_num[0]+" : "+topic_num[1]+"</option>";
                }else{
                  option += "<option class='option' title='Topic "+topic_num[0]+"' value='Topic"+topic_num[0]+"'>Topic "+topic_num[0]+" : "+topic_num[1]+"</option>";
                } 
              }
              $("#topic").html(option);
              $('#topic').selectpicker('refresh');

              var mark = 0;
              for(var i = 0;i<=(data[1].length-1);i++){
                var mark = mark+parseInt(data[1][i].coursework);
              }
              // console.log(mark);
              var full_mark = '{{$coursework}}';
              document.getElementById('fx_id').value = num[2];
              document.getElementById('mark_record').innerHTML = "The Final Examination of coursework is {{$coursework}}%, It already insert "+(mark-parseInt(data[0].coursework))+"%.";
              document.getElementById('mark_record_2').innerHTML = "So, It Cannot insert over "+(full_mark-(mark-parseInt(data[0].coursework)))+"% of coursework.";
              document.getElementById("coursework").max = (full_mark-(mark-parseInt(data[0].coursework)));
              document.getElementById('folder_name').value = data[0].assessment_name;
              document.getElementById('coursework').value = data[0].coursework;
            } 
          });
          $('#folderNameEdit').modal('show');
          return false;
        });

        $(document).on('click', '.remove_button', function(){
          var id = $(this).attr("id");
          var num = id.split("_");
          if(confirm('Are you sure you want to remove the it?')) {
            window.location = "{{$character}}/FinalExamination/remove/"+num[2];
          }     
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
            var course_id = $('#course_id').val();
            var id = course_id+"---"+checkedValue;
            window.location = "{{$character}}/FinalExamination/download/zipFiles/"+id+"/checked";
          }else{
            alert("Please select the document first.");
          }
        });

    $(document).on('click', '#checkAction', function(){
      var course_id = $('#course_id').val();
      if(confirm('Are you sure want to use previous semester of final assessment list? (Important : If the course is a long semester, you will get the last long semester of the final assessment list. On the contrary, if it is a short semester, you will get the last short semester.')) {
        window.location = "{{$character}}/FinalExamination/create/previous/"+course_id;
      }
      return false;
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
                url: '{{ url($character."/FinalExamination/destoryFiles") }}',
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
</script>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->semester_name}} : {{$course[0]->short_form_name}} / {{$course[0]->subject_code}} {{$course[0]->subject_name}} ( {{$course[0]->name}} )</p>
        <p class="pass_page">
            <a href="{{$character}}/home" class="first_page"> Home </a>/
            <a href="{{$character}}/Moderator">Moderator </a>/
            <a href="{{$character}}/Moderator/course/{{$course[0]->course_id}}">{{$course[0]->semester_name}} : {{$course[0]->short_form_name}} / {{$course[0]->subject_code}} {{$course[0]->subject_name}} ( {{$course[0]->name}} )</a>/
            <a href="{{$character}}/Moderator/FinalExam/{{$course[0]->course_id}}">Final Assessment</a>/
            <span class="now_page">Final ( List )</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 0px 10px;">
        <div class="col-md-12">
             <p class="page_title">Final ( List )</p>
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

            @if(\Session::has('Failed'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <Strong>{{\Session::get('Failed')}}</Strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            <div class="col-md-6 row" style="margin-top: -25px;">
                <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                    <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                        <i class="fa fa-search" aria-hidden="true" style="font-size: 20px;"></i>
                    </p>
                </div>
                <div class="col-11" style="padding-left: 20px;">
                    <div class="form-group">
                        <label for="full_name" class="bmd-label-floating">Search</label>
                        <input type="text" name="search" class="form-control tooltip_hover" id="input" style="font-size: 18px;">
                        <span class="tooltiptext">
                            <span>
                                <i class="fa fa-info-circle" style="color: #0d2f81;" aria-hidden="true"></i> Important : 
                            </span>
                            <hr style="background-color: #d9d9d9;margin: 3px 0px;">
                            <span>1. All Data in table</span>
                        </span>
                    </div>
                </div>
            </div>
            <hr style="margin:0px 10px 5px 10px;">
            <div style="overflow-x: auto;padding:0px 10px 10px 10px;">
            <table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);border:none;" id="dtBasicExample">
              <thead>
                <tr style="background-color: #d9d9d9;">
                  <th style="border-left:1px solid #e6e6e6;color:black;border-bottom: 1px solid #d9d9d9;text-align: center;">No.</th>
                  <th style="border-left:1px solid #e6e6e6;color:black;border-bottom: 1px solid #d9d9d9;text-align: center;">Assessment Name</th>
                  <th style="border-left:1px solid #e6e6e6;color:black;border-bottom: 1px solid #d9d9d9;text-align: center;">CLO</th>
                  <th style="border-left:1px solid #e6e6e6;color:black;border-bottom: 1px solid #d9d9d9;text-align: center;">Topic</th>
                  <th style="border-left:1px solid #e6e6e6;color:black;border-bottom: 1px solid #d9d9d9;text-align: center;">Coursework</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $num = 1;
                $main_id = "";
                ?>
                @foreach($ass_final as $row)
                  <tr>
                    <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">{{$num}}</td>
                    <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">{{$row->assessment_name}}</td>
                    <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">{{$row->CLO}}</td>
                    <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">{{$row->topic}}</td>
                    <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">{{$row->coursework}}</td>
                  </tr>
                  <?php
                  $num++;
                  ?>
                @endforeach
              </tbody>
            </table>
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
        <h5 class="modal-title title2" id="exampleModalLabel">Open New Assessment</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="post" action="{{$character}}/FinalExamination/openNewAssessment">
        {{csrf_field()}}
      <div class="modal-body body2">
        <div id="message"></div>
        <br>
        <input type="hidden" name="course_id" value="{{$course[0]->course_id}}">
        <div class="row">
            <div class="col-md-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                    <i class="fa fa-file" aria-hidden="true" style="font-size: 18px;"></i>
                </p>
            </div>
                <div class="col-11" style="padding-left: 20px;">
                <div class="form-group">
                      <label for="subject_type" class="bmd-label-floating">Assessment Name</label>
                      <input type="text" name="assessment_name" class="form-control" required/>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                    <i class="fa fa-list-alt" aria-hidden="true" style="font-size: 18px;"></i>
                </p>
            </div>
                <div class="col-11" style="padding-left: 20px;">
                <div class="form-group">
                      <label for="subject_type" class="bmd-label-floating">Course Learning Outcome ( CLO )</label>
                      <select class="selectpicker form-control" name="CLO[]" data-width="100%" title="Choose one" multiple required>
                        <?php
                          $num = 1;
                          $check = "";
                          foreach($TP_Ass as $row){
                            $assessment_list = explode('///',$row->assessment);
                            $markdown = explode(',',$row->markdown);
                            $assessment = explode(',',$assessment_list[0]);
                            for($i = 0; $i<=count($assessment)-1;$i++){
                              $assessment_rep = str_replace(' ','',$assessment[$i]);
                              if($assessment_rep=="FinalExamination"){
                                if($markdown[$i]=="yes"){
                                  $check .= $row->am_id.',';
                                  echo "<option title='CLO ".$num."' class='option' value='CLO".$num."'>CLO ".$num." : ".$row->CLO." ( ".$row->domain_level." , ".$row->PO." ) </option>";
                                }
                              }
                            }
                            $num++;
                          }
                      ?>
                      </select>
                      <input type="hidden" name="CLO_ALL" value="{{$check}}">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                    <i class="fa fa-tag" aria-hidden="true" style="font-size: 18px;"></i>
                </p>
            </div>
                <div class="col-11" style="padding-left: 20px;">
                <div class="form-group">
                      <label for="subject_type" class="bmd-label-floating">Topic(s) Covered</label>
                      <select class="selectpicker form-control" name="topic[]" data-width="100%" title="Choose one" multiple required>
                        @foreach($tp as $row)
                          @if($row->lecture_topic!=NULL)
                            <?php
                              $topic_num = explode('///',$row->lecture_topic)
                            ?>
                            <option class="option" title="Topic {{$topic_num[0]}}" value="Topic{{$topic_num[0]}}">Topic {{$topic_num[0]}} : {{$topic_num[1]}}</option>
                          @endif
                        @endforeach
                      </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                    <i class="fa fa-percent" aria-hidden="true" style="font-size: 18px;"></i>
                </p>
            </div>
                <div class="col-11" style="padding-left: 20px;">
                <div class="form-group">
                      <label for="subject_type" class="bmd-label-floating">Coursework</label>
                      <input type="hidden" name="total" value="{{$coursework}}">
                      <input type="number" name="coursework" min="0" max="{{$coursework-$mark}}" class="form-control" required/>
                      <span class="bmd-help" id="mark_record">The Final Examination of coursework is {{$coursework}}%, It already insert {{$mark}}%.</span>
                      <span class="bmd-help" id="mark_record_2">So, It Cannot insert over {{$coursework-$mark}}% of coursework.</span>
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
      <form method="post" action="{{$character}}/FinalExamination/updateAssessmentName">
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
                      <label for="subject_type" class="label">Assessment Name</label>
                      <input type="hidden" name="fx_id" id="fx_id">
                      <input type="text" name="assessment_name" class="form-control" id="folder_name" placeholder="Folder" required/>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                    <i class="fa fa-list-alt" aria-hidden="true" style="font-size: 18px;"></i>
                </p>
            </div>
                <div class="col-11" style="padding-left: 20px;">
                <div class="form-group">
                      <label for="subject_type" class="bmd-label-floating">Course Learning Outcome ( CLO )</label>
                      <select class="selectpicker form-control" name="CLO[]" data-width="100%" id="CLO" title="Choose one" multiple required>
                      </select>
                      <input type="hidden" name="CLO_ALL" id="CLO_ALL">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                    <i class="fa fa-tag" aria-hidden="true" style="font-size: 18px;"></i>
                </p>
            </div>
                <div class="col-11" style="padding-left: 20px;">
                <div class="form-group">
                      <label for="subject_type" class="bmd-label-floating">Topic(s) Covered</label>
                      <select class="selectpicker form-control" name="topic[]" data-width="100%" id="topic" title="Choose one" multiple required>
                      </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                    <i class="fa fa-percent" aria-hidden="true" style="font-size: 18px;"></i>
                </p>
            </div>
                <div class="col-11" style="padding-left: 20px;">
                <div class="form-group">
                      <label for="subject_type" class="label">Coursework</label>
                      <input type="hidden" name="total" value="{{$coursework}}">
                      <input type="number" name="coursework" min="0" max="{{$coursework-$mark}}" class="form-control" id="coursework" required/>
                      <span class="bmd-help" id="mark_record">The Final Examination of coursework is {{$coursework}}%, It already insert {{$mark}}%.</span>
                      <span class="bmd-help" id="mark_record_2">So, It Cannot insert over {{$coursework-$mark}}% of coursework.</span>
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
      <form method="post" action="{{$character}}/FinalExamination/uploadFiles" enctype="multipart/form-data"
        class="dropzone" id="dropzoneFile" style="margin: 20px;font-size: 20px;color:#a6a6a6;border-style: double;">
        @csrf

      </form>
      <form method="post" action="{{$character}}/FinalExamination/storeFiles">
        {{csrf_field()}}
        <input type="hidden" name="count" value="0" id="count">
        <input type="hidden" value="{{$course[0]->course_id}}" name="course_id">
        <input type="hidden" name="ass_fx_type" id="ass_type" value="Question">
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