<?php
$title = "Course";
$option1 = "id='selected-sidebar'";
?>
@extends('layouts.nav_dean')

@section('content')
<script type="text/javascript">
function loadImage() {
  alert("Image is loaded");
}
</script>
<style type="text/css">
.short-div{
    height: 200px;
}
.editor{
    height: 100px;
}
.editor_t{
    height: 100px;
}
.editor_a{
    height: 100px;
}
.editor_r{
    height: 100px;
}
#topic_sub{
    width:95%;
    padding:0px 0px 20px 0px;
    border-bottom: 1px solid black;
}
.topic_remove{
    text-align: right;
    padding:0px 40px 0px 0px;
}
@media only screen and (max-width: 600px) {
    #topic_sub{
        margin:0px;
        padding:0px 0px 20px 0px;
        width: 100%;
    }
    .topic_remove{
        text-align: right;
        padding:0px;
    }
}
</style>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/course_list">Courses </a>/
            <a href="/course/action/{{$course[0]->course_id}}">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</a>/
            <a href="/teachingPlan/{{$course[0]->course_id}}">Teaching Plan</a>/
            <span class="now_page">Manage Assessment Method</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 0px 10px;">
        <div class="col-md-12">
            <p class="page_title">Methods of Assessment</p>
            <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                <div id="action_sidebar" class="w3-animate-right" style="display: none;width: 270px;">
                    <div style="text-align: right;padding:10px;">
                        <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                  <ul class="sidebar-action-ul">
                    <a id="checkAction"><li class="sidebar-action-li"><i class="fa fa-fast-backward" style="padding: 0px 10px 0px 0px;" aria-hidden="true"></i>Previous of Assessment Method</li></a>
                    <a href="/teachingPlan/create/new/assessment/{{$course[0]->course_id}}"><li class="sidebar-action-li"><i class="fa fa-list-ol" style="padding: 0px 10px 0px 0px;" aria-hidden="true"></i>Create New Assessment Method</li></a>
                  </ul>
                </div>
            <br>
            <br>
            <hr style="margin-top: -10px;margin-bottom: 0px;padding: 0px;">
            @if(\Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <Strong>{{\Session::get('success')}}</Strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            @if(\Session::has('Failed'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <Strong>{{\Session::get('Failed')}}</Strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            <div class="details" style="padding: 0px 5px 0px 5px;">
                <div class="row" style="padding:0px;"> 
                    <div class="col-md-12" style="padding:0px;">
                         <form method="post" action="{{action('Dean\TeachingPlanController@storeTPAss', $course[0]->course_id)}}" id="form">
                            {{csrf_field()}}
                            <input type="hidden" id="course_id" value="{{$course[0]->course_id}}">
                            <?php
                            $i = 1;
                            ?>
                            @if(count($TP_Ass)>0)
                                @foreach($TP_Ass as $row)
                                <input type="hidden" name="am_id_{{$i}}" value="{{$row->am_id}}">
                                <p class="col-12 align-self-center week" id="{{$i}}" style="padding:10px 10px;font-size: 20px;margin: 0px;">
                                    <i class="fa fa-plus" id="icon_{{$i}}" aria-hidden="true" style="font-size: 20px;color: #0d2f81"></i> CLO {{$i}}
                                </p>
                                <div class="teachingPlan" style="border-bottom: 1px solid grey;padding:0px 20px;">
                                    <div class="row plan" id="plan_detail_{{$i}}" style="padding: 0px 20px;display: none;"0>
                                        <div class="col-md-12 row" style="padding:0px; margin: 0px;display: inline-block;">
                                            <div class="col-md-12 row">
                                                <div class="col-1 align-self-center" style="padding: 15px 0px 0px 0px;">
                                                    <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                                        <i class="fa fa-tag" aria-hidden="true" style="font-size: 18px;"></i>
                                                    </p>
                                                </div>
                                                <div class="col-11" style="position:relative;">
                                                    <div class="form-group">
                                                        <label class="label" style="font-size:12px">Course Learning Outcomes ( CLO )</label>
                                                        <input type="text" name="CLO_{{$i}}" class="form-control" placeholder="" value="{{$row->CLO}}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 row">
                                                <div class="col-1 align-self-center" style="padding: 15px 0px 0px 0px;">
                                                    <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                                        <i class="fa fa-thumb-tack" aria-hidden="true" style="font-size: 18px;"></i>
                                                    </p>
                                                </div>
                                                <div class="col-11"><div class="form-group">
                                                    <label class="label" style="font-size:12px">Programme Outcomes ( PO )</label>
                                                    <input type="text" name="PO_{{$i}}" class="form-control" placeholder="" value="{{$row->PO}}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 row">
                                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 0px;">
                                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                                    <i class="fa fa-list" aria-hidden="true" style="font-size: 18px;"></i>
                                                </p>
                                            </div>
                                            <div class="col-5">
                                                <div class="form-group">
                                                    <label class="label" style="font-size:12px">Domain & Taxonomy Level</label>
                                                    <input type="text" name="domain_level_{{$i}}" class="form-control" placeholder="e.g. A2/C3" value="{{$row->domain_level}}">
                                                </div>
                                            </div>
                                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 0px;">
                                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                                    <i class="fa fa-code-fork" aria-hidden="true" style="font-size: 18px;"></i>
                                                </p>
                                            </div>
                                            <?php 
                                                strpos($row->method, 'Lecture');
                                            ?>
                                            <div class="col-5">
                                                <div class="form-group">
                                                    <label class="label" style="font-size:12px">Teaching Methods</label>
                                                    <select class="selectpicker form-control" multiple name="method_{{$i}}[]" data-width="100%" title="Mutiple Choose">
                                                        <option class="option" <?php if(strpos($row->method, 'Lecture')!== false){ echo "selected";}?>>Lecture</option>
                                                        <option class="option" <?php if(strpos($row->method, 'Tutorial')!== false){ echo "selected";}?>>Tutorial</option>
                                                        <option class="option" <?php if(strpos($row->method, 'Practical')!== false){ echo "selected";}?>>Practical</option>
                                                        <option class="option" <?php if(strpos($row->method, 'Others')!== false){ echo "selected";}?>>Others</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 row">
                                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 0px;">
                                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                                    <i class="fa fa-table" aria-hidden="true" style="font-size: 18px;"></i>
                                                </p>
                                            </div>
                                            <label class="label col-11" style="padding:20px 0px 0px 15px;">Assessment Methods & Mark Breakdown</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12 row">
                                        <div class="col-1 align-self-center" style="padding: 15px 0px 0px 0px;">   
                                    </div>
                                        <div class="col-11">
                                            <table border="1" width="100%" id="table_{{$i}}" style="text-align:center;">
                                                <?php
                                                    $assessment = explode('///',$row->assessment);
                                                    $list_ass = explode(',',$assessment[0]);
                                                    $mark = explode(',',$assessment[1]);
                                                    echo "<tr>";
                                                    for($k = 0;$k<(count($list_ass)-1);$k++){
                                                        echo "<td>".$list_ass[$k]."</td>";
                                                    }
                                                    echo "</tr>";
                                                    echo "<tr>";
                                                    for($k = 0;$k<(count($mark)-1);$k++){
                                                        echo "<td>".$mark[$k]."%</td>";
                                                    }
                                                    echo "</tr>";
                                                    echo "<tr>";
                                                    $check = explode(',',$row->markdown);
                                                    for($c = 0; $c<(count($check)-1);$c++){
                                                        if($check[$c]!=""){
                                                            echo "<td><input type='checkbox' name='assessment_".$i."_".$c."' value='yes' checked></td>";
                                                        }else{
                                                            echo "<td><input type='checkbox' name='assessment_".$i."_".$c."' value='yes'></td>";
                                                        }
                                                    }
                                                    echo "</tr>";
                                                ?>
                                            </table>
                                            <br>
                                        </div>
                                    </div>
                            </div>
                        </div>
                        <?php
                        $i++;
                        ?>
                        @endforeach
                        <input type="hidden" name="num" value="{{count($list_ass)-1}}">
                        <input type="hidden" name="assessment_name" value="{{$assessment[0]}}">
                        <input type="hidden" name="assessment_num" value="{{$assessment[1]}}">
                        <input type="hidden" name="count" value="{{($i-1)}}">
                        <div class="form-group" style="text-align: right;margin: 0px!important;padding-top: 20px;padding-right: 20px;">
                            <input type="submit" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;margin: 0px!important;" value="Save Change">&nbsp;
                        </div>
                        @endif  
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $(document).on('click', '.week', function(){  
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
    });

    $(document).on('click', '#checkAction', function(){
      var course_id = $('#course_id').val();
      if(confirm('Are you sure want to use previous semester of assessment method? (Important : If the course is a long semester, you will get the last long semester of the assessment method. On the contrary, if it is a short semester, you will get the last short semester.')) {
        window.location = "/teachingPlan/create/previous/assessment/"+course_id;
      }
      return false;
    });

    function w3_open() {
      document.getElementById("action_sidebar").style.display = "block";
      document.getElementById("button_open").style.display = "none";
    }
    function w3_close() {
      document.getElementById("action_sidebar").style.display = "none";
      document.getElementById("button_open").style.display = "block";
    }
</script>
@endsection