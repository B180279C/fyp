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

  $(document).on('click', '#checkDownloadAction', function(){
      var checkedValue = ""; 
      var inputElements = document.getElementsByClassName('group_download');
      for(var i=0; inputElements[i]; i++){
        if(inputElements[i].checked){
          checkedValue += inputElements[i].value+"---";
        }
      }
      if(checkedValue!=""){
        var course_id = $('#course_id').val();
        var id = course_id+"---"+checkedValue;
        window.location = "/PastYearNote/download/zipFiles/"+id+"/searched";
      }else{
          alert("Please select the document first.");
      }
  });

  $(function () {
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        if($('.search').val()!=""){
          var value = $('.search').val();
          var semester = $('#semester').val();
          var view_place = $('#view_place').val();
          var course_id = $('#course_id').val();
          $.ajax({
              type:'POST',
              url:'/PastYear/lectureNote/searchPreviousFiles',
              data:{value:value,course_id:course_id,semester:semester,view_place:view_place},
              success:function(data){
                document.getElementById("lecture_note").innerHTML = data;
              }
          });
        }
        $(".search").keyup(function(){
            var value = $('.search').val();
            var semester = $('#semester').val();
            var view_place = $('#view_place').val();
            var course_id = $('#course_id').val();
            $.ajax({
               type:'POST',
               url:'/PastYear/lectureNote/searchPreviousFiles',
               data:{value:value,course_id:course_id,semester:semester,view_place:view_place},
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
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->subject_code}} {{$course[0]->subject_name}}</p>
        <p class="pass_page">
            <a href="/course/action/{{$course[0]->course_id}}" class="first_page"> Past Year </a>/
            <a href="/PastYearNote/{{$course[0]->course_id}}"> Lecture Note </a>/
            <?php
            if(isset($ln)){
              echo '<a href="/PastYearNote/'.$course[0]->course_id.'/course/'.$previous[0]->course_id.'">'.$previous[0]->semester_name.'</a>/';
              $place = explode(',,,',($ln->note_place));
              $place_name = explode(',,,',($data));
              $i=1;
              while(isset($place[$i])!=""){
                echo '<a href="/PastYearNote/'.$course[0]->course_id.'/folder/'.$place[$i].'/">'.$place_name[$i].'</a>/';
                $i++;
              }
              echo '<span class="now_page">'.$ln->note_name.'</span>/';
              $title = $ln->note_name;
              $view_place = $ln->note_place.",,,".$ln->ln_id;
              $download_id = $ln->ln_id;
              $download = "folder";
            }else{
              echo '<span class="now_page">'.$previous[0]->semester_name.'</span>/';
              $title = $previous[0]->semester_name;
              $view_place = "Note";
              $download_id = $previous[0]->course_id;
              $download = "Semester";
            }
            ?>
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 10px 10px;">
        <div class="col-md-12">
             <p class="page_title">{{$title}}</p>
             <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                <div id="action_sidebar" class="w3-animate-right" style="display: none">
                    <div style="text-align: right;padding:10px;">
                        <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                  <ul class="sidebar-action-ul">
                      <p class="title_method">Download</p>
                      <input type="hidden" id="course_id" value="{{$course[0]->course_id}}">
                        <a id="checkDownloadAction"><li class="sidebar-action-li"><i class="fa fa-check-square-o" style="padding: 0px 10px;" aria-hidden="true"></i>Checked Item</li></a>
                        <a href='/PastYearNote/download/zipFiles/{{$download_id}}/{{$download}}'><li class="sidebar-action-li"><i class="fa fa-download" style="padding: 0px 10px;" aria-hidden="true"></i>All Result</li></a>
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
            <div class="details" style="padding: 0px 5px;">
                <div class="col-md-6 row" style="padding:0px 20px;position: relative;top: -30px;">
                    <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                        <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                            <i class="fa fa-search" aria-hidden="true" style="font-size: 20px;"></i>
                        </p>
                    </div>
                    <div class="col-11" style="padding-left: 20px;">
                        <div class="form-group">
                            <label for="full_name" class="bmd-label-floating">Search</label>
                            <input type="hidden" id="semester" value="{{$previous[0]->course_id}}">

                            <input type="hidden" id="view_place" value="{{$view_place}}">
                            <input type="text" name="search" class="form-control search" id="input" style="font-size: 18px;">
                        </div>
                    </div>
                </div>
                <div class="row" id="lecture_note" style="margin-top: -25px;">
                    @foreach($lecture_note as $row)
                      @if($row->note_type=="folder")
                        <div class="col-12 row align-self-center" id="course_list">
                          <div class="col-12 row align-self-center">
                            <div class="checkbox_style align-self-center">
                              <input type="checkbox" value="{{$row->ln_id}}" class="group_download">
                            </div>
                            <a href="/PastYearNote/{{$course[0]->course_id}}/folder/{{$row->ln_id}}" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">
                              <div class="col-1" style="position: relative;top: -2px;">
                                <img src="{{url('image/folder2.png')}}" width="25px" height="25px"/>
                              </div>
                              <div class="col-10" id="assessment_word">
                                @if($row->used_by!=null)
                                  @foreach($all_note as $all_row)
                                    @if(($row->used_by)==($all_row->ln_id))
                                      <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>{{$row->note_name}} <span style="color: grey;">( Used In : {{$all_row->semester_name}} )</span></b></p>
                                    @endif
                                  @endforeach
                                @else
                                  <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>{{$row->note_name}}</b></p>
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
                              <input type="checkbox" value="{{$row->ln_id}}" class="group_download">
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
                                      <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>{{$row->note_name}} <span style="color: grey;">( Used In : {{$all_row->semester_name}} )</span></b></p>
                                    @endif
                                  @endforeach
                                @else
                                  <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"> <b>{{$row->note_name}}</b></p>
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
                            <div class="col-12 row align-self-center">
                              <div class="checkbox_style align-self-center">
                                  <input type="checkbox" value="{{$row->ln_id}}" class="group_download">
                                </div>
                              <a href="/images/lectureNote/{{$row->note}}" data-toggle="lightbox" data-gallery="example-gallery_student" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="{{$row->note_name}} {{$semester_name}}">
                                <div class="col-1" style="position: relative;top: -2px;">
                                  <img src="{{url('image/img_icon.png')}}" width="25px" height="20px"/>
                                </div>
                                <div class="col-10" id="assessment_word">
                                   @if($row->used_by!=null)
                                      @foreach($all_note as $all_row)
                                        @if(($row->used_by)==($all_row->ln_id))
                                          <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>{{$row->note_name}} <span style="color: grey;">( Used In : {{$all_row->semester_name}} )</span></b></p>
                                        @endif
                                      @endforeach
                                    @else
                                      <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>{{$row->note_name}}</b></p>
                                    @endif  
                                </div>
                              </a>
                            </div>
                          </div>
                        @endif
                      @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection