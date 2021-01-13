<?php
$title = "Reviewer";
$option4 = "id='selected-sidebar'";
?>
@extends('layouts.layout')

@section('content')
<style type="text/css">
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
  function w3_open() {
      document.getElementById("action_sidebar").style.display = "block";
      document.getElementById("button_open").style.display = "none";
  }
  function w3_close() {
      document.getElementById("action_sidebar").style.display = "none";
      document.getElementById("button_open").style.display = "block";
  }

  function ModerationForm(actionFA_id){
    window.location = "{{$character}}/Reviewer/FinalExamination/report/"+actionFA_id;
    return false;
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
                if(response[2].length>0){
                  $('.mark_color').css('color', 'green');
                  $('.status').html("<span style='color:green'>Complete</span>");
                }else{
                  $('.status').html("<span style='color:red'>Not Complete</span>");
                }
                status = true;
              }else{
                $('.mark_color').css('color', 'red');
                $('.status').html("<span style='color:red'>Not Complete</span>");
              }

              var row = table.insertRow(0);
              var cell = row.insertCell(0);
              var cell1 = row.insertCell(1);
              cell.style.backgroundColor = "#d9d9d9";
              cell1.style.backgroundColor = "#d9d9d9";
              cell1.style.textAlign  = "center";
              cell1.style.borderLeft  = "1px solid #e6e6e6";
              cell1.style.width  = "15%";
              cell1.id = "myThTd";
              document.getElementById("myThTd").colSpan = "2";
              cell.innerHTML  = "<b>"+name+" ( "+percentage+ "% )</b>";
              cell1.innerHTML = '<b>Action & Status</b>';


              var row_paper = table.insertRow(1);
              var cell = row_paper.insertCell(0);
              var cell1 = row_paper.insertCell(1);
              var cell2 = row_paper.insertCell(2);
              cell1.style.textAlign  = "center";
              cell2.style.textAlign  = "center";
              cell.style.borderLeft  = "1px solid #d9d9d9";
              cell.style.borderBottom  = "1px solid #d9d9d9";
              cell1.style.borderLeft  = "1px solid #d9d9d9";
              cell1.style.borderBottom  = "1px solid #d9d9d9";
              cell2.style.borderLeft  = "1px solid #d9d9d9";
              cell2.style.borderBottom  = "1px solid #d9d9d9";
              cell2.style.borderRight  = "1px solid #d9d9d9";
              cell.innerHTML  = "Assessment List";
              cell1.innerHTML = '<a href="{{$character}}/Reviewer/FinalExamination/list/'+percentage+'/'+course_id+'/" style="font-size:18px;width:100%;display:block;" class="question_link"><i class="fa fa-plus" aria-hidden="true" ></i></a>';
              if(status==true){
                cell2.innerHTML = '<i class="fa fa-check correct" aria-hidden="true"></i>';
              }else{
                cell2.innerHTML = '<i class="fa fa-times wrong" aria-hidden="true"></i>';
              }

              var row_paper = table.insertRow(2);
              var cell = row_paper.insertCell(0);
              var cell1 = row_paper.insertCell(1);
              var cell2 = row_paper.insertCell(2);
              cell1.style.textAlign  = "center";
              cell2.style.textAlign  = "center";
              cell.style.borderLeft  = "1px solid #d9d9d9";
              cell.style.borderBottom  = "1px solid #d9d9d9";
              cell1.style.borderLeft  = "1px solid #d9d9d9";
              cell1.style.borderBottom  = "1px solid #d9d9d9";
              cell2.style.borderLeft  = "1px solid #d9d9d9";
              cell2.style.borderBottom  = "1px solid #d9d9d9";
              cell2.style.borderRight  = "1px solid #d9d9d9";
              cell.innerHTML  = "Question Paper & Solution";
              cell1.innerHTML = '<a href="{{$character}}/Reviewer/FinalExamination/question/'+percentage+'/'+course_id+'/" style="font-size:18px;width:100%;display:block;" class="question_link"><i class="fa fa-plus" aria-hidden="true" ></i></a>';
              if(response[2].length>0){
                cell2.innerHTML = '<i class="fa fa-check correct" aria-hidden="true"></i>';
              }else{
                cell2.innerHTML = '<i class="fa fa-times wrong" aria-hidden="true"></i>';
              }
              

              var row_result = table.insertRow(3);
              var cell = row_result.insertCell(0);
              var cell1 = row_result.insertCell(1);
              var cell2 = row_result.insertCell(2);
              cell1.style.textAlign  = "center";
              cell2.style.textAlign  = "center";
              cell.style.borderLeft  = "1px solid #d9d9d9";
              cell.style.borderBottom  = "1px solid #d9d9d9";
              cell1.style.borderLeft  = "1px solid #d9d9d9";
              cell1.style.borderBottom  = "1px solid #d9d9d9";
              cell2.style.borderLeft  = "1px solid #d9d9d9";
              cell2.style.borderBottom  = "1px solid #d9d9d9";
              cell2.style.borderRight  = "1px solid #d9d9d9";
              cell.innerHTML  = "Student Result";
              cell1.innerHTML = '<a href="{{$character}}/Reviewer/FinalResult/'+course_id+'/" style="font-size:18px;width:100%;display:block;" class="question_link"><i class="fa fa-plus" aria-hidden="true" ></i></a>';
              // if(response[1].length>=9){
              //   cell2.innerHTML = '<i class="fa fa-check correct" aria-hidden="true"></i>';
              // }else{
              //   cell2.innerHTML = '<i class="fa fa-times wrong" aria-hidden="true"></i>';
              // }
              cell2.innerHTML = response[1].length;


              var moderation_done = $('#moderation_done').val();
              var actionFA_id = $('#actionFA_id').val();
              if(moderation_done=="Yes"){
                var row = table.insertRow(4);
                var cell = row.insertCell(0);
                var cell1 = row.insertCell(1);
                cell.style.borderLeft  = "1px solid #d9d9d9";
                cell1.style.textAlign  = "center";
                cell1.id = "myTd";
                document.getElementById("myTd").colSpan = "4";
                cell.innerHTML  = "<b>Final Assessment ( FA ) Moderation</b>";
                cell1.innerHTML  = "<button class='btn btn-raised btn-primary' style='background-color: #3C5AFF;padding:5px 10px;margin:0px;' onclick='ModerationForm("+actionFA_id+")'><b><i class='fa fa-download'></i> Final Examination Moderation Form</b></button>";
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
            <a href="{{$character}}/Reviewer">{{$cha}} </a>/
            <a href="{{$character}}/Reviewer/course/{{$course[0]->course_id}}">{{$course[0]->semester_name}} : {{$course[0]->short_form_name}} / {{$course[0]->subject_code}} {{$course[0]->subject_name}} ( {{$course[0]->name}} )</a>/
            <span class="now_page">Final Assessment</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 10px 10px;">
        <div class="col-md-12">
             <p class="page_title">Final Assessment</p>
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
                foreach($ass_final as $row){
                  $mark = $mark+$row->coursework;
                }
                ?>
                <input type="hidden" id="course_id" value="{{$course[0]->course_id}}">
                <?php
                $action_count = count($action);
                $c = 1;
                $c_more = 1;
                $moderation_done = "No";
                $actionFA_id = "";
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
                          $status = '<span style="color:red;">Rejected</span>'.$person;
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
                            $status = '<span style="color:#3C5AFF;">Waiting For ( '.$moderator_person_name->position.' : '.$moderator_person_name->name.' ) to Moderation</span>';
                            $remarks = "";
                            $actionFA_id = $row_action->actionFA_id;
                            echo '<input type="hidden" id="actionFA_id" value='.$actionFA_id.'>';
                            $self = "";
                        }else if($row_action->status=="Waiting For Verified"){
                            // $status = '<span style="color:green;">Waiting For ( '.$verified_person_name->position.' : '.$verified_person_name->name.' ) to verify</span>';
                            $status = '<span style="color:green;">Waiting For ( HOD ) to verify</span>';
                            $remarks = $row_action->remarks;
                            $moderation_done = "Yes";
                            $actionFA_id = $row_action->actionFA_id;
                            echo '<input type="hidden" id="actionFA_id" value='.$actionFA_id.'>';
                            $self = $row_action->self_declaration;
                        }else if($row_action->status=="Waiting For Rectification"){
                            $status = '<span style="color:green;">Waiting For Lecturer to Rectify</span>';
                            $remarks = $row_action->remarks;
                            $moderation_done = "Yes";
                            $actionFA_id = $row_action->actionFA_id;
                            echo '<input type="hidden" id="actionFA_id" value='.$actionFA_id.'>';
                            $self = "";
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
                        }else if($row_action->status=="Waiting For Approve"){
                            $self = $row_action->self_declaration;
                            $status = '<span style="color:green;">Waiting For ( Dean ) to Approve</span>';
                            $moderation_done = "Yes";
                            $remarks = $row_action->remarks;
                            $action_degree = $row_action->degree;
                            $feedback = $row_action->feedback;
                            $suggest = $row_action->suggest;
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
                  <div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> The Final Assessment ( FA ) : <b class="mark_color"> <span id="mark">{{$mark}}</span> / <span class="total"></span></b></span></div>
                  <div class="col-12" style="padding: 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Status : <span class="status"></span></span></div>
                </div>
                @endif
                <input type="hidden" id="moderation_done" value="{{$moderation_done}}">
                <input type="hidden" id="actionFA_id" value="{{$actionFA_id}}">
                <hr style="margin: 5px 5px 0px 5px;background-color:black;">
                <p style="padding: 5px 5px 5px 12px;margin: 0px;font-size: 18px;">Final Assessment List</p>
                <div style="overflow-x: auto;padding:3px 10px 5px 10px;">
                <table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);" id="table" class="table table-hover">
                </table>
                </div>
              </div>
            </div>
        </div>
    </div>
</div>
@endsection