<?php
$title = "Reviewer";
$option4 = "id='selected-sidebar'";
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
        var original_id = $('#original_id').val();
        var id = original_id+"---"+course_id+"---"+checkedValue;
        
        if($('.search').val()!=""){
          var data = $('#data').val();
          window.location = "{{$character}}/PastYear/sampleResult/list/download/zipFiles/"+id+"/"+data;
        }else{
          window.location = "{{$character}}/PastYear/sampleResult/list/download/zipFiles/"+id+"/checked";
        }
      }else{
        alert("Please select the document first.");
      }
    });

    $(document).on('click', '.download_button', function(){
      var id = $(this).attr("id");
      var num = id.split("_");
      var original_id = $('#original_id').val();
      window.location = "{{$character}}/Reviewer/PastYear/assessment/download/"+original_id+"-"+num[2];
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
          var original_id = $('#original_id').val();
          $.ajax({
              type:'POST',
              url: "{{route($route_name.'.r.PY.searchSampleResult')}}",
              data:{value:value,course_id:course_id,original_id:original_id},
              success:function(data){
                document.getElementById("assessments").innerHTML = data;
              }
          });
        }
        $(".search").keyup(function(){
            var value = $('.search').val();
            var course_id = $('#course_id').val();
            var original_id = $('#original_id').val();
            $.ajax({
               type:'POST',
               url: "{{route($route_name.'.r.PY.searchSampleResult')}}",
               data:{value:value,course_id:course_id,original_id:original_id},
               success:function(data){
                  document.getElementById("assessments").innerHTML = data;
               }
            });
        });
    });
</script>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->subject_code}} {{$course[0]->subject_name}}</p>
        <p class="pass_page">
            <a href="{{$character}}/Reviewer/course/{{$id}}" class="first_page">Past Year</a>/
            <a href="{{$character}}/Reviewer/PastYear/assessment/{{$id}}">Continuous Assessment</a>/
            <span class="now_page">{{$previous[0]->semester_name}} ( R )</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 5px 10px;">
        <div class="col-md-12">
             <p class="page_title">{{$previous[0]->semester_name}} ( R )</p>
             <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                <div id="action_sidebar" class="w3-animate-right" style="display: none">
                    <div style="text-align: right;padding:10px;">
                        <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                  <ul class="sidebar-action-ul">
                      <p class="title_method">Download</p>
                      <input type="hidden" id="course_id" value="{{$previous[0]->course_id}}">
                        <a id="checkDownloadAction"><li class="sidebar-action-li"><i class="fa fa-check-square-o" style="padding: 0px 10px;" aria-hidden="true"></i>Checked Item</li></a>
                        <a href='{{$character}}/PastYear/sampleResult/list/download/zipFiles/{{$id}}---{{$previous[0]->course_id}}/All'><li class="sidebar-action-li"><i class="fa fa-download" style="padding: 0px 10px;" aria-hidden="true"></i>All Result</li></a>
                  </ul>
                </div>
                <br>
                <br>
              <div class="details" style="padding: 0px 5px;">
                  <div class="col-md-6 row" style="padding:0px 20px;position: relative;top:-25px;">
                      <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                          <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                              <i class="fa fa-search" aria-hidden="true" style="font-size: 20px;"></i>
                          </p>
                      </div>
                      <div class="col-11" style="padding-left: 20px;">
                          <div class="form-group">
                              <label for="full_name" class="bmd-label-floating">Search</label>
                              <input type="hidden" id="course_id" value="{{$previous[0]->course_id}}">
                              <input type="hidden" id="original_id" value="{{$id}}">
                              @if($search=="All")
                              <input type="text" name="search" class="form-control search tooltip_hover" id="input" style="font-size: 18px;">
                              @else
                                <input type="text" name="search" class="form-control search tooltip_hover" id="input" style="font-size: 18px;" value="{{$search}}">
                              @endif
                              <span class="tooltiptext">
                                <span>
                                    <i class="fa fa-info-circle" style="color: #0d2f81;" aria-hidden="true"></i> Important : 
                                </span>
                                <hr style="background-color: #d9d9d9;margin: 3px 0px;">
                                <span>1. Student Id OR Name</span><br/>
                                <span>2. Batch </span><br/>
                                <span>3. Submitted By ( Lecturer / Student )</span><br/>
                              </span>
                          </div>
                      </div>
                  </div>
                  <div class="row" id="assessments"  style="margin-top: -25px;">
                    @foreach($sample_stored as $row)
                    <div class="col-12 row align-self-center" id="course_list">
                      <div class="col-12 row align-self-center">
                        <div class="checkbox_style align-self-center">
                          <input type="checkbox" value="{{$row->ass_id}}" class="group_download">
                        </div>
                        <a href="{{$character}}/Reviewer/PastYear/sampleResult/{{$id}}/name/{{$row->ass_id}}/All" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">
                          <div class="col-1" style="position: relative;top: -2px;">
                            <img src="{{url('image/file.png')}}" width="20px" height="25px"/>
                          </div>
                          <div class="col-10" id="assessment_name">
                            <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>{{$row->sample_stored}}</b></p>
                          </div>
                        </a>
                      </div>
                    </div>
                    @endforeach
                  </div>
              </div>
        </div>
    </div>
</div>
@endsection