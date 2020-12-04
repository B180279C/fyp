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
            url:'/FinalExamination/AssessmentNameEdit',
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
            window.location = "/FinalExamination/remove/"+num[2];
          }     
        });

        $(document).on('click', '#checkDownloadAction', function(){
            var checkedValue = ""; 
            var inputElements = document.getElementsByClassName('group_download');
            for(var i=0; inputElements[i]; i++){
              if(inputElements[i].checked){
                checkedValue += inputElements[i].value+"_";
              }
            }
            if(checkedValue!=""){
              var course_id = $('#course_id').val();
              var id = course_id+"_"+checkedValue;
              window.location = "/FinalExamination/AllZipFiles/"+id+"/checked";
            }else{
              alert("Please select the document first.");
            }
          });
  });

  $(function () {
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
              url:'/FinalExamination/searchAssessmentList/',
              data:{value:value,course_id:course_id},
              success:function(data){
                document.getElementById("assessments").innerHTML = data;
              }
          });
        }
        $(".search").keyup(function(){
            var value = $('.search').val();
            var course_id = $('#course_id').val();
            $.ajax({
               type:'POST',
               url:'/FinalExamination/searchAssessmentList/',
               data:{value:value,course_id:course_id},
               success:function(data){
                  document.getElementById("assessments").innerHTML = data;
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
            <a href="/FinalExamination/{{$course[0]->course_id}}">Final Assessment</a>/
            <span class="now_page">Final ( Q & S )</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 10px 10px;">
        <div class="col-md-12">
             <p class="page_title">Final ( Q & S )</p>
             <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                <div id="action_sidebar" class="w3-animate-right" style="display: none">
                    <div style="text-align: right;padding:10px;">
                        <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                  <ul class="sidebar-action-ul">
                      <a id="open_folder"><li class="sidebar-action-li"><i class="fa fa-folder" style="padding: 0px 10px;" aria-hidden="true"></i>Make a New Assessment</li></a>
                      @if((count($ass_final)!=0))
                      <p class="title_method">Download</p>
                        <a id="checkDownloadAction"><li class="sidebar-action-li"><i class="fa fa-check-square-o" style="padding: 0px 10px;" aria-hidden="true"></i>Checked Item</li></a>
                        <a href='/FinalExamination/AllZipFiles/{{$course[0]->course_id}}/All'><li class="sidebar-action-li"><i class="fa fa-download" style="padding: 0px 10px;" aria-hidden="true"></i>All Result</li></a>
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
                          <input type="hidden" value="{{$course[0]->course_id}}" id="course_id">
                          <input type="text" name="search" class="form-control search" id="input" style="font-size: 18px;">
                      </div>
                  </div>
              </div>
              
              <div class="row" id="assessments" style="margin-top: -25px;">
              <?php
              $i=0;
              ?>
              @foreach($ass_final as $row)
                <div class="col-12 row align-self-center" id="course_list">
                    <div class="col-9 row align-self-center" style="padding-left: 20px;">
                      <div class="checkbox_style align-self-center">
                        <input type="checkbox" name="group{{$row->fx_id}}" value="{{$row->fx_id}}" class="group_download">
                      </div>
                      <a href='/FinalExamination/view_list/{{$row->fx_id}}' class="col-11 row" style="padding:10px 0px;margin-left:0px;color:#0d2f81;border:0px solid black;" id="show_image_link">
                        <div class="col-1" style="position: relative;top: -2px;">
                          <img src="{{url('image/file.png')}}" width="20px" height="25px"/>
                        </div>
                        <div class="col-10" id="course_name">
                          <p style="margin: 0px 0px 0px 5px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>{{$row->assessment_name}} ( {{$row->coursework}}% )</b></p>
                        </div>
                      </a>
                    </div>
                    <div class="col-3" id="course_action_two">
                      <i class="fa fa-wrench edit_button" aria-hidden="true" id="edit_button_{{$row->fx_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>&nbsp;
                      <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_{{$row->fx_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>
                    </div>
                </div>
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
      <form method="post" action="{{action('Dean\FinalExaminationController@openNewAssessment')}}">
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
      <form method="post" action="{{action('Dean\FinalExaminationController@updateAssessmentName')}}">
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
@endsection