<?php
$title = "Course";
$option1 = "id='selected-sidebar'";
?>
@extends('layouts.nav_dean')

@section('content')

<script type="text/javascript">
function w3_open() {
    document.getElementById("action_sidebar").style.display = "block";
    document.getElementById("button_open").style.display = "none";
}
function w3_close() {
    document.getElementById("action_sidebar").style.display = "none";
    document.getElementById("button_open").style.display = "block";
}
$(document).on('click', '#open_new_CQI', function(){
    $('#openCQIModal').modal('show');
});

$(document).on('click', '.edit_button', function(){
    var id = $(this).attr("id");
    var num = id.split("_");
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type:'POST',
        url:'/teachingPlan/CQI/Edit/',
        data:{value : num[2]},
        success:function(data){
          document.getElementById('CQI_id').value = num[2];
          document.getElementById('action').value = data.action; 
          document.getElementById('plan').value = data.plan; 
        } 
    });
    $('#CQIEditModel').modal('show');
});

$(document).on('click', '.remove_button', function(){
    var id = $(this).attr("id");
    var num = id.split("_");
    if(confirm('Are you sure you want to remove the it?')) {
        window.location = "/teachingPlan/CQIRemove/"+num[2];
    }
    return false;
});
$(document).on('click', '#add', function(){
    var count = $('#count').val();
    count++;
    $('#CQI').append('<div id="CQI_'+count+'"><p style="margin:0px;font-size: 20px;">CQI '+count+'.<button type="button" name="remove" id="'+count+'" class="btn btn-raised btn-danger btn_remove" style="float:right;"><i class="fa fa-times" aria-hidden="true"></i></button></p><br/><div class="row"><div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;"><p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;"><i class="fa fa-long-arrow-right" aria-hidden="true" style="font-size: 18px;"></i></p></div><div class="col-11" style="padding-left: 20px;"><div class="form-group"><label for="subject_type" class="label" style="font-size:12px;border:0px solid black;margin:0px!important;">Proposed Improvement Action(s)</label><textarea class="form-control" name="action_'+count+'" rows="1"></textarea></div></div></div><div class="row"><div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;"><p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;"><i class="fa fa-th-list" aria-hidden="true" style="font-size: 18px;"></i></p></div><div class="col-11" style="padding-left: 20px;"><div class="form-group"><label for="subject_type" class="label" style="font-size:12px;">Plan for this Trimester</label><textarea class="form-control" name="plan_'+count+'" rows="1"></textarea></div></div></div></div>');
    $('#count').val(count);
});
$(document).on('click', '.btn_remove', function(){  
    var button_id = $(this).attr("id");   
    $('#CQI_'+button_id+'').remove(); 
});
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
            <span class="now_page">Manage CQI</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 0px 10px;">
        <div class="col-md-12">
            <p class="page_title">Continual Quality Improvement ( CQI )</p>
            <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                <div id="action_sidebar" class="w3-animate-right" style="display: none">
                    <div style="text-align: right;padding:10px;">
                        <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                  <ul class="sidebar-action-ul">
                    <a id="open_new_CQI"><li class="sidebar-action-li"><i class="fa fa-fast-backward" style="padding: 0px 10px 0px 0px;" aria-hidden="true"></i>Create New CQI</li></a>
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

            @if(\Session::has('Failed'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <Strong>{{\Session::get('Failed')}}</Strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            <div class="details" style="padding: 0px 5px 5px 5px;">
                <table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);" id="table" class="table table-hover">
                    <thead>
                    <tr style="background-color: #d9d9d9;">
                        <th style="color: black;"><center><b>No</b></center></th>
                        <th style="border-left:1px solid #cccccc;text-align:center;color: black;"><b>Proposed Improvement Action(s)<br/>(From Previous trimester Course Report)</b></th>
                        <th style="border-left:1px solid #cccccc;text-align:center;color: black;"><b>Plan for this Trimester<br/>(action(s) must be shown in Part D, if applicable)<br/>(to be transferred to this trimester Course Report)</b></th>
                        <th style="border-left:1px solid #cccccc;text-align:center;color: black;"><b>Action</b></th>
                    </tr>
                    </thead>
                    <?php
                    $i = 1;
                    ?>
                    @foreach($CQI as $row)
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
                            <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;" width="10%">
                                <center><i class="fa fa-wrench edit_button" aria-hidden="true" id="edit_button_{{$row->CQI_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>
                                <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_{{$row->CQI_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i></center>
                            </td>
                        </tr>
                        <?php
                        $i++
                        ?>
                    @endforeach
                    
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="openCQIModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content content2">
      <div class="modal-header header2" style="box-shadow: 1px 1px 2px #aaaaaa;">
        <h5 class="modal-title title2" id="exampleModalLabel">Open New CQI</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="post" action="{{action('Dean\TeachingPlanController@storeTPCQI')}}">
        {{csrf_field()}}
        <input type="hidden" name="count" id="count" value="1">
        <input type="hidden" name="course_id" value="{{$course[0]->course_id}}">
        <div id="CQI" style="padding:5px 20px 0px 20px">
            <div id="CQI_1">
            <p style="margin:0px;font-size: 20px;">CQI 1.</p>
            <div class="row">
                <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                    <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                        <i class="fa fa-long-arrow-right" aria-hidden="true" style="font-size: 18px;"></i>
                    </p>
                </div>
                    <div class="col-11" style="padding-left: 20px;">
                    <div class="form-group">
                          <label for="subject_type" class="label">Proposed Improvement Action(s)</label>
                          <textarea class="form-control" name="action_1" rows="1"></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                    <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                        <i class="fa fa-th-list" aria-hidden="true" style="font-size: 18px;"></i>
                    </p>
                </div>
                    <div class="col-11" style="padding-left: 20px;">
                    <div class="form-group">
                          <label for="subject_type" class="label">Plan for this Trimester</label>
                          <textarea class="form-control" name="plan_1" rows="1"></textarea>
                    </div>
                </div>
            </div>
            </div>
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-raised btn-success" id="add"><i class="fa fa-plus" aria-hidden="true" style="font-size: 18px;position: relative;top: 3px;"></i></button>
        &nbsp;
        <button type="button" class="btn btn-raised btn-secondary" data-dismiss="modal">Close</button>
        &nbsp;
        <input type="submit" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;margin: 0px 13px 0px 0px!important;" value="Save Changes">
      </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="CQIEditModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content content2">
      <div class="modal-header header2" style="box-shadow: 1px 1px 2px #aaaaaa;">
        <h5 class="modal-title title2" id="exampleModalLabel">Open New CQI</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="post" action="{{action('Dean\TeachingPlanController@CQIUpdate')}}">
        {{csrf_field()}}
        <input type="hidden" name="CQI_id" id="CQI_id">
        <div id="CQI" style="padding:5px 20px 0px 20px">
            <div id="CQI_1">
            <p style="margin:0px;font-size: 20px;">&nbsp;</p>
            <div class="row">
                <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                    <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                        <i class="fa fa-long-arrow-right" aria-hidden="true" style="font-size: 18px;"></i>
                    </p>
                </div>
                    <div class="col-11" style="padding-left: 20px;">
                    <div class="form-group">
                          <label for="subject_type" class="label">Proposed Improvement Action(s)</label>
                          <textarea class="form-control" name="action" rows="1" id="action"></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                    <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                        <i class="fa fa-th-list" aria-hidden="true" style="font-size: 18px;"></i>
                    </p>
                </div>
                    <div class="col-11" style="padding-left: 20px;">
                    <div class="form-group">
                          <label for="subject_type" class="label">Plan for this Trimester</label>
                          <textarea class="form-control" name="plan" rows="1" id="plan"></textarea>
                    </div>
                </div>
            </div>
            </div>
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-raised btn-secondary" data-dismiss="modal">Close</button>
        &nbsp;
        <input type="submit" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;margin: 0px 13px 0px 0px!important;" value="Save Changes">
      </div>
      </form>
    </div>
  </div>
</div>
@endsection