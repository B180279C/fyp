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
                            $button_verify = "Yes";
                        }else if($row_action->status=="Waiting For Approved"){
                            $status = '<span style="color:green;">Waiting For HOD to Approve</span>';
                            $remarks = $row_action->remarks;
                        }else{
                            $status = $status = '<span style="color:red;">Rejected</span> by '.$row_action->for_who;
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
                            if($CLO_List[$i]==$row_tp->am_id){
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
                  <tbody>
                    <tr></tr>
                  </tbody>
                </table>
                </div>
                <hr style="margin: 5px 5px;background-color:black;">
              </div>
            </div>
        </div>
    </div>
</div>
@endsection