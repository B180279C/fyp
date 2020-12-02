<?php
$title = "Moderator";
$option3 = "id='selected-sidebar'";
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
        if(checkedValue=="1_2_3_"){
            document.getElementById("myForm").submit();
        }else{
            alert("If you select the verify button, that need to active all the verify checkbox.");
        }
    }else{
        if(checkedValue!="1_2_3_"){
            document.getElementById("myForm").submit();
        }else{
            alert("If you select the reject button, that need to inactive one or more verify checkbox.");
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

function ModerationForm(actionCA_id){
  window.location = "/Moderator/Assessment/report/"+actionCA_id;
}

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

  var count_assessemnt = $('#count_assessemnt').val();
  for(var i = 1;i<=(count_assessemnt);i++){
    var quill_editor = new Quill('#suggest_'+i, {
      theme: 'snow'
    });
  }
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
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/Moderator">Courses </a>/
            <a href="/Moderator/course/{{$course[0]->course_id}}">{{$course[0]->semester_name}} : {{$course[0]->short_form_name}} / {{$course[0]->subject_code}} {{$course[0]->subject_name}} ( {{$course[0]->name}} )</a>/
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
                foreach($action as $row_action){
                    if($action_count!=$c){
                        if($c_more==1){
                            echo "<a href='' style='display:block;border:0px solid black;margin-top:-15px;padding:0px 10px 10px 10px;' class='more' id='less'>Less...</a>";
                            $c_more++;
                        }
                        if($row_action->status=="Rejected"){
                            $status = '<span style="color:red;">Rejected</span> by '.$row_action->for_who.'&nbsp;&nbsp;<button class="btn btn-raised btn-primary" style="background-color: #3C5AFF;padding:5px 10px;" onclick="ModerationForm('.$row_action->actionCA_id.')">Previous Moderation Form</button>';
                            $remarks = $row_action->remarks;
                            $self = $row_action->self_declaration;
                        }
                        echo '<div class="row action_list" style="margin:-10px 0px 10px 0px;padding:0px;display:none;">';
                        echo '<div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;">The Continuous Assessment : <b class="mark_color"> <span id="mark">'.$mark.'</span> / <span class="total"></span></b></span></div>';
                        echo '<div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;">Status : <span>'.$status.'</span></span></div>';
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
                            $button_verify = "Yes";
                            $actionCA_id = $row_action->actionCA_id;
                        }else if($row_action->status=="Waiting For Verified"){
                            $status = '<span style="color:green;">Waiting For HOD to Verify</span>&nbsp;&nbsp;<button class="btn btn-raised btn-primary" style="background-color: #3C5AFF;padding:5px 10px;" onclick="ModerationForm('.$row_action->actionCA_id.')">Moderation Form</button>';
                            $remarks = $row_action->remarks;
                            $moderation_done = "Yes";
                            $action_AORR = $row_action->AccOrRec;
                            $suggest = $row_action->suggest;
                            $self = $row_action->self_declaration;
                        }else if($row_action->status=="Waiting For Rectification"){
                            $status = '<span style="color:green;">Waiting For Lecturer to Rectify</span>&nbsp;&nbsp;<button class="btn btn-raised btn-primary" style="background-color: #3C5AFF;padding:5px 10px;" onclick="ModerationForm('.$row_action->actionCA_id.')">Moderation Form</button>';
                            $remarks = $row_action->remarks;
                            $moderation_done = "Yes";
                            $action_AORR = $row_action->AccOrRec;
                            $suggest = $row_action->suggest;
                        }else{
                            $status = '<span style="color:red;">Rejected</span> by '.$row_action->for_who.'&nbsp;&nbsp;<button class="btn btn-raised btn-primary" style="background-color: #3C5AFF;padding:5px 10px;" onclick="ModerationForm('.$row_action->actionCA_id.')">Moderation Form</button>';
                            $remarks = $row_action->remarks;
                            $self = $row_action->self_declaration;
                        }
                        if($action_count != 1){
                            // echo '<hr style="margin: -10px 5px 0px 5px;background-color:black;">';
                            echo "<a href='' style='border:0px solid black;margin-top:-15px;padding:0px 10px 10px 10px;display:block;' class='more' id='more'>More...</a>";
                        }
                        echo '<div class="row" style="border: 0px solid black;margin:-10px 0px 1px 0px;padding:0px;">';
                        echo '<div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;">The Continuous Assessment : <b class="mark_color"> <span id="mark">'.$mark.'</span> / <span class="total"></span></b></span></div>';
                        echo '<div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;">Status : <span>'.$status.'</span></span></div>';
                        if($self!=""){
                          echo '<div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;">Self-Declaration : <span><b>'.$self.'</b></span></span></div>';
                        }
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
            <p style="padding: 5px 5px 5px 12px;margin: 0px;font-size: 18px;">Assessment List & Method</p>
            <div style="overflow-x: auto;padding:3px 10px 5px 10px;">
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
                <hr style="margin: 5px 5px;background-color:black;">
                @if($moderation_done=="Yes")
                <p style="padding: 5px 5px 5px 12px;margin: 0px;font-size: 18px;">Accepted Or Rectification</p>
                <div style="overflow-x: auto;padding:3px 10px 5px 10px;">
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
                <hr style="margin: 5px 5px;background-color:black;">
                <p style="padding: 5px 5px 0px 12px;margin: 0px;font-size: 18px;">Suggestion for improvement</p>
                <div class="row" style="overflow-x: auto;padding:0px 0px 5px 0px;margin: -10px 0px 0px 0px;border:0px solid black;">
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
                @if($button_verify=="Yes")
                <form id="myForm" method="post" action="{{action('Dean\Moderator\M_AssessmentController@M_Ass_Moderate_Action')}}" style="margin: 0px;">
                  {{csrf_field()}}
                  <input type="hidden" name="actionCA_id" value="{{$actionCA_id}}">
                  <input type="hidden" name="course_id" value="{{$course[0]->course_id}}">
                <p style="padding: 5px 5px 5px 12px;margin: 0px;font-size: 18px;">Accepted Or Rectification</p>
                <div style="overflow-x: auto;padding:3px 10px 5px 10px;">
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
                            $CLO = $row->CLO;
                            $CLO_sel = explode('///',$CLO);
                            $CLO_List = explode(',',$CLO_sel[0]);
                            for($i = 0;$i<=count($CLO_List)-1;$i++){
                              if($CLO_List[$i]==("CLO".$num)){
                                $check = true;
                              }
                            }
                          ?>
                          @if($check == true)
                          <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;vertical-align: middle;"><input type="radio" name="CLO_{{$num}}_{{$row->ass_id}}" value="A" checked></td>
                          <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;vertical-align: middle;"><input type="radio" name="CLO_{{$num}}_{{$row->ass_id}}" value="R"></td>
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
                <hr style="margin: 5px 5px;background-color:black;">
                <p style="padding: 5px 5px 0px 12px;margin: 0px;font-size: 18px;">Suggestion for improvement</p>
                <div class="row" style="overflow-x: auto;padding:0px 0px 5px 0px;margin: -10px 0px 0px 0px;border:0px solid black;">
                    <?php 
                    $c = 1;
                    ?>
                    @foreach($assessments as $row)
                    <div class="col-12" style="margin-top: 10px;border:0px solid black">
                           <a href='/assessment/view/whole_paper/{{$row->ass_id}}' target='_blank' id="show_image_link" style="color:#0d2f81;"> {{$row->assessment_name}} : </a>
                    </div>
                    <div class="col-12" style="border: 0px solid black;margin-top:10px;margin-bottom: 0px;">
                        <div id="suggest_{{$c}}" class="editor">
                        </div>
                        <input type="hidden" name="ass_id_{{$c}}" value="{{$row->ass_id}}">
                        <textarea style="display:none" id="remark_{{$c}}" name="remark_{{$c}}"></textarea>
                    </div>
                    <?php
                    $c++
                    ?>
                    @endforeach
                    <input type="hidden" id="count_assessemnt" value="{{count($assessments)}}">
                </form>
                <div class="col-12" style="text-align: right;margin: 0px!important;padding-top: 18px;padding-right: 10px;">
                    <input type="button" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;margin: 0px!important;" value="Generate Moderation Form" onclick="Submit_Moderation()">&nbsp;
                </div>
              </div>
              @endif
            </div>
        </div>
    </div>
</div>
@endsection