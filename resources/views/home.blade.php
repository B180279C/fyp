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
@extends('layouts.nav_student')
   
@section('content')
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
        left:-50px;
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
        <div class="col-md-12">
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
                              <p class="file_name_two">{{$row->subject_code}}  &nbsp;{{$row->subject_name}} ({{$row->name}})</p>
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
                                        if(${$w.'hour'.$i}==""){
                                            ${$w.'hour'.$i} .= $s_hour."/".$forh."/".$row->subject_code.":".$row->subject_name;
                                        }else{
                                            ${$w.'hour'.$i} .= "<br/>".$s_hour."/".$forh."/".$row->subject_code.":".$row->subject_name;
                                        }
                                    }
                                }
                            }
                            if($week=="Tuesday"&&$w==2){
                                for($i=1;$i<=15;$i++){
                                    if($s_hour==${'time'.$i}){
                                        if(${$w.'hour'.$i}==""){
                                            ${$w.'hour'.$i} .= $s_hour."/".$forh."/".$row->subject_code.":".$row->subject_name;
                                        }else{
                                            ${$w.'hour'.$i} .= "<br/>".$s_hour."/".$forh."/".$row->subject_code.":".$row->subject_name;
                                        }
                                    }
                                }
                            }
                            if($week=="Wednesday"&&$w==3){
                                for($i=1;$i<=15;$i++){
                                    if($s_hour==${'time'.$i}){
                                        if(${$w.'hour'.$i}==""){
                                            ${$w.'hour'.$i} .= $s_hour."/".$forh."/".$row->subject_code.":".$row->subject_name;
                                        }else{
                                            ${$w.'hour'.$i} .= "<br/>".$s_hour."/".$forh."/".$row->subject_code.":".$row->subject_name;
                                        }
                                    }
                                }
                            }
                            if($week=="Thursday"&&$w==4){
                                for($i=1;$i<=15;$i++){
                                    if($s_hour==${'time'.$i}){
                                        if(${$w.'hour'.$i}==""){
                                            ${$w.'hour'.$i} .= $s_hour."/".$forh."/".$row->subject_code.":".$row->subject_name;
                                        }else{
                                            ${$w.'hour'.$i} .= "<br/>".$s_hour."/".$forh."/".$row->subject_code.":".$row->subject_name;
                                        }
                                    }
                                }
                            }
                            if($week=="Friday"&&$w==5){
                                for($i=1;$i<=15;$i++){
                                    if($s_hour==${'time'.$i}){
                                        if(${$w.'hour'.$i}==""){
                                            ${$w.'hour'.$i} .= $s_hour."/".$forh."/".$row->subject_code.":".$row->subject_name;
                                        }else{
                                            ${$w.'hour'.$i} .= "<br/>".$s_hour."/".$forh."/".$row->subject_code.":".$row->subject_name;
                                        }
                                    }
                                }
                            }
                            if($week=="Saturday"&&$w==6){
                                for($i=1;$i<=15;$i++){
                                    if($s_hour==${'time'.$i}){
                                        if(${$w.'hour'.$i}==""){
                                            ${$w.'hour'.$i} .= $s_hour."/".$forh."/".$row->subject_code.":".$row->subject_name;
                                        }else{
                                            ${$w.'hour'.$i} .= "<br/>".$s_hour."/".$forh."/".$row->subject_code.":".$row->subject_name;
                                        }
                                    }
                                }
                            }
                            if($week=="Sunday"&&$w==7){
                                for($i=1;$i<=15;$i++){
                                    if($s_hour==${'time'.$i}){
                                        if(${$w.'hour'.$i}==""){
                                            ${$w.'hour'.$i} .= $s_hour."/".$forh."/".$row->subject_code.":".$row->subject_name;
                                        }else{
                                            ${$w.'hour'.$i} .= "<br/>".$s_hour."/".$forh."/".$row->subject_code.":".$row->subject_name;
                                        }
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
                        <?php
                        for($w=1;$w<=7;$w++){
                            if(${$w.'hour'.$i}!=""){
                                $br = explode('<br/>',${$w.'hour'.$i});
                                if(isset($br[1])){
                                    echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;font-size: 10px;vertical-align: middle;background-color:#ffe6e6;'>";
                                    for($b=0;$b<=(count($br)-1);$b++){
                                        $or = explode('/',$br[$b]);
                                        $name = explode(':',$or[2]);
                                        if($b>0){
                                            echo '<hr/>';
                                        }
                                        echo '<b>'.$name[0];
                                        echo '<br/>'.$name[1].'</b>';
                                        if($or[1]=="Half"){
                                            echo '<br/>';
                                            echo '<span style="color: red">Odd / Even Week</span>';
                                        }
                                    }
                                }else{
                                    echo "<td style='border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;font-size: 10px;vertical-align: middle;'>";
                                    $or = explode('/',${$w.'hour'.$i});
                                    $name = explode(':',$or[2]);
                                    echo '<b>'.$name[0];
                                    echo '<br/>'.$name[1].'</b>';
                                    if($or[1]=="Half"){
                                        echo '<br/>';
                                        echo '<span style="color: red">Odd / Even Week</span>';
                                    }
                                }
                                echo '</td>'; 
                            }else{
                                echo '<td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;vertical-align: middle;"></td>';
                            }    
                        }
                        ?>
                        </tr>
                    @endfor
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
