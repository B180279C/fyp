<?php
$title = "Course";
$option1 = "id='selected-sidebar'";
?>
@extends('layouts.nav_dean')

@section('content')
<style type="text/css">
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
  $(document).ready(function(){
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
        var course_id = $('#course_id').val();
        $.ajax({
            type:'POST',
            url:'/FinalExamination/getSyllabusData',
            data:{course_id:course_id},
            success:function(response){
              // console.log(response);
              var count = 0;
              var new_count = 0;
              var table = document.getElementById("table");
              
              for(var i = 0;i<=(response.length-1);i++){
                  if((response[i][1]==null)&&(response[i][2]!=null)&&(response[i][3]!=null)&&(response[i][9]!=null)){
                    var name = response[i][3];
                    var percentage = response[i][9];
                  }
              }
              var row = table.insertRow(0);
              var cell = row.insertCell(0);
              var cell1 = row.insertCell(1);
              var cell2 = row.insertCell(2);
              cell1.style.textAlign  = "center";
              cell2.style.textAlign  = "center";
              cell1.style.width  = "15%";
              cell2.style.width  = "15%";
              cell.style.backgroundColor  = "#e9ecef";
              cell1.style.backgroundColor  = "#e9ecef";
              cell2.style.backgroundColor  = "#e9ecef";
              cell.innerHTML  = "<b>"+name+" ( "+percentage+ "% )</b>";
              cell1.innerHTML = '<b>Add</b>';
              cell2.innerHTML = '<b>Status</b>';

              var row_paper = table.insertRow(1);
              var cell = row_paper.insertCell(0);
              var cell1 = row_paper.insertCell(1);
              var cell2 = row_paper.insertCell(2);
              cell1.style.textAlign  = "center";
              cell2.style.textAlign  = "center";
              cell.innerHTML  = "Question Paper & Solution";
              cell1.innerHTML = '<a href="/FinalExamination/question/'+course_id+'/" style="font-size:18px;width:100%;display:block;" class="question_link"><i class="fa fa-plus" aria-hidden="true" ></i></a>';
              cell2.innerHTML = '<i class="fa fa-times wrong" aria-hidden="true"></i>';

              var row_result = table.insertRow(2);
              var cell = row_result.insertCell(0);
              var cell1 = row_result.insertCell(1);
              var cell2 = row_result.insertCell(2);
              cell1.style.textAlign  = "center";
              cell2.style.textAlign  = "center";
              cell.innerHTML  = "Student Result";
              cell1.innerHTML = '<a href="/FinalResult/'+course_id+'/" style="font-size:18px;width:100%;display:block;" class="question_link"><i class="fa fa-plus" aria-hidden="true" ></i></a>';
              cell2.innerHTML = '<i class="fa fa-times wrong" aria-hidden="true"></i>';
            }
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
            <span class="now_page">Final Assessment</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 10px 10px;">
        <div class="col-md-12">
             <p class="page_title">Final Assessment</p>
            <div class="details" style="padding: 0px 5px 0px 5px;">
              <div style="overflow-x: auto;padding:15px 0px 5px 0px;">
                <input type="hidden" id="course_id" value="{{$course[0]->course_id}}">
                <table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);" id="table" class="table table-hover">
                </table>
              </div>
            </div>

            <!-- <hr style="margin: 5px 5px;background-color:#d9d9d9;">
            <h5 style="position: relative;top:10px;left: 10px;">Final Assessment of Other Semester</h5>
            <br>
            <div class="details" style="padding: 0px 5px 5px 5px;">
                <div class="col-md-6 row" style="padding:0px 20px;position: relative;top: -30px;">
                    <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                        <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                            <i class="fa fa-search" aria-hidden="true" style="font-size: 20px;"></i>
                        </p>
                    </div>
                    <div class="col-11" style="padding-left: 20px;">
                        <div class="form-group">
                            <label for="full_name" class="bmd-label-floating">Search</label>
                            <input type="hidden" id="course_id" value="{{$course[0]->course_id}}">
                            <input type="text" name="search" class="form-control search" id="input" style="font-size: 18px;">
                        </div>
                    </div>
                </div>
                <div class="row" id="assessments" style="position: relative;top: -25px;">
                  @foreach($previous_semester as $row)
                  <div class="col-12 row align-self-center" id="course_list">
                    <a href="" id="show_image_link" class="col-9 row align-self-center">
                      <div class="col-12 row" style="padding:10px;color:#0d2f81;">
                        <div class="col-1" style="position: relative;top: -2px;">
                          <img src="{{url('image/folder2.png')}}" width="25px" height="25px"/>
                        </div>
                        <div class="col-10" id="course_name">
                          <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>{{$row->semester_name}}</b></p>
                        </div>
                      </div>
                    </a>
                  </div>
                  @endforeach
                </div>
            </div> -->


        </div>
    </div>
</div>
@endsection