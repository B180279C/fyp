<?php
$title = "Assessment";
$option6 = "id='selected-sidebar'";
?>
@extends('layouts.layout')

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

  $(document).ready(function(){
    oTable = $('#dtBasic').DataTable(
        {
            "bLengthChange" : false,
            "bInfo": false,
            pagingType: 'input',
            pageLength: 10,
            language: {
                oPaginate: {
                   sNext: '<i class="fa fa-forward"></i>',
                   sPrevious: '<i class="fa fa-backward"></i>',
                   sFirst: '<i class="fa fa-step-backward"></i>',
                   sLast: '<i class="fa fa-step-forward"></i>'
                }
            }
    });
    $('#input').keyup(function(){
        oTable.search($(this).val()).draw();
    });
    $(".clickable-row").click(function() {
      var id = $(this).attr("class").split(' ');
      if(id[1]=="Pending"||id[1]=="Waiting For Moderation"){
        alert('The Course of Coutinuous Assessment is still pending.')
      }else{
        window.location = $(this).data("href");
      }     
    });
    $('.group_checkbox').click(function(){
      if($(this).prop("checked") == true){
        $('.group_download').prop("checked", true);
      }
      else if($(this).prop("checked") == false){
        $('.group_download').prop("checked", false);
      }
    });
  });

  $(document).on('click', '#checkDownloadAction', function(){
      var checkedValue = ""; 
      var inputElements = document.getElementsByClassName('group_download');
      for(var i=0; inputElements[i]; i++){
        if(inputElements[i].checked){
          checkedValue += inputElements[i].value+"---";
        }
      }
      if(checkedValue!=""){
        var id = checkedValue;
        window.location = "{{$character}}/report/assessment/download/zipFiles/"+id+"/checked";
      }else{
          alert("Please select the document first.");
      }
  });
</script>
<style type="text/css">
.checkbox_group_style{
  border:0px solid black;
  padding: 1px 10px 0px 10px!important;
  margin: 0px!important;
}
.checkbox_style{
  border:0px solid black;
  padding: 0px 5px!important;
  margin: 0px!important;
  display: inline;
  width: 28px;
}
.group{
  margin-top:3px;
  padding-left: 15px;
  border:0px solid black;
  display: inline;
  padding: 0px!important;
  margin: 0px!important;
}
.question_link:hover{
    background-color: #d9d9d9;
    text-decoration: none;
    color: #0d2f81;
}
#show_image_link:hover{
    text-decoration: none;
}
.plus:hover{
    background-color: #f2f2f2;
}
@media only screen and (max-width: 600px) {
  #assessment_name{
    margin-left: 0px;
    padding-top: 0px;
  }
  #assessment_word{
    margin-left: 0px;
    padding-top: 0px;
  }
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
  #lecturer_name{
    display: none;
  }
}
@media only screen and (min-width: 600px) {
    #course_list{
      margin-left: 0px;
      padding: 4px 15px;
    }
    #assessment_name{
        margin-left:-53px;
        padding-top:0px;
    }
    #assessment_word{
        margin-left:-48px;
        padding-top:0px;
    }
    #course_name{
        margin-left:-18px;
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
    #lecturer_name{
      text-align: right;
      position:relative;
      top:7px;
      border:0px solid black;
      padding: 0px;
      display: block;
    }
}
</style>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">Report</p>
        <p class="pass_page">
            <a href="{{$character}}/home" class="first_page"> Home </a>/
            <a href="{{$character}}/report/course/List/"> Report List </a>/
            
            <span class="now_page">Moderation Form ( CA )</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 5px 10px;">
        <div class="col-md-12">
             <p class="page_title">Moderation Form ( CA )</p>
             <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                <div id="action_sidebar" class="w3-animate-right" style="display: none">
                    <div style="text-align: right;padding:10px;">
                        <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                  <ul class="sidebar-action-ul">
                      <p class="title_method">Download</p>
                      <input type="hidden" id="course_id" value="{{$course[0]->course_id}}">
                        <a id="checkDownloadAction"><li class="sidebar-action-li"><i class="fa fa-check-square-o" style="padding: 0px 10px;" aria-hidden="true"></i>Checked Item</li></a>
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

            @if(\Session::has('failed'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php
                $new_str = str_replace('.', '. <br />', Session::get('failed'));
                echo $new_str;
                ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
                <div class="col-md-6 row" style="padding:0px 20px;position: relative;top: -30px;">
                    <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                        <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                            <i class="fa fa-search" aria-hidden="true" style="font-size: 20px;"></i>
                        </p>
                    </div>
                    <div class="col-11" style="padding-left: 20px;">
                        <div class="form-group">
                            <label for="full_name" class="bmd-label-floating">Search</label>
                            <input type="hidden" id="semester" value="previous">
                            <input type="hidden" value="{{$course[0]->course_id}}" id="course_id">
                            <input type="text" name="search" class="form-control search" id="input" style="font-size: 18px;">
                        </div>
                    </div>
                </div>
              </div>
            </div>
            <div class="col-12 row" style="padding: 0px 0px 5px 10px;margin: -25px 0px 0px 0px;">
              <div class="checkbox_group_style align-self-center">
                <input type="checkbox" name="group_lecturer" id='group_lecturer' class="group_checkbox">
              </div>
              <p style="font-size: 18px;margin:0px 0px 0px 5px;display: inline-block;">
                Newest Semester of Courses
              </p>
            </div>
            <div style="overflow-x: auto;padding-bottom: 5px;">
              <table id="dtBasic" style="width: 100%;border:0px solid black;padding: 0px;margin: 0px;">
              <thead>
                <tr style="display: none;"><th></th><th></th><th></th><th></th></tr>
              </thead> 
              <tbody>
                  @foreach($course as $row)
                  <?php
                    $status = "Pending";
                    $color = "grey";
                    foreach($action as $action_row){
                      if($action_row->course_id==$row->course_id){
                        $status = $action_row->status_data;
                        if($status == "Rejected"){
                          $color = "red";
                        }else if($status == "Approved"){
                          $color = "green";
                        }else{
                          $color = "blue";
                        }
                      }
                    }
                  ?>
                 <tr id="course_list" style="height: 50px;">
                  <td width="5%" align="center">
                    @if($status!="Pending"&&$status!="Waiting For Moderation")
                      <input type="checkbox" value="{{$row->course_id}}" class="group_download">
                    @endif
                  </td>
                  <td width="3%" class='clickable-row {{$status}}' data-href='{{$character}}/report/assessment/download/{{$row->course_id}}'>
                    <img src="{{url('image/docs.png')}}" width="25px" height="25px"/>
                  </td>
                  <td class='clickable-row {{$status}}' data-href='{{$character}}/report/assessment/download/{{$row->course_id}}'>
                    <b>{{$row->semester_name}}</b> : {{$row->short_form_name}} / {{$row->subject_code}} {{$row->subject_name}} ( {{$row->name}} )
                  </td>
                  <td class='clickable-row {{$status}}' data-href='{{$character}}/report/assessment/download/{{$row->course_id}}'>
                    <p style="padding:0px;margin:0px;color:<?php echo $color;?>">{!!$status!!}</p>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
</div>
@endsection