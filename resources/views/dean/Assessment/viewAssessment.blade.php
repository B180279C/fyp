<?php
$title = "Course";
$option1 = "id='selected-sidebar'";
?>
@extends('layouts.nav_dean')

@section('content')
<style type="text/css">
.question_link:hover{
    text-decoration: none;
}
#course_list:hover{
    text-decoration: none;
    background-color: #f2f2f2;
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
  $(document).ready(function(){
    $(document).on('click', '.plus', function(){
      var id = $(this).attr("id");
      $('#previous_'+id).slideToggle("slow", function(){
        if($('#previous_'+id).is(":visible")){
          $('#icon_'+id).removeClass('fa fa-plus');
          $('#icon_'+id).addClass('fa fa-minus');
        }else{
          $('#icon_'+id).removeClass('fa fa-minus');
          $('#icon_'+id).addClass('fa fa-plus');
        }
      });
    });
    $(document).on('click', '.download_button', function(){
        var id = $(this).attr("id");
        var num = id.split("_");
      window.location = "/assessment/download/"+num[2];
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
  $(document).ready(function(){
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        var course_id = $('#course_id').val();
        $.ajax({
            type:'POST',
            url:'/assessment/getSyllabusData',
            data:{course_id:course_id},
            success:function(response){
              // console.log(response[0]);
              var count = 0;
              var new_count = 0;
              var table = document.getElementById("table");
              
              for(var i = 0;i<=(response.length-1);i++){
                  if((response[i][1]==null)&&(response[i][2]!=null)&&(response[i][3]!=null)&&(response[i][9]!=null)){
                    if(count == 0){
                      count = response[i][2];
                    }else{
                      new_count = response[i][2];
                      if(new_count>count){
                        count = new_count;
                      }else{
                        break;
                      }
                    }
                    var row = table.insertRow(count);
                    var cell = row.insertCell(0);
                    var cell1 = row.insertCell(1);
                    var cell2 = row.insertCell(2);
                    // var cell3 = row.insertCell(3);
                    // var cell4 = row.insertCell(4);
                    cell1.style.textAlign  = "center";
                    cell2.style.textAlign  = "center";
                    // cell3.style.textAlign  = "center";
                    // cell4.style.textAlign  = "center";
                    cell.innerHTML  = response[i][3]+" ( "+response[i][9]+ "% )";
                    cell1.innerHTML = '<a href="/assessment/create/'+course_id+'/question/'+response[i][3]+'" style="font-size:18px;" class="question_link"><i class="fa fa-plus" aria-hidden="true" ></i></a>';
                    cell2.innerHTML = '<i class="fa fa-times wrong" aria-hidden="true"></i>';
                    // cell3.innerHTML = '<a href="/assessment/studentResult/'+course_id+'/'+response[i][3]+'" style="font-size:20px;" class="question_link"><i class="fa fa-plus" aria-hidden="true" ></i> Add</a>';
                    // cell4.innerHTML = "10";
                  }
              }
            }
        });

        if($('.search').val()!=""){
          var value = $('.search').val();
          var course_id = $('#course_id').val();
          $.ajax({
              type:'POST',
              url:'/assessment/list/searchListKey/',
              data:{value:value,course_id:course_id},
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
            var course_id = $('#course_id').val();
            $.ajax({
               type:'POST',
               url:'/assessment/list/searchListKey/',
               data:{value:value,course_id:course_id},
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
            <span class="now_page">Continuous Assessment</span>/
        </p>
        <hr style="margin: -10px 10px;">
    </div>
    <div class="row" style="padding: 10px 10px 10px 10px;">
        <div class="col-md-12">
             <p style="display: inline;font-size: 25px;position: relative;top: 5px;left:8px;color: #0d2f81">Continuous Assessment</p>
             <h5 style="position: relative;top:10px;left: 10px;">Assessment List ( {{$course[0]->semester_name}} )</h5>
            <div class="details" style="padding: 0px 5px 0px 5px;">
              <div style="overflow-x: auto;padding:15px 0px 5px 0px;">
                <input type="hidden" id="course_id" value="{{$course[0]->course_id}}">
                <table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);" id="table" class="table table-hover">
                  <thead class="thead-light">
                      <tr>
                        <th scope="col"><b>Continuous Assessment</b></th>
                        <th style="text-align: center;" width="15%" scope="col"><b>Question</b></th>
                        <th style="text-align: center;" width="15%" scope="col"><b>Status</b></th>
                        <!-- <th style="text-align: center;background-color: #bfbfbf;" width="15%"><b>Student Result</b></th>
                        <th style="text-align: center;background-color: #bfbfbf;" width="15%"><b>Count</b></th> -->
                      </tr>
                  </thead>
                  <tbody>
                    <tr></tr>
                  </tbody>
                </table>
              </div>
            </div>
            <hr style="margin: 5px 5px;background-color:#d9d9d9;">
            <h5 style="position: relative;top:10px;left: 10px;">Assessment List of Other Semester</h5>
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
                    <a href="/assessment/{{$course[0]->course_id}}/previous/{{$row->course_id}}/list" id="show_image_link" class="col-9 row align-self-center">
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
            </div>
        </div>
    </div>
</div>
@endsection