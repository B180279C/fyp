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
.more:hover{
    text-decoration:none;
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
    $('#less').hide();
    $(document).on("click",".more", function(){
      $('#more').hide();
      $('#less').show();
      $('.action_list').slideToggle("slow", function(){
        // check paragraph once toggle effect is completed
        if($('.action_list').is(":visible")){
          $('#more').hide();
          $('#less').show();
        }else{
          $('#more').show();
          $('#less').hide();
        }
      });
      return false;
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

  function submitAction(){
    var course_id = $('#course_id').val();
    window.location = "/assessment/Action/Submit/"+course_id; 
  }

  function submitActionSecond(){
        var course_id = $('#course_id').val();
        if(confirm('Please ensure your assessment is fixed all error and full complete already. Are you sure want to submit again to moderator.')) {
            window.location = "/assessment/Action/Submit/"+course_id; 
        }
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
              var total = 0;
              
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

                    total = total + response[0][i][9]; 
                    var assessment_count = 0;
                    var status = "fa fa-times wrong";
                    for(var a = 0; a<=(response[1].length-1);a++){
                      var assessment = response[0][i][3];
                      assessment = assessment.replace(' ', '');
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
                    cell.style.borderLeft  = "1px solid #d9d9d9";
                    cell.style.borderBottom  = "1px solid #d9d9d9";
                    cell1.style.textAlign  = "center";
                    cell1.style.borderLeft  = "1px solid #d9d9d9";
                    cell1.style.borderBottom  = "1px solid #d9d9d9";
                    cell2.style.textAlign  = "center";
                    cell2.style.borderLeft  = "1px solid #d9d9d9";
                    cell2.style.borderBottom  = "1px solid #d9d9d9";
                    cell3.style.textAlign  = "center";
                    cell3.style.borderLeft  = "1px solid #d9d9d9";
                    cell3.style.borderBottom  = "1px solid #d9d9d9";
                    cell4.style.textAlign  = "center";
                    cell4.style.borderLeft  = "1px solid #d9d9d9";
                    cell4.style.borderBottom  = "1px solid #d9d9d9";
                    cell4.style.borderRight  = "1px solid #d9d9d9";
                    cell.innerHTML  = response[0][i][3]+" ( "+response[0][i][9]+ "% )";
                    cell1.innerHTML = '<a href="/assessment/create/'+course_id+'/question/'+response[0][i][9]+'/'+response[0][i][3]+'" style="font-size:18px;margin-left:15%;width:70%;display:block;" class="question_link"><i class="fa fa-plus" aria-hidden="true" ></i></a>';
                    cell2.innerHTML = '<i class="'+status+'" aria-hidden="true"></i>';
                    if(status=="fa fa-check correct"){
                      cell3.innerHTML = '<a href="/AssessmentResult/'+course_id+'/question/'+response[0][i][3]+'" style="font-size:18px;width:100%;display:block;" class="question_link"><i class="fa fa-plus" aria-hidden="true" ></i></a>';
                    }else{
                      cell3.innerHTML = '<i class="fa fa-lock wrong" aria-hidden="true" style="font-size:20px;"></i>';
                    }
                    cell4.innerHTML = assessment_count;
                  }
              }
              $('.total').html(total);
              var mark = document.getElementById('mark').innerHTML;
              if(mark==total){
                $('.mark_color').css('color', 'green');
                $('.status').html("<span style='color:green'>Complete</span>&nbsp;&nbsp;<button class='btn btn-raised btn-primary' style='background-color: #3C5AFF;padding:5px 10px;' onclick='submitAction()'>Submit to Moderator</button>");
              }else{
                $('.mark_color').css('color', 'red');
                $('.status').html("<span style='color:red'>Not Complete</span>");
              }
              // var row = table.insertRow(count+1);
              // var cell = row.insertCell(0);
              // var cell1 = row.insertCell(1);
              // cell1.style.textAlign  = "center";
              // cell1.id = "myTd";
              // document.getElementById("myTd").colSpan = "4";
              // cell.innerHTML  = "<b>Continuous Assessment ( CA ) Moderation</b>";
              // cell1.innerHTML  = "<a href='' id='show_image_link' style='width:100%;display:block;'' class='question_link'><b>Continuous Assessment Moderation Form</b></a>";
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
            <span class="now_page">Continuous Assessment</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 10px 10px;">
        <div class="col-md-12">
            <p class="page_title">Continuous Assessment ( CA )</p>
            @if(\Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <Strong>{{\Session::get('success')}}</Strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            <div class="details" style="padding: 0px 5px 0px 5px;">
              <div style="overflow-x: auto;padding:15px 0px 5px 0px;">
                <?php
                $mark = 0;
                foreach($assessments as $row){
                  $mark = $mark+$row->coursework;
                }
                ?>
                <input type="hidden" id="course_id" value="{{$course[0]->course_id}}">
                <?php
                $action_count = count($action);
                $c = 1;
                $c_more = 1;
                foreach($action as $row_action){
                    if($action_count!=$c){
                        if($c_more==1){
                            echo "<a href='' style='display:block;border:0px solid black;margin-top:-15px;padding:0px 10px 10px 10px;' class='more' id='less'>Less...</a>";
                            $c_more++;
                        }
                        if($row_action->status=="Waiting For Moderation"){
                            $status = '<span style="color:#3C5AFF;">Waiting For Moderation</span>';
                            $remarks = "";
                        }else if($row_action->status=="Waiting For Approved"){
                            $status = '<span style="color:green;">Waiting For Approved</span>';
                        }else{
                            $status = '<span style="color:red;">Rejected</span> by '.$row_action->for_who;
                            $remarks = $row_action->remarks;
                        }
                        echo '<div class="row action_list" style="border-bottom:1px solid black;margin:-10px 0px 10px 0px;padding:0px;display:none;">';
                        echo '<div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;">The Continuous Assessment : <b class="mark_color"> <span id="mark">'.$mark.'</span> / <span class="total"></span></b></span></div>';
                        echo '<div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;">Status : <span>'.$status.'</span></span></div>';
                        echo '<div class="col-12" style="padding: 3px 12px 5px 12px;"><span style="font-size: 17px;">Remark : </span>'.$remarks.'</div>';
                        echo '</div>';
                    }

                    if($action_count==$c){
                        if($row_action->status=="Waiting For Moderation"){
                            $status = '<span style="color:#3C5AFF;">Waiting For Moderation</span>';
                            $remarks = "";
                        }else if($row_action->status=="Waiting For Approved"){
                            $status = '<span style="color:green;">Waiting For HOD to Approve</span>';
                            $remarks = $row_action->remarks;
                        }else{
                            $status = $status = '<span style="color:red;">Rejected</span> by '.$row_action->for_who."&nbsp;&nbsp;&nbsp;<button class='btn btn-raised btn-primary' style='background-color: #3C5AFF;padding:5px 10px;' onclick='submitActionSecond()'>Submit Again to Moderator</button>";
                            $remarks = $row_action->remarks;
                        }
                        if($action_count != 1){
                            // echo '<hr style="margin: -10px 5px 0px 5px;background-color:black;">';
                            echo "<a href='' style='border:0px solid black;margin-top:-15px;padding:0px 10px 10px 10px;display:block;' class='more' id='more'>More...</a>";
                        }
                        echo '<div class="row" style="border: 0px solid black;margin:-10px 0px 1px 0px;padding:0px;">';
                        echo '<div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;">The Continuous Assessment : <b class="mark_color"> <span id="mark">'.$mark.'</span> / <span class="total"></span></b></span></div>';
                        echo '<div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;">Status : <span>'.$status.'</span></span></div>';
                        if($remarks!=""){
                            echo '<div class="col-12" style="padding: 3px 12px 0px 12px;"><span style="font-size: 17px;">Remark : </span>'.$remarks.'</div>';
                        }
                        echo '</div>';     
                    }
                    $c++;
                }
                ?>
                @if(count($action)==0)
                <div class="row" style="border: 0px solid black;margin:-10px 0px 0px 0px;padding:0px;">
                  <div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;">The Continuous Assessment : <b class="mark_color"> <span id="mark">{{$mark}}</span> / <span class="total"></span></b></span></div>
                  <div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;">Status : <span class="status"></span></span></div>
                </div>
                @endif
                <hr style="margin: 5px 5px 0px 5px;background-color:black;">
                <p style="padding: 5px 5px 5px 12px;margin: 0px;font-size: 18px;">Assessment List</p>
                <div style="overflow-x: auto;padding:3px 10px 5px 10px;">
                  <table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);padding: 0px;" id="table" class="table">
                    <thead>
                        <tr style="background-color: #d9d9d9;">
                          <td><b>Continuous Assessment List ( {{$course[0]->semester_name}} )</b></td>
                          <td style="border-left:1px solid #e6e6e6;color:black;text-align: center;" width="20%"><b>Question & Solution</b></td>
                          <td style="border-left:1px solid #e6e6e6;color:black;text-align: center;" width="10%"><b>Status</b></td>
                          <td style="border-left:1px solid #e6e6e6;color:black;text-align: center;" width="15%"><b>Student Result</b></td>
                          <td style="border-left:1px solid #e6e6e6;color:black;text-align: center;" width="10%"><b>Count</b></td>
                        </tr>
                    </thead>
                    <tbody>
                      <tr></tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
        </div>
    </div>
</div>
@endsection