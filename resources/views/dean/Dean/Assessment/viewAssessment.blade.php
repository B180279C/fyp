<?php
$title = "Dean";
$option4 = "id='selected-sidebar'";
?>
@extends('layouts.nav_dean')

@section('content')
<style type="text/css">
.question_link:hover{
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
      $('.action_list').css('borderBottom','0px solid black');
      $('.action_list').slideToggle("slow", function(){
        // check paragraph once toggle effect is completed
        if($('.action_list').is(":visible")){
          $('.action_list').css('borderBottom','1px solid black');
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
      window.location = "/Dean/assessment/download/"+num[2];
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

  function submitActionThird(){
    $('#openDocumentModal').modal('show');
  }

  function submitSelfD_form(status){
    $('#self_status').val(status);
    document.getElementById("self_declaration_form").submit();
  }

function ModerationForm(actionCA_id){
  window.location = "/Dean/Assessment/report/"+actionCA_id;
  return false;
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
            url:'/Dean/assessment/getSyllabusData',
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
                    cell1.innerHTML = '<a href="/Dean/assessment/create/'+course_id+'/question/'+response[0][i][9]+'/'+response[0][i][3]+'" style="font-size:18px;margin-left:15%;width:70%;display:block;" class="question_link"><i class="fa fa-plus" aria-hidden="true" ></i></a>';
                    cell2.innerHTML = '<i class="'+status+'" aria-hidden="true"></i>';
                    if(status=="fa fa-check correct"){
                      cell3.innerHTML = '<a href="/Dean/AssessmentResult/'+course_id+'/question/'+response[0][i][3]+'" style="font-size:18px;width:100%;display:block;" class="question_link"><i class="fa fa-plus" aria-hidden="true" ></i></a>';
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
                $('.status').html("<span style='color:green'>Complete</span>");
              }else{
                $('.mark_color').css('color', 'red');
                $('.status').html("<span style='color:red'>Not Complete</span>");
              }
              var moderation_done = $('#moderation_done').val();
              var actionCA_id = $('#actionCA_id').val();
              if(moderation_done=="Yes"){
                var row = table.insertRow(count+1);
                var cell = row.insertCell(0);
                var cell1 = row.insertCell(1);
                cell.style.borderLeft  = "1px solid #d9d9d9";
                cell1.style.textAlign  = "center";
                cell1.id = "myTd";
                document.getElementById("myTd").colSpan = "4";
                cell.innerHTML  = "<b>Continuous Assessment ( CA ) Moderation</b>";
                cell1.innerHTML  = "<button class='btn btn-raised btn-primary' style='background-color: #3C5AFF;padding:5px 10px;margin:0px;' onclick='ModerationForm("+actionCA_id+")'><b><i class='fa fa-download'></i> Continuous Assessment Moderation Form</b></button>";
              }
            }
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
                $moderation_done = "No";
                $actionCA_id = "";
                $self = "";
                foreach($action as $row_action){
                  $iconM = '<i class="fa fa-times-circle" aria-hidden="true" style="color: grey;"></i>';
                  $iconC = '<i class="fa fa-times-circle" aria-hidden="true" style="color: grey;"></i>';
                  $iconW = '<i class="fa fa-times-circle" aria-hidden="true" style="color: grey;"></i>';
                    if($action_count!=$c){
                        if($c_more==1){
                            echo "<a href='' style='display:block;border:0px solid black;margin-top:-15px;padding:0px 10px 10px 10px;' class='more' id='less'>Less...</a>";
                            $c_more++;
                        }
                        if($row_action->status=="Rejected"){
                          $iconM = '<i class="fa fa-times-circle" aria-hidden="true" style="color: red;"></i>';
                          $iconC = '<i class="fa fa-times-circle" aria-hidden="true" style="color: red;"></i>';
                          $iconW = '<i class="fa fa-times-circle" aria-hidden="true" style="color: red;"></i>';
                            $now = "Verified of Moderation Form ( CA )";
                            $status = '<span style="color:red;">Rejected</span> by ( '.$verified_person_name->position." : ".$verified_person_name->name.' )';
                            $remarks_count = explode('///',$row_action->remarks);
                            $remarks = $remarks_count[1];
                            $color = 'red';
                            $self = $row_action->self_declaration;
                            $verified_count = explode('_',$remarks_count[0]);
                            for($array_num=0;$array_num<(count($verified_count)-1);$array_num++){
                                if($verified_count[$array_num]=="1"){
                                    $iconM = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
                                    $checkbox_M = '<b style="color: green"> Verified</b>';
                                }else if($verified_count[$array_num]=="2"){
                                    $iconC = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
                                    $checkbox_C = '<b style="color: green"> Verified</b>';
                                }else{
                                    $iconW = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
                                    $checkbox_W = '<b style="color: green"> Verified</b>';
                                }
                            }
                            $tp_count = count($verified_count)-1;
                        }
                        echo '<div class="row action_list" style="margin:-10px 0px 10px 0px;padding:0px;display:none;">';
                        echo '<div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Status : <span>'.$status.'</span></span></div>';
                        echo '<div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> The Continuous Assessment : <b class="mark_color"> <span id="mark">'.$mark.'</span> / <span class="total"></span></b></span></div>';
                        if($self!=""){
                          echo '<div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Self-Declaration : <span><b>'.$self.'</b></span></span></div>';
                        }
                        // echo '<div class="col-12" style="padding: 0px 15px;"><span style="font-size: 15px;">'.$iconM.' Assessment List & Method</span></div>';
                        // echo '<div class="col-12" style="padding: 0px 15px;"><span style="font-size: 15px;">'.$iconC.' Accepted Or Rectification</span></div>';
                        // echo '<div class="col-12" style="padding: 0px 15px;"><span style="font-size: 15px;">'.$iconW.' Suggestion for improvement</span></div>';
                        // echo '<div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> '.$now.' : <b style="color:'.$color.'">'.$tp_count.' / 3</b></span></div>';
                        // if($row_action->verified_date!=Null){
                        //     echo '<div class="col-12" style="padding: 0px 12px 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Verified By : <b> ( '.$verified_person_name->position." : ".$verified_person_name->name.' ) </b></span></div>';
                        // }
                        echo '<div class="col-12" style="padding: 3px 12px 5px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Remark : </span>'.$remarks.'</div>';
                        echo '</div>';
                    }

                    if($action_count==$c){
                        if($row_action->status=="Waiting For Moderation"){
                            $status = '<span style="color:#3C5AFF;">Waiting For ( '.$moderator_person_name->position.' : '.$moderator_person_name->name.' ) to Moderation</span>';
                            $remarks = "";
                            $now = "Moderated of Continuous Assessment ( CA )";
                            $color = "black";
                            $actionCA_id = $row_action->actionCA_id;
                            echo '<input type="hidden" id="actionCA_id" value='.$actionCA_id.'>';
                            $tp_count = 0;
                            $self = "";
                        }else if($row_action->status=="Waiting For Verified"){
                            // $status = '<span style="color:green;">Waiting For ( '.$verified_person_name->position.' : '.$verified_person_name->name.' ) to verify</span>';
                            $status = '<span style="color:green;">Waiting For ( HOD ) to verify</span>';
                            $remarks = $row_action->remarks;
                            $moderation_done = "Yes";
                            $actionCA_id = $row_action->actionCA_id;
                            $self = $row_action->self_declaration;
                            $now = "Verified of Moderation Form ( CA )";
                            $color = "black";
                            $actionCA_id = $row_action->actionCA_id;
                            echo '<input type="hidden" id="actionCA_id" value='.$actionCA_id.'>';
                            $tp_count = 0;
                        }else if($row_action->status=="Waiting For Rectification"){
                            $status = '<span style="color:green;">Waiting For Lecturer to Rectify</span>';
                            $iconM = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
                            $iconC = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
                            $iconW = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
                            $remarks = $row_action->remarks;
                            $moderation_done = "Yes";
                            $now = "Moderated of Continuous Assessment ( CA )";
                            $color = "green";
                            $self = "";
                            $actionCA_id = $row_action->actionCA_id;
                            echo '<input type="hidden" id="actionCA_id" value='.$actionCA_id.'>';
                            $tp_count = 3;
                        }else if($row_action->status=="Rejected"){
                            $iconM = '<i class="fa fa-times-circle" aria-hidden="true" style="color: red;"></i>';
                            $iconC = '<i class="fa fa-times-circle" aria-hidden="true" style="color: red;"></i>';
                            $iconW = '<i class="fa fa-times-circle" aria-hidden="true" style="color: red;"></i>';
                            $checkbox_M = '<b style="color: red"> Rejected</b>';
                            $checkbox_C = '<b style="color: red"> Rejected</b>';
                            $checkbox_W = '<b style="color: red"> Rejected</b>';
                            if($row_action->verified_date==Null){
                                $person = "";
                            }else{
                                $now = "Approved";
                                $person = " By ( ".$verified_by[0]->position.' : '.$verified_by[0]->name." )";
                            }
                            $moderation_done = "Yes";
                            $action_AORR = $row_action->AccOrRec;
                            $suggest = $row_action->suggest;
                            $now = "Verified of Moderation Form ( CA )";
                            $status = '<span style="color:red;">Rejected</span>'.$person;
                            $remarks_count = explode('///',$row_action->remarks);
                            $remarks = $remarks_count[1];
                            $color = 'red';
                            $verified_count = explode('_',$remarks_count[0]);
                            for($array_num=0;$array_num<(count($verified_count)-1);$array_num++){
                                if($verified_count[$array_num]=="1"){
                                    $iconM = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
                                    $checkbox_M = '<b style="color: green"> Verified</b>';
                                }else if($verified_count[$array_num]=="2"){
                                    $iconC = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
                                    $checkbox_C = '<b style="color: green"> Verified</b>';
                                }else{
                                    $iconW = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
                                    $checkbox_W = '<b style="color: green"> Verified</b>';
                                }
                            }
                            $tp_count = count($verified_count)-1;
                            $actionCA_id = $row_action->actionCA_id;
                            echo '<input type="hidden" id="actionCA_id" value='.$actionCA_id.'>';
                        }else{
                          $now = "Verified of Moderation Form ( CA )";
                          $status = '<span style="color:green;">Verified</span>';
                          $iconM = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
                          $iconC = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
                          $iconW = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
                          $tp_count = 3;
                          $remarks = $row_action->remarks;
                          $color = 'green';
                          $moderation_done = "Yes";
                          $action_AORR = $row_action->AccOrRec;
                          $suggest = $row_action->suggest;
                          $actionCA_id = $row_action->actionCA_id;
                          echo '<input type="hidden" id="actionCA_id" value='.$actionCA_id.'>';
                          $self = $row_action->self_declaration;
                          $checkbox_M = '<b style="color: green"> Verified</b>';
                          $checkbox_C = '<b style="color: green"> Verified</b>';
                          $checkbox_W = '<b style="color: green"> Verified</b>';
                        }
                        if($action_count != 1){
                            // echo '<hr style="margin: -10px 5px 0px 5px;background-color:black;">';
                            echo "<a href='' style='border:0px solid black;margin-top:-15px;padding:0px 10px 10px 10px;display:block;' class='more' id='more'>More...</a>";
                        }
                        echo '<div class="row" style="border: 0px solid black;margin:-10px 0px 1px 0px;padding:0px;">';
                        
                        echo '<div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Status : <span>'.$status.'</span></span></div>';
                        
                        echo '<div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> The Continuous Assessment : <b class="mark_color"> <span id="mark">'.$mark.'</span> / <span class="total"></span></b></span></div>';
                        if($self!=""){
                          echo '<div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Self-Declaration : <span><b>'.$self.'</b></span></span></div>';
                        }
                        if($row_action->moderator_date!=Null){
                            echo '<div class="col-12" style="padding: 0px 12px 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Moderated By : <b> ( '.$moderator_person_name->position." : ".$moderator_person_name->name.' ) </b></span></div>';    
                        }
                        if($row_action->verified_date!=Null){
                            echo '<div class="col-12" style="padding: 0px 12px 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Verified By : <b> ( '.$verified_by[0]->position.' : '.$verified_by[0]->name.' ) </b></span></div>';
                        }
                        if($remarks!=""){
                            echo '<div class="col-12" style="padding: 3px 12px 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Remark : </span>'.$remarks.'</div>';
                        }
                        echo '</div>';     
                    }
                    $c++;
                }
                ?>
                @if(count($action)==0)
                <div class="row" style="border: 0px solid black;margin:-10px 0px 0px 0px;padding:0px;">
                  <div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> The Continuous Assessment ( CA ) : <b class="mark_color"> <span id="mark">{{$mark}}</span> / <span class="total"></span></b></span></div>
                  <div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Status : <span class="status"></span></span></div>
                </div>
                @endif
                <input type="hidden" id="moderation_done" value="{{$moderation_done}}">
                <input type="hidden" id="actionCA_id" value="{{$actionCA_id}}">
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