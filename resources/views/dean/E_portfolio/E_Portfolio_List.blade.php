<?php
$title = "E-Portfolio";
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
$(document).on('click', '.download_button', function(){
    var id = $(this).attr("id");
    var num = id.split("_");
    window.location = "{{$character}}/E_Portfolio/report/"+num[2];
    return false;
});

$(document).ready(function(){
    $(document).on('click', '#checkDownloadAction', function(){
      var checkedValue = ""; 
      var inputElements = document.getElementsByClassName('group_download');
      for(var i=0; inputElements[i]; i++){
        if(inputElements[i].checked){
          checkedValue += inputElements[i].value+"---";
        }
      }
      if(checkedValue!=""){
        // var course_id = $('#course_id').val();
        var id = checkedValue;
        window.location = "{{$character}}/E_Portfolio/download/zipFiles/"+id+"/checked";
      }else{
          alert("Please select the document first.");
      }
    });
});
$(function () {
    $.ajaxSetup({
       	headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    	}
    });
    if($('.search').val()!=""){
        var value = $('.search').val();
       	$.ajax({
            type:'POST',
            url:'{{$character}}/E_Portfolio/searchCourse/',
            data:{value:value},
            success:function(data){
              document.getElementById("course").innerHTML = data;
            }
        });
    }
    $(".search").keyup(function(){
        var value = $('.search').val();
        $.ajax({
            type:'POST',
            url:'{{$character}}/E_Portfolio/searchCourse/',
            data:{value:value},
            success:function(data){
              document.getElementById("course").innerHTML = data;
            }
        });
    });
});
</script>
<style type="text/css">

#show_image_link:hover{
    text-decoration: none;
}
@media only screen and (min-width: 600px) {
	#course_list{
      margin-left: 0px;
      padding: 4px 15px;
    }
    #course_name{
      padding:0px;
      margin:0px 0px 0px -25px;
      border:0px solid black;
      position: relative;
      top: 8px;
    }
    #course_action_two{
      text-align: right;
      margin-left: 5px;
      padding: 8px 0px 0px 0px;
    }
    #course_action{
      text-align: center;
      padding:8px 0px;
      margin:0px;
      border:0px solid black;
    }
	.checkbox_style{
	  border:0px solid black;
	  padding: 0px 0px!important;
	  margin: 0px!important;
	  display: inline;
	  width: 28px;
	}
	#course_image{
		padding: 0px 5px;
		margin:0px;
		margin-left: -20px;
		text-align: left;
		vertical-align: middle;
		border:0px solid black;
		position: relative;
		top: -2px;
	}
}
@media only screen and (max-width: 600px) {
	#course_name{
	  	padding: 0px;
	  	border:0px solid black;
	  	position: relative;
	  	left: 30px;
	}
	#course_list{
	    margin-left: 0px;
	    padding: 4px 15px
	}
	#file_name{
	    margin: 0px;
	    padding:0px;
	}
	#course_action{
      border:0px solid black;
      padding:0px 0px 0px 0px;
      margin:0px;
    }
    .checkbox_style{
	  border:0px solid black;
	  padding: 0px 0px 0px 0px!important;
	  margin: 0px 0px 0px -10px!important;
	  display: inline;
	}
	#course_image{
		padding: 0px 10px 0px 5px;
		text-align: center;
		vertical-align: middle;
		margin-left: -10px;
		position: relative;
		top: -2px;
	}
}
</style>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">E - Portfolio</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <span class="now_page">E - Portfolio</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 5px 10px;">
        <div class="col-md-12">
            <p class="page_title">E - Portfolio</p>
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
            <!-- <h5 style="position:relative;margin-top:10px;left: 10px;">Standard Operating Procedure ( SOP )</h5>
            <div style="overflow-x: auto;padding:0px 10px 5px 10px;">
            <table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);border:none;" id="dtBasicExample">
              <thead>
                <tr style="background-color: #d9d9d9;">
                  <th style="border-left:1px solid #e6e6e6;color:black;border-bottom: 1px solid #d9d9d9;text-align: center;">No.</th>
                  <th style="border-left:1px solid #e6e6e6;color:black;border-bottom: 1px solid #d9d9d9;text-align: center;">Courses Detail</th>
                  <th style="border-left:1px solid #e6e6e6;color:black;border-bottom: 1px solid #d9d9d9;text-align: center;">Type of Materials</th>
                  <th style="border-left:1px solid #e6e6e6;color:black;border-bottom: 1px solid #d9d9d9;text-align: center;">Action</th>
                  <th style="border-left:1px solid #e6e6e6;color:black;border-bottom: 1px solid #d9d9d9;text-align: center;">Responce</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
            </div>
            <hr style="margin: 15px 5px 5px 5px;background-color:black;">
            <div class="row">
                <h5 style="position: relative;top:4px;left: 10px;" class="tp_title col-10" id="1">
                    Courses of Materials (<i class="fa fa-plus" aria-hidden="true" id="icon" style="color: #0d2f81;position: relative;top: 2px;"></i>)
                </h5>
            </div> -->
            <div class="details" style="padding: 0px 5px 0px 5px;">
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
                <div class="row" id="course" style="margin-top: -25px;">
                  <div class="col-md-12">
                    <p style="font-size: 18px;margin:0px 0px 0px 5px;">Newest Semester of Courses</p>
                  </div>
                  @foreach($course_reviewer as $row)
	                  <div class="col-12 row align-self-center" id="course_list">
	                      <div class="col-11 row align-self-center" style="border:0px solid black;margin: 0px;">
	                        <div class="checkbox_style align-self-center">
	                          <input type="checkbox" value="{{$row->course_id}}" class="group_q group_download">
	                        </div>
	                        <a href="{{$character}}/E_Portfolio/list/{{$row->course_id}}" id="show_image_link" class="col-11 row" style="margin:0px;color:#0d2f81;border:0px solid black;width: 100%;">
	                          <div class="col-1 align-self-center" id="course_image">
	                            <img src="{{url('image/portfolio.png')}}" width="25px" height="25px"/>
	                          </div>
	                          <div class="col-11 align-self-center" id="course_name">
	                            <p id="file_name"><b>{{$row->semester_name}}</b> : {{$row->short_form_name}} / {{$row->subject_code}} {{$row->subject_name}} ( {{$row->name}} )</p>
	                          </div>
	                        </a>
	                      </div>
	                      <div class="col-1 align-self-center" id="course_action">
	                        <i class="fa fa-download download_button" aria-hidden="true" id="download_button_{{$row->course_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>
	                      </div>
	                  </div>
                      @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection