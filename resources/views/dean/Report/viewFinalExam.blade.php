<?php
$title = "Final_Assessment";
$option6 = "id='selected-sidebar'";
?>
@extends('layouts.layout')

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
.td_table{
	border-left:1px solid #d9d9d9;
	border-bottom: 1px solid #d9d9d9;
	text-align: center;
	vertical-align: middle;
}
.td_table_no{
	border-left:1px solid #d9d9d9;
	border-bottom: 1px solid #d9d9d9;
	text-align: center;
	vertical-align: middle;
	font-weight: bold;
	background-color: #d9d9d9;
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

function checkPercentage(id,type){
	var r_u = parseInt($('#r_u_'+id).val());
	var a_a = parseInt($('#a_a_'+id).val());
	var e_c = parseInt($('#e_c_'+id).val());

	if((r_u+a_a+e_c)>100){
		alert('Your percentage is over 100%');
		$('#'+type+'_'+id).val("");
	}
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
    }else if(Action=="Approve"){
      document.getElementById("myForm").submit();
    }else{
        if(confirm("Are you sure want to reject it?")){
            document.getElementById("myForm").submit();
        }
    }
}


function ModerationForm(actionFA_id){
  window.location = "{{$character}}/Reviewer/FinalExamination/report/"+actionFA_id;
}

$(document).ready(function(){
  $(document).on("click","#downloadReport", function(){
    var course_id = $('#course_id').val();
    window.location = "{{$character}}/report/final_assessment/download/"+course_id;
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
  var quill_editor = new Quill('#feedback', {
      theme: 'snow'
  });

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
            url:'{{$character}}/Reviewer/FinalExamination/getSyllabusData',
            data:{course_id:course_id},
            success:function(response){
              var count = 0;
              var new_count = 0;
              var table = document.getElementById("table");
              var status = false;    
              for(var i = 0;i<=(response[0].length-1);i++){
                  if((response[0][i][1]==null)&&(response[0][i][2]!=null)&&(response[0][i][3]!=null)&&(response[0][i][9]!=null)){
                    var name = response[0][i][3];
                    var percentage = response[0][i][9];
                  }
              }
              $('.total').html(percentage);
              var mark = document.getElementById('mark').innerHTML;
              if(mark==percentage){
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
            <a href="{{$character}}/home" class="first_page"> Home </a>/
            <a href="{{$character}}/report/course/List/"> Report List </a>/
            <a href="{{$character}}/report/final_assessment/">Moderation Form ( FA )</a>/
            <span class="now_page">{{$course[0]->semester_name}} : {{$course[0]->short_form_name}} / {{$course[0]->subject_code}} {{$course[0]->subject_name}}</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 5px 10px;">
        <div class="col-md-12">
             <p class="page_title">Final Assessment ( FA )</p>
             @if(count($action)>0)
                @if($action_big[0]->status!="Waiting For Moderation")
                <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                    <div id="action_sidebar" class="w3-animate-right" style="display: none;">
                        <div style="text-align: right;padding:10px;">
                            <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                        </div>
                      <ul class="sidebar-action-ul">
                        <p class="title_method">Report</p>
                        <a id="downloadReport"><li class="sidebar-action-li"><i class="fa fa-file-text-o" style="padding: 0px 10px 0px 0px;" aria-hidden="true"></i>Moderation Form ( FA )</li></a>
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
              <div style="padding:15px 0px 0px 0px;">
                <input type="hidden" id="course_id" value="{{$course[0]->course_id}}">
                <?php
	            $mark = 0;
	            foreach($ass_final as $row){
	              $mark = $mark+$row->coursework;
	            }
	            ?>
	            <?php
	                $action_count = count($action);
	                $c = 1;
	                $c_more = 1;
	                $button_verify = "No";
                  $button_approve = "No";
	                $moderation_done = "No";
	                $self = "";
	                foreach($action as $row_action){
	                    if($action_count!=$c){
	                        if($c_more==1){
	                            echo "<a href='' style='display:block;border:0px solid black;margin-top:-15px;padding:0px 10px 10px 10px;' class='more' id='less'>Less...</a>";
	                            $c_more++;
	                        }
	                        if($row_action->status=="Rejected"){
                              if($row_action->verified_date==Null){
                                  $person = " By ( ".$verified_person_name->position." : ".$verified_person_name->name." )";
                              }else{
                                  $now = "Approved";
                                  $person = " By ( ".$approved_person_name->position." : ".$approved_person_name->name." )";
                              }
                              $status = '<span style="color:red;">Rejected</span>'.$person.'&nbsp;&nbsp;&nbsp;<button class="btn btn-raised btn-primary" style="background-color: #3C5AFF;padding:1px 15px;" onclick="ModerationForm('.$row_action->actionFA_id.')">Previous Moderation Form</button>';
	                            $remarks_count = explode('///',$row_action->remarks);
                              $remarks = $remarks_count[1];
	                            $self = $row_action->self_declaration;
	                        }
	                        echo '<div class="row action_list" style="margin:-10px 0px 10px 0px;padding:0px;display:none;">';
                          echo '<div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Status : <span>'.$status.'</span></span></div>';
                          echo '<div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> The Continuous Assessment : <b class="mark_color"> <span id="mark">'.$mark.'</span> / <span class="total"></span></b></span></div>';
                          if($self!=""){
                            echo '<div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Self-Declaration : <span><b>'.$self.'</b></span></span></div>';
                          }
                          echo '<div class="col-12" style="padding: 0px 12px 5px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Remark : </span>'.$remarks.'</div>';
                          echo '</div>';
	                    }

	                    if($action_count==$c){
	                        if($row_action->status=="Waiting For Moderation"){
	                            $status = '<span style="color:#3C5AFF;">Waiting For Moderation</span>';
                              $remarks = "";
                              $actionFA_id = $row_action->actionFA_id;
                              echo '<input type="hidden" id="actionFA_id" value='.$actionFA_id.'>';
                              $self = "";
	                        }else if($row_action->status=="Waiting For Verified"){
	                            $status = '<span style="color:green;">Waiting For ( HOD ) to Verify</span>';
	                            $remarks = $row_action->remarks;
	                            $moderation_done = "Yes";
	                            $action_degree = $row_action->degree;
	                            $suggest = $row_action->suggest;
	                            $feedback = $row_action->feedback;
	                            $self = $row_action->self_declaration;
                              if($character=="/hod"){
                                $button_verify = "Yes";
                              }
                              $actionFA_id = $row_action->actionFA_id;
                              echo '<input type="hidden" id="actionFA_id" value='.$actionFA_id.'>';

	                        }else if($row_action->status=="Waiting For Rectification"){
	                            $status = '<span style="color:green;">Waiting For Lecturer to Rectify</span>';
	                            $remarks = $row_action->remarks;
	                            $moderation_done = "Yes";
	                            $action_degree = $row_action->degree;
	                            $feedback = $row_action->feedback;
	                            $suggest = $row_action->suggest;
	                        }else if($row_action->status=="Rejected"){
	                            if($row_action->verified_date==Null){
                                  $person = " By ( ".$verified_person_name->position." : ".$verified_person_name->name." )";
                              }else{
                                  $now = "Approved";
                                  $person = " By ( ".$approved_person_name->position." : ".$approved_person_name->name." )";
                              }
                              $status = '<span style="color:red;">Rejected</span>'.$person;
                              $remarks_count = explode('///',$row_action->remarks);
	                            $remarks = $remarks_count[1];
	                            $self = $row_action->self_declaration;
                              $moderation_done = "Yes";
                              $action_degree = $row_action->degree;
                              $feedback = $row_action->feedback;
                              $suggest = $row_action->suggest;
                              $actionFA_id = $row_action->actionFA_id;
                              echo '<input type="hidden" id="actionFA_id" value='.$actionFA_id.'>';
	                        }else if($row_action->status=="Waiting For Approve"){
                              $self = $row_action->self_declaration;
                              $status = '<span style="color:green;">Waiting For ( Dean ) to Approve</span>';
                              $moderation_done = "Yes";
                              if($character==""){
                                $button_approve = "Yes";
                              }
                              $action_degree = $row_action->degree;
                              $feedback = $row_action->feedback;
                              $suggest = $row_action->suggest;
                              $remarks = $row_action->remarks;
                              $actionFA_id = $row_action->actionFA_id;
                              echo '<input type="hidden" id="actionFA_id" value='.$actionFA_id.'>';
                          }else{
                              $self = $row_action->self_declaration;
                              $status = '<span style="color:green;">Approval For Printing</span>';
                              $moderation_done = "Yes";
                              $action_degree = $row_action->degree;
                              $feedback = $row_action->feedback;
                              $suggest = $row_action->suggest;
                              $remarks = "";
                              $actionFA_id = $row_action->actionFA_id;
                              echo '<input type="hidden" id="actionFA_id" value='.$actionFA_id.'>';
                          }
	                        if($action_count != 1){
                            // echo '<hr style="margin: -10px 5px 0px 5px;background-color:black;">';
                            echo "<a href='' style='border:0px solid black;margin-top:-15px;padding:0px 10px 10px 10px;display:block;' class='more' id='more'>More...</a>";
                          }
                          echo '<div class="row" style="border: 0px solid black;margin:-10px 0px 1px 0px;padding:0px;">';
                          echo '<div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> The Final Assessment : <b class="mark_color"> <span id="mark">'.$mark.'</span> / <span class="total"></span></b></span></div>';
                          echo '<div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Status : <span>'.$status.'</span></span></div>';
                          if($self!=""){
                            echo '<div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Self-Declaration : <span><b>'.$self.'</b></span></span></div>';
                          }
                          if($row_action->moderator_date!=Null){
                              echo '<div class="col-12" style="padding: 0px 12px 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Moderated By : <b> ( '.$moderator_person_name->position." : ".$moderator_person_name->name.' ) </b></span></div>';
                          }
                          if($row_action->verified_date!=Null){
                              echo '<div class="col-12" style="padding: 0px 12px 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Verified By : <b> ( '.$verified_person_name->position." : ".$verified_person_name->name.' ) </b></span></div>';
                          }
                          if($row_action->approved_date!=Null){
                              echo '<div class="col-12" style="padding: 0px 12px 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Approved By : <b> ( '.$approved_person_name->position." : ".$approved_person_name->name.' ) </b></span></div>';
                          }
                          if($remarks!=""){
                              echo '<div class="col-12" style="padding: 0px 12px 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Remarks from ( Verifier ) : </span>'.$remarks.'</div>';
                          }
                          echo '</div>';    
	                    }
	                    $c++;
	                }
	                ?>
	            @if(count($action)==0)
	            <div class="row" style="border: 0px solid black;margin:-10px 0px 0px 0px;padding:0px;">
	                  <div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> The Final Assessment : <b class="mark_color"> <span id="mark">{{$mark}}</span> / <span class="total"></span></b></span></div>
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
            @if($moderation_done=="No")
              <div style="overflow-x: auto;padding:3px 10px 5px 10px;display: none;" id="plan_detail_1">
                <table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);padding: 0px;" id="table" class="table">
                  <thead>
                      <tr style="background-color: #d9d9d9;">
                        <th style="border-left:1px solid #e6e6e6;color:black;text-align: center;" width="10%"><b>Question No.</b></th>
                        <th style="border-left:1px solid #e6e6e6;color:black;text-align: center;" width="25%"><b>Topic(s)<br/>covered</b></th>
                        <th style="border-left:1px solid #e6e6e6;color:black;text-align: center;"><b>Course Learning Outcome(s) covered</b></th>
                        <th style="border-left:1px solid #e6e6e6;color:black;text-align: center;" width="25%"><b>Bloom's <br/>Taxanomy Level*</b></th>
                      </tr>
                      @foreach($ass_final as $row)
                        <tr>
                          <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;vertical-align: middle;"><a href='{{$character}}/Moderator/final_assessment/view/whole_paper/{{$row->fx_id}}' target='_blank' id="show_image_link" style="color:#0d2f81;">{{$row->assessment_name}}</a></td>
                          <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;vertical-align: middle;">
                          {{$row->topic}}
                          </td>
                          <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;vertical-align: middle;">
                          {{$row->CLO}}
                          </td>
                          <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;vertical-align: middle;">
                          <?php
                          $CLO_sel = explode(',',$row->CLO);
                          ?>
                          @for($i = 0; $i<=count($CLO_sel)-1;$i++)
                            <?php
                              $num = 1;
                            ?>
                            @foreach($TP_Ass as $row_ass)
                              @if(('CLO'.$num) == $CLO_sel[$i])
                                {{$row_ass->domain_level}},
                              @endif
                              <?php
                              $num++;
                              ?>
                            @endforeach
                          @endfor
                          </td>
                        </tr>
                      @endforeach
                  </thead>
                </table>
                <table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);padding: 0px;" id="table" class="table">
                <?php
                $num = 1;
                ?>
                @foreach($TP_Ass as $row_ass)
                  <tr>
                  <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #e6e6e6;text-align: center;vertical-align: middle;background-color: #d9d9d9;" width="10%"><b>CLO {{$num}}</b></td>  
                  <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: left;vertical-align: middle;">
                    {{$row_ass->CLO}} ( {{$row_ass->domain_level}} , {{$row_ass->PO}} )
                  </td>
                  </tr>
                <?php
              $num++;
              ?>
                @endforeach
                </table>
                </div>
              @endif
            @if($moderation_done=="Yes")
            <div style="overflow-x: auto;padding:3px 10px 5px 10px;display: none;" id="plan_detail_1">
              *(New) is created after moderation*
                <table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);padding: 0px;" id="table" class="table">
                  <thead>
                      <tr style="background-color: #d9d9d9;">
                      	<th style="border-left:1px solid #e6e6e6;color:black;text-align: center;" width="10%"><b>Question No.</b></th>
                      	<th style="border-left:1px solid #e6e6e6;color:black;text-align: center;" width="25%"><b>Topic(s)<br/>covered</b></th>
                      	<th style="border-left:1px solid #e6e6e6;color:black;text-align: center;"><b>Course Learning Outcome(s) covered</b></th>
                      	<th style="border-left:1px solid #e6e6e6;color:black;text-align: center;" width="25%"><b>Bloom's <br/>Taxanomy Level*</b></th>
                      </tr>
                      @foreach($all_ass_final as $row)
                      <?php
                        $get = false;
                        $array = array();
                        $full_suggest = explode('///NextAss///',$suggest);
                        for($n = 0;$n<=(count($full_suggest)-1);$n++){
                          $getFxId = explode('<???>',$full_suggest[$n]);
                          if($getFxId[0]==$row->fx_id){
                            $get = true;
                          }
                          array_push($array,$getFxId[0]);
                        }
                      ?>
                      @if((($row->fx_id>=max($array))&&($row->status!="Remove"))||($get == true))
                      	<tr>
                      		<td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;vertical-align: middle;">
                            @if($row->status=="Active")
                            <a href='{{$character}}/Reviewer/final_assessment/view/whole_paper/{{$row->fx_id}}' target='_blank' id="show_image_link" style="color:#0d2f81;">
                            @endif
                              {{$row->assessment_name}}
                              @if($row->status=="Remove")
                                  (Removed)
                              @endif
                              @if($row->fx_id>max($array))
                                  (New)
                              @endif
                            </a>
                          </td>
                      		<td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;vertical-align: middle;">
                      		{{$row->topic}}
                      		</td>
                      		<td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;vertical-align: middle;">
                          {{$row->CLO}}
                      		</td>
                      		<td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;vertical-align: middle;">
                      		<?php
								          $CLO_sel = explode(',',$row->CLO);
                      		?>
                      		@for($i = 0; $i<=count($CLO_sel)-1;$i++)
                      			<?php
                      				$num = 1;
                      			?>
                      			@foreach($TP_Ass as $row_ass)
	                      			@if(('CLO'.$num) == $CLO_sel[$i])
	                      				{{$row_ass->domain_level}},
	                      			@endif
	                      			<?php
	                      			$num++;
	                      			?>
                      			@endforeach
                      		@endfor
                      		</td>
                      	</tr>
                        @endif
                      @endforeach
                  </thead>
                </table>
                <table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);padding: 0px;" id="table" class="table">
               	<?php
               	$num = 1;
               	?>
                @foreach($TP_Ass as $row_ass)
                  <tr>
                 	<td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #e6e6e6;text-align: center;vertical-align: middle;background-color: #d9d9d9;" width="10%"><b>CLO {{$num}}</b></td> 	
                 	<td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: left;vertical-align: middle;">
                 		{{$row_ass->CLO}} ( {{$row_ass->domain_level}} , {{$row_ass->PO}} )
                 	</td>
                  </tr>
                <?php
	            $num++;
	            ?>
                @endforeach
                </table>
                </div>
                <hr style="margin: 6px 5px 5px 5px;background-color:#d9d9d9;">
                
                <?php
                $degree = explode('///',$action_degree);
                ?>
                <div class="row">
                <h5 style="position: relative;top:3px;left: 10px;" class="tp_title col-10" id="2">
                    Indicate the degree (<i class="fa fa-plus" aria-hidden="true" id="icon_2" style="color: #0d2f81;position: relative;top: 2px;"></i>)
                </h5>
                </div>
                <div style="overflow-x: auto;padding:3px 10px 5px 10px;display: none;" id="plan_detail_2">
                	<table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);padding: 0px;" id="table" class="table">
	                    <thead>
	                    	<tr style="background-color: #d9d9d9;">
	                    		<th rowspan="2" style="border-left:1px solid #e6e6e6;color:black;text-align: center;"><b>No.</b></th>
	                    		<th rowspan="2" width="40%" style="border-left:1px solid #e6e6e6;color:black;text-align: center;"></th>
	                    		<th style="border-left:1px solid #e6e6e6;color:black;text-align: center;">5</th> 
	                    		<th style="border-left:1px solid #e6e6e6;color:black;text-align: center;">4</th> 
	                    		<th style="border-left:1px solid #e6e6e6;color:black;text-align: center;">3</th> 
	                    		<th style="border-left:1px solid #e6e6e6;color:black;text-align: center;">2</th> 
	                    		<th style="border-left:1px solid #e6e6e6;color:black;text-align: center;">1</th> 
	                    	</tr>
	                    	<tr style="background-color: #d9d9d9;">
	                    		<th style="border-left:1px solid #e6e6e6;color:black;text-align: center;">Strongly Agree</th>
	                    		<th style="border-left:1px solid #e6e6e6;color:black;text-align: center;">Agree</th>
	                    		<th style="border-left:1px solid #e6e6e6;color:black;text-align: center;">Neutral</th>
	                    		<th style="border-left:1px solid #e6e6e6;color:black;text-align: center;">Disagree</th>
	                    		<th style="border-left:1px solid #e6e6e6;color:black;text-align: center;">Strongly Disagree</th>
	                    	</tr>	
	                    	<tr>
	                    		<th colspan="7" style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;color:black;text-align: center;"><center><b>QUESTION PAPER</b></center></th>
	                    	</tr>
	                    </thead>
	                    <tr>
	                    	<td class="td_table_no">1</td>
	                    	<td style="border-left: 1px solid #e6e6e6;background-color: #d9d9d9;">Questions are within the scope of the course syllabus and are aligned to the mapped CLOs and PLOs</td>
	                    	@for($i = 1;$i<=5;$i++)
	                    	<?php
	                    		$degree_result = explode('_',$degree[0]);
	                    	?>
	                    	@if($degree_result[1]==$i)
	                    	<td class="td_table" style="vertical-align: middle;"><i class="fa fa-check correct"></i></td>
	                    	@else
	                    	<td class="td_table"></td>
	                    	@endif
	                    	@endfor
	                    </tr>
	                    <tr>
	                    	<td class="td_table_no">2</td>
	                    	<td style="border-left: 1px solid #e6e6e6;background-color: #d9d9d9;">Questions are arranged according to complexity from lower difficult level to higher difficulty level</td>
	                    	@for($i = 1;$i<=5;$i++)
	                    	<?php
	                    		$degree_result = explode('_',$degree[1]);
	                    	?>
	                    	@if($degree_result[1]==$i)
	                    	<td class="td_table" style="vertical-align: middle;"><i class="fa fa-check correct"></i></td>
	                    	@else
	                    	<td class="td_table"></td>
	                    	@endif
	                    	@endfor
	                    </tr>
	                    <tr>
	                    	<td class="td_table_no">3</td>
	                    	<td style="border-left: 1px solid #e6e6e6;background-color: #d9d9d9;">None of the questions in the examination questions paper are overlap.</td>
	                    	@for($i = 1;$i<=5;$i++)
	                    	<?php
	                    		$degree_result = explode('_',$degree[2]);
	                    	?>
	                    	@if($degree_result[1]==$i)
	                    	<td class="td_table" style="vertical-align: middle;"><i class="fa fa-check correct"></i></td>
	                    	@else
	                    	<td class="td_table"></td>
	                    	@endif
	                    	@endfor
	                    </tr>
	                    <tr>
	                    	<td class="td_table_no">4</td>
	                    	<td style="border-left: 1px solid #e6e6e6;background-color: #d9d9d9;">Question are free from factual errors.</td>
	                    	@for($i = 1;$i<=5;$i++)
	                    	<?php
	                    		$degree_result = explode('_',$degree[3]);
	                    	?>
	                    	@if($degree_result[1]==$i)
	                    	<td class="td_table" style="vertical-align: middle;"><i class="fa fa-check correct"></i></td>
	                    	@else
	                    	<td class="td_table"></td>
	                    	@endif
	                    	@endfor
	                    </tr>
	                    <tr>
	                    	<td class="td_table_no">5</td>
	                    	<td style="border-left: 1px solid #e6e6e6;background-color: #d9d9d9;">Questions are free from racial/ethnic, religious, sexual and political bias and other sensitive issues.</td>
	                    	@for($i = 1;$i<=5;$i++)
	                    	<?php
	                    		$degree_result = explode('_',$degree[4]);
	                    	?>
	                    	@if($degree_result[1]==$i)
	                    	<td class="td_table" style="vertical-align: middle;"><i class="fa fa-check correct"></i></td>
	                    	@else
	                    	<td class="td_table"></td>
	                    	@endif
	                    	@endfor
	                    </tr>
	                    <tr>
	                    	<td class="td_table_no">6</td>
	                    	<td style="border-left: 1px solid #e6e6e6;background-color: #d9d9d9;">Optional questions (if any) are equivalent in terms of CLO and marks awarded (if applicable).</td>
	                    	@for($i = 1;$i<=5;$i++)
	                    	<?php
	                    		$degree_result = explode('_',$degree[5]);
	                    	?>
	                    	@if($degree_result[1]==$i)
	                    	<td class="td_table" style="vertical-align: middle;"><i class="fa fa-check correct"></i></td>
	                    	@else
	                    	<td class="td_table"></td>
	                    	@endif
	                    	@endfor
	                    </tr>
	                    <tr>
	                    	<td class="td_table_no">7</td>
	                    	<td style="border-left: 1px solid #e6e6e6;background-color: #d9d9d9;">Questions and the descriptions are simple and clear.</td>
	                    	@for($i = 1;$i<=5;$i++)
	                    	<?php
	                    		$degree_result = explode('_',$degree[6]);
	                    	?>
	                    	@if($degree_result[1]==$i)
	                    	<td class="td_table" style="vertical-align: middle;"><i class="fa fa-check correct"></i></td>
	                    	@else
	                    	<td class="td_table"></td>
	                    	@endif
	                    	@endfor
	                    </tr>
	                    <tr>
	                    	<td class="td_table_no">8</td>
	                    	<td style="border-left: 1px solid #e6e6e6;background-color: #d9d9d9;">Scientific / technical terminologies are relevant to the course.</td>
	                    	@for($i = 1;$i<=5;$i++)
	                    	<?php
	                    		$degree_result = explode('_',$degree[7]);
	                    	?>
	                    	@if($degree_result[1]==$i)
	                    	<td class="td_table" style="vertical-align: middle;"><i class="fa fa-check correct"></i></td>
	                    	@else
	                    	<td class="td_table"></td>
	                    	@endif
	                    	@endfor
	                    </tr>
	                    <tr>
	                    	<td class="td_table_no">9</td>
	                    	<td style="border-left: 1px solid #e6e6e6;background-color: #d9d9d9;">Labels and descriptions used for diagrams, tables and figures are clear and consistent.</td>
	                    	@for($i = 1;$i<=5;$i++)
	                    	<?php
	                    		$degree_result = explode('_',$degree[8]);
	                    	?>
	                    	@if($degree_result[1]==$i)
	                    	<td class="td_table" style="vertical-align: middle;"><i class="fa fa-check correct"></i></td>
	                    	@else
	                    	<td class="td_table"></td>
	                    	@endif
	                    	@endfor
	                    </tr>
	                    <tr>
	                    	<td colspan="7" style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;color:black;text-align: center;"><center><b>MARKING SCHEME</b></center></td>
	                    </tr>
	                    <tr>
	                    	<td class="td_table_no">1</td>
	                    	<td style="border-left: 1px solid #e6e6e6;background-color: #d9d9d9;">Marks(s) stated in the marking scheme are based on the examination paper set.</td>
	                    	@for($i = 1;$i<=5;$i++)
	                    	<?php
	                    		$degree_result = explode('_',$degree[9]);
	                    	?>
	                    	@if($degree_result[1]==$i)
	                    	<td class="td_table" style="vertical-align: middle;"><i class="fa fa-check correct"></i></td>
	                    	@else
	                    	<td class="td_table"></td>
	                    	@endif
	                    	@endfor
	                    </tr>
	                    <tr>
	                    	<td class="td_table_no">2</td>
	                    	<td style="border-left: 1px solid #e6e6e6;background-color: #d9d9d9;">Answer for each question is correct and appropriate to CLOs.</td>
	                    	@for($i = 1;$i<=5;$i++)
	                    	<?php
	                    		$degree_result = explode('_',$degree[10]);
	                    	?>
	                    	@if($degree_result[1]==$i)
	                    	<td class="td_table" style="vertical-align: middle;"><i class="fa fa-check correct"></i></td>
	                    	@else
	                    	<td class="td_table"></td>
	                    	@endif
	                    	@endfor
	                    </tr>
	                    <tr>
	                    	<td class="td_table_no">3</td>
	                    	<td style="border-left: 1px solid #e6e6e6;background-color: #d9d9d9;">Total marks for each question and/or section for the whole examination paper correctly calculated and stated.</td>
	                    	@for($i = 1;$i<=5;$i++)
	                    	<?php
	                    		$degree_result = explode('_',$degree[11]);
	                    	?>
	                    	@if($degree_result[1]==$i)
	                    	<td class="td_table" style="vertical-align: middle;"><i class="fa fa-check correct"></i></td>
	                    	@else
	                    	<td class="td_table"></td>
	                    	@endif
	                    	@endfor
	                    </tr>
                	</table>
                </div>
                <hr style="margin: 6px 5px 5px 5px;background-color:#d9d9d9;">
                <div class="row">
                  <h5 style="position: relative;top:3px;left: 10px;" class="tp_title col-10" id="3">Suggestion for improvement (<i class="fa fa-plus" aria-hidden="true" id="icon_3" style="color: #0d2f81;position: relative;top: 2px;"></i>)</h5>
                </div>
                <div style="overflow-x: auto;padding:3px 10px 5px 10px;display: none;" id="plan_detail_3">
                <?php
                $num = 1;
                ?>
                @foreach($all_ass_final as $row)
                	<?php
                      $suggest_list = array("");
                      $percentage = array("","","");
                      $full_suggest = explode('///NextAss///',$suggest);
                      for($n = 0;$n<=(count($full_suggest)-1);$n++){
                        $getFxId = explode('<???>',$full_suggest[$n]);
                        if($getFxId[0]==$row->fx_id){
                          $suggest_list = explode('%-PER-%',$getFxId[1]);
                          $percentage = explode(',',$suggest_list[1]);
                        }
                      }
                    ?>
                  @if($suggest_list[0]!="")
                	<table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);padding: 0px;" id="table" class="table">
                		<input type="hidden" name="fx_id_{{$num}}" value="{{$row->fx_id}}">
                		<tr style="background-color: #d9d9d9;">
                			<td colspan="2" class="td_table" style="text-align: left;">
                        @if($row->status=="Active")
                          <a href='{{$character}}/Reviewer/final_assessment/view/whole_paper/{{$row->fx_id}}' target='_blank' id="show_image_link" style="color:#0d2f81;">
                        @endif
                        {{$row->assessment_name}}
                          @if($row->status=="Remove")
                            (Removed)
                          @endif
                      </td>
                		</tr>
                		<tr>
                			<td colspan="2" class="td_table" style="text-align: left;">
                				<div id="suggest_{{$num}}" class="editor">
                					{!!$suggest_list[0]!!}
		                        </div>
		                        <textarea style="display:none" id="remark_{{$num}}" name="remark_{{$num}}"></textarea>
                			</td>
                		</tr>
                		<tr>
                			<td class="td_table" style="text-align: left;">Percentage of work involving remembering and understanding %</td>
                			<td class="td_table" style="text-align: left;"><input type="number" name="r_u_{{$num}}" id="r_u_{{$num}}" class="form-control" placeholder="***" value="{{$percentage[0]}}" readonly></td>
                		</tr>
                		<tr>
                			<td class="td_table" style="text-align: left;">Percentage of work involving application & analysis %</td>
                			<td class="td_table" style="text-align: left;"><input type="number" name="a_a_{{$num}}" id="a_a_{{$num}}" class="form-control" placeholder="***" value="{{$percentage[1]}}" readonly></td>
                		</tr>
                		<tr>
                			<td class="td_table" style="text-align: left;">Percentage of work involving evaluation and creation %</td>
                			<td class="td_table" style="text-align: left;"><input type="number" name="e_c_{{$num}}" id="e_c_{{$num}}" class="form-control" placeholder="***" value="{{$percentage[2]}}" readonly></td>
                		</tr>
                		<tr>
                			<td class="td_table" style="text-align: right;"><b>Total</b></td>
                			<td class="td_table" style="text-align: left;">100%</td>
                		</tr>
                	</table>
                	<?php
	                $num++;
	                ?>
                  @endif
                @endforeach
                <input type="hidden" id="count_assessemnt" name="count_assessemnt" value="{{count($all_ass_final)}}">
              	</div>
              	<hr style="margin:6px 5px 5px 5px;background-color:#d9d9d9;">
                <div class="row">
                    <h5 style="position: relative;top:3px;left: 10px;" class="tp_title col-10" id="4">Any Other Feedback (<i class="fa fa-plus" aria-hidden="true" id="icon_4" style="color: #0d2f81;position: relative;top: 2px;"></i>)</h5>
                </div>
                <div style="overflow-x: auto;padding:5px 0px 5px 0px;margin: -10px 0px 0px 0px;display: none;" id="plan_detail_4">
                    <div class="col-12" style="border: 0px solid black;margin-top:10px;margin-bottom: 0px;">
                        <div id="feedback" class="editor">
                        	{!!$feedback!!}
                        </div>
                        <textarea style="display:none" id="feedback" name="feedback"></textarea>
                    </div>
				        </div>
                @endif
                <hr style="margin:6px 5px 0px 5px;background-color:black;">
              </div>
            </div>
        </div>
    </div>
</div>
@endsection