<?php
$title = "Course";
$option1 = "id='selected-sidebar'";
?>
@extends('layouts.nav_dean')

@section('content')

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
            <span class="now_page">Manage Weekly Plan</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 0px 10px;">
        <div class="col-md-12">
            <p class="page_title">Weekly Plan</p>
            <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                <div id="action_sidebar" class="w3-animate-right" style="display: none">
                    <div style="text-align: right;padding:10px;">
                        <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                  <ul class="sidebar-action-ul">
                    <a id="checkAction"><li class="sidebar-action-li"><i class="fa fa-fast-backward" style="padding: 0px 10px 0px 0px;" aria-hidden="true"></i>Previous of Weekly Plan</li></a>
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
                <form method="post" action="{{action('Dean\TeachingPlanController@storeTP', $course[0]->course_id)}}" id="form">
                {{csrf_field()}}
                    <?php
                    $i = 1;
                    ?>
                    @if(count($TP)>0)
                    <input type="hidden" id="checkEmpty" value="1">
                    <input type="hidden" id="result" value="">
                    @foreach($TP as $row)
                        <p class="col-12 align-self-center week" id="{{$i}}" style="padding:10px 10px;font-size: 20px;margin: 0px;">
                            <i class="fa fa-plus" id="icon_{{$i}}" aria-hidden="true" style="font-size: 20px;color: #0d2f81"></i>
                            Week {{$i}}
                    </p>
                    <div class="teachingPlan" style="border-bottom: 1px solid grey;padding:0px 20px;">
                        <div class="row plan" id="plan_detail_{{$i}}" style="padding: 0px 20px;display: none;">
                            <div class="col-md-12">
                                <button type="button" name="add" id="add_{{$i}}" class="btn btn-raised btn-success add" style="float:right;margin:0px!important;">Add Lecture Topic</button>
                            </div>
                            <div class="col-md-9 row" id="topic_list_{{$i}}" style="padding: 0px; margin: 0px;display: inline-block;">
                                <?php
                                $m = 1;
                                ?>
                                @foreach($topic as $row_topic)
                                    @if($row_topic->tp_id == $row->tp_id)
                                <div id="list_row_{{$row_topic->topic_id}}">
                                    @if($m>1)
                                        <div class="col-md-12 topic_remove" style="margin-top: 5px;">
                                            <button type="button" id="db_remove_{{$row_topic->topic_id}}" name="remove" class="btn btn-raised btn-danger db_remove"><i class="fa fa-times" aria-hidden="true" style="color:white;"></i></button>
                                        </div>
                                    @endif
                                <div class="col-md-8 topic" style="display: inline-block;height: 50px;">
                                    <div class="row">
                                        <div class="col-1 align-self-center" style="padding: 15px 0px 0px 0px;">
                                            <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                                <i class="fa fa-tag" aria-hidden="true" style="font-size: 18px;"></i>
                                            </p>
                                        </div>
                                        <div class="col-11" style="padding-left: 20px;">
                                            <div class="form-group">
                                                <label class="label">Lecture Topic</label>
                                                <input type="hidden" id="lecture_topic_data_{{$i}}_{{$m}}" value="{{$row_topic->lecture_topic}}">
                                                <select class="selectpicker form-control" name="lecture_topic_{{$i}}_{{$m}}" title="Choose One" data-width="100%" id="lecture_topic_{{$i}}_{{$m}}">
                                                </select>
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
                                                <input type="text" name="hour_{{$i}}_{{$m}}" class="form-control" placeholder="Time" value="{{$row_topic->lecture_hour}}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12" id="topic_sub" style="display: inline-block;">
                                    <p class="text-center align-self-center" style="margin: 0px 18px 10px 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;display: inline-block;">
                                        <i class="fa fa-info" aria-hidden="true" style="font-size: 18px;"></i>
                                    </p>
                                    <label class="bmd-label-floating">Sub-Topic</label>
                                    <div id="editor_{{$i}}_{{$m}}" class="editor">
                                        <div>
                                            {!!$row_topic->sub_topic!!}
                                        </div>
                                    </div>
                                    <textarea style="display:none" id="sub_topic_{{$i}}_{{$m}}" name="sub_topic_{{$i}}_{{$m}}"></textarea>
                                </div>
                                <br>
                                <br>
                                <?php
                                $m++;
                                ?>
                                </div>
                                @endif
                                @endforeach
                            </div>
                            <input type="hidden" name="topic_count_{{$i}}" id="topic_count_{{$i}}" value="{{$m-1}}">
                            <div class="col-md-3" style="padding:20px 0px 0px 0px;">
                              <div class="short-div">
                                    <p class="text-center align-self-center" style="margin: 0px 18px 10px 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;display: inline-block;">
                                        <i class="fa fa-file-text" aria-hidden="true" style="font-size: 18px;padding-left:1px;"></i>
                                    </p>
                                    <label class="bmd-label-floating">Tutorials</label>
                                    <div id="editor_t_{{$i}}" class="editor_t">
                                        {!!$row->tutorial!!}
                                    </div>
                                    <textarea style="display:none" id="tutorials_{{$i}}" name="tutorials_{{$i}}"></textarea>
                              </div>
                              <br>
                              <div class="short-div">
                                  <p class="text-center align-self-center" style="margin: 0px 18px 10px 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;display: inline-block;">
                                        <i class="fa fa-thumb-tack" aria-hidden="true" style="font-size: 18px;"></i>
                                    </p>
                                    <label class="bmd-label-floating">Assessment</label>
                                    <div id="editor_a_{{$i}}" class="editor_a">
                                        {!!$row->assessment!!}
                                    </div>
                                    <textarea style="display:none" id="assessments_{{$i}}" name="assessments_{{$i}}"></textarea>
                              </div>
                              <br>
                              <div class="short-div">
                                  <p class="text-center align-self-center" style="margin: 0px 18px 10px 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;display: inline-block;">
                                        <i class="fa fa-exclamation" aria-hidden="true" style="font-size: 18px;"></i>
                                    </p>
                                    <label class="bmd-label-floating">Remarks</label>
                                    <div id="editor_r_{{$i}}" class="editor_r">
                                        {!!$row->remarks!!}
                                    </div>
                                    <textarea style="display:none" id="remarks_{{$i}}" name="remarks_{{$i}}"></textarea>
                              </div>
                              <br>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="count" id="count_{{$i}}" value='{{$i}}'>
                    <input type="hidden" name="course_id" id="course_id" value="{{$course[0]->course_id}}">
                    <?php 
                    $i++;
                    ?>
                    @endforeach
                    @else
                    <input type="hidden" id="checkEmpty" value="0">
                    <input type="hidden" id="result" value="">
                    <?php
                        if($course[0]->semester =='A'){
                            $weeks = 7;
                        }else{
                            $weeks = 14;
                        }
                    ?>
                            @for($i=1;$i<=$weeks;$i++)
                            <p class="col-12 align-self-center week" id="{{$i}}" style="padding:10px 10px;font-size: 20px;margin: 0px;">
                            <i class="fa fa-plus" id="icon_{{$i}}" aria-hidden="true" style="font-size: 20px;color: #0d2f81"></i>
                            Week {{$i}}
                    </p>
                    <div class="teachingPlan" style="border-bottom: 1px solid grey;padding:0px 20px;">
                        <div class="row plan" id="plan_detail_{{$i}}" style="padding: 0px 20px;display: none;">
                            <div class="col-md-12">
                                <button type="button" name="add" id="add_{{$i}}" class="btn btn-raised btn-success add" style="float:right;margin:0px!important;">Add Lecture Topic</button>
                            </div>
                            <div class="col-md-9 row" id="topic_list_{{$i}}" style="padding: 0px; margin: 0px;display: inline-block;">
                                <div class="col-md-8 topic" style="display: inline-block;height: 50px;">
                                    <div class="row">
                                        <div class="col-1 align-self-center" style="padding: 15px 0px 0px 0px;">
                                            <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                                <i class="fa fa-tag" aria-hidden="true" style="font-size: 18px;"></i>
                                            </p>
                                        </div>
                                        <div class="col-11" style="padding-left: 20px;">
                                            <div class="form-group">
                                                <label class="label">Lecture Topic</label>
                                                <select class="selectpicker form-control" name="lecture_topic_{{$i}}_1" data-width="100%" title="Choose One" id="lecture_topic_{{$i}}_1">   
                                                </select>
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
                                                <input type="text" name="hour_{{$i}}_1" class="form-control" placeholder="Time">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12" id="topic_sub" style="display: inline-block;">
                                    <p class="text-center align-self-center" style="margin: 0px 18px 10px 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;display: inline-block;">
                                        <i class="fa fa-info" aria-hidden="true" style="font-size: 18px;"></i>
                                    </p>
                                    <label class="bmd-label-floating">Sub-Topic</label>
                                    <div id="editor_{{$i}}_1" class="editor">
                                        -
                                    </div>
                                    <textarea style="display:none" id="sub_topic_{{$i}}_1" name="sub_topic_{{$i}}_1"></textarea>
                                </div>
                            </div>
                            <input type="hidden" name="topic_count_{{$i}}" id="topic_count_{{$i}}" value="1">
                            <div class="col-md-3" style="padding:20px 0px 0px 0px;">
                              <div class="short-div">
                                    <p class="text-center align-self-center" style="margin: 0px 18px 10px 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;display: inline-block;">
                                        <i class="fa fa-file-text" aria-hidden="true" style="font-size: 18px;padding-left:1px;"></i>
                                    </p>
                                    <label class="bmd-label-floating">Tutorials</label>
                                    <div id="editor_t_{{$i}}" class="editor_t">
                                        -
                                    </div>
                                    <textarea style="display:none" id="tutorials_{{$i}}" name="tutorials_{{$i}}"></textarea>
                              </div>
                              <br>
                              <div class="short-div">
                                  <p class="text-center align-self-center" style="margin: 0px 18px 10px 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;display: inline-block;">
                                        <i class="fa fa-thumb-tack" aria-hidden="true" style="font-size: 18px;"></i>
                                    </p>
                                    <label class="bmd-label-floating">Assessment</label>
                                    <div id="editor_a_{{$i}}" class="editor_a">
                                        -
                                    </div>
                                    <textarea style="display:none" id="assessments_{{$i}}" name="assessments_{{$i}}"></textarea>
                              </div>
                              <br>
                              <div class="short-div">
                                  <p class="text-center align-self-center" style="margin: 0px 18px 10px 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;display: inline-block;">
                                        <i class="fa fa-exclamation" aria-hidden="true" style="font-size: 18px;"></i>
                                    </p>
                                    <label class="bmd-label-floating">Remarks</label>
                                    <div id="editor_r_{{$i}}" class="editor_r">
                                        -
                                    </div>
                                    <textarea style="display:none" id="remarks_{{$i}}" name="remarks_{{$i}}"></textarea>
                              </div>
                              <br>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="count" id="count_{{$i}}" value='{{$i}}'>
                    <input type="hidden" name="course_id" id="course_id" value="{{$course[0]->course_id}}">
                            @endfor
                    @endif
                @if($course[0]->semester =='A')
                    <input type="hidden" name="week" id="week" value='7'>
                @else
                    <input type="hidden" name="week" id="week" value='14'>
                @endif
                <div class="form-group" style="text-align: right;margin: 0px!important;padding-top: 20px;padding-right: 20px;">
                    <input type="submit" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;margin: 0px!important;" value="Save Change">&nbsp;
                </div>
                </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function w3_open() {
      document.getElementById("action_sidebar").style.display = "block";
      document.getElementById("button_open").style.display = "none";
    }
    function w3_close() {
      document.getElementById("action_sidebar").style.display = "none";
      document.getElementById("button_open").style.display = "block";
    }
    $(document).ready(function(){
        var week = $('#week').val();
        if(week==7){
            var run = 7;
        }else{
            var run = 14;
        }    
        for(var num=1;num<=run;num++){
            var topic_count = $('#topic_count_'+num).val();
            for(var m=1;m<=topic_count;m++){
                var quill_editor = new Quill('#editor_'+num+'_'+m, {
                theme: 'snow'
                });
            }
            var quill_tutorials = new Quill('#editor_t_'+num, {
                theme: 'snow'
            });
            var quill_assessment = new Quill('#editor_a_'+num, {
                theme: 'snow'
            });
            var quill_remark = new Quill('#editor_r_'+num, {
                theme: 'snow'
            });
        }   
    });
    $(document).ready(function(){
        $('.add').click(function(){
            var id = $(this).attr("id");
            var week = id.split('add_');
            // alert(week[1]);
            var topic_count = $('#topic_count_'+week[1]).val();
            topic_count++;
            document.getElementById('topic_count_'+week[1]).value = topic_count;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });   
            var result = $('#result').val(); 
            $('#topic_list_'+week[1]).append('<div id="list_row_'+week[1]+'_'+topic_count+'"><div class="col-md-12 topic_remove" style="margin-top: 5px;"><button type="button" id="remove_'+week[1]+'_'+topic_count+'" name="remove" class="btn btn-raised btn-danger btn_remove"><i class="fa fa-times" aria-hidden="true" style="color:white;"></i></button></div><div class="col-md-8 topic" style="padding-top:10px;display: inline-block;height: 80px;"><div class="row"><div class="col-1 align-self-center" style="padding: 15px 0px 0px 0px;"><p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;"><i class="fa fa-tag" aria-hidden="true" style="font-size: 18px;"></i></p></div><div class="col-11" style="padding-left: 20px;"><div class="form-group"><label class="label" style="font-size:12px">Lecture Topic</label><select class="form-control selectpicker" title="Choose One" name="lecture_topic_'+week[1]+'_'+topic_count+'" data-width="100%" id="lecture_topic_'+week[1]+'_'+topic_count+'">'+result+'</select></div></div></div></div><div class="col-md-3" style="display: inline-block;height: 80px;"><div class="row"><div class="col-1 align-self-center" style="padding: 15px 0px 0px 0px;"><p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;"><i class="fa fa-clock-o" aria-hidden="true" style="font-size: 20px;"></i></p></div><div class="col-11" style="padding-left: 20px;"><div class="form-group"><label class="label" style="font-size:12px">Hour</label><input type="text" name="hour_'+week[1]+'_'+topic_count+'" class="form-control" placeholder="Time"></div></div></div></div><div class="col-12" id="topic_sub" style="display: inline-block;"><p class="text-center align-self-center" style="margin: 0px 18px 10px 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;display: inline-block;"><i class="fa fa-info" aria-hidden="true" style="font-size: 18px;"></i></p><label class="bmd-label-floating">Sub-Topic</label><div id="editor_'+week[1]+'_'+topic_count+'" class="editor">-</div><textarea style="display:none" id="sub_topic_'+week[1]+'_'+topic_count+'" name="sub_topic_'+week[1]+'_'+topic_count+'"></textarea></div></div>');
                $('.selectpicker').selectpicker();
                var quill = new Quill('#editor_'+week[1]+'_'+topic_count, {
                    theme: 'snow'
                });
        });
        $(document).on('click', '.btn_remove', function(){  
           var button = $(this).attr("id");
           var id = button.split("remove_");   
           $('#list_row_'+id[1]).remove();
        });
    });

    $(document).ready(function(){
        $('.week').click(function(){
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
        $(document).on('click', '#checkAction', function(){
          var course_id = $('#course_id').val();
          if(confirm('Are you sure want to use previous semester of assessment method? (Important : If the course is a long semester, you will get the last long semester of the assessment method. On the contrary, if it is a short semester, you will get the last short semester.')) {
            window.location = "/teachingPlan/create/previous/weekly/"+course_id;
          }
          return false;
        });
    });

    $("#form").on("submit",function(){
        var week = $('#week').val();
        for(var i=1;i<=week;i++){
            var topic_count = $('#topic_count_'+i).val();
            for(var m=1;m<=topic_count;m++){
                var quill_editor = new Quill('#editor_'+i+'_'+m);
                $("#sub_topic_"+i+"_"+m).val(quill_editor.root.innerHTML);
            }
            var quill_tutorials = new Quill('#editor_t_'+i);
            $("#tutorials_"+i).val(quill_tutorials.root.innerHTML);

            var quill_assessment = new Quill('#editor_a_'+i);
            $("#assessments_"+i).val(quill_assessment.root.innerHTML);

            var quill_remark = new Quill('#editor_r_'+i);
            $("#remarks_"+i).val(quill_remark.root.innerHTML);
        }
    });
    $(document).ready(function(){
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        var course_id = $('#course_id').val();
            $.ajax({
                type:'POST',
                url:'/teachingPlan/getSyllabusData',
                data:{course_id:course_id},
                success:function(response){
                    var checkEmpty = $('#checkEmpty').val();
                    var week = $('#week').val();
                    for(var i = 0;i<=(response.length-1);i++){
                        if(response[i][2]==null&&response[i][10]=="L"&&response[i][11]=="T"){
                            var count = i;
                        }   
                    }
                    var list = 0;
                    for(var num = count;num<=(response.length-1);num++){
                        if(response[num][2]=="Continuous Assessment"&&response[num][9]=="Percentage (%) "){
                            break;
                        }
                        if(response[num][2]!=null){
                            var list = list+1;
                        }
                    }
                    if(checkEmpty==0){
                        for(var run = 1;run<=week;run++){
                            var result = "";
                            for(var num = count;num<=(count+list+3);num++){
                                if(response[num][2]=="Continuous Assessment"&&response[num][9]=="Percentage (%) "){
                                    break;
                                }
                                if(response[num][2]!=null){
                                    var str = response[num][2];
                                    var first = str.split("\n");
                                    var sentence = first[0].split(". ");
                                    $('#lecture_topic_'+run+'_1').append('<option class="option" value="'+sentence[0]+'///'+sentence[1]+'">'+sentence[1]+'</option>');
                                    result = result+"<option class='option' value='"+sentence[0]+'///'+sentence[1]+"'>"+sentence[1]+"</option>";
                                    $('#lecture_topic_'+run+'_1').selectpicker('refresh');
                                }
                            }
                            $('#result').val(result);
                        }
                    }else{
                        for(var run = 1;run<=week;run++){
                            var topic_count = $('#topic_count_'+run).val();
                            for(var m=1;m<=topic_count;m++){
                                var result = "";
                                var data = $('#lecture_topic_data_'+run+'_'+m).val();
                                console.log(data);
                                for(var num = count;num<=(count+list+3);num++){
                                    if(response[num][2]!=null){
                                        var str = response[num][2];
                                        var first = str.split("\n");
                                        var sentence = first[0].split(". ");
                                        if(response[num][2]!="Continuous Assessment"){
                                            if(sentence[0]+'///'+sentence[1]==data){
                                                $('#lecture_topic_'+run+'_'+m).append('<option class="option" value="'+sentence[0]+'///'+sentence[1]+'" selected>'+sentence[1]+'</option>');
                                            }else{
                                                $('#lecture_topic_'+run+'_'+m).append('<option class="option" value="'+sentence[0]+'///'+sentence[1]+'">'+sentence[1]+'</option>');
                                            }
                                            result = result+"<option class='option' value='"+sentence[0]+'///'+sentence[1]+"'>"+sentence[1]+"</option>";
                                        }
                                        $('#lecture_topic_'+run+'_'+m).selectpicker('refresh');
                                    }
                                }
                                $('#result').val(result);
                            }
                        }
                    }
                }
            });
        $(document).on('click', '.db_remove', function(){  
           var button = $(this).attr("id");
           var id = button.split("db_remove_");
            if (confirm("Are you sure want to remove it ?")) {
                $.ajax({
                   type:'POST',
                   url:'/removeTopic',
                   data:{value:id[1]},
                   success:function(data){
                        $('#list_row_'+id[1]).remove();
                   }
                });
            } else {
                return false;
            }
        });
    });
</script>
@endsection