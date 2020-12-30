<?php
$title = "Department";
$option2 = "id='selected-sidebar'";
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

    $(function () {
        $('.group_checkbox').click(function(){
          if($(this).prop("checked") == true){
            $('.group_download_list').prop("checked", true);
          }
          else if($(this).prop("checked") == false){
            $('.group_download_list').prop("checked", false);
          }
        });
        $(document).on('click', '#checkDownloadAction', function(){
          var checkedValue = ""; 
          var inputElements = document.getElementsByClassName('group_download_list');
          for(var i=0; inputElements[i]; i++){
            if(inputElements[i].checked){
              checkedValue += inputElements[i].value+"---";
            }
          }
          if(checkedValue!=""){
            var id = checkedValue;
            window.location = "/dean/staff/CV/download/zipFiles/"+id;
          }else{
              alert("Please select the document first.");
          }
        });
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        if($('.search').val()!=""){
            var value = $('.search').val();
            // var department_id = $('#department_id').val();
            $.ajax({
               type:'POST',
               url:'/searchLecturerCV',
               data:{value:value},
               success:function(data){
                    document.getElementById("lecturer_CV").innerHTML = data;
                    $('.group_checkbox').click(function(){
                      if($(this).prop("checked") == true){
                        $('.group_download_list').prop("checked", true);
                      }
                      else if($(this).prop("checked") == false){
                        $('.group_download_list').prop("checked", false);
                      }
                    });
               }
            });
        }
        $(".search").keyup(function(){
            var value = $('.search').val();
            // var department_id = $('#department_id').val();
            $.ajax({
               type:'POST',
               url:'/searchLecturerCV',
               data:{value:value},
               success:function(data){
                    document.getElementById("lecturer_CV").innerHTML = data;
                    $('.group_checkbox').click(function(){
                      if($(this).prop("checked") == true){
                        $('.group_download_list').prop("checked", true);
                      }
                      else if($(this).prop("checked") == false){
                        $('.group_download_list').prop("checked", false);
                      }
                    });
               }
            });
        });
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
.hover:hover{
  background-color: #d9d9d9;
}
.dropzone .dz-preview{
  border-bottom: 1px solid black;
  padding-left: 10px;
  padding-top: 10px;
  padding-bottom: -30px!important;
  width: 95%;
}
.dropzone .dz-preview .dz-filename {
  display: none;
}
.dropzone .dz-preview .dz-size {
  display: none;
}
.dropzone .dz-preview .dz-remove {
  text-align: left;
  padding-left: 25px;
  display: inline-block;
}
#icon_image{
  padding-top: 3px;border-bottom:1px solid #aaaaaa;
}
#checkbox{
  border-bottom:1px solid #aaaaaa;padding: 5px 0px;margin: 0px; text-align: center;
}
.name:hover{
  text-decoration: none;
  color: #0d2f81;
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
  .name{
    border-bottom:1px solid #aaaaaa;
    margin: 0px;
    color: #0d2f81;
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
        margin-left:-48px;
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
    .name{
      border-bottom:1px solid #aaaaaa;
      margin: 0px 0px 0px -30px;
      color: #0d2f81;
    }
}
</style>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$faculty->faculty_name}}</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/FacultyPortFolio"> Faculty ( Portfolio )</a>/
            <span class="now_page">Lecturer CV </span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 0px 10px;">
        <div class="col-md-12">
            <p class="page_title">Lecturer CV</p>
            <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                <div id="action_sidebar" class="w3-animate-right" style="display: none">
                    <div style="text-align: right;padding:10px;">
                        <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                  <ul class="sidebar-action-ul">
                      <p class="title_method">Download</p>
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
            <div class="details" style="padding: 0px 5px 5px 5px;">
<!--                 <h5 style="color: #0d2f81;">List of Lecturer CV</h5> -->
                <div class="col-md-6 row" style="padding:0px 20px;position: relative;top: -30px;">
                    <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                        <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                            <i class="fa fa-search" aria-hidden="true" style="font-size: 20px;"></i>
                        </p>
                    </div>
                    <div class="col-11" style="padding-left: 20px;">
                        <div class="form-group">
                            <label for="full_name" class="bmd-label-floating">Search</label>
                            <input type="text" name="search" class="form-control search" id="input" style="font-size: 18px;">
                        </div>
                    </div>
                </div>
                <!-- <hr style="margin: 0px 0px 15px 0px;"> -->
                <div class="row" id="lecturer_CV" style="padding: 0px;margin-bottom:0px;margin-top: -25px;">
                    <div class="col-12 row" style="padding: 0px 0px 5px 10px;margin:0px;">
                    <div class="checkbox_group_style align-self-center">
                      <input type="checkbox" name="group_lecturer" id='group_lecturer' class="group_checkbox">
                    </div>
                    <p style="font-size: 18px;margin:0px 0px 0px 5px;display: inline-block;">
                      Listing of CV
                    </p>
                  </div>
                    @foreach($faculty_staff as $row)
                        <?php
                            if($row->lecturer_CV!=""){
                                $ext = explode(".", $row->lecturer_CV);
                            }
                        ?>
                        <div class="col-12 row align-self-center" id="course_list">
                          <div class="col-12 row align-self-center">
                            <div class="checkbox_style align-self-center">
                              <input type="checkbox" value="{{$row->staff_id}}" class="group_download_list">
                            </div>
                            <a href="/dean/staff/CV/{{$row->staff_id}}" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">
                              <div class="col-1" style="position: relative;top: -2px;">
                               @if($ext[1]=="pdf")
                                <img src="{{url('image/pdf.png')}}" width="25px" height="25px"/>
                                @elseif($ext[1]=="docx")
                                <img src="{{url('image/docs.png')}}" width="25px" height="25px"/>
                                @elseif($ext[1]=="xlsx")
                                <img src="{{url('image/excel.png')}}" width="25px" height="25px"/>
                                @elseif($ext[1]=="pptx")
                                <img src="{{url('image/pptx.png')}}" width="25px" height="25px"/>
                                @elseif($ext[1]=="ppt")
                                <img src="{{url('image/pptx.png')}}" width="25px" height="25px"/>
                               @endif
                              </div>
                              <div class="col-10" id="course_name">
                                  <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>{{$row->staff_id}}_{{$row->name}}</b></p> 
                              </div>
                            </a>
                          </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
