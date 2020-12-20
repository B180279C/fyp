<?php
$title = "Home";
$option0 = "id='selected-sidebar'";
function getFullTime($s_hour,$e_hour){
    $s_hour = intval($s_hour);
    $e_hour = intval($e_hour);
    $hour = $e_hour - $s_hour;
    $zero = "0";
    if($s_hour>=1000){
        $zero = "";
    }
    $f_time = "";
    $current = "";
    for($time = 100;$time<=$hour;$time=$time+100){
        if($current==""){
            $current = intval($s_hour+100);
            if($current>=1000){
                $f_time .= $zero.$s_hour."-".($s_hour+100);
            }else{
                $f_time .= $zero.$s_hour."-0".($s_hour+100);
            }
        }else{
            $zero = "0";
            if($current>=1000){
                $zero = "";
            }
            $added_hour = intval($current+100);
            if($added_hour>=1000){
                $f_time .= ",".$zero.$current."-".$added_hour;
            }else{
                $f_time .= ",".$zero.$current."-0".$added_hour;
            }
            $current = $added_hour;
        }
    }
    return $f_time;
}
?>
@extends('layouts.layout')
   
@section('content')

<script type="text/javascript">
    $(document).ready(function(){
        $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('.qr_code').click(function(){
            var id = $(this).attr("id");
            var string = id.split("?");
            var date = string[1];
            var num = string[0].split("-");
            if(confirm('Are you sure you want to create the qr code at now? Important: QR code just active in 15 minutes only.')){
                $.ajax({
                    type:'POST',
                    url:'{{$character}}/Attendance/openQR_Code',
                    data:{tt_id:num[0],week:num[1],less_hour:num[2],date:date},
                    success:function(data){
                        var value = data.split('-');
                        window.open("http://127.0.0.1:8000{{$character}}/Attendance/QR_code/"+value[0]+"/"+value[1], "_blank", "toolbar=yes, scrollbars=yes, resizable=yes");
                    }
                });
            }
        });
    });
</script>
<style type="text/css">
.show_image_link:hover{
    text-decoration: none;
}
#table{
   overflow-y:scroll;
   height:500px;
   display:block;
}
@media only screen and (max-width: 800px) {
    .file_name_two{
        width: auto;
        padding: 0px;
        margin: 0px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    .img{
        border-bottom: 1px solid #d9d9d9;
        border-radius: 0px;
        margin-top:5px;
    }
    #timeline_div{
        border:0px solid black;
        padding:0px 20px;
        margin-bottom: 20px;
    }
}
@media only screen and (min-width: 800px) {
    .file_name_two{
        position: relative;
        top:0px;
        left:-20px;
        width: auto;
        margin: 0px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    .img{
        border-right: 1px solid #d9d9d9;
        border-bottom: 1px solid #d9d9d9;
        border-radius: 0px;
        margin-top:5px;
    }
    .timeline{
        position: relative;
        left: 0px;
        top: 10px;
    }
    .box{
        position: relative;
        left: -20px;
    }
    #timeline_div{
        border:0px solid black;
        padding: 0px 20px 0px 0px;
    }
}
</style>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">Home</p>
        <!-- <p class="pass_page">
            <a href="{{$character}}/home" class="first_page"> Home </a>/
            <span class="now_page">Profile</span>/
        </p> -->
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 0px 10px;">
        <div class="col-md-8">
            <div class="img">
                <p class="page_title" style="position: relative;left: 0px ;top: -5px;">Newest Semester of Courses</p>
                <hr style="margin-top: 0px;margin-bottom: 5px;">
                <div class="details" style="padding: 0px;border:0px solid black;">
                    <div class="row" id="course" style="padding: 0px;margin:0px;">
                      @foreach($course as $row)
                        <a href="{{$character}}/course/action/{{$row->course_id}}" class="col-md-12 align-self-center" id="course_list" style="padding: 0px;margin:0px;">
                          <div class="col-md-12 row" style="padding:10px 0px;color:#0d2f81;border:0px solid black;margin: 0px;">
                            <div class="col-1 align-self-center" style="padding: 0px margin:0px;">
                              <img src="{{url('image/subject.png')}}" width="25px" height="24px"/>
                            </div>
                            <div class="col-10">
                              <p class="file_name_two">{{$row->subject_code}}  &nbsp;<b>{{$row->subject_name}}</b></p>
                            </div>
                          </div>
                        </a>
                      @endforeach
                      @if(count($course)==0)
                      <div style="display: block;border:3px solid #99c2ff;padding: 50px;width: 100%;color: #99c2ff;font-size: 18px;">
                            <center><b>Empty</b></center>
                      </div>
                      @endif
                    </div>
                </div>
            </div>
            <div class="img">
                <p class="page_title" style="position: relative;left: 0px ;top: -5px;">Timetable</p>
                <hr style="margin-top: 0px;margin-bottom: 5px;">
                <?php
                for($w=1;$w<=7;$w++){
                    for($i=1;$i<=15;$i++){
                        ${$w.'hour'.$i} = "";
                        ${$w.'or'.$i} = "";
                        ${'time'.$i} = (600+($i*100));
                        ${'end'.$i} = (700+($i*100));
                        if(${'time'.$i}<1000){
                            ${'time'.$i} = "0".${'time'.$i};
                        }
                        if(${'end'.$i}<1000){
                            ${'end'.$i} = "0".${'end'.$i};
                        }
                    }
                }
                // echo $time5;
                foreach($timetable as $row){
                    $week = $row->week;
                    $forh = $row->F_or_H;
                    $class_hour = explode(',',$row->class_hour);
                    for($t=0;$t<=(count($class_hour)-1);$t++){
                        $hour = explode('-',$class_hour[$t]);
                        $s_hour = $hour[0];
                        $e_hour = $hour[1];
                        for($w=1;$w<=7;$w++){
                            if($week=="Monday"&&$w==1){
                                for($i=1;$i<=15;$i++){
                                    if($s_hour==${'time'.$i}){
                                        ${$w.'hour'.$i} = $s_hour."/".$forh."/".$row->subject_code.":".$row->subject_name;
                                    }
                                }
                            }
                            if($week=="Tuesday"&&$w==2){
                                for($i=1;$i<=15;$i++){
                                    if($s_hour==${'time'.$i}){
                                        ${$w.'hour'.$i} = $s_hour."/".$forh."/".$row->subject_code.":".$row->subject_name;
                                    }
                                }
                            }
                            if($week=="Wednesday"&&$w==3){
                                for($i=1;$i<=15;$i++){
                                    if($s_hour==${'time'.$i}){
                                        ${$w.'hour'.$i} = $s_hour."/".$forh."/".$row->subject_code.":".$row->subject_name;
                                    }
                                }
                            }
                            if($week=="Thursday"&&$w==4){
                                for($i=1;$i<=15;$i++){
                                    if($s_hour==${'time'.$i}){
                                        ${$w.'hour'.$i} = $s_hour."/".$forh."/".$row->subject_code.":".$row->subject_name;
                                    }
                                }
                            }
                            if($week=="Friday"&&$w==5){
                                for($i=1;$i<=15;$i++){
                                    if($s_hour==${'time'.$i}){
                                        ${$w.'hour'.$i} = $s_hour."/".$forh."/".$row->subject_code.":".$row->subject_name;
                                    }
                                }
                            }
                            if($week=="Saturday"&&$w==6){
                                for($i=1;$i<=15;$i++){
                                    if($s_hour==${'time'.$i}){
                                        ${$w.'hour'.$i} = $s_hour."/".$forh."/".$row->subject_code.":".$row->subject_name;
                                    }
                                }
                            }
                            if($week=="Sunday"&&$w==7){
                                for($i=1;$i<=15;$i++){
                                    if($s_hour==${'time'.$i}){
                                        ${$w.'hour'.$i} = $s_hour."/".$forh."/".$row->subject_code.":".$row->subject_name;
                                    }
                                }
                            }
                        }
                    }
                }
                ?>
                <table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);" id="table" class="table table-hover">
                    <thead>
                        <tr style="background-color: #d9d9d9;text-align: center;">
                            <th style="border-left:1px solid #e6e6e6;color:black;"><b>Time</b></th>
                            <th style="border-left:1px solid #e6e6e6;color:black;" width="13.5%"><b>Monday</b></th>
                            <th style="border-left:1px solid #e6e6e6;color:black;" width="13.5%"><b>Tuesday</b></th>
                            <th style="border-left:1px solid #e6e6e6;color:black;" width="13.5%"><b>Wednesday</b></th>
                            <th style="border-left:1px solid #e6e6e6;color:black;" width="13.5%"><b>Thursday</b></th>
                            <th style="border-left:1px solid #e6e6e6;color:black;" width="13.5%"><b>Friday</b></th>
                            <th style="border-left:1px solid #e6e6e6;color:black;" width="13.5%"><b>Saturday</b></th>
                            <th style="border-left:1px solid #e6e6e6;color:black;" width="13.5%"><b>Sunday</b></th>
                        </tr>
                    </thead>
                    @for($i=1;$i<=15;$i++)
                        <tr>
                            <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;font-size: 12px;vertical-align: middle;"><b>{{(${'time'.$i}).' / '.(${'end'.$i})}}</b></td>
                        @for($w=1;$w<=7;$w++)
                            @if(${$w.'hour'.$i}!="")
                            <?php
                                $or = explode('/',${$w.'hour'.$i});
                                $name = explode(':',$or[2]);
                            ?>
                            <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;font-size: 10px;">
                                <b>{{$name[0]}}
                                <br/>{{$name[1]}}</b>
                                @if($or[1]=="Half")
                                <br/>
                                <span style="color: red">Odd / Even Week</span>
                                @endif
                            </td> 
                            @else
                            <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;"></td>               
                            @endif      
                        @endfor
                        </tr>
                    @endfor
                </table>
            </div>
        </div>
        <div class="col-md-4" id='timeline_div'>
            <p class="page_title timeline">Timeline ( {{date('Y-m-d')}} )</p>
            <hr style="margin-top: 15px;margin-bottom: 5px;">
            <div id="box" class="details" style="border:0px solid black;padding: 0px;margin:0px;">
                <?php
                $array = array();
                $num_a = 0;
                foreach($attendance as $att_row){
                    $s_hour = explode('-',$att_row->hour);
                    $e_hour = explode('-',$att_row->hour);
                    $last_hour = getFullTime($s_hour[0],$e_hour[1]);
                    if($att_row->A_date==date('Y-m-d')){
                        $timetable_hour = $att_row->class_hour;
                        $explode_th = explode(',',$timetable_hour);
                        $sperate = explode(',',$last_hour);
                        $less_hour = $att_row->less_hour;
                        if(count($sperate)>count($explode_th)){
                            $less_hour = count($explode_th)-count($sperate);
                        }
                        if($less_hour>0){
                            $time = "( ".$s_hour[0]." - ".$e_hour[1]." ) Fill up (".$less_hour." Hour)";
                        }else if($less_hour<0){
                            $time = "( ".$s_hour[0]." - ".$e_hour[1]." ) Minus on (".$less_hour." Hour)";
                        }else{
                            $time = "( ".$s_hour[0]." - ".$e_hour[1]." )";
                        }
                        echo '<table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);" id="timeline_table" class="table table-hover">';
                        echo "<tr style='background-color: #d9d9d9;text-align: left;'>";
                        echo "<th colspan='2' style='border-left:1px solid #d9d9d9;color:black;'>".$row->subject_code."<br/>".$row->subject_name."<br/>".$time."</th>";
                        echo "</tr>";
                        echo "<tr>";
                        echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center><a class='qr_code' id='".$att_row->tt_id."-".$att_row->A_week."-".$less_hour."?".$att_row->A_date."'><center><img src=".url('image/qr_code.png')." width='25px' height='25px'/></center></a></center></td>";
                        echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center><a href='".$character."/Attendance/".$att_row->tt_id."-".$att_row->A_week."-".$less_hour."/student_list/".$att_row->A_date."' class='show_image_link' target='_blank'>List</a></center></td>";
                        echo "</tr>";
                        echo "</table>";
                        $num_a++;
                    }
                    array_push($array,$att_row->tt_id.'/'.$last_hour);
                }
                if($last_semester->semester =='A'){
                    $weeks = 7;
                    $startDate = $last_semester->startDate;
                }else{
                    $weeks = 14;
                    $startDate = $last_semester->startDate;
                }
                for($i=1;$i<=$weeks;$i++){
                    $count_hour = 0;
                    if($i==1){
                        foreach($timetable as $row){
                            $week = "Next ".$row->week;
                            $NewDate = date('Y-m-d', strtotime($startDate . $week));
                            $date = date('Y-m-d');
                            $hour = explode(',',$row->class_hour);
                            $s_hour = explode('-',$hour[0]);
                            $e_hour = explode('-',$hour[count($hour)-1]);
                            $check = false;
                            $less = "";
                            $less_hour = 0;
                            for($a=0;$a<=(count($array)-1);$a++){
                                $array_tt_id = explode('/',$array[$a]);
                                if($row->tt_id==$array_tt_id[0]){
                                    if($row->class_hour==$array_tt_id[1]){
                                        $check = true;
                                    }else{
                                        $array_hour_count = explode(',',$array_tt_id[1]);
                                        $full_hour = count($hour);
                                        $less_hour = ($full_hour-count($array_hour_count));
                                        $less = "Fill up ( ".$less_hour." Hour )";
                                        if($less_hour<=0){
                                            $check = true;
                                        }
                                    }
                                }
                            }
                            if($NewDate==$date){
                                if($check==false){
                                    $num_a++;
                                    if($row->F_or_H=="Full"){
                                        $count_hour = $count_hour + count($hour);
                                        echo '<table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);" id="timeline_table" class="table table-hover">';
                                        echo "<tr style='background-color: #d9d9d9;text-align: left;'>";
                                        echo "<th colspan='2' style='border-left:1px solid #d9d9d9;color:black;'>".$row->subject_code."<br/>".$row->subject_name."<br/>( ".$s_hour[0]." - ".$e_hour[1]." ) ".$less."</th>";
                                        echo "</tr>";
                                        echo "<tr>";
                                        echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center><a class='qr_code' id='".$row->tt_id."-".$i."-".$less_hour."?".$NewDate."'><center><img src=".url('image/qr_code.png')." width='25px' height='25px'/></center></a></center></td>";
                                        echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center><a href='".$character."/Attendance/".$row->tt_id."-".$i."-".$less_hour."/student_list/".$NewDate."' class='show_image_link' target='_blank'>List</a></center></td>";
                                        echo "</tr>";
                                        echo "</table>";
                                    }else{
                                        if ($i % 2) {
                                            $count_hour = $count_hour + count($hour);
                                            echo '<table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);" id="timeline_table" class="table table-hover">';
                                            echo "<tr style='background-color: #d9d9d9;text-align: left;'>";
                                            echo "<th colspan='2' style='border-left:1px solid #d9d9d9;color:black;'>".$row->subject_code."<br/>".$row->subject_name."<br/>( ".$s_hour[0]." - ".$e_hour[1]." ) ".$less."</th>";
                                            echo "</tr>";
                                            echo "<tr>";
                                            echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center><a class='qr_code' id='".$row->tt_id."-".$i."-".$less_hour."?".$NewDate."'><center><img src=".url('image/qr_code.png')." width='25px' height='25px'/></center></a></center></td>";
                                            echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center><a href='".$character."/Attendance/".$row->tt_id."-".$i."-".$less_hour."/student_list/".$NewDate."' class='show_image_link' target='_blank'>List</a></center></td>";
                                            echo "</tr>";
                                            echo "</table>";
                                        }
                                    }
                                }
                            }
                        }
                    }else{
                        $startDate = strtotime($last_semester->startDate);
                        $add_date = $startDate+(($i-1)*(86400*7));
                        $add_startDate = date('Y-m-d',$add_date);
                        foreach($timetable as $row){
                            $week = "Next ".$row->week;
                            $NewDate = date('Y-m-d', strtotime($add_startDate . $week));
                            $date = date('Y-m-d');
                            $hour = explode(',',$row->class_hour);
                            $s_hour = explode('-',$hour[0]);
                            $e_hour = explode('-',$hour[count($hour)-1]);
                            $check = false;
                            $less = "";
                            $less_hour = 0;
                            for($a=0;$a<=(count($array)-1);$a++){
                                $array_tt_id = explode('/',$array[$a]);
                                if($row->tt_id==$array_tt_id[0]){
                                    if($row->class_hour==$array_tt_id[1]){
                                        $check = true;
                                    }else{
                                        $array_hour_count = explode(',',$array_tt_id[1]);
                                        $full_hour = count($hour);
                                        $less_hour = ($full_hour-count($array_hour_count));
                                        $less = "Fill up ( ".$less_hour." Hour )";
                                        if($less_hour<=0){
                                            $check = true;
                                        }
                                    }
                                }
                            }
                            if($NewDate==$date){
                                if($check==false){
                                    $num_a++;
                                    if($row->F_or_H=="Full"){
                                        $count_hour = $count_hour + count($hour);
                                        echo '<table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);" id="timeline_table" class="table table-hover">';
                                        echo "<tr style='background-color: #d9d9d9;text-align: left;'>";
                                        echo "<th colspan='2' style='border-left:1px solid #d9d9d9;color:black;'>".$row->subject_code."<br/>".$row->subject_name."<br/>( ".$s_hour[0]." - ".$e_hour[1]." ) ".$less."</th>";
                                        echo "</tr>";
                                        echo "<tr>";
                                        echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center><a class='qr_code' id='".$row->tt_id."-".$i."-".$less_hour."?".$NewDate."'><center><img src=".url('image/qr_code.png')." width='25px' height='25px'/></center></a></center></td>";
                                        echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center><a href='".$character."/Attendance/".$row->tt_id."-".$i."-".$less_hour."/student_list/".$NewDate."' class='show_image_link' target='_blank'>List</a></center></td>";
                                        echo "</tr>";
                                        echo "</table>";
                                    }else{
                                        if ($i % 2) {
                                            $count_hour = $count_hour + count($hour);
                                            echo '<table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);" id="timeline_table" class="table table-hover">';
                                            echo "<tr style='background-color: #d9d9d9;text-align: left;'>";
                                            echo "<th colspan='2' style='border-left:1px solid #d9d9d9;color:black;'>".$row->subject_code."<br/>".$row->subject_name."<br/>( ".$s_hour[0]." - ".$e_hour[1]." ) ".$less."</th>";
                                            echo "</tr>";
                                            echo "<tr>";
                                            echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center><a class='qr_code' id='".$row->tt_id."-".$i."-".$less_hour."?".$NewDate."'><center><img src=".url('image/qr_code.png')." width='25px' height='25px'/></center></a></center></td>";
                                            echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;'><center><a href='".$character."/Attendance/".$row->tt_id."-".$i."-".$less_hour."/student_list/".$NewDate."' class='show_image_link' target='_blank'>List</a></center></td>";
                                            echo "</tr>";
                                            echo "</table>";
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                ?>
                @if($num_a==0)
                <div style="display: block;border:3px solid #99c2ff;padding: 50px;width: 100%;color: #99c2ff;font-size: 18px;">
                    <center><b>Empty</b></center>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
