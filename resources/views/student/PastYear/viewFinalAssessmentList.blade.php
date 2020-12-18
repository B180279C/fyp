<?php
$title = "Course";
$option1 = "id='selected-sidebar'";
?>
@extends('layouts.layout')

@section('content')
<style type="text/css">
.checkbox_group_style{
  border:0px solid black;
  padding: 1px 10px 0px 10px!important;
  margin: 0px!important;
}
.checkbox_style{
  border:0px solid black;
  padding: 0px 5px 0px 20px!important;
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
    $('[data-toggle="lightbox"]').click(function(event) {
             event.preventDefault();
                 $(this).ekkoLightbox({
                   type: 'image',
                   onContentLoaded: function() {
                     var container = $('.ekko-lightbox-container');
                     var content = $('.modal-content');
                     var backdrop = $('.modal-backdrop');
                     var overlay = $('.ekko-lightbox-nav-overlay');
                     var modal = $('.modal');
                     var image = container.find('img');
                     var windowHeight = $(window).height();
                     var dialog = container.parents('.modal-dialog');
                     var data_header = $('.modal-header');
                     var data_title = $('.modal-title');
                     var body = $('.modal-body');
                     console.log(image.width());

                     if((image.width() > 380) && (image.width() < 441)){
                        dialog.css('max-width','700px');
                        image.css('height','900px');
                        image.css('width','700px');
                        overlay.css('height','900px');
                     }else{
                        overlay.css('height','100%');
                     }
                     // backdrop.css('opacity','1');
                     data_header.css('background-color','white');
                     data_header.css('padding','10px');
                     data_header.css('margin','0px 24px');
                     data_header.css('border-bottom','1px solid black');
                     data_title.css('font-size','18px');

                     body.css('padding-top','0px');
                     content.css('background', "none");
                     content.css('-webkit-box-shadow', "0 5px 15px rgba(0,0,0,0)");
                     content.css('-moz-box-shadow', "0 5px 15px rgba(0,0,0,0)");
                     content.css('-o-box-shadow', "0 5px 15px rgba(0,0,0,0)");
                     content.css('box-shadow', "0 5px 15px rgba(0,0,0,0)");
                   }
                 });
        });
    $(document).on('click', '.plus', function(){
      var id = $(this).attr("id"); 
      $('#assessment_list_'+id).slideToggle("slow", function(){
        if($('#assessment_list_'+id).is(":visible")){
          $('#icon_'+id).removeClass('fa fa-plus');
          $('#icon_'+id).addClass('fa fa-minus');
        }else{
          $('#icon_'+id).removeClass('fa fa-minus');
          $('#icon_'+id).addClass('fa fa-plus');
        }
      });
    });

    $(document).on('click', '.download_button', function(){
      var id = $(this).attr("id");
      var num = id.split("_");
      var original_id = $('#original_id').val();
      window.location = "{{$character}}/PastYear/FinalAssessment/download/"+original_id+"-"+num[2];
    });

    $('.group_checkbox').click(function(){
      var id = $(this).attr("id");
      var type = id.split("group_");
      if($(this).prop("checked") == true){
        $('.group_'+type[1]).prop("checked", true);
      }
      else if($(this).prop("checked") == false){
        $('.group_'+type[1]).prop("checked", false);
      }
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
        var fx_id = $('#fx_id').val();
        var id = fx_id+"---"+checkedValue;
        window.location = "{{$character}}/PastYear/FinalAssessment/list/download/zipFiles/"+id+"/checked";
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
          var fx_id = $('#fx_id').val();
          var original_id = $('#original_id').val();
          $.ajax({
              type:'POST',
              url: "{{$character}}/PastYear/FinalAssessment/list/searchAssessmentlist/",
              data:{value:value,fx_id:fx_id,original_id:original_id},
              success:function(data){
                document.getElementById("assessments").innerHTML = data;
                $('.group_checkbox').click(function(){
                  var id = $(this).attr("id");
                  var type = id.split("group_");
                  if($(this).prop("checked") == true){
                    $('.group_'+type[1]).prop("checked", true);
                  }
                  else if($(this).prop("checked") == false){
                    $('.group_'+type[1]).prop("checked", false);
                  }
                });
                $('[data-toggle="lightbox"]').click(function(event) {
                  event.preventDefault();
                  $(this).ekkoLightbox({
                    type: 'image',
                    onContentLoaded: function() {
                      var container = $('.ekko-lightbox-container');
                      var content = $('.modal-content');
                      var backdrop = $('.modal-backdrop');
                      var overlay = $('.ekko-lightbox-nav-overlay');
                      var modal = $('.modal');
                      var image = container.find('img');
                      var windowHeight = $(window).height();
                      var dialog = container.parents('.modal-dialog');
                      var data_header = $('.modal-header');
                      var data_title = $('.modal-title');
                      var body = $('.modal-body');
                      // console.log(image.width());

                      if((image.width() > 380) && (image.width() < 430)){
                         dialog.css('max-width','700px');
                         image.css('height','900px');
                         image.css('width','700px');
                         overlay.css('height','900px');
                      }else{
                         overlay.css('height','100%');
                      }
                      // backdrop.css('opacity','1');
                      data_header.css('background-color','white');
                      data_header.css('padding','10px');
                      data_header.css('margin','0px 24px');
                      data_header.css('border-bottom','1px solid black');
                      data_title.css('font-size','18px');

                      body.css('padding-top','0px');
                      content.css('background', "none");
                      content.css('-webkit-box-shadow', "0 5px 15px rgba(0,0,0,0)");
                      content.css('-moz-box-shadow', "0 5px 15px rgba(0,0,0,0)");
                      content.css('-o-box-shadow', "0 5px 15px rgba(0,0,0,0)");
                      content.css('box-shadow', "0 5px 15px rgba(0,0,0,0)");
                    }
                  });
                });
              }
          });
        }
        $(".search").keyup(function(){
            var value = $('.search').val();
            var fx_id = $('#fx_id').val();
            var original_id = $('#original_id').val();
            $.ajax({
               type:'POST',
               url: "{{$character}}/PastYear/FinalAssessment/list/searchAssessmentlist/",
               data:{value:value,fx_id:fx_id,original_id:original_id},
               success:function(data){
                  document.getElementById("assessments").innerHTML = data;
                  $('.group_checkbox').click(function(){
                    var id = $(this).attr("id");
                    var type = id.split("group_");
                    if($(this).prop("checked") == true){
                      $('.group_'+type[1]).prop("checked", true);
                    }
                    else if($(this).prop("checked") == false){
                      $('.group_'+type[1]).prop("checked", false);
                    }
                  });
                  $('[data-toggle="lightbox"]').click(function(event) {
                  event.preventDefault();
                  $(this).ekkoLightbox({
                    type: 'image',
                    onContentLoaded: function() {
                      var container = $('.ekko-lightbox-container');
                      var content = $('.modal-content');
                      var backdrop = $('.modal-backdrop');
                      var overlay = $('.ekko-lightbox-nav-overlay');
                      var modal = $('.modal');
                      var image = container.find('img');
                      var windowHeight = $(window).height();
                      var dialog = container.parents('.modal-dialog');
                      var data_header = $('.modal-header');
                      var data_title = $('.modal-title');
                      var body = $('.modal-body');
                      // console.log(image.width());

                      if((image.width() > 380) && (image.width() < 430)){
                         dialog.css('max-width','700px');
                         image.css('height','900px');
                         image.css('width','700px');
                         overlay.css('height','900px');
                      }else{
                         overlay.css('height','100%');
                      }
                      // backdrop.css('opacity','1');
                      data_header.css('background-color','white');
                      data_header.css('padding','10px');
                      data_header.css('margin','0px 24px');
                      data_header.css('border-bottom','1px solid black');
                      data_title.css('font-size','18px');

                      body.css('padding-top','0px');
                      content.css('background', "none");
                      content.css('-webkit-box-shadow', "0 5px 15px rgba(0,0,0,0)");
                      content.css('-moz-box-shadow', "0 5px 15px rgba(0,0,0,0)");
                      content.css('-o-box-shadow', "0 5px 15px rgba(0,0,0,0)");
                      content.css('box-shadow', "0 5px 15px rgba(0,0,0,0)");
                    }
                  });
                });
               }
            });
        });
    });
</script>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->subject_code}} {{$course[0]->subject_name}}</p>
        <p class="pass_page">
             <a href="{{$character}}/course/action/{{$course[0]->course_id}}" class="first_page">Past Year</a>/
            <a href="{{$character}}/PastYear/FinalAssessment/{{$course[0]->course_id}}">Final Assessment</a>/
            <a href="{{$character}}/PastYear/FinalAssessment/{{$course[0]->course_id}}/assessment_name/{{$previous[0]->course_id}}">{{$previous[0]->semester_name}}</a>/
            <span class="now_page">{{$AssFinal->assessment_name}}</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 5px 10px;">
        <div class="col-md-12">
             <p class="page_title">{{$AssFinal->assessment_name}}</p>
             <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                <div id="action_sidebar" class="w3-animate-right" style="display: none">
                    <div style="text-align: right;padding:10px;">
                        <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                  <ul class="sidebar-action-ul">
                      <p class="title_method">Download</p>
                      <input type="hidden" id="fx_id" value="{{$AssFinal->fx_id}}">
                        <a id="checkDownloadAction"><li class="sidebar-action-li"><i class="fa fa-check-square-o" style="padding: 0px 10px;" aria-hidden="true"></i>Checked Item</li></a>
                        <a href='{{$character}}/PastYear/FinalAssessment/list/download/zipFiles/{{$AssFinal->fx_id}}/All'><li class="sidebar-action-li"><i class="fa fa-download" style="padding: 0px 10px;" aria-hidden="true"></i>All Result</li></a>
                  </ul>
                </div>
                <br>
                <br>
              <div class="details" style="padding: 0px 5px;">
                  <div class="col-md-6 row" style="padding:0px 20px;position: relative;top:-25px;">
                      <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                          <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                              <i class="fa fa-search" aria-hidden="true" style="font-size: 20px;"></i>
                          </p>
                      </div>
                      <div class="col-11" style="padding-left: 20px;">
                          <div class="form-group">
                              <label for="full_name" class="bmd-label-floating">Search</label>
                              <input type="hidden" id="fx_id" value="{{$AssFinal->fx_id}}">
                              <input type="hidden" id="original_id" value="{{$course[0]->course_id}}">
                              <input type="text" name="search" class="form-control search" id="input" style="font-size: 18px;">
                          </div>
                      </div>
                  </div>
                  <div class="row" id="assessments"  style="margin-top: -25px;">
                    <?php
                      $i=0;
                    ?>
                    @foreach($group_list as $row_group)
                      <div style="border-bottom:1px solid black;padding:0px;" class="col-md-12">
                        <div class="col-12 row" style="padding:10px;margin: 0px;">
                          <div class="checkbox_group_style">
                            <input type="checkbox" id='group_{{$row_group->ass_fx_type}}' class="group_checkbox">
                          </div>
                          <h5 class="group plus" id="{{$i}}">{{$row_group->ass_fx_type}} (<i class="fa fa-minus" aria-hidden="true" id="icon_{{$i}}" style="color: #0d2f81;position: relative;top: 2px;"></i>)</h5>
                        </div>
                        <div id="assessment_list_{{$i}}" class="col-12 row align-self-center list" style="margin-left:0px;padding:0px;">
                      @foreach($assessment_list as $row)
                      @if($row_group->ass_fx_type == $row->ass_fx_type)
                        <div class="col-12 row align-self-center" id="course_list">
                          <div class="col-9 row align-self-center" >
                            <div class="checkbox_style align-self-center">
                              <input type="checkbox" value="{{$row->ass_fx_id}}_{{$row->ass_fx_type}}" class="group_{{$row_group->ass_fx_type}} group_download">
                            </div>
                            <a href="{{$character}}/PastYear/images/final_assessment/{{$course[0]->course_id}}-{{$row->ass_fx_document}}" data-toggle="lightbox" data-gallery="example-gallery" class="col-11 row" style="padding:10px 0px;margin-left:5px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="{{$previous[0]->semester_name}} : {{$AssFinal->assessment_name}} / {{$row_group->ass_fx_type}} / {{$row->ass_fx_name}} <br> <a href='{{$character}}/PastYear/final_assessment/view/whole_paper/{{$course[0]->course_id}}-{{$row->fx_id}}' class='full_question' target='_blank'>Whole paper</a>">
                              <div class="col-1" style="position: relative;top: -2px;">
                                <img src="{{url('image/img_icon.png')}}" width="25px" height="20px"/>
                              </div>
                              <div class="col-10" id="course_name">
                                <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>{{$row->ass_fx_name}}</b></p>
                              </div>
                            </a>
                          </div>
                          <div class="col-3" id="course_action_two">
                            <i class="fa fa-download download_button" aria-hidden="true" id="download_button_{{$row->ass_fx_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>&nbsp;
                          </div>
                        </div>
                      @endif
                      @endforeach
                      <?php
                      $i++;
                      ?>
                      </div>
                    </div>
                    @endforeach
                    @if($i==0)
                    <div style="display: block;border:1px solid black;padding: 50px;width: 100%;margin: 10px 20px 0px 20px;">
                      <center>Empty</center>
                    </div>
                    @endif
                  </div>
              </div>
        </div>
    </div>
</div>
@endsection