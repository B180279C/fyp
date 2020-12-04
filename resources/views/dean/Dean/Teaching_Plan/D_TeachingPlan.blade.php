<?php
$title = "Dean";
$option4 = "id='selected-sidebar'";
?>
@extends('layouts.nav_dean')

@section('content')
<script type="text/javascript">
    $(document).ready(function(){
        $(document).on("click",".tp_title", function(){
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
        var quill_editor = new Quill('#remarks', {
            theme: 'snow'
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

function TP_Report(course_id){
    window.location = "/teachingPlan/report/"+course_id;
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
    if(Action=="Approve"){
        if(checkedValue=="1_2_3_"){
            document.getElementById("myForm").submit();
        }else{
            alert("If you select the Approve button, that need to active all the verified checkbox.");
        }
    }else{
        if(checkedValue!="1_2_3_"){
            document.getElementById("myForm").submit();
        }else{
            alert("If you select the Reject button, that need to inactive one or more Verified checkbox.");
        }
    }
    
}
function w3_open() {
    document.getElementById("action_sidebar").style.display = "block";
    document.getElementById("button_open").style.display = "none";
}
function w3_close() {
    document.getElementById("action_sidebar").style.display = "none";
    document.getElementById("button_open").style.display = "block";
}
</script>
<style type="text/css">
.editor{
    height: 100px;
    display: block;
}
.more:hover{
    text-decoration:none;
}
#topic_sub{
    width:95%;
    padding:0px 0px 20px 0px;
    border-bottom: 1px solid black;
}
.topic_remove{
    text-align: right;
    padding:10px 40px 0px 0px;
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
            <a href="/home" class="first_page"> Home </a>/
            <a href="/Moderator">Moderator </a>/
            <a href="/Moderator/course/{{$course[0]->course_id}}">{{$course[0]->semester_name}} : {{$course[0]->short_form_name}} / {{$course[0]->subject_code}} {{$course[0]->subject_name}} ( {{$course[0]->name}} )</a>/
            <span class="now_page">Teaching Plan</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 0px 10px;">
        <div class="col-md-12">
            <p class="page_title">Teaching Plan</p>
            <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                <div id="action_sidebar" class="w3-animate-right" style="display: none">
                    <div style="text-align: right;padding:10px;">
                        <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                  <ul class="sidebar-action-ul">
                    <p class="title_method">Report</p>
                    <a href="/teachingPlan/report/{{$course[0]->course_id}}/"><li class="sidebar-action-li"><i class="fa fa-file-text-o" style="padding: 0px 10px 0px 0px;" aria-hidden="true"></i>Teaching Plan Report</li></a>
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
            $button_verify = "No";
            $now = "Verified";
            $checkbox_M = '<input type="checkbox" checked class="group_verify" value="1"><b style="color: green"> Verified</b>';
            $checkbox_C = '<input type="checkbox" checked class="group_verify" value="2"><b style="color: green">  Verified</b>';
            $checkbox_W = '<input type="checkbox" checked class="group_verify" value="3"><b style="color: green">  Verified</b>';
            foreach ($action as $row_action){
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
                        if($row_action->verified_date==Null){
                            $person = " By ( ".$approved_by[0]->position." : ".$approved_by[0]->name." )";
                        }else{
                            $person = "";
                        }
                        $status = '<span style="color:red;">Rejected</span>'.$person;
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
                        $status = '<span style="color:#3C5AFF;">Waiting For Verified</span>';
                        $tp_count = 0;
                        $remarks = "";
                        $color = 'black';
                    }else if($row_action->status=="Waiting For Approved"){
                        $checkbox_M = '<b style="color: green"> Verified</b>';
                        $checkbox_C = '<b style="color: green"> Verified</b>';
                        $checkbox_W = '<b style="color: green"> Verified</b>';
                        $status = '<span style="color:green;">Waiting For ( HOD ) to Approve</span>';
                        $iconM = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
                        $iconC = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
                        $iconW = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
                        $tp_count = 3;
                        $remarks = $row_action->remarks;
                        $color = 'green';
                        // $button_verify = "Yes";
                    }else if($row_action->status=="Rejected"){
                        $iconM = '<i class="fa fa-times-circle" aria-hidden="true" style="color: red;"></i>';
                        $iconC = '<i class="fa fa-times-circle" aria-hidden="true" style="color: red;"></i>';
                        $iconW = '<i class="fa fa-times-circle" aria-hidden="true" style="color: red;"></i>';
                        $checkbox_M = '<b style="color: red"> Rejected</b>';
                        $checkbox_C = '<b style="color: red"> Rejected</b>';
                        $checkbox_W = '<b style="color: red"> Rejected</b>';
                        if($row_action->verified_date==Null){
                            $person = " By ( ".$approved_by[0]->position." : ".$approved_by[0]->name." )";
                        }else{
                            $person = " By ( ".$verified_person_name->position." : ".$verified_person_name->name." )";
                        }

                        $status = '<span style="color:red;">Rejected</span>'.$person;
                        $remarks_count = explode('///',$row_action->remarks);
                        $remarks = $remarks_count[1];
                        $now = "Approved";
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
                        $now = "Approved";
                        $status = '<span style="color:green;">Approved</span>';
                        $iconM = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
                        $iconC = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
                        $iconW = '<i class="fa fa-check-circle" aria-hidden="true" style="color: green;"></i>';
                        $tp_count = 3;
                        $remarks = $row_action->remarks;
                        $color = 'green';
                        $checkbox_M = '<b style="color: green"> Approved</b>';
                        $checkbox_C = '<b style="color: green"> Approved</b>';
                        $checkbox_W = '<b style="color: green"> Approved</b>';
                    }
                    if($action_count != 1){
                        echo "<a href='' style='border:0px solid black;margin-top:-20px;padding:0px 10px 10px 10px;display:block;' class='more' id='more'>More...</a>";
                    }
                    echo '<div class="row" style="border: 0px solid black;margin:-10px 0px 1px 0px;padding:0px;">';
                    echo '<div class="col-12" style="padding: 0px 12px 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Status : '.$status.'</span></div>';                
                    echo '<div class="col-12" style="padding: 0px 15px;"><span style="font-size: 15px;">'.$iconM.' Method of Assessment</span></div>';
                    echo '<div class="col-12" style="padding: 0px 15px;"><span style="font-size: 15px;">'.$iconC.' Continual Quality Improvement (CQI)</span></div>';
                    echo '<div class="col-12" style="padding: 0px 15px;"><span style="font-size: 15px;">'.$iconW.' Weekly Plan</span></div>';
                    echo '<div class="col-12" style="padding: 3px 12px 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> '.$now.' of Teaching Plan : <b style="color:'.$color.'">'.$tp_count.'/3</b></span></div>';
                    if($row_action->approved_date==Null&&$row_action->verified_date!=Null){
                        echo '<div class="col-12" style="padding: 0px 12px 0px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Verified By : <b> ( '.$verified_person_name->position." : ".$verified_person_name->name.' ) </b></span></div>';    
                    }
                    if($remarks!=""){
                        echo '<div class="col-12" style="padding: 3px 12px 5px 12px;"><span style="font-size: 17px;"><i class="fa fa-circle" aria-hidden="true" style="font-size:5px;vertical-align:middle;"></i> Remark : </span>'.$remarks.'</div>';
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
            }else{
                $completed = '<b style="color: red;">Not Complete</b>';
            }
            ?>
            <div class="row" style="border: 0px solid black;margin:-10px 0px 0px 0px;padding:0px;">
                    <div class="col-12" style="padding: 0px 12px 8px 12px;"><span style="font-size: 17px;">The Teaching Plan : <b>{{$num}}/3</b></span></div>
                    <div class="col-12" style="padding: 0px 15px;"><span style="font-size: 15px;">{!!$iconM!!} Method of Assessment</span></div>
                    <div class="col-12" style="padding: 0px 15px;"><span style="font-size: 15px;">{!!$iconC!!} Continual Quality Improvement (CQI)</span></div>
                    <div class="col-12" style="padding: 0px 15px;"><span style="font-size: 15px;">{!!$iconW!!} Weekly Plan</span></div>
                    <div class="col-12" style="padding: 8px 12px 0px 12px;"><span style="font-size: 17px;">Status : {!!$pending!!}</span></div>
            </div>
            @endif
            <hr style="margin: 5px 5px;background-color:black;">
            @if(count($TP_Ass)>0)
            <div class="row">
                <h5 style="position: relative;top:4px;left: 10px;" class="tp_title col-10" id="1">
                    Methods of Assessment (<i class="fa fa-plus" aria-hidden="true" id="icon_1" style="color: #0d2f81;position: relative;top: 2px;"></i>)
                </h5>
                <div class="col-2" style="text-align: right;padding:3px 35px 0px 35px;font-size: 17px;">{!!$checkbox_M!!}</div>
            </div>
            <div style="overflow-x: auto;padding:0px 10px 5px 10px;display: none;" id="plan_detail_1">
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
            <div class="row">
                <h5 style="position: relative;top:4px;left: 10px;" class="tp_title col-10" id="1">
                    Methods of Assessment (<i class="fa fa-plus" aria-hidden="true" id="icon_1" style="color: #0d2f81;position: relative;top: 2px;"></i>)
                </h5>
                <div class="col-2" style="text-align: right;padding:3px 35px 0px 35px;font-size: 17px;">{!!$checkbox_M!!}</div>
            </div>
            <div style="border:1px solid black;padding: 50px;margin: 0px 10px;display: none;" id="plan_detail_1">
                <center>Empty</center>
            </div>
            <hr style="margin: 3px 5px;background-color:#d9d9d9;">
            @endif
            @if(count($TP_CQI)>0)
            <div class="row">
                <h5 style="position: relative;top:3px;left: 10px;" class="tp_title col-10" id="2">Continual Quality Improvement (CQI) (<i class="fa fa-plus" aria-hidden="true" id="icon_2" style="color: #0d2f81;position: relative;top: 2px;"></i>)</h5>
                <div class="col-2" style="text-align: right;padding:3px 35px 0px 35px;font-size: 17px;">{!!$checkbox_C!!}</div>
            </div>
            <div style="overflow-x: auto;padding:0px 10px 5px 10px;display: none;" id="plan_detail_2">
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
            <div class="row">
                <h5 style="position: relative;top:3px;left: 10px;" class="tp_title col-10" id="2">Continual Quality Improvement (CQI) (<i class="fa fa-plus" aria-hidden="true" id="icon_2" style="color: #0d2f81;position: relative;top: 2px;"></i>)</h5>
                <div class="col-2" style="text-align: right;padding:3px 35px 0px 35px;font-size: 17px;">{!!$checkbox_C!!}</div>
            </div>
            <div style="display: none;border:1px solid black;padding: 50px;margin: 0px 10px;" id="plan_detail_2">
                <center>Empty</center>
            </div>
            <hr style="margin: 3px 5px;background-color:#d9d9d9;">
            @endif
            @if(count($TP)>0)
            <div class="row">
                <h5 style="position: relative;top:3px;left: 10px;" class="tp_title col-10" id="3">Weekly Plan (<i class="fa fa-plus" aria-hidden="true" id="icon_3" style="color: #0d2f81;position: relative;top: 2px;"></i>)</h5>
                <div class="col-2" style="text-align: right;padding:3px 35px 0px 35px;font-size: 17px;">{!!$checkbox_W!!}</div>
            </div>
            <div style="overflow-x: auto;padding:0px 10px;display: none;" id="plan_detail_3">
                <table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);" id="table" class="table table-hover">
                    <thead>
                        <tr style="background-color: #d9d9d9;">
                            <th style="color: black;"><center><b>Week</b></center></th>
                            <th style="border-left:1px solid #cccccc;text-align:center;color: black;" width="40%"><center><b>Lecture Note<br/>(including sub-topics)</b></center></th>
                            <th style="border-left:1px solid #cccccc;text-align:center;color: black;"><center><b>Lecture (F2F) Hour</b></center></th>
                            <th style="border-left:1px solid #cccccc;text-align:center;color: black;" width="15%"><center><b>Tutorial / Practical</b></center></th>
                            <th style="border-left:1px solid #cccccc;text-align:center;color: black;" width="15%"><center><b>Assessment</b></center></th>
                            <th style="border-left:1px solid #cccccc;text-align:center;color: black;" width="15%"><center><b>Remarks (CQI Action / Activity)</b></center></th>
                        </tr>
                    </thead>
                    <?php
                    $array = array();
                    $num = 0;
                    ?>
                    @foreach($TP as $row)
                        <?php
                        $count = 0;
                        ?>
                        @foreach($topic as $row_topic)
                            @if($row_topic->tp_id == $row->tp_id)
                            <?php
                            $count++;
                            ?>
                            @endif
                        @endforeach
                        <?php
                        array_push($array, $count);
                        $num++;
                        ?>
                    @endforeach

                    <?php
                        $array_count = 0;
                    ?>
                    @foreach($TP as $row)
                        <?php
                        $i = 0;
                        ?>
                        @foreach($topic as $row_topic)
                            @if($row_topic->tp_id == $row->tp_id)
                            <?php
                            $i++;
                            $l_topic = "";
                            if($row_topic->lecture_topic!=""){
                                $lecture_topic = explode('///',$row_topic->lecture_topic);
                                $l_topic = $lecture_topic[1];
                            }
                            ?>
                                @if($i==1)
                                    <tr>
                                        <td rowspan="{{$array[$array_count]}}" style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;">{{$row->week}}</td>
                                        <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;"><b>Topic : {{$l_topic}}</b><br/>{!!$row_topic->sub_topic!!}</td>
                                        <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;">{{$row_topic->lecture_hour}}</td>
                                        <td rowspan="{{$array[$array_count]}}" style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;">{!!$row->tutorial!!}</td>
                                        <td rowspan="{{$array[$array_count]}}" style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;">{!!$row->assessment!!}</td>
                                        <td rowspan="{{$array[$array_count]}}" style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;">{!!$row->remarks!!}</td>
                                    </tr>
                                @else
                                    <tr>
                                        <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;"><b>Topic : {{$l_topic}}</b><br/>{!!$row_topic->sub_topic!!}</td>
                                        <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;">{{$row_topic->lecture_hour}}</td>
                                    </tr>
                                @endif
                            @endif
                        @endforeach
                        <?php
                            $array_count++;
                        ?>
                    @endforeach
                </table>
            </div>
            @else
            <div class="row">
                <h5 style="position: relative;top:3px;left: 10px;" class="tp_title col-10" id="3">Weekly Plan (<i class="fa fa-plus" aria-hidden="true" id="icon_3" style="color: #0d2f81;position: relative;top: 2px;"></i>)</h5>
                <div class="col-2" style="text-align: right;padding:3px 35px 0px 35px;font-size: 17px;">{!!$checkbox_W!!}</div>
            </div>
            <div style="display: none;border:1px solid black;padding: 50px;margin: 0px 10px;" id="plan_detail_3">
                <center>Empty</center>
            </div>
            @endif
            <hr style="margin: 5px 5px;background-color:black;">
            @if($button_verify=="Yes")
            <div class="row" style="height: auto;margin: 5px -10px 10px -10px;">
                <form id="myForm" method="post" action="{{action('Dean\Dean\D_TeachingPlanController@D_TP_VerifyAction')}}" style="width: 100%;margin: 0px;">
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
                    <input type="button" class="btn btn-raised btn-success" style="color: white;margin: 0px!important;" value="Approve" onclick="Submit_Action('Approve')">&nbsp;
                    <input type="button" class="btn btn-raised btn-danger" style="color: white;margin: 0px!important;" value="Reject" onclick="Submit_Action('Reject')">&nbsp;
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection