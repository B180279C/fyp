<?php
$title = "Course";
$option1 = "id='selected-sidebar'";
?>
@extends('layouts.layout')

@section('content')
<script type="text/javascript">
    $(document).ready(function(){
        $("#tp").on("click",".week", function(){
            var id = $(this).attr("id");
            $('#plan_detail_'+id).slideToggle("slow", function(){
                // check paragraph once toggle effect is completed
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
    });
    function w3_open() {
      document.getElementById("action_sidebar").style.display = "block";
      document.getElementById("button_open").style.display = "none";
    }
    function w3_close() {
      document.getElementById("action_sidebar").style.display = "none";
      document.getElementById("button_open").style.display = "block";
    }

    function submitAction(){
        var course_id = $('#course_id').val();
        window.location = "{{$character}}/teachingPlan/Action/Submit/"+course_id; 
    }

    function submitActionSecond(){
        var course_id = $('#course_id').val();
        if(confirm('Please ensure your teaching plan is fixed all error and full complete already. Are you sure want to submit again to moderator.')) {
            window.location = "{{$character}}/teachingPlan/Action/Submit/"+course_id; 
        }
    }

    $(function () {
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        if($('.search').val()!=""){
          var value = $('.search').val();
          var course_id = $('#course_id').val();
          $.ajax({
              type:'POST',
              url:'{{$character}}/teachingPlan/searchPlan',
              data:{value:value,course_id:course_id},
              success:function(data){
                document.getElementById("tp").innerHTML = data;
              }
          });
        }
        $(".search").keyup(function(){
            var value = $('.search').val();
            var course_id = $('#course_id').val();
            $.ajax({
               type:'POST',
               url:'{{$character}}/teachingPlan/searchPlan',
               data:{value:value,course_id:course_id},
               success:function(data){
                    document.getElementById("tp").innerHTML = data;
               }
            });
        });
    });
</script>
<style type="text/css">
#topic_sub{
    width:95%;
    padding:0px 0px 20px 0px;
    border-bottom: 1px solid black;
}
.topic_remove{
    text-align: right;
    padding:10px 40px 0px 0px;
}
.more:hover{
    text-decoration:none;
}
@media only screen and (max-width: 600px) {
    #topic_sub{
        margin:0px;
        padding:0px 0px 20px 0px;
        width: 100%;
    }
    .topic_remove{
        text-align: right;
        padding:10px 0px 0px 0px;
    }
}
</style>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</p>
        <p class="pass_page">
            <a href="{{$character}}/home" class="first_page"> Home </a>/
            <a href="{{$character}}/course_list">Courses </a>/
            <a href="{{$character}}/course/action/{{$course[0]->course_id}}">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</a>/
            <span class="now_page">Teaching Plan</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 10px 10px;">
        <div class="col-md-12">
            <p class="page_title">Teaching Plan</p>
             <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                <div id="action_sidebar" class="w3-animate-right" style="display: none">
                    <div style="text-align: right;padding:10px;">
                        <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                  <ul class="sidebar-action-ul">
                    <a href="{{$character}}/teachingPlan/create/assessment/{{$course[0]->course_id}}"><li class="sidebar-action-li"><i class="fa fa-list-ol" style="padding: 0px 10px 0px 0px;" aria-hidden="true"></i>Manage Assessment Method</li></a>
                    <a href="{{$character}}/teachingPlan/create/CQI/{{$course[0]->course_id}}"><li class="sidebar-action-li"><i class="fa fa-plus-circle" style="padding: 0px 10px 0px 0px;" aria-hidden="true"></i>Manage CQI</li></a>
                    <a href="{{$character}}/teachingPlan/create/weekly/{{$course[0]->course_id}}"><li class="sidebar-action-li"><i class="fa fa-pencil" style="padding: 0px 10px 0px 0px;" aria-hidden="true"></i>Manage Weekly Plan</li></a>
                    <p class="title_method">Report</p>
                    <a href="{{$character}}/teachingPlan/report/{{$course[0]->course_id}}/"><li class="sidebar-action-li"><i class="fa fa-file-text-o" style="padding: 0px 10px 0px 0px;" aria-hidden="true"></i>Teaching Plan Report</li></a>
                  </ul>
            </div>
            <br>
            <br>
            @if(\Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <Strong>{{\Session::get('success')}}</Strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            <?php
            $action_count = count($action);
            $c = 1;
            $c_more = 1;
            $now = "Verified";
            foreach($action as $row_action){
                $iconM = '<i class="fa fa-times-circle" aria-hidden="true" style="color: grey;"></i>';
                $iconC = '<i class="fa fa-times-circle" aria-hidden="true" style="color: grey;"></i>';
                $iconW = '<i class="fa fa-times-circle" aria-hidden="true" style="color: grey;"></i>';
                if($action_count!=$c){
                    if($c_more==1){
                        echo "<a href='' style='display:block;border:0px solid black;margin-top:-20px;padding:0px 10px 10px 10px;' class='more' id='less'>Less...</a>";
                        $c_more++;
                    }
                    if($row_action->status=="Rejected"){
                        $iconM = '<i class="fa fa-times-circle" aria-hidden="true" style="color: red;"></i>';
                        $iconC = '<i class="fa fa-times-circle" aria-hidden="true" style="color: red;"></i>';
                        $iconW = '<i class="fa fa-times-circle" aria-hidden="true" style="color: red;"></i>';
                        $color = 'red';
                        if($row_action->verified_date==Null){
                            $person = "( ".$verified_person_name->position." : ".$verified_person_name->name." )";
                        }else{
                            $person = "( ".$approved_person_name->position." : ".$approved_person_name->name." )";
                        }
                        $status = '<span style="color:red;">Rejected</span> by '.$person;
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
                    }
                    echo '<div class="row action_list" style="margin:-10px 0px 10px 0px;padding:0px;display:none;">';
                    echo '<div class="col-12" style="padding: 0px 12px 5px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Status : '.$status.'</span></div>';
                    echo '<div class="col-12" style="padding: 0px 15px;"><span style="font-size: 15px;">'.$iconM.' Method of Assessment</span></div>';
                    echo '<div class="col-12" style="padding: 0px 15px;"><span style="font-size: 15px;">'.$iconC.' Continual Quality Improvement (CQI)</span></div>';
                    echo '<div class="col-12" style="padding: 0px 15px;"><span style="font-size: 15px;">'.$iconW.' Weekly Plan</span></div>';
                    echo '<div class="col-12" style="padding: 3px 12px 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Verified of Teaching Plan : <b style="color:'.$color.'">'.$tp_count.'/3</b></span></div>';
                    echo '<div class="col-12" style="padding: 3px 12px 5px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Remark : </span>'.$remarks.'</div>';
                    echo '</div>';
                }

                if($action_count==$c){
                    if($row_action->status=="Waiting For Verified"){
                        $status = '<span style="color:#3C5AFF;">Waiting For ( '.$verified_person_name->position." : ".$verified_person_name->name.' ) to Verify</span>';
                        $tp_count = 0;
                        $remarks = "";
                        $color = 'red';
                    }else if($row_action->status=="Waiting For Approved"){
                        // $status = '<span style="color:green;">Waiting For ( '.$approved_person_name->position." : ".$approved_person_name->name.' ) to Approve</span>';
                        $status = '<span style="color:green;">Waiting For ( HOD ) to Approve</span>';
                        $iconM = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
                        $iconC = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
                        $iconW = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
                        $tp_count = 3;
                        $color = 'green';
                        $remarks = $row_action->remarks;
                        $person = '<div class="col-12" style="padding: 8px 12px 0px 12px;"><span style="font-size: 17px;">Verified By : <span style="font-weight:bold;">'.$verified_person_name->name." ( ".$verified_person_name->staff_id." )</span>".'</span></div>';
                    }else if($row_action->status=="Rejected"){
                        $color = 'red';
                        $iconM = '<i class="fa fa-times-circle" aria-hidden="true" style="color: red;"></i>';
                        $iconC = '<i class="fa fa-times-circle" aria-hidden="true" style="color: red;"></i>';
                        $iconW = '<i class="fa fa-times-circle" aria-hidden="true" style="color: red;"></i>';
                        if($row_action->verified_date==Null){
                            $person = "( ".$verified_person_name->position." : ".$verified_person_name->name." )";
                        }else{
                            $person = "( ".$approved_person_name->position." : ".$approved_person_name->name." )";
                            $now = "Approved";
                        }
                        $status = '<span style="color:red;">Rejected</span> by '.$person."&nbsp;&nbsp;&nbsp;<button class='btn btn-raised btn-primary' style='background-color: #3C5AFF;padding:1px 15px;' onclick='submitActionSecond()'>Submit Again to Moderator ( ".$verified_person_name->position." : ".$verified_person_name->name." )</button>";
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
                    }else{
                        $now = "Approved";
                        $status = '<span style="color:green;">Approved</span>';
                        $iconM = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
                        $iconC = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
                        $iconW = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
                        $tp_count = 3;
                        $remarks = $row_action->remarks;
                        $color = 'green';
                    }
                    if($action_count != 1){
                        // echo '<hr style="margin: -10px 5px 0px 5px;background-color:black;">';
                        echo "<a href='' style='border:0px solid black;margin-top:-20px;padding:0px 10px 10px 10px;display:block;' class='more' id='more'>More...</a>";
                    }
                    echo '<div class="row" style="border: 0px solid black;margin:-10px 0px 1px 0px;padding:0px;">';
                    echo '<div class="col-12" style="padding: 0px 12px 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Status : '.$status.'</span></div>';                
                    echo '<div class="col-12" style="padding: 0px 15px;"><span style="font-size: 15px;">'.$iconM.' Method of Assessment</span></div>';
                    echo '<div class="col-12" style="padding: 0px 15px;"><span style="font-size: 15px;">'.$iconC.' Continual Quality Improvement (CQI)</span></div>';
                    echo '<div class="col-12" style="padding: 0px 15px;"><span style="font-size: 15px;">'.$iconW.' Weekly Plan</span></div>';
                    echo '<div class="col-12" style="padding: 3px 12px 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> '.$now.' of Teaching Plan : <b style="color:'.$color.'">'.$tp_count.'/3</b></span></div>';
                    if($row_action->verified_date!=Null){
                        echo '<div class="col-12" style="padding: 0px 12px 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Verified By : <b> ( '.$verified_person_name->position." : ".$verified_person_name->name.' ) </b></span></div>';    
                    }
                    if($row_action->approved_date!=Null){
                        echo '<div class="col-12" style="padding: 0px 12px 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Approved By : <b> ( '.$approved_person_name->position." : ".$approved_person_name->name.' ) </b></span></div>';    
                    }
                    if($remarks!=""){
                        echo '<div class="col-12" style="padding: 3px 12px 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Remark : </span>'.$remarks.'</div>';
                    }
                    echo '</div>';
                    
                }
                $c++;
            }
            ?>
            @if($action_count==0)
            <?php
            $num=0;
            $iconM = '<i class="fa fa-times-circle" aria-hidden="true" style="color: grey;"></i>';
            $iconC = '<i class="fa fa-times-circle" aria-hidden="true" style="color: grey;"></i>';
            $iconW = '<i class="fa fa-times-circle" aria-hidden="true" style="color: grey;"></i>';
            $pending = '<span style="color: grey;">Pending</span>';
            if(count($TP_Ass)>0){
                $num++;
                $iconM = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
            }
            if(count($TP_CQI)>0){
                $num++;
                $iconC = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
            }
            if(count($TP)>0){
                $num++;
                $iconW = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
            }

            if($num==3){
                $completed = '<b style="color: green;">Complete</b>';
                $pending = $completed."&nbsp;&nbsp;&nbsp;<button class='btn btn-raised btn-primary' style='background-color: #3C5AFF;padding:1px 15px;' onclick='submitAction()'>Submit to Moderator ( ".$verified_person_name->position." : ".$verified_person_name->name." )</button>";
            }else{
                $completed = '<b style="color: red;">Not Complete</b>';
            }
            ?>
            <div class="row" style="border: 0px solid black;margin:-10px 0px 0px 0px;padding:0px;">
                <div class="col-12" style="padding: 0px 12px 8px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i>The Teaching Plan : <b>{{$num}}/3</b> ( {!!$completed!!} )</span></div>
                <div class="col-12" style="padding: 0px 15px;"><span style="font-size: 15px;">{!!$iconM!!} Method of Assessment</span></div>
                <div class="col-12" style="padding: 0px 15px;"><span style="font-size: 15px;">{!!$iconC!!} Continual Quality Improvement (CQI)</span></div>
                <div class="col-12" style="padding: 0px 15px;"><span style="font-size: 15px;">{!!$iconW!!} Weekly Plan</span></div>
                <div class="col-12" style="padding: 8px 12px 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i>Status : {!!$pending!!}</span></div>
            </div>
            @endif
            <hr style="margin: 6px 5px 5px 5px;background-color:black;">
            @if(count($TP_Ass)>0)
            <h5 style="position:relative;margin-top: 10px;left: 10px;">Methods of Assessment</h5>
            <div style="overflow-x: auto;padding:3px 10px 5px 10px;">
                <?php
                    $m = 0;
                    $n = 0;
                    $all_assessment = explode('///',$TP_Ass[0]->assessment);
                    $assessment = explode(',',$all_assessment[0]);
                    $assessment_num = explode(',',$all_assessment[1]);
                ?>
                <table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);" id="table" class="table table-hover">
                    <thead>
                    <tr style="background-color: #d9d9d9;">
                        <th rowspan="3" style="border-left:1px solid #e6e6e6;color:black;"><b>No</b></th>
                        <th rowspan="3" width="20%" scope="col" style="border-left:1px solid #e6e6e6;color:black;"><b>Course Outcomes (CO)</b></th>
                        <th rowspan="3" width="10%" scope="col" style="border-left:1px solid #e6e6e6;color:black;"><b>Programme Outcomes(PO)</b></th>
                        <th rowspan="3" width="10%" scope="col" style="border-left:1px solid #e6e6e6;color:black;"><b>Domain & Taxonomy Level (e.g A2/ C3)</b></th>
                        <th rowspan="3" width="10%" scope="col" style="border-left:1px solid #e6e6e6;color:black;"><b>Teaching Methods</b></th>
                        <th width="40%"colspan="{{(count($assessment)-1)}}" scope="col" style="text-align: center;border-left:1px solid #e6e6e6;color:black;"><b>Assessemnt Methods & Mark Breakdown</b></th>
                    </tr>
                    <?php
                        echo "<tr style='text-align: center;background-color: #d9d9d9;'>";
                            while((isset($assessment[$m]))&&($assessment[$m]!="")){
                                echo '<th style="color:black;"><b>'.$assessment[$m].'</b></th>';
                                $m++;
                            }
                        echo "</tr>";
                        echo "<tr style='text-align: center;background-color: #d9d9d9;'>";
                            while((isset($assessment_num[$n]))&&($assessment_num[$n]!="")){
                                echo '<th style="color:black;"><b>'.$assessment_num[$n].'%</b></th>';
                                $n++;
                            }
                        echo "</tr>";
                    ?>
                    </thead>
                    <?php
                    $num = 1;
                    ?>
                    @foreach($TP_Ass as $row_ass)
                        <tr>
                            <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;">{{$num}}</td>
                            <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;">{{$row_ass->CLO}}</td>
                            <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;">{{$row_ass->PO}}</td>
                            <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;">{{$row_ass->domain_level}}</td>
                            <td style="border-left:1px solid #d9d9d9;border-right:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;">{{rtrim($row_ass->method,',')}}</td>
                            <?php
                            $check = explode(',',$row_ass->markdown);
                            for($c = 0; $c<=($n-1);$c++){
                                if($check[$c]!=""){
                                    echo '<td style="text-align: center;border-bottom: 1px solid #d9d9d9;"><i class="fa fa-check correct" aria-hidden="true"></i></td>';
                                }else{
                                    echo '<td style="text-align: center;border-bottom: 1px solid #d9d9d9;"><i class="fa fa-times wrong" aria-hidden="true"></i></td>';
                                }
                            }
                            ?>
                        </tr>
                    <?php
                    $num++;
                    ?>
                    @endforeach
                </table>
            </div>
            <hr style="margin: 5px 5px;background-color:#d9d9d9;">
            @else
            <h5 style="position:relative;margin-top: 10px;left: 10px;">Methods of Assessment</h5>
            <div style="display: block;border:1px solid black;padding: 50px;margin: 0px 10px;">
                <center>Empty</center>
            </div>
            <br>
            <hr style="margin: 5px 5px;background-color:#d9d9d9;">
            @endif
            @if(count($TP_CQI)>0)
            <h5 style="position: relative;top: 5px;left: 10px;">Continual Quality Improvement (CQI)</h5>
            <div style="overflow-x: auto;padding:8px 10px 5px 10px;">
            <table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);" id="table" class="table table-hover">
                <thead>
                    <tr style="background-color: #d9d9d9;">
                        <th style="color: black;"><center><b>No</b></center></th>
                        <th style="border-left:1px solid #cccccc;text-align:center;color: black;"><b>Proposed Improvement Action(s)<br/>(From Previous trimester Course Report)</b></th>
                        <th style="border-left:1px solid #cccccc;text-align:center;color: black;"><b>Plan for this Trimester<br/>(action(s) must be shown in Part D, if applicable)<br/>(to be transferred to this trimester Course Report)</b></th>
                    </tr>
                    </thead>
                    <?php
                    $i = 1;
                    ?>
                @foreach($TP_CQI as $row)
                    <tr>
                        <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;">
                            {{$i}} 
                        </td>
                        <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;">
                            {{$row->action}}
                        </td>
                        <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;">
                            {{$row->plan}}
                        </td>
                    </tr>
                    <?php
                    $i++
                    ?>
                @endforeach
            </table>
            </div>
            <hr style="margin: 5px 5px;background-color:#d9d9d9;">
            @else
            <h5 style="position: relative;top: 5px;left: 10px;">Continual Quality Improvement (CQI)</h5>
            <div style="display: block;border:1px solid black;padding: 50px;margin: 15px 10px 0px 10px;">
                <center>Empty</center>
            </div>
            <br>
            <hr style="margin: 5px 5px;background-color:#d9d9d9;">
            @endif
            <h5 style="position: relative;top:5px;left: 10px;">Weekly Plan</h5>
            <br>
            <div class="details" style="padding: 0px 5px 5px 5px;">
                <div class="col-md-6 row" style="padding:0px 20px;position: relative;top: -35px;">
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
                <div class="row" id="tp" style="position: relative;margin-top: -35px;padding:0px;"> 
                    <div class="col-md-12" style="padding:0px;">
                    <?php
                    $i = 1;
                    ?>
                    @foreach($TP as $row)
                    <p class="col-12 align-self-center week" id="{{$i}}" style="padding:10px 10px;font-size: 20px;margin: 0px;">
                            <i class="fa fa-plus" id="icon_{{$i}}" aria-hidden="true" style="font-size: 20px;color: #0d2f81"></i>
                            Week {{$i}}
                    </p>
                    <div class="teachingPlan" style="border-bottom: 1px solid grey;padding:0px 20px;">
                        <div class="row plan" id="plan_detail_{{$i}}" style="padding: 0px 20px;display: none;">
                            <div class="col-md-9 row" id="topic_list_{{$i}}" style="padding: 0px; margin: 0px;display: inline-block;">
                                @foreach($topic as $row_topic)
                                    @if($row_topic->tp_id == $row->tp_id)
                                <div class="col-md-8 topic" style="display: inline-block;height: 50px;">
                                    <div class="row">
                                        <div class="col-1 align-self-center" style="padding: 10px 0px 0px 0px;">
                                            <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                                <i class="fa fa-tag" aria-hidden="true" style="font-size: 18px;"></i>
                                            </p>
                                        </div>
                                        <div class="col-11" style="padding-left: 20px;">
                                            <div class="form-group">
                                                <label class="label">Lecture Topic</label>
                                                @if($row_topic->lecture_topic!="")
                                                <?php
                                                    $lecture_topic = explode('///',$row_topic->lecture_topic);
                                                ?>
                                                <input type="text" class="form-control" placeholder="Topic" readonly value="{{$lecture_topic[1]}}" style="background-color: white;">
                                                @else
                                                <input type="text" class="form-control" placeholder="Topic" readonly value="{{$row_topic->lecture_topic}}" style="background-color: white;">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3" style="display: inline-block;height: 80px;">
                                    <div class="row">
                                        <div class="col-1 align-self-center" style="padding: 15px 0px 0px 0px;">
                                            <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                                <i class="fa fa-clock-o" aria-hidden="true" style="font-size: 20px;"></i>
                                            </p>
                                        </div>
                                        <div class="col-11" style="padding-left: 20px;">
                                            <div class="form-group">
                                                <label class="label">Hour</label>
                                                <input type="text" class="form-control" placeholder="Time" readonly value="{{$row_topic->lecture_hour}}" style="background-color: white;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12" id="topic_sub" style="display: inline-block;">
                                    <p class="text-center align-self-center" style="margin: 0px 18px 10px 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;display: inline-block;">
                                        <i class="fa fa-info" aria-hidden="true" style="font-size: 18px;"></i>
                                    </p>
                                    <label class="bmd-label-floating">Sub-Topic</label>
                                    <div>
                                        {!!$row_topic->sub_topic!!}
                                    </div>
                                </div>
                                <br>
                                <br>
                                @endif
                            @endforeach
                            </div> 
                            <input type="hidden" name="topic_count_{{$i}}" id="topic_count_{{$i}}" value="1">
                            <div class="col-md-3" style="padding:20px 0px 0px 0px;">
                              <div class="short-div">
                                    <p class="text-center align-self-center" style="margin: 0px 18px 10px 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;display: inline-block;">
                                        <i class="fa fa-file-text" aria-hidden="true" style="font-size: 18px;padding-left:1px;"></i>
                                    </p>
                                    <label class="bmd-label-floating">Tutorials</label>
                                    <div>
                                        {!!$row->tutorial!!}
                                    </div>
                              </div>
                              <hr>
                              <div class="short-div">
                                  <p class="text-center align-self-center" style="margin: 0px 18px 10px 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;display: inline-block;">
                                        <i class="fa fa-thumb-tack" aria-hidden="true" style="font-size: 18px;"></i>
                                    </p>
                                    <label class="bmd-label-floating">Assessment</label>
                                    <div>
                                        {!!$row->assessment!!}
                                    </div>
                              </div>
                              <hr>
                              <div class="short-div">
                                  <p class="text-center align-self-center" style="margin: 0px 18px 10px 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;display: inline-block;">
                                        <i class="fa fa-exclamation" aria-hidden="true" style="font-size: 18px;"></i>
                                    </p>
                                    <label class="bmd-label-floating">Remarks</label>
                                    <div>
                                        {!!$row->remarks!!}
                                    </div>
                              </div>
                              <hr>
                            </div>
                        </div>
                    </div>
                    <?php 
                    $i++;
                    ?>
                    @endforeach
                    <?php
                    if($i==1){
                    ?>
                    <div style="display: block;border:1px solid black;padding: 50px;margin: 0px 20px;">
                        <center>Empty</center>
                    </div>
                    <?php
                    }
                    ?>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection