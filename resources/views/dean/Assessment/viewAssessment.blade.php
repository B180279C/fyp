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
              // console.log(response[1]);
              var count = 0;
              var new_count = 0;
              var table = document.getElementById("table");
              
              
              for(var i = 0;i<=(response[0].length-1);i++){
                  if((response[0][i][1]==null)&&(response[0][i][2]!=null)&&(response[0][i][3]!=null)&&(response[0][i][9]!=null)){
                    if(count == 0){
                      count = response[0][i][2];
                    }else{
                      new_count = response[0][i][2];
                      if(new_count>count){
                        count = new_count;
                      }else{
                        break;
                      }
                    }

                    var assessment_count = 0;
                    var status = "fa fa-times wrong";
                    for(var a = 0; a<=(response[1].length-1);a++){
                      var assessment = response[0][i][3];
                      var last_char = assessment.charAt(assessment.length-1);
                      if(last_char==" "){
                        assessment = assessment.substring(0, assessment.length - 1);
                      }
                      if(assessment == response[1][a]['assessment']){
                        assessment_count++;
                        status = "fa fa-check correct";
                      }
                    }

                    var row = table.insertRow(count);
                    var cell = row.insertCell(0);
                    var cell1 = row.insertCell(1);
                    var cell2 = row.insertCell(2);
                    var cell3 = row.insertCell(3);
                    var cell4 = row.insertCell(4);
                    cell1.style.textAlign  = "center";
                    cell2.style.textAlign  = "center";
                    cell3.style.textAlign  = "center";
                    cell4.style.textAlign  = "center";
                    cell.innerHTML  = response[0][i][3]+" ( "+response[0][i][9]+ "% )";
                    cell1.innerHTML = '<a href="/assessment/create/'+course_id+'/question/'+response[0][i][3]+'" style="font-size:18px;margin-left:15%;width:70%;display:block;" class="question_link"><i class="fa fa-plus" aria-hidden="true" ></i></a>';
                    cell2.innerHTML = '<i class="'+status+'" aria-hidden="true"></i>';
                    if(status=="fa fa-check correct"){
                      cell3.innerHTML = '<a href="/AssessmentResult/'+course_id+'/question/'+response[0][i][3]+'" style="font-size:18px;width:100%;display:block;" class="question_link"><i class="fa fa-plus" aria-hidden="true" ></i></a>';
                    }else{
                      cell3.innerHTML = '<i class="fa fa-lock wrong" aria-hidden="true" style="font-size:20px;"></i>';
                    }
                    cell4.innerHTML = assessment_count;
                  }
              }
              var row = table.insertRow(count+1);
              var cell = row.insertCell(0);
              var cell1 = row.insertCell(1);
              cell1.style.textAlign  = "center";
              cell1.id = "myTd";
              document.getElementById("myTd").colSpan = "4";
              cell.innerHTML  = "<b>Continuous Assessment ( CA ) Moderation</b>";
              cell1.innerHTML  = "<a href='' id='show_image_link' style='width:100%;display:block;'' class='question_link'><b>Continuous Assessment Moderation Form</b></a>";
            }
        });

        // if($('.search').val()!=""){
        //   var value = $('.search').val();
        //   var course_id = $('#course_id').val();
        //   $.ajax({
        //       type:'POST',
        //       url:'/assessment/list/searchListKey/',
        //       data:{value:value,course_id:course_id},
        //       success:function(data){
        //         document.getElementById("assessments").innerHTML = data;
        //         $('[data-toggle="lightbox"]').click(function(event) {
        //           event.preventDefault();
        //           $(this).ekkoLightbox({
        //             type: 'image',
        //             onContentLoaded: function() {
        //               var container = $('.ekko-lightbox-container');
        //               var content = $('.modal-content');
        //               var backdrop = $('.modal-backdrop');
        //               var overlay = $('.ekko-lightbox-nav-overlay');
        //               var modal = $('.modal');
        //               var image = container.find('img');
        //               var windowHeight = $(window).height();
        //               var dialog = container.parents('.modal-dialog');
        //               var data_header = $('.modal-header');
        //               var data_title = $('.modal-title');
        //               var body = $('.modal-body');
        //               // console.log(image.width());

        //               if((image.width() > 380) && (image.width() < 430)){
        //                  dialog.css('max-width','700px');
        //                  image.css('height','900px');
        //                  image.css('width','700px');
        //                  overlay.css('height','900px');
        //               }else{
        //                  overlay.css('height','100%');
        //               }
        //               // backdrop.css('opacity','1');
        //               data_header.css('background-color','white');
        //               data_header.css('padding','10px');
        //               data_header.css('margin','0px 24px');
        //               data_header.css('border-bottom','1px solid black');
        //               data_title.css('font-size','18px');

        //               body.css('padding-top','0px');
        //               content.css('background', "none");
        //               content.css('-webkit-box-shadow', "0 5px 15px rgba(0,0,0,0)");
        //               content.css('-moz-box-shadow', "0 5px 15px rgba(0,0,0,0)");
        //               content.css('-o-box-shadow', "0 5px 15px rgba(0,0,0,0)");
        //               content.css('box-shadow', "0 5px 15px rgba(0,0,0,0)");
        //             }
        //           });
        //         });
        //       }
        //   });
        // }
        // $(".search").keyup(function(){
        //     var value = $('.search').val();
        //     var course_id = $('#course_id').val();
        //     $.ajax({
        //        type:'POST',
        //        url:'/assessment/list/searchListKey/',
        //        data:{value:value,course_id:course_id},
        //        success:function(data){
        //             document.getElementById("assessments").innerHTML = data;
        //             $('[data-toggle="lightbox"]').click(function(event) {
        //           event.preventDefault();
        //           $(this).ekkoLightbox({
        //             type: 'image',
        //             onContentLoaded: function() {
        //               var container = $('.ekko-lightbox-container');
        //               var content = $('.modal-content');
        //               var backdrop = $('.modal-backdrop');
        //               var overlay = $('.ekko-lightbox-nav-overlay');
        //               var modal = $('.modal');
        //               var image = container.find('img');
        //               var windowHeight = $(window).height();
        //               var dialog = container.parents('.modal-dialog');
        //               var data_header = $('.modal-header');
        //               var data_title = $('.modal-title');
        //               var body = $('.modal-body');
        //               // console.log(image.width());

        //               if((image.width() > 380) && (image.width() < 430)){
        //                  dialog.css('max-width','700px');
        //                  image.css('height','900px');
        //                  image.css('width','700px');
        //                  overlay.css('height','900px');
        //               }else{
        //                  overlay.css('height','100%');
        //               }
        //               // backdrop.css('opacity','1');
        //               data_header.css('background-color','white');
        //               data_header.css('padding','10px');
        //               data_header.css('margin','0px 24px');
        //               data_header.css('border-bottom','1px solid black');
        //               data_title.css('font-size','18px');

        //               body.css('padding-top','0px');
        //               content.css('background', "none");
        //               content.css('-webkit-box-shadow', "0 5px 15px rgba(0,0,0,0)");
        //               content.css('-moz-box-shadow', "0 5px 15px rgba(0,0,0,0)");
        //               content.css('-o-box-shadow', "0 5px 15px rgba(0,0,0,0)");
        //               content.css('box-shadow', "0 5px 15px rgba(0,0,0,0)");
        //             }
        //           });
        //         });
        //        }
        //     });
        // });
    });
</script>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/course_list">Courses </a>/
            <a href="/course/action/{{$course[0]->course_id}}">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</a>/
            <span class="now_page">Continuous Assessment</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 10px 10px;">
        <div class="col-md-12">
             <p class="page_title">Continuous Assessment ( CA )</p>
             <!-- <h5 style="position: relative;top:10px;left: 10px;">Assessment List ( {{$course[0]->semester_name}} )</h5> -->
            <div class="details" style="padding: 0px 5px 0px 5px;">
              <div style="overflow-x: auto;padding:15px 0px 5px 0px;">
                <input type="hidden" id="course_id" value="{{$course[0]->course_id}}">
                <table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);padding: 0px;" id="table" class="table">
                  <thead>
                      <tr>
                        <td style="background-color:#e9ecef;"><b>Continuous Assessment List ( {{$course[0]->semester_name}} )</b></td>
                        <td style="text-align: center;background-color:#e9ecef; " width="20%"><b>Question & Solution</b></td>
                        <td style="text-align: center;background-color:#e9ecef;" width="10%"><b>Status</b></td>
                        <td style="text-align: center;background-color:#e9ecef; " width="15%"><b>Student Result</b></td>
                        <td style="text-align: center;background-color:#e9ecef;" width="10%"><b>Count</b></td>
                      </tr>
                  </thead>
                  <tbody>
                    <tr></tr>
                  </tbody>
                </table>
              </div>

              <!-- <h5 style="position: relative;top:-5px;left: 5px;">Moderation</h5>
              <div style="position: relative;left: 10px;">
              <p><b>  1. </b><a href="">Submit</a> the Continuous Assessments to Moderator.</p>
              <p><b>  2. </b>The Moderation Process of Continuous Assessment for all undergraduates and postgraduate in Southern UC.</p>
              <p><b>  3. </b>The Course of Continuous Assessments has been moderated.</p>
              </div> -->

            </div>
        </div>
    </div>
</div>
@endsection