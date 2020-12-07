<?php
$title = "Dean";
$option4 = "id='selected-sidebar'";
?>
@extends('layouts.nav_dean')

@section('content')
<style type="text/css">
.more:hover{
    text-decoration:none;
}
.editor{
  height: 100px;
  display: block;
}
.question_link:hover{
    background-color: #d9d9d9;
    text-decoration: none;
    color: #0d2f81;
}
#show_image_link:hover{
    text-decoration: none;
    font-weight: bold;
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
function ModerationForm(actionCA_id){
  window.location = "/Moderator/Assessment/report/"+actionCA_id;
  return false;
}

function Submit_Action(Action){
    var quill_editor = new Quill('#remarks');
    $("#remarks_data").val(quill_editor.root.innerHTML);
    var checkedValue = ""; 
    var inputElements = document.getElementsByClassName('group_verify');
    for(var i=0; inputElements[i]; i++){
        if(inputElements[i].checked){
            checkedValue += inputElements[i].value+"_";
        }
    }

    $('#verify').val(checkedValue);
    $('#result').val(Action);
    if(Action=="Verify"){
      document.getElementById("myForm").submit();
    }else{
        if(confirm("Are you sure want to reject it?")){
            document.getElementById("myForm").submit();
        }
    }
}
function Submit_Moderation(){
  var count_assessemnt = $('#count_assessemnt').val();
  for(var i = 1;i<=(count_assessemnt);i++){
    var quill_editor = new Quill('#suggest_'+i);
    $("#remark_"+i).val(quill_editor.root.innerHTML);
  }
  document.getElementById("myForm").submit();
}

$(document).ready(function(){
  $(document).on("click","#downloadReport", function(){
    var actionCA_id = $('#actionCA_id').val();
    window.location = "/Dean/Assessment/report/"+actionCA_id;
  });
  $(document).on("click",".tp_title", function(){
    var id = $(this).attr("id");
    $('#plan_detail_'+id).slideToggle("slow", function(){
      if($('#plan_detail_'+id).is(":visible")){
        $('#icon_'+id).removeClass('fa fa-plus');
        $('#icon_'+id).addClass('fa fa-minus');
      }else{
        $('#icon_'+id).removeClass('fa fa-minus');
        $('#icon_'+id).addClass('fa fa-plus');
      }
    });
  });
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

  var count_assessemnt = $('#count_assessemnt').val();
  for(var i = 1;i<=(count_assessemnt);i++){
    var quill_editor = new Quill('#suggest_'+i, {
      theme: 'snow'
    });
  }

  var quill_editor = new Quill('#remarks', {
    theme: 'snow'
  });

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
                }
              }
              $('.total').html(total);
              var mark = document.getElementById('mark').innerHTML;
              if(mark==total){
                $('.mark_color').css('color', 'green');
                $('.status').html("<span style='color:grey'>Pending</span>");
              }else{
                $('.mark_color').css('color', 'red');
                $('.status').html("<span style='color:red'>Not Complete</span>");
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
            <a href="/Moderator">Courses </a>/
            <a href="/Moderator/course/{{$course[0]->course_id}}">{{$course[0]->semester_name}} : {{$course[0]->short_form_name}} / {{$course[0]->subject_code}} {{$course[0]->subject_name}} ( {{$course[0]->name}} )</a>/
            <span class="now_page">Continuous Assessment</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 0px 10px;">
        <div class="col-md-12">
             <p class="page_title">Continuous Assessment ( CA )</p>
           @if(count($action)>0)
             @if($action_big[0]->status!="Waiting For Moderation")
             <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                <div id="action_sidebar" class="w3-animate-right" style="display: none;">
                    <div style="text-align: right;padding:10px;">
                        <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                  <ul class="sidebar-action-ul">
                    <p class="title_method">Report</p>
                    <a id="downloadReport"><li class="sidebar-action-li"><i class="fa fa-file-text-o" style="padding: 0px 10px 0px 0px;" aria-hidden="true"></i>Moderation Form ( CA )</li></a>
                  </ul>
            </div>
            @endif
          @endif
             @if(\Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <Strong>{{\Session::get('success')}}</Strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            <div class="details" style="padding: 0px 5px 0px 5px;">
              <div style="padding:15px 0px 5px 0px;">
                <input type="hidden" id="course_id" value="{{$course[0]->course_id}}">
                <!-- <table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);padding: 0px;" id="table" class="table">
                  <thead>
                      <tr style="background-color: #d9d9d9;">
                        <th style="border-left:1px solid #e6e6e6;color:black;text-align: center;" width="50%" rowspan="2"><b>Course Learning Outcome Covered</b></th>
                        <th style="border-left:1px solid #e6e6e6;color:black;text-align: center;" colspan="{{count($assessments)}}"><b>Conitnuous Assessment & Bloom's Taxanomy Level</b></th>
                      </tr>
                      <tr style="background-color: #d9d9d9;">
                        @foreach($assessments as $row)
                          <th style="border-left:1px solid #e6e6e6;color:black;text-align: center;" width="{{50/count($assessments)}}%">{{$row->assessment_name}}</th>
                        @endforeach
                      </tr>
                      <?php
                      $num = 1;
                      ?>
                      @foreach($TP_Ass as $row_tp)
                      <tr>
                        <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;">CLO {{$num}} : {{$row_tp->CLO}}</td>
                        @foreach($assessments as $row)
                        <?php
                          $check = "";
                          $CLO = $row->CLO;
                          $CLO_sel = explode('///',$CLO);
                          $CLO_List = explode(',',$CLO_sel[0]);
                          for($i = 0;$i<=count($CLO_List)-1;$i++){
                            if($CLO_List[$i]==$row_tp->am_id){
                              $check = $row_tp->domain_level;
                            }
                          }
                        ?>
                        <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;vertical-align: middle;">{{$check}}</td>
                        @endforeach
                      </tr>
                      <?php
                        $num++;
                      ?>
                      @endforeach
                  </thead>
                  <tbody>
                    <tr></tr>
                  </tbody>
                </table> -->
            <?php
            $mark = 0;
            foreach($assessments as $row){
              $mark = $mark+$row->coursework;
            }
            ?>
            <?php
                $action_count = count($action);
                $c = 1;
                $c_more = 1;
                $button_verify = "No";
                $moderation_done = "No";
                $self = "";
                // $checkbox_M = '<input type="checkbox" checked class="group_verify" value="1"><b style="color: green"> Verify</b>';
                // $checkbox_C = '<input type="checkbox" checked class="group_verify" value="2"><b style="color: green"> Verify</b>';
                // $checkbox_W = '<input type="checkbox" checked class="group_verify" value="3"><b style="color: green"> Verify</b>';
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
                            if($row_action->verified_date==Null){
                                $person = "";
                            }else{
                                $now = "Approved";
                                $person = " By ( ".$verified_by[0]->position.' : '.$verified_by[0]->name." )";
                            }
                            $status = '<span style="color:red;">Rejected</span> by ( '.$verified_by[0]->position.' : '.$verified_by[0]->name.' )&nbsp;&nbsp;&nbsp;<button class="btn btn-raised btn-primary" style="background-color: #3C5AFF;padding:1px 15px;" onclick="ModerationForm('.$row_action->actionCA_id.')">Previous Moderation Form</button>';
                            $color = "red";
                            $remarks_count = explode('///',$row_action->remarks);
                            $remarks = $remarks_count[1];
                            $verified_count = explode('_',$remarks_count[0]);
                            for($array_num=0;$array_num<(count($verified_count)-1);$array_num++){
                                if($verified_count[$array_num]=="1"){
                                    $iconM = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
                                }else if($verified_count[$array_num]=="2"){
                                    $iconC = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
                                }else{
                                    $iconW = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
                                }
                            }
                            $tp_count = count($verified_count)-1;
                            $self = $row_action->self_declaration;
                        }
                        echo '<div class="row action_list" style="margin:-10px 0px 10px 0px;padding:0px;display:none;">';
                        echo '<div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;">Status : <span>'.$status.'</span></span></div>';
                        // echo '<div class="col-12" style="padding: 0px 15px;"><span style="font-size: 15px;">'.$iconM.' Method of Assessment</span></div>';
                        // echo '<div class="col-12" style="padding: 0px 15px;"><span style="font-size: 15px;">'.$iconC.' Continual Quality Improvement (CQI)</span></div>';
                        // echo '<div class="col-12" style="padding: 0px 15px;"><span style="font-size: 15px;">'.$iconW.' Weekly Plan</span></div>';
                        echo '<div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;">The Continuous Assessment : <b class="mark_color"> <span id="mark">'.$mark.'</span> / <span class="total"></span></b></span></div>';
                        if($self!=""){
                          echo '<div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;">Self-Declaration : <span><b>'.$self.'</b></span></span></div>';
                        }
                        echo '<div class="col-12" style="padding: 3px 12px 5px 12px;"><span style="font-size: 17px;">Remark : </span>'.$remarks.'</div>';
                        echo '</div>';
                    }

                    if($action_count==$c){
                        if($row_action->status=="Waiting For Moderation"){
                            $status = '<span style="color:#3C5AFF;">Waiting For Moderation</span>';
                            $remarks = "";
                            $actionCA_id = $row_action->actionCA_id;
                            echo '<input type="hidden" id="actionCA_id" value='.$actionCA_id.'>';
                            $tp_count = 0;
                            $self = "";
                        }else if($row_action->status=="Waiting For Verified"){
                            // $status = '<span style="color:green;">Waiting For Verification</span>';
                            $status = '<span style="color:green;">Waiting For ( HOD ) to verify</span>';
                            $remarks = $row_action->remarks;
                            $moderation_done = "Yes";
                            $action_AORR = $row_action->AccOrRec;
                            $suggest = $row_action->suggest;
                            $self = $row_action->self_declaration;
                            $actionCA_id = $row_action->actionCA_id;
                            $color = "black";
                            // $button_verify = "Yes";
                            $tp_count = 0;
                            $now = "Verified of Moderation Form ( CA )";
                            echo '<input type="hidden" id="actionCA_id" value='.$actionCA_id.'>';
                        }else if($row_action->status=="Waiting For Rectification"){
                            $status = '<span style="color:green;">Waiting For Lecturer to Rectify</span>';
                            $remarks = $row_action->remarks;
                            $moderation_done = "Yes";
                            $action_AORR = $row_action->AccOrRec;
                            $suggest = $row_action->suggest;
                            $actionCA_id = $row_action->actionCA_id;
                            echo '<input type="hidden" id="actionCA_id" value='.$actionCA_id.'>';
                            $self = "";
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
                        if(($row_action->verified_date==NULL)&&($row_action->moderator_date!=NULL)){
                          echo '<div class="col-12" style="padding: 0px 12px 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Moderated By : <b> ( '.$moderator_person_name->position." : ".$moderator_person_name->name.' ) </b></span></div>';    
                        }
                        // echo '<div class="col-12" style="padding: 0px 15px;"><span style="font-size: 15px;">'.$iconM.' Assessment List & Method</span></div>';
                        // echo '<div class="col-12" style="padding: 0px 15px;"><span style="font-size: 15px;">'.$iconC.' Accepted Or Rectification</span></div>';
                        // echo '<div class="col-12" style="padding: 0px 15px;"><span style="font-size: 15px;">'.$iconW.' Suggestion for improvement</span></div>';
                        // echo '<div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> '.$now.' : <b style="color:'.$color.'">'.$tp_count.' / 3</b></span></div>';
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
                  <div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> The Continuous Assessment : <b class="mark_color"> <span id="mark">{{$mark}}</span> / <span class="total"></span></b></span></div>
                  <div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Status : <span class="status"></span></span></div>
            </div>
            @endif
            <hr style="margin: 5px 5px 5px 5px;background-color:black;">
            <div class="row">
                <h5 style="position: relative;top:3px;left: 10px;" class="tp_title col-10" id="1">
                    Assessment List & Method (<i class="fa fa-plus" aria-hidden="true" id="icon_1" style="color: #0d2f81;position: relative;top: 2px;"></i>)
                </h5>
                <!-- <div class="col-2" style="text-align: right;padding:3px 35px 0px 35px;font-size: 17px;"></div> -->
            </div>
            <div style="overflow-x: auto;padding:3px 10px 5px 10px;display: none;" id="plan_detail_1">
                <table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);padding: 0px;" id="table" class="table">
                  <thead>
                      <tr style="background-color: #d9d9d9;">
                        <th style="border-left:1px solid #e6e6e6;color:black;text-align: center;" width="50%" rowspan="2"><b>Course Learning Outcome Covered</b></th>
                        <th style="border-left:1px solid #e6e6e6;color:black;text-align: center;" colspan="{{count($assessments)}}"><b>Continuous Assessment</b></th>
                      </tr>
                      <tr style="background-color: #d9d9d9;">
                        @foreach($assessments as $row)
                          <th style="border-left:1px solid #e6e6e6;color:black;text-align: center;" width="{{50/count($assessments)}}%"><a href='/assessment/view/whole_paper/{{$row->ass_id}}' target='_blank' id="show_image_link" style="color:#0d2f81;">{{$row->assessment_name}}</a></th>
                        @endforeach
                      </tr>
                      <?php
                      $num = 1;
                      ?>
                      @foreach($TP_Ass as $row_tp)
                      <tr>
                        <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;">CLO {{$num}} : {{$row_tp->CLO}} ( {{$row_tp->domain_level}} , {{$row_tp->PO}} )</td>
                        @foreach($assessments as $row)
                        <?php
                          $check = "<i class='fa fa-times' style='color:red'></i>";
                          $CLO = $row->CLO;
                          $CLO_sel = explode('///',$CLO);
                          $CLO_List = explode(',',$CLO_sel[0]);
                          for($i = 0;$i<=count($CLO_List)-1;$i++){
                            if($CLO_List[$i]==("CLO".$num)){
                              $check = "<i class='fa fa-check' style='color:green'></i>";
                            }
                          }
                        ?>
                        <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;vertical-align: middle;">{!!$check!!}</td>
                        @endforeach
                      </tr>
                      <?php
                        $num++;
                      ?>
                      @endforeach
                  </thead>
                </table>
                </div>
                <hr style="margin: 6px 5px 5px 5px;background-color:#d9d9d9;">
                @if($moderation_done=="Yes")
                <div class="row">
                  <h5 style="position: relative;top:3px;left: 10px;" class="tp_title col-10" id="2">Accepted Or Rectification (<i class="fa fa-plus" aria-hidden="true" id="icon_2" style="color: #0d2f81;position: relative;top: 2px;"></i>)</h5>
                  <!-- <div class="col-2" style="text-align: right;padding:3px 35px 0px 35px;font-size: 17px;"></div> -->
                </div>
                <!-- <p style="padding: 5px 5px 5px 12px;margin: 0px;font-size: 18px;">Accepted Or Rectification</p> -->
                <div style="overflow-x: auto;padding:3px 10px 5px 10px;display: none;" id="plan_detail_2">
                  <table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);padding: 0px;" id="table" class="table">
                    <thead>
                      <tr style="background-color: #d9d9d9;">
                        <th style="text-align: right;color: black;" colspan="2"><b>Assessment</b></th>
                        @foreach($assessments as $row)
                          <th style="border-left:1px solid #e6e6e6;color:black;text-align: center;" width="{{50/count($assessments)}}%" colspan="2"><a href='/assessment/view/whole_paper/{{$row->ass_id}}' target='_blank' id="show_image_link" style="color:#0d2f81;">{{$row->assessment_name}}</a></th>
                        @endforeach
                        <tr style="background-color: #d9d9d9;">
                          <th style="text-align: right;color: black;" colspan="2"><b>% of Coursework</b></th>
                          @foreach($assessments as $row)
                          <th style="border-left:1px solid #e6e6e6;color:black;text-align: center;" width="{{50/count($assessments)}}%" colspan="2">{{$row->coursework}}%</th>
                          @endforeach
                        </tr>
                        <tr>
                          <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;"></td>
                          <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: right;"><b>A = Accepted & R = Rectification</b></td>
                          @foreach($assessments as $row)
                          <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;"><b>A</b></td>
                          <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;"><b>R</b></td>
                          @endforeach
                        </tr>
                        <?php
                        $num = 1;
                        ?>
                        @foreach($TP_Ass as $row_tp)
                        <tr>
                          <td ><b>{{$num}}</b></td>
                          <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;">CLO {{$num}} : {{$row_tp->CLO}} ( {{$row_tp->domain_level}} , {{$row_tp->PO}} )</td>
                          @foreach($assessments as $row)
                          <?php
                            $check = false;
                            $Acc = "";
                            $rec = "";
                            $AccOrRec_list = explode('///',$action_AORR);
                            for($m = 0;$m<=(count($AccOrRec_list)-1);$m++){
                              $AorR = explode('::',$AccOrRec_list[$m]);
                              if($AorR[0]=="CLO_".$num."_".$row->ass_id){
                                $check = true;
                                if($AorR[1]=="A"){
                                  $Acc = '<i class="fa fa-check" style="color:green"></i>';
                                }else{
                                  $rec = '<i class="fa fa-check" style="color:red"></i>';
                                }
                              }
                            }
                          ?>
                          @if($check == true)
                          <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;vertical-align: middle;">{!!$Acc!!}</td>
                          <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;vertical-align: middle;">{!!$rec!!}</td>
                          @else
                          <td colspan="2" style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;vertical-align: middle;">
                          </td>
                          @endif
                          @endforeach
                        </tr>
                        <?php
                          $num++;
                        ?>
                      @endforeach
                      </tr>
                    </thead>
                  </table>
                  <input type="hidden" name="count_CLO" value="{{$num-1}}">
                </div>
                <hr style="margin:6px 5px 5px 5px;background-color:#d9d9d9;">
                <div class="row">
                  <h5 style="position: relative;top:3px;left: 10px;" class="tp_title col-10" id="3">Suggestion for improvement (<i class="fa fa-plus" aria-hidden="true" id="icon_3" style="color: #0d2f81;position: relative;top: 2px;"></i>)</h5>
                  <!-- <div class="col-2" style="text-align: right;padding:3px 35px 0px 35px;font-size: 17px;"></div> -->
                </div>
                <div style="overflow-x: auto;padding:3px 10px 5px 10px;display: none;" id="plan_detail_3">
                    <?php 
                    $c = 1;
                    ?>
                    @foreach($assessments as $row)
                    <?php
                      $full_suggest = explode('///NextAss///',$suggest);
                      for($n = 0;$n<=(count($full_suggest)-1);$n++){
                        $getAssId = explode('<???>',$full_suggest[$n]);
                        if($getAssId[0]==$row->ass_id){
                          $suggest_list = $getAssId[1];
                        }
                      }
                    ?>
                    <div class="col-12" style="margin-top: 10px;border:0px solid black">
                           <a href='/assessment/view/whole_paper/{{$row->ass_id}}' target='_blank' id="show_image_link" style="color:#0d2f81;"> {{$row->assessment_name}} : </a>
                    <div id="suggest_{{$c}}" class="editor">
                      {!!$suggest_list!!}
                    </div>
                    </div>
                    <?php
                    $c++
                    ?>
                    @endforeach
                    <input type="hidden" id="count_assessemnt" value="{{count($assessments)}}">
                </div>
                @endif
                <hr style="margin:6px 5px 0px 5px;background-color:black;">
                @if($button_verify=="Yes")
                <div class="row" style="height: auto;margin: 5px -10px 10px -10px;">
                  <form id="myForm" method="post" action="{{action('Dean\Dean\D_AssessmentController@D_Ass_Verify_Action')}}" style="width: 100%;margin: 0px;">
                      {{csrf_field()}}
                      <input type="hidden" name="verify" id="verify">
                      <input type="hidden" name="course_id" value="{{$course[0]->course_id}}">
                      <input type="hidden" name="result" id="result">
                      <div class="col-12" style="border: 0px solid black;margin: 0px;padding: 0px 20px;font-size: 16px;">
                          Remarks : 
                      </div>
                      <div class="col-12" style="border: 0px solid black;margin-top:5px;margin-bottom: 0px;">
                          <div id="remarks" class="editor">
                          </div>
                          <textarea style="display:none" id="remarks_data" name="remarks"></textarea>
                      </div>
                  </form>
                  <div class="col-12" style="text-align: right;margin: 0px!important;padding-top: 10px;padding-right: 10px;">
                      <input type="button" class="btn btn-raised btn-success" style="color: white;margin: 0px!important;" value="Verify" onclick="Submit_Action('Verify')">&nbsp;
                      <input type="button" class="btn btn-raised btn-danger" style="color: white;margin: 0px!important;" value="Reject" onclick="Submit_Action('Reject')">&nbsp;
                  </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection