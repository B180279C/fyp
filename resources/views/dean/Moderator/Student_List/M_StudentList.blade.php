<?php
$title = "Course";
$option3 = "id='selected-sidebar'";
?>
@extends('layouts.nav_dean')

@section('content')
<script type="text/javascript">
  $(document).on('click', '.plus', function(){
    var id = $(this).attr("id"); 
      $('#student_'+id).slideToggle("slow", function(){
        if($('#student_'+id).is(":visible")){
          $('#icon_'+id).removeClass('fa fa-plus');
          $('#icon_'+id).addClass('fa fa-minus');
        }else{
          $('#icon_'+id).removeClass('fa fa-minus');
          $('#icon_'+id).addClass('fa fa-plus');
        }
    });
  });
  $(function () {

        $('#showData').hide();
        $('#errorData').hide();
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        if($('.search').val()!=""){
          var value = $('.search').val();
          var course_id = $('#course_id').val();
          $.ajax({
              type:'POST',
              url:'/searchModeratorStudent',
              data:{value:value,course_id:course_id},
              success:function(data){
                document.getElementById("assign_student").innerHTML = data;
              }
          });
        }
        $(".search").keyup(function(){
            var value = $('.search').val();
            var course_id = $('#course_id').val();
            $.ajax({
               type:'POST',
               url:'/searchModeratorStudent',
               data:{value:value,course_id:course_id},
               success:function(data){
                    document.getElementById("assign_student").innerHTML = data;
               }
            });
        });
    });
</script>
<style type="text/css">
.dropzoneModel{
  border-bottom: 1px solid black;
  padding-left: 0px;
  padding-top: 10px;
  padding-bottom: 10px!important;
  width: 95%;
}
.dropzone .dz-preview .dz-filename {
  display: none;
}
.dropzone .dz-preview .dz-size {
  display: none;
}
.dropzone .dz-preview .dz-remove{
  text-align: left;
  display: inline-block;
}
#syllabus_link:hover{
  text-decoration: none;
}
.InModel{
  padding-left: 25px;
}
.tablebody{
  background-color: white!important;
  color: black;
  height: 60px;
  padding-left: 10px;
}
.tablehead{
  background-color: #0d2f81!important; color: gold;
}
</style>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/Moderator">Moderator </a>/
            <a href="/Moderator/course/{{$course[0]->course_id}}">{{$course[0]->semester_name}} : {{$course[0]->short_form_name}} / {{$course[0]->subject_code}} {{$course[0]->subject_name}} ( {{$course[0]->name}} )</a>/
            <span class="now_page">Student List</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 10px 10px;">
        <div class="col-md-12">
            <p class="page_title">Student List ( {{count($assign_student)}} )</p>
            <div class="details" style="padding: 0px 5px 0px 5px;">
                <div class="col-md-6 row" style="padding:0px 20px;position: relative;top: -10px;">
                    <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                        <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                            <i class="fa fa-search" aria-hidden="true" style="font-size: 20px;"></i>
                        </p>
                    </div>
                    <div class="col-11" style="padding-left: 20px;">
                        <div class="form-group">
                            <label for="full_name" class="bmd-label-floating">Search</label>
                            <input type="hidden" value="{{$course[0]->course_id}}" id="course_id">
                            <input type="text" name="search" class="form-control search" id="input" style="font-size: 18px;">
                        </div>
                    </div>
                </div>
                <div class="row" id="assign_student" style="margin-top:-15px;padding: 0px 15px;">
                    <?php
                      $i=0;
                    ?>
                    @foreach($batch as $row_batch)
                    <div style="border-bottom:1px solid black;padding:0px;" class="col-md-12">
                    <div class="col-12 row" style="padding:15px 10px 5px 10px;margin: 0px;">
                    <h5 class="group plus" id="{{$i}}">{{$row_batch->batch}} (<i class="fa fa-minus" aria-hidden="true" id="icon_{{$i}}" style="color: #0d2f81;position: relative;top: 2px;"></i>)</h5>
                    </div>
                    <div id="student_{{$i}}" class="col-12 row align-self-center list" style="margin-left:0px;padding:0px 0px 5px 0px;">
                      @foreach($assign_student as $row)
                      @if($row->batch == $row_batch->batch)
                      <div class="col-md-4" style="margin: 0px;padding:2px;">
                        <center>
                        <a href="" class="row" id="course_list" style="border: 1px solid #cccccc;border-radius: 10px;color: black;font-weight: bold;margin: 0px;">
                            <div class="col-12" style="color: #0d2f81;padding: 10px;">
                              <p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;"><b>{{$row->name}} ( {{$row->student_id}})</b>
                              </p>
                            </div>
                        </a>
                        </center>
                      </div>
                      @endif
                      @endforeach
                      <?php
                      $i++;
                      ?>
                      </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection