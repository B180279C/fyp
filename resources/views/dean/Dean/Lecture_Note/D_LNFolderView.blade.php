<?php
$title = "Moderator";
$option3 = "id='selected-sidebar'";
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
  $(document).ready(function(){  

    $(document).on('click', '#checkDownloadAction', function(){
      var checkedValue = ""; 
      var inputElements = document.getElementsByClassName('group_download_list');
      for(var i=0; inputElements[i]; i++){
        if(inputElements[i].checked){
          checkedValue += inputElements[i].value+"---";
        }
      }
      if(checkedValue!=""){
        var course_id = $('#course_id').val();
        var id = course_id+"---"+checkedValue;
        window.location = "/lectureNote/download/zipFiles/"+id+"/checked";
      }else{
          alert("Please select the document first.");
      }
    });

    $(document).on('click', '.download_button', function(){
        var id = $(this).attr("id");
        var num = id.split("_");
        window.location = "/lectureNote/download/"+num[2];
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
          var place = $('#place').val();
          var course_id = $('#course_id').val();
          $.ajax({
              type:'POST',
              url:'/Dean/lectureNote/searchFiles',
              data:{value:value,course_id:course_id,place:place},
              success:function(data){
                document.getElementById("lecture_note").innerHTML = data;
              }
          });
        }
        $(".search").keyup(function(){
            var value = $('.search').val();
            var place = $('#place').val();
            var course_id = $('#course_id').val();
            $.ajax({
               type:'POST',
               url:'/Dean/lectureNote/searchFiles',
               data:{value:value,course_id:course_id,place:place},
               success:function(data){
                    document.getElementById("lecture_note").innerHTML = data;
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
        margin-left:-27px;
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
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/Dean/">Dean </a>/
            <a href="/Dean/course/{{$course[0]->course_id}}">{{$course[0]->semester_name}} : {{$course[0]->short_form_name}} / {{$course[0]->subject_code}} {{$course[0]->subject_name}} ( {{$course[0]->name}} )</a>/
            <a href="/Dean/lectureNote/{{$course[0]->course_id}}">Lecture Note</a>/
            <?php
                $place = explode(',,,',($lecture_note->note_place));
                $place_name = explode(',,,',($data));
                $i=1;
                if($lecture_note->used_by!=""){
                  foreach($all_note as $all_row){
                    if(($lecture_note->used_by)==($all_row->ln_id)){
                      $note_name = $lecture_note->note_name." (".$all_row->semester_name.")";
                    }
                  }
                }else{
                  $note_name = $lecture_note->note_name;
                }
                while(isset($place[$i])!=""){
                  if($lecture_note->used_by!=""){
                    foreach($all_note as $all_row){
                      if(($lecture_note->used_by)==($all_row->ln_id)){
                        echo "<a href='/Dean/lectureNote/folder/$place[$i]'>".$place_name[$i]." (".$all_row->semester_name.")</a>/";
                      }
                    }
                  }else{
                    echo "<a href='/Dean/lectureNote/folder/$place[$i]'>".$place_name[$i]."</a>/";
                  }
                  $i++;
                }
            ?>
          <span class="now_page">{{$note_name}}</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 10px 10px;">
        <div class="col-md-12">
             <p class="page_title">{{$note_name}}</p>
             <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                <div id="action_sidebar" class="w3-animate-right" style="display: none">
                    <div style="text-align: right;padding:10px;">
                        <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                  <ul class="sidebar-action-ul">
                      <a id="checkDownloadAction"><li class="sidebar-action-li"><i class="fa fa-check-square-o" style="padding: 0px 10px;" aria-hidden="true"></i>Checked Item</li></a>
                      <a href='/lectureNote/download/zipFiles/{{$lecture_note->ln_id}}/folder'><li class="sidebar-action-li"><i class="fa fa-download" style="padding: 0px 10px;" aria-hidden="true"></i>All Note</li></a>
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
                            <input type="hidden" id="place" value="{{$lecture_note->note_place}},,,{{$lecture_note->ln_id}}">
                            <input type="hidden" value="{{$course[0]->course_id}}" id="course_id">
                            <input type="text" name="search" class="form-control search" id="input" style="font-size: 18px;">
                        </div>
                    </div>
                </div>
                <div class="row" id="lecture_note" style="margin-top:-25px;">
                      <?php
                      $i=0;
                      ?>
                      @foreach($lecture_note_list as $row)
                        @if($row->note_type=="folder")
                        <div class="col-12 row align-self-center" id="course_list">
                          <div class="col-12 row align-self-center">
                            <div class="checkbox_style align-self-center">
                              <input type="checkbox" value="{{$row->ln_id}}" class="group_download_list">
                            </div>
                            <a href="/Dean/lectureNote/folder/{{$row->ln_id}}" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">
                              <div class="col-1" style="position: relative;top: -2px;">
                                <img src="{{url('image/folder2.png')}}" width="25px" height="25px"/>
                              </div>
                              <div class="col-10" id="assessment_word">
                                @if($row->used_by!=null)
                                  @foreach($all_note as $all_row)
                                    @if(($row->used_by)==($all_row->ln_id))
                                      <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>{{$row->note_name}} <span style="color: grey;">( Used In : {{$all_row->semester_name}} )</span></b></p>
                                    @endif
                                  @endforeach
                                @else
                                  <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>{{$row->note_name}}</b></p>
                                @endif  
                              </div>
                            </a>
                          </div>
                          </div>
                        @else
                          <?php
                            $ext = "";
                            if($row->note){
                              $ext = explode(".", $row->note);
                            }
                          ?>
                        @if(($ext[1] == "pdf")||($ext[1] == "docx")||($ext[1] == "xlsx")||($ext[1] == "pptx"))
                        <div class="col-12 row align-self-center" id="course_list">
                          <div class="col-12 row align-self-center">
                            <div class="checkbox_style align-self-center">
                              <input type="checkbox" value="{{$row->ln_id}}" class="group_download_list">
                            </div>
                            <a href="{{action('Dean\LectureNoteController@downloadLN',$row->ln_id)}}" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">
                              <div class="col-1" style="position: relative;top: -2px;">
                               @if($ext[1]=="pdf")
                                <img src="{{url('image/pdf.png')}}" width="25px" height="25px"/>
                                @elseif($ext[1]=="docx")
                                <img src="{{url('image/docs.png')}}" width="25px" height="25px"/>
                                @elseif($ext[1]=="xlsx")
                                <img src="{{url('image/excel.png')}}" width="25px" height="25px"/>
                                @elseif($ext[1]=="pptx")
                                <img src="{{url('image/pptx.png')}}" width="25px" height="25px"/>
                               @endif
                              </div>
                              <div class="col-10" id="assessment_word">
                                @if($row->used_by!=null)
                                  @foreach($all_note as $all_row)
                                    @if(($row->used_by)==($all_row->ln_id))
                                      <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>{{$row->note_name}} <span style="color: grey;">( Used In : {{$all_row->semester_name}} )</span></b></p>
                                    @endif
                                  @endforeach
                                @else
                                  <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>{{$row->note_name}}</b></p>
                                @endif  
                              </div>
                            </a>
                          </div>
                        </div>
                          @else
                          @if($row->used_by!=null)
                            @foreach($all_note as $all_row)
                              @if(($row->used_by)==($all_row->ln_id))
                                <?php
                                  $semester_name = "<span style='color: grey;'>( Used In :".$all_row->semester_name.")</span>";
                                ?>
                              @endif
                            @endforeach
                          @else
                            <?php
                              $semester_name = "";
                            ?>
                          @endif
                          <div class="col-12 row align-self-center" id="course_list">
                            <div class="col-9 row align-self-center">
                              <div class="checkbox_style align-self-center">
                                  <input type="checkbox" value="{{$row->ln_id}}" class="group_download_list">
                                </div>
                              <a href="/images/lectureNote/{{$row->note}}" data-toggle="lightbox" data-gallery="example-gallery_student" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="{{$row->note_name}} {{$semester_name}}">
                                <div class="col-1" style="position: relative;top: -2px;">
                                  <img src="{{url('image/img_icon.png')}}" width="25px" height="20px"/>
                                </div>
                                <div class="col-10" id="course_name">
                                   @if($row->used_by!=null)
                                      @foreach($all_note as $all_row)
                                        @if(($row->used_by)==($all_row->ln_id))
                                          <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>{{$row->note_name}} <span style="color: grey;">( Used In : {{$all_row->semester_name}} )</span></b></p>
                                        @endif
                                      @endforeach
                                    @else
                                      <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>{{$row->note_name}}</b></p>
                                    @endif  
                                </div>
                              </a>
                            </div>
                            <div class="col-3" id="course_action_two">
                              <i class="fa fa-download download_button" aria-hidden="true" id="download_button_{{$row->ln_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>
                            </div>
                          </div>
                          @endif
                        @endif
                      <?php
                      $i++;
                      ?>
                      @endforeach
                      <?php
                      if($i==0){
                      ?>
                      <div style="display: block;border:1px solid black;padding: 50px;width: 100%;margin: 0px 20px;">
                              <center>Empty</center>
                      </div>
                      <?php
                      }
                      ?>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection