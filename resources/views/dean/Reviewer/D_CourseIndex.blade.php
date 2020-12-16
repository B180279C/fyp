<?php
$title = "Reviewer";
$option4 = "id='selected-sidebar'";
?>
@extends('layouts.layout')

@section('content')
<style type="text/css">
.view:hover{
    text-decoration:none;
}
</style>
<script type="text/javascript">
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
              url:'{{$character}}/searchCourse',
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
               url:'{{$character}}/searchCourse',
               data:{value:value},
               success:function(data){
                    document.getElementById("course").innerHTML = data;
               }
            });
        });
    });
    $(document).ready(function() {
        oTable = $('#dtBasicExample').DataTable(
        {
            "bLengthChange" : false,
            "bInfo": false,
            pagingType: 'input',
            pageLength: 8,
            language: {
                oPaginate: {
                   sNext: '<i class="fa fa-forward"></i>',
                   sPrevious: '<i class="fa fa-backward"></i>',
                   sFirst: '<i class="fa fa-step-backward"></i>',
                   sLast: '<i class="fa fa-step-forward"></i>'
                }
            }
        });
        $(document).on("click",".tp_title", function(){
            $('#plan_detail').slideToggle("slow", function(){
                // check paragraph once toggle effect is completed
                if($('#plan_detail').is(":visible")){
                    $('#icon').removeClass('fa fa-plus');
                    $('#icon').addClass('fa fa-minus');
                }else{
                    $('#icon').removeClass('fa fa-minus');
                    $('#icon').addClass('fa fa-plus');
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
@media only screen and (max-width: 600px) {
  #showData{
    margin-right: 20px;
  }
}
</style>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$cha}}</p>
        <p class="pass_page">
            <a href="{{$character}}/home" class="first_page"> Home </a>/
            <span class="now_page">{{$cha}}</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 8px 10px;">
        <div class="col-md-12">
            <p class="page_title">Courses of Reviewing</p>
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
             <h5 style="position:relative;margin-top:10px;left: 10px;">Standard Operating Procedure ( SOP )</h5>
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
                <?php
                $num = 1;
                ?>
                @if($character=="/hod")
                  @foreach($action as $row)
                  <tr>
                    <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">{{$num}}</td>
                    <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;">{{$row->subject_code}} {{$row->subject_name}} ( {{$row->name}} )</td>
                    <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">Teaching Plan</td>
                    <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">Approval</td>
                    <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;"><a href="{{$character}}/Reviewer/teachingPlan/{{$row->course_id}}" class="view" target='_blank'><i class="fa fa-long-arrow-right" aria-hidden="true"></i> View</a></td>
                  </tr>
                  <?php
                  $num++;
                  ?>
                  @endforeach
                  @foreach($action2 as $row2)
                  <tr>
                    <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">{{$num}}</td>
                    <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;">{{$row2->subject_code}} {{$row2->subject_name}} ( {{$row2->name}} )</td>
                    <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">Coutinuous Assessment ( CA )</td>
                    <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">Verification</td>
                    <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;"><a href="{{$character}}/Reviewer/Assessment/{{$row2->course_id}}" class="view" target='_blank'><i class="fa fa-long-arrow-right" aria-hidden="true"></i> View</a></td>
                  </tr>
                  <?php
                  $num++;
                  ?>
                  @endforeach
                  @foreach($action3 as $row3)
                  <tr>
                    <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">{{$num}}</td>
                    <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;">{{$row3->subject_code}} {{$row3->subject_name}} ( {{$row3->name}} )</td>
                    <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">Final Assessment ( FA )</td>
                    <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">Verification</td>
                    <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;"><a href="{{$character}}/Reviewer/FinalExamination/{{$row3->course_id}}" class="view" target='_blank'><i class="fa fa-long-arrow-right" aria-hidden="true"></i> View</a></td>
                  </tr>
                  <?php
                  $num++;
                  ?>
                  @endforeach
                @else
                @foreach($action3 as $row3)
                  <tr>
                    <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">{{$num}}</td>
                    <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;">{{$row3->subject_code}} {{$row3->subject_name}} ( {{$row3->name}} )</td>
                    <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">Final Assessment ( FA )</td>
                    <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">Approval For Printing</td>
                    <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;"><a href="/Reviewer/FinalExamination/{{$row3->course_id}}" class="view" target='_blank'><i class="fa fa-long-arrow-right" aria-hidden="true"></i> View</a></td>
                  </tr>
                  <?php
                  $num++;
                  ?>
                  @endforeach
                @endif
                
              </tbody>
            </table>
            </div>
            <hr style="margin: 15px 5px 5px 5px;background-color:black;">
            <div class="row">
                <h5 style="position: relative;top:4px;left: 10px;" class="tp_title col-10" id="1">
                    Courses of Materials (<i class="fa fa-plus" aria-hidden="true" id="icon" style="color: #0d2f81;position: relative;top: 2px;"></i>)
                </h5>
            </div>
            <div class="details" style="padding: 0px 5px;display: none;" id="plan_detail">
                <div class="col-md-6 row" style="padding:0px 20px;position: relative;top: -10px;">
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
                <div class="row" id="course" style="margin-top: -10px;">
                  <div class="col-md-12">
                    <p style="font-size: 18px;margin:0px 0px 0px 10px;">Newest Semester of Courses</p>
                  </div>
                  @foreach($course_reviewer as $row)
                        <a href="{{$character}}/Reviewer/course/{{$row->course_id}}" class="col-md-12 align-self-center" id="course_list">
                          <div class="col-md-12 row" style="padding:10px;color:#0d2f81;">
                            <div class="col-1" style="padding-top: 3px;">
                              <img src="{{url('image/folder2.png')}}" width="25px" height="25px"/>
                            </div>
                            <div class="col" id="course_name">
                              <p style="margin: 0px;"><b>{{$row->semester_name}}</b> : {{$row->short_form_name}} / {{$row->subject_code}} {{$row->subject_name}} ( {{$row->name}} )</p>
                            </div>
                          </div>
                        </a>
                      @endforeach
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="openDocumentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Upload Files</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div style="margin: 20px 20px 0px 20px;">
        <p style="color:#0d2f81; "><b>Template:</b></p>
        <p><b>  1. </b>Please download Template by clicking <a href='{{asset("/templete/multiple_courses.xlsx")}}' id="templete_link">Template</a>.</p>
        <p><b>  2. </b>Delete the example data.</p>
        <p><b>  3. </b>Fill in the Subject details and other details in file.</p>
      </div>
      <form method="post" action="{{route('dropzone.uploadCourses')}}" enctype="multipart/form-data"
        class="dropzone" id="dropzoneFile" style="margin: 20px;font-size: 20px;color:#a6a6a6;border-style: double;">
        @csrf
      </form>
      <div id="showData" style="padding: 0px 20px 20px 20px;overflow-x:auto;">
        <table id="dtBasicExample" style="box-shadow: 0px 2px 5px #aaaaaa;border:none;width:100%;">
          <thead class="tablehead">
            <tr style="height: 60px;text-align: left;">
              <th style="padding-left: 10px;">No</th>
              <th style="padding-left: 10px;">Subject</th>
              <th style="padding-left: 10px;">Credit</th>
              <th style="padding-left: 10px;">Lecturer</th>
              <th style="padding-left: 10px;">Moderator</th>
              <th style="padding-left: 10px;">Verified By</th>
              <th style="padding-left: 10px;">Approved By</th>
            </tr>
          </thead>
        </table>
      </div>
      <div id="errorData" style="padding: 0px 20px 20px 20px;">
        <p><b>Something going wrong. </b>Please Check Again the excel file of data. <br/>(<b>Important : </b>All result cannot be empty, Lecturer and Moderator cannot be same.)</p>
      </div>
      <form method="post" action="{{action('Dean\CourseController@storeCourses')}}">
        {{csrf_field()}}
        <div id="writeInput"></div>
        <div class="modal-footer">
        <button type="button" class="btn btn-raised btn-secondary" data-dismiss="modal">Close</button>
        &nbsp;
        <input type="submit" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;margin-right: 13px;" value="Save Changes">
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
