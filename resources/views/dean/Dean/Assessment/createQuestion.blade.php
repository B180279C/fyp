<?php
$title = "Dean";
$option4 = "id='selected-sidebar'";
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
    padding: 0px 0px 0px 5px;
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
        margin-left:-50px;
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
            url:'/assessment/AssessmentNameEdit',
            data:{value : num[2]},
            success:function(data){
              var clo = data[0].CLO;
              var clo_list = clo.split(",");
              var option = "";
              var question = '{{$question}}';
              for(var c = 0;c<=(data[2].length-1);c++){
                var assessment_list = data[2][c].assessment.split('///');
                var markdown = data[2][c].markdown.split(',');
                var assessment = assessment_list[0].split(',');
                for(var i = 0; i<=assessment.length-1;i++){
                  var assessment_rep = assessment[i].replace(' ','');
                  if(assessment_rep==question){
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
              // $('#CLO_ALL').val(clo_full);
              var mark = 0;
              for(var i = 0;i<=(data[1].length-1);i++){
                var mark = mark+parseInt(data[1][i].coursework);
              }
              // console.log(mark);
              var full_mark = '{{$coursework}}';
              document.getElementById('ass_id').value = num[2];
              document.getElementById('mark_record').innerHTML = "The {{$question}} of coursework is {{$coursework}}%, It already insert "+(mark-parseInt(data[0].coursework))+"%.";
              document.getElementById('mark_record_2').innerHTML = "So, It Cannot insert over "+(full_mark-(mark-parseInt(data[0].coursework)))+"% of coursework.";
              document.getElementById("coursework").max = (full_mark-(mark-parseInt(data[0].coursework)));
              document.getElementById('folder_name').value = data[0].assessment_name;
              document.getElementById('coursework').value = data[0].coursework;
            } 
          });
          $('#folderNameEdit').modal('show');
          return false;
        });

        $(document).on('click', '.download_button', function(){
          var id = $(this).attr("id");
          var num = id.split("_");
          window.location = "/Dean/assessment/download/"+num[2];
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
              window.location = "/assessment/AllZipFiles/"+id+"/checked";
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
          var question = $('#question').val();
          $.ajax({
              type:'POST',
              url:'/Dean/assessment/searchAssessmentList/',
              data:{value:value,course_id:course_id,question:question},
              success:function(data){
                document.getElementById("assessments").innerHTML = data;
              }
          });
        }
        $(".search").keyup(function(){
            var value = $('.search').val();
            var course_id = $('#course_id').val();
            var question = $('#question').val();
            $.ajax({
               type:'POST',
               url:'/Dean/assessment/searchAssessmentList/',
               data:{value:value,course_id:course_id,question:question},
               success:function(data){
                  document.getElementById("assessments").innerHTML = data;
               }
            });
        });
    });
</script>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->semester_name}} : {{$course[0]->short_form_name}} / {{$course[0]->subject_code}} {{$course[0]->subject_name}} ( {{$course[0]->name}} )</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/Dean">Dean </a>/
            <a href="/Dean/course/{{$course[0]->course_id}}">{{$course[0]->semester_name}} : {{$course[0]->short_form_name}} / {{$course[0]->subject_code}} {{$course[0]->subject_name}} ( {{$course[0]->name}} )</a>/
            <a href="/Dean/viewAssessment/{{$course[0]->course_id}}">Continuous Assessment</a>/
            <span class="now_page">{{$question}} ( Q & S )</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 0px 10px;">
        <div class="col-md-12">
             <p class="page_title">{{$question}} ( Q & S )</p>
             <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                <div id="action_sidebar" class="w3-animate-right" style="display: none">
                    <div style="text-align: right;padding:10px;">
                        <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                  <ul class="sidebar-action-ul">
                      @if((count($assessments)!=0))
                      <p class="title_method">Download</p>
                        <a id="checkDownloadAction"><li class="sidebar-action-li"><i class="fa fa-check-square-o" style="padding: 0px 10px;" aria-hidden="true"></i>Checked Item</li></a>
                        <a href='/assessment/AllZipFiles/{{$course[0]->course_id}}_{{$question}}/All'><li class="sidebar-action-li"><i class="fa fa-download" style="padding: 0px 10px;" aria-hidden="true"></i>All Result</li></a>
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
                          <input type="hidden" value="{{$question}}" id="question">
                          <input type="text" name="search" class="form-control search" id="input" style="font-size: 18px;">
                      </div>
                  </div>
              </div>
              
              <div class="row" id="assessments" style="margin-top: -25px;">
              <?php
              $i=0;
              ?>
              @foreach($assessments as $row)
                <div class="col-12 row align-self-center" id="course_list">
                    <div class="col-12 row align-self-center" style="padding-left: 20px;">
                      <div class="checkbox_style align-self-center">
                        <input type="checkbox" name="group{{$row->ass_id}}" value="{{$row->ass_id}}" class="group_download">
                      </div>
                      <a href='/Dean/assessment/view_list/{{$row->ass_id}}' class="col-11 row" style="padding:10px 0px;margin-left:0px;color:#0d2f81;border:0px solid black;" id="show_image_link">
                        <div class="col-1" style="position: relative;top: -2px;">
                          <img src="{{url('image/file.png')}}" width="20px" height="25px"/>
                        </div>
                        <div class="col-10" id="course_name">
                          <p style="margin: 0px 0px 0px 5px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>{{$row->assessment_name}}</b></p>
                        </div>
                      </a>
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
@endsection