<?php
$title = "Course";
$option1 = "id='selected-sidebar'";
?>
@extends('layouts.layout')

@section('content')
<style type="text/css">
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
.dropzone .dz-preview .dz-remove-new{
  text-align: left;
  padding-left: 25px;
  display: inline-block;
  font-size: 14px;
}
#show_image_link:hover{
    text-decoration: none;
}
@media only screen and (max-width: 600px) {
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
}
@media only screen and (min-width: 600px) {
    #course_list{
      margin-left: 0px;
      padding: 4px 15px;
    }
    #course_name{
        margin-left:-50px;
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
}
@media only screen and (min-width: 600px) {
  .tooltiptext{
    width:300px;
    background-color:#e6e6e6;
    color: black;
    text-align: left;
    border-radius: 6px;
    border:1px solid black;
    padding: 5px 10px;
    position: absolute;
    z-index: 1;
    top:38%;
    left:103%;
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
  function isset(element) {
    return element.length > 0;
  }
  $(document).ready(function(){
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
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        $(document).on('click', '#open_folder', function(){
            $('#openFolderModal').modal('show');
        });
        $(document).on('click', '#open_document', function(){
            $('#openDocumentModal').modal('show');
        });

        $(document).on('keyup', '.filename', function(){  
          var id = $(this).attr("id");
          var value = document.getElementById(id).value;
          $("#form"+id).val(value);
        });

        $(document).on('click', '.edit_button', function(){
          var id = $(this).attr("id");
          var num = id.split("_");
          // var main_id = $('#main_id').val();
          // var check_is_main = main_id.split(',');
          // var main = false;
          // for(var m = 0; m<(check_is_main.length-1);m++){
          //   if (num[2]==check_is_main[m]){
          //     var main = true;
          //     if(!confirm("Are you sure want to edit the Main Assessment List? Important: If edited the '(Q & S), and Result Stored In', the question & solution, and student result will change be empty.")){
          //         return false;
          //     }
          //   }
          // }
          $.ajax({
            type:'POST',
            url:'{{$character}}/assessment/AssessmentNameEdit',
            data:{value : num[2]},
            success:function(data){
              if(main==true){
                $('.title_main').html('( Main )');
                $('#main_tf').val('true');
              }else{
                $('.title_main').html('');
                $('#main_tf').val('false');
              }
              var clo = data[0].CLO;
              var stored = data[0].sample_stored;
              var clo_list = clo.split(",");
              var option = "";
              var question = '{{$question}}';
              for(var c = 0;c<=(data[2].length-1);c++){
                var assessment_list = data[2][c].assessment.split('///');
                var markdown = data[2][c].markdown.split(',');
                var assessment = assessment_list[0].split(',');
                for(var i = 0; i<=assessment.length-1;i++){
                  var assessment_rep = assessment[i].replace(' ','');
                  if(assessment_rep==question){
                    if(markdown[i]=="yes"){
                      var selected = false;
                      for(var d = 0;d<=(clo_list.length-1);d++){
                        if(clo_list[d]==("CLO"+(c+1))){
                          var selected = true;
                        }
                      }
                      if(selected==true){
                        option += "<option title='CLO "+(c+1)+"' class='option' value='CLO"+(c+1)+"' selected>CLO "+(c+1)+" : "+data[2][c].CLO+" ( "+data[2][c].domain_level+" , "+data[2][c].PO+" ) </option>";
                      }else{
                        option += "<option title='CLO "+(c+1)+"' class='option' value='CLO"+(c+1)+"'>CLO "+(c+1)+" : "+data[2][c].CLO+" ( "+data[2][c].domain_level+" , "+data[2][c].PO+" ) </option>";
                      }
                    }
                  }
                }
              }

              // var stored_option = "";
              // for(var s = 0;s<=(data[3].length-1);s++){
              //   if(stored==data[3][s].sample_stored){
              //     $('#selected').val(data[3][s].sample_stored);
              //     stored_option += "<option class='option' selected>"+data[3][s].sample_stored+"</option>";
              //   }else{
              //     stored_option += "<option class='option'>"+data[3][s].sample_stored+"</option>";
              //   }
              //   $('#sample').html("");
              //   $('#sample').selectpicker('refresh');
              // }
              // $('#sample').append(stored_option);
              // $('#sample').selectpicker('refresh');

              $("#CLO").html(option);
              $('#CLO').selectpicker('refresh');

              
              // $('#CLO_ALL').val(clo_full);
              var mark = 0;
              for(var i = 0;i<=(data[1].length-1);i++){
                var mark = mark+parseInt(data[1][i].coursework);
              }
              // console.log(mark);
              var full_mark = '{{$coursework}}';
              document.getElementById('ass_id').value = num[2];
              document.getElementById('mark_record').innerHTML = "The {{$question}} of coursework is {{$coursework}}%, It already insert "+(mark-parseInt(data[0].coursework))+"%.";
              document.getElementById('mark_record_2').innerHTML = "So, It Cannot insert over "+(full_mark-(mark-parseInt(data[0].coursework)))+"% of coursework.";
              document.getElementById("coursework").max = (full_mark-(mark-parseInt(data[0].coursework)));
              document.getElementById('folder_name').value = data[0].assessment_name;
              document.getElementById('coursework').value = data[0].coursework;
              document.getElementById('stored_edit').value = data[0].sample_stored;
              
              $('#sample').selectpicker('refresh');
            }
          });
          $('#folderNameEdit').modal('show');
          return false;
        });

        $(document).on('click', '.download_button', function(){
          var id = $(this).attr("id");
          var num = id.split("_");
          window.location = "{{$character}}/assessment/download/"+num[2];
        });

        $(document).on('click', '.remove_button', function(){
          var id = $(this).attr("id");
          var num = id.split("_");
          if(confirm('Are you sure you want to remove the it?')) {
            window.location = "{{$character}}/assessment/remove/"+num[2];
          }     
        });

        $(document).on('click', '#checkDownloadAction', function(){
            var checkedValue = ""; 
            var inputElements = document.getElementsByClassName('group_download');
            for(var i=0; inputElements[i]; i++){
              if(inputElements[i].checked){
                checkedValue += inputElements[i].value+"_";
              }
            }
            if(checkedValue!=""){
              var course_id = $('#course_id').val();
              var id = course_id+"_"+checkedValue;
              window.location = "{{$character}}/assessment/AllZipFiles/"+id+"/checked";
            }else{
              alert("Please select the document first.");
            }
          });

    $(document).on('click', '#checkAction', function(){
      var course_id = $('#course_id').val();
      var question = $('#question').val();
      if(confirm('Are you sure want to use previous semester of assessment list? (Important : If the course is a long semester, you will get the last long semester of the assessment list. On the contrary, if it is a short semester, you will get the last short semester.')) {
        window.location = "{{$character}}/assessment/create/previous/"+course_id+"/"+question;
      }
      return false;
    });
  });


  $(document).on('click', '.add', function(){
    $('#not_add').html('');  
    $('#not_add').append('<div class="form-group"><label for="subject_type" class="label" style="font-size:12px;position:relative;top:8px;">(Q & S),and Result Store in</label><input type="text" name="stored" class="form-control" required/></div>');
    $('.remove').show();
    $('.add').hide();
  });

  $(document).on('click', '.remove', function(){
    $('#not_add').html('');
    var course_id = $('#course_id').val();
    var question = $('#question').val();
    $.ajax({
      type:'POST',
      url:'{{route($route_name.".getSampleStored")}}',
      data:{course_id:course_id,question:question},
      success:function(data){
        $('#not_add').html('');  
        $('#not_add').append('<div class="form-group"><label for="subject_type" class="label" style="font-size:12px;position:relative;top:8px;">(Q & S),and Result Store in</label><select class="selectpicker form-control" name="stored" id="stored" data-width="100%" title="Choose one" required>'+data+'</select></div>');
        $('#stored').selectpicker('refresh');
        $('.remove').hide();
        $('.add').show();
      }
    });
  });

  $(document).on('click', '.edit_add', function(){
    $('#not_add_edit').html('');  
    $('#not_add_edit').append('<div class="form-group"><label for="subject_type" class="label" style="font-size:12px;position:relative;top:8px;">(Q & S),and Result Store in</label><input type="text" name="stored" class="form-control" required/></div>');
    $('.edit_remove').show();
    $('.edit_add').hide();
  });

  $(document).on('click', '.edit_remove', function(){
    $('#not_add_edit').html('');
    var course_id = $('#course_id').val();
    var question = $('#question').val();
    var selected = $('#selected').val();
    $.ajax({
      type:'POST',
      url:'{{route($route_name.".getSampleStoredEdit")}}',
      data:{course_id:course_id,question:question,selected:selected},
      success:function(data){
        $('#not_add_edit').html('');  
        $('#not_add_edit').append('<div class="form-group"><label for="subject_type" class="label" style="font-size:12px;position:relative;top:8px;">(Q & S),and Result Store in</label><select class="selectpicker form-control" name="stored" id="sample" data-width="100%" title="Choose one" required>'+data+'</select></div>');
        $('#sample').selectpicker('refresh');
        $('.edit_remove').hide();
        $('.edit_add').show();
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
          var course_id = $('#course_id').val();
          var question = $('#question').val();
          $.ajax({
              type:'POST',
              url:'{{route($route_name.".searchAssessmentList")}}',
              data:{value:value,course_id:course_id,question:question},
              success:function(data){
                document.getElementById("assessments").innerHTML = data;
              }
          });
        }
        $(".search").keyup(function(){
            var value = $('.search').val();
            var course_id = $('#course_id').val();
            var question = $('#question').val();
            $.ajax({
               type:'POST',
               url:'{{route($route_name.".searchAssessmentList")}}',
               data:{value:value,course_id:course_id,question:question},
               success:function(data){
                  document.getElementById("assessments").innerHTML = data;
               }
            });
        });
    });


</script>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</p>
        <p class="pass_page">
            <a href="{{$character}}/home" class="first_page"> Home </a>/
            <a href="{{$character}}/course_list">Courses </a>/
            <a href="{{$character}}/course/action/{{$course[0]->course_id}}">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</a>/
            <a href="{{$character}}/assessment/{{$course[0]->course_id}}">Continuous Assessment</a>/
            <span class="now_page">{{$question}} ( Q & S )</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 0px 10px;">
        <div class="col-md-12">
             <p class="page_title">{{$question}} ( Q & S )</p>
             @if(count($TP_Ass)!=0)
             <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                <div id="action_sidebar" class="w3-animate-right" style="display: none;width: 260px;">
                    <div style="text-align: right;padding:10px;">
                        <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                  <ul class="sidebar-action-ul">
                      <p class="title_method">Download</p>
                        <a id="checkDownloadAction"><li class="sidebar-action-li"><i class="fa fa-check-square-o" style="padding: 0px 10px;" aria-hidden="true"></i>Checked Item</li></a>
                        <a href='{{$character}}/assessment/AllZipFiles/{{$course[0]->course_id}}_{{$question}}/All'><li class="sidebar-action-li"><i class="fa fa-download" style="padding: 0px 10px;" aria-hidden="true"></i>All Result</li></a>
                  </ul>
                </div>
              @endif
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
            <!-- <h5 style="position:relative;left: 10px;margin-top: -15px;">Assessment List</h5>
            <div style="overflow-x: auto;padding:0px 10px 5px 10px;">
            <table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);border:none;" id="dtBasicExample">
              <thead>
                <tr style="background-color: #d9d9d9;">
                  <th style="border-left:1px solid #e6e6e6;color:black;border-bottom: 1px solid #d9d9d9;text-align: center;">No.</th>
                  <th style="border-left:1px solid #e6e6e6;color:black;border-bottom: 1px solid #d9d9d9;text-align: center;">Assessment Name</th>
                  <th style="border-left:1px solid #e6e6e6;color:black;border-bottom: 1px solid #d9d9d9;text-align: center;">CLO</th>
                  <th style="border-left:1px solid #e6e6e6;color:black;border-bottom: 1px solid #d9d9d9;text-align: center;">Coursework</th>
                  <th style="border-left:1px solid #e6e6e6;color:black;border-bottom: 1px solid #d9d9d9;text-align: center;">Stored</th>
                  <th style="border-left:1px solid #e6e6e6;color:black;border-bottom: 1px solid #d9d9d9;text-align: center;">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $num = 1;
                $main_id = "";
                ?>
                @foreach($assessments as $row)
                  <tr>
                    <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">{{$num}}</td>
                    <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">{{$row->assessment_name}}</td>
                    <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">{{$row->CLO}}</td>
                    <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">{{$row->coursework}}</td>
                    <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">
                      {{$row->sample_stored}}
                    </td>
                    <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">
                      <i class="fa fa-wrench edit_button" aria-hidden="true" id="edit_button_{{$row->ass_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>&nbsp;
                      <i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_{{$row->ass_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>
                    </td>
                  </tr>
                  <?php
                  $num++;
                  ?>
                @endforeach
              </tbody>
            </table>
            <input type="hidden" id="main_id" value="<?php echo $main_id?>">
            </div> -->
            <!-- <hr style="margin: 20px 5px 10px 5px;background-color:black;"> -->
            <!-- <h5 style="position:relative;left: 10px;">Question & Solution</h5> -->
            <div class="details" style="padding: 0px 5px 5px 5px;">
              <div class="col-md-6 row" style="padding:0px 20px;position: relative;top: -25px;">
                  <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                      <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                          <i class="fa fa-search" aria-hidden="true" style="font-size: 20px;"></i>
                      </p>
                  </div>
                  <div class="col-11" style="padding-left: 20px;">
                      <div class="form-group">
                          <label for="full_name" class="bmd-label-floating">Search</label>
                          <input type="hidden" value="{{$course[0]->course_id}}" id="course_id">
                          <input type="hidden" value="{{$question}}" id="question">
                          <input type="text" name="search" class="form-control search tooltip_hover" id="input" style="font-size: 18px;">
                          <span class="tooltiptext">
                              <span>
                                  <i class="fa fa-info-circle" style="color: #0d2f81;" aria-hidden="true"></i> Important : 
                              </span>
                              <hr style="background-color: #d9d9d9;margin: 3px 0px;">
                              <span>1. Q & S stored in {{$question}}</span><br/>
                          </span>
                      </div>
                  </div>
              </div>
              
              <div class="row" id="assessments" style="margin-top: -25px;">
              <?php
              $i=0;
              ?>
              @foreach($sample_stored as $row)
                <div class="col-12 row align-self-center" id="course_list">
                    <div class="col-12 row align-self-center" style="padding-left: 20px;">
                      <div class="checkbox_style align-self-center">
                        <input type="checkbox" name="group{{$row->ass_id}}" value="{{$row->ass_id}}" class="group_download">
                      </div>
                      <a href='{{$character}}/assessment/view_list/{{$row->ass_id}}' class="col-11 row" style="padding:10px 0px;margin-left:0px;color:#0d2f81;border:0px solid black;" id="show_image_link">
                        <div class="col-1" style="position: relative;top: -2px;">
                          <img src="{{url('image/file.png')}}" width="20px" height="25px"/>
                        </div>
                        <div class="col-10" id="course_name">
                          <p style="margin: 0px 0px 0px 5px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>{{$row->sample_stored}}</b></p>
                        </div>
                      </a>
                    </div>
                </div>
              <?php
              $i++;
              ?>
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

<style type="text/css">
  .content2{
    position:relative;
    background-color:#fff!important;
    background-clip:padding-box;
    border-radius:3px;
    -webkit-box-shadow:0 3px 9px rgba(0,0,0,.5)!important;
    box-shadow:0 3px 9px rgba(0,0,0,.5)!important;
    outline:0;
  }
  .header2{
    padding: 10px!important;
    margin: 0px!important;
    border:none!important;
  }
  .title2{
    font-size: 21.6px!important;
  }
  .body2{
    padding-top: 20px!important;
  }
</style>

<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="openFolderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content content2">
      <div class="modal-header header2">
        <h5 class="modal-title title2" id="exampleModalLabel">Open New Assessment</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="post" action="{{$character}}/assessment/openNewAssessment">
        {{csrf_field()}}
      <div class="modal-body body2">
        <div id="message"></div>
        <br>
        <div class="row">
            <div class="col-md-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                    <i class="fa fa-file" aria-hidden="true" style="font-size: 18px;"></i>
                </p>
            </div>
                <div class="col-11" style="padding-left: 20px;">
                <div class="form-group">
                      <label for="subject_type" class="bmd-label-floating">Assessment Name</label>
                      <input type="hidden" name="course_id" value="{{$course[0]->course_id}}">
                      <input type="text" name="assessment_name" class="form-control" required/>
                      <input type="hidden" name="assessment" value="{{$question}}">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                    <i class="fa fa-list-alt" aria-hidden="true" style="font-size: 18px;"></i>
                </p>
            </div>
                <div class="col-11" style="padding-left: 20px;">
                <div class="form-group">
                      <label for="subject_type" class="bmd-label-floating">Course Learning Outcome ( CLO )</label>
                      <select class="selectpicker form-control" name="CLO[]" data-width="100%" title="Choose one" multiple required>
                        <?php
                          $num = 1;
                          $check = "";
                          foreach($TP_Ass as $row){
                            $assessment_list = explode('///',$row->assessment);
                            $markdown = explode(',',$row->markdown);
                            $assessment = explode(',',$assessment_list[0]);
                            for($i = 0; $i<=count($assessment)-1;$i++){
                              $assessment_rep = str_replace(' ','',$assessment[$i]);
                              if($assessment_rep==$question){
                                if($markdown[$i]=="yes"){
                                  $check .= 'CLO'.$num.',';
                                  echo "<option title='CLO ".$num."' class='option' value='CLO".$num."'>CLO ".$num." : ".$row->CLO." ( ".$row->domain_level." , ".$row->PO." ) </option>";
                                }
                              }
                            }
                            $num++;
                          }
                      ?>
                      </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                    <i class="fa fa-percent" aria-hidden="true" style="font-size: 18px;"></i>
                </p>
            </div>
                <div class="col-11" style="padding-left: 20px;">
                <div class="form-group">
                      <label for="subject_type" class="bmd-label-floating">Coursework</label>
                      <input type="number" name="coursework" min="0" max="{{$coursework-$mark}}" class="form-control" required/>
                      <input type="hidden" name="total" value="{{$coursework}}">
                      <span class="bmd-help">The {{$question}} of coursework is {{$coursework}}%, It already insert {{$mark}}%.</span>
                      <span class="bmd-help">So, It Cannot insert over {{$coursework-$mark}}% of coursework.</span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-1 align-self-center" style="padding: 25px 0px 0px 2%;">
                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                    <i class="fa fa-folder" aria-hidden="true" style="font-size: 18px;"></i>
                </p>
            </div>
            @if((count($assessments)==0))
            <div id="empty_now" class="col-11" style="padding-left: 20px;">
                <div class="form-group">
                    <label for="subject_type" class="bmd-label-floating">(Q & S), Result Store in</label>
                    <input type="text" name="stored" class="form-control" required/>
                </div>
            </div>
            @else
            <div id="not_empty" class="col-11 row">
                <div class="col-11" style="padding-left: 20px;" id="not_add">
                  <div class="form-group">
                    <label for="subject_type" class="bmd-label-floating" style="font-size: 12px;">(Q & S),and Result Store in</label>
                    <select class="selectpicker form-control" id="stored" name="stored" data-width="100%" title="Choose one" required>
                      @foreach($sample_stored as $row)
                      <option class="option">{{$row->sample_stored}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-1 align-self-center" style="padding: 25px 0px 0px 2%;">
                    <button type="button" class="btn btn-raised btn-success add"><i class="fa fa-plus" aria-hidden="true"></i></button>
                    <button type="button" class="btn btn-raised btn-danger remove" style="display: none;"><i class="fa fa-times" aria-hidden="true"></i></button>
                </div>
            </div>
            @endif
        </div>
        <br>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-raised btn-secondary" data-dismiss="modal">Close</button>
        &nbsp;
        <input type="submit" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;margin: 0px!important;" value="Save Changes">
      </div>
      </form>
    </div>
  </div>
</div>


<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="folderNameEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content content2">
      <div class="modal-header header2">
        <h5 class="modal-title title2" id="exampleModalLabel">Edit Folder Name <span class="title_main"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="post" action="{{$character}}/assessment/updateAssessmentName">
        {{csrf_field()}}
      <div class="modal-body body2">
        <div id="message"></div>
        <br>
        <div class="row">
            <div class="col-md-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                    <i class="fa fa-folder" aria-hidden="true" style="font-size: 18px;"></i>
                </p>
            </div>
                <div class="col-11" style="padding-left: 20px;">
                <div class="form-group">
                      <label for="subject_type" class="label">Assessment Name</label>
                      <input type="hidden" name="ass_id" id="ass_id">
                      <input type="text" name="assessment_name" class="form-control" id="folder_name" placeholder="Folder" required/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                    <i class="fa fa-list-alt" aria-hidden="true" style="font-size: 18px;"></i>
                </p>
            </div>
                <div class="col-11" style="padding-left: 20px;">
                <div class="form-group">
                      <label for="subject_type" class="bmd-label-floating">Course Learning Outcome ( CLO )</label>
                      <select class="selectpicker form-control" name="CLO[]" data-width="100%" id="CLO" title="Choose one" multiple required>
                      </select>
                      <input type="hidden" name="CLO_ALL" id="CLO_ALL">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                    <i class="fa fa-percent" aria-hidden="true" style="font-size: 18px;"></i>
                </p>
            </div>
                <div class="col-11" style="padding-left: 20px;">
                <div class="form-group">
                      <label for="subject_type" class="label">Coursework</label>
                      <input type="hidden" name="total" value="{{$coursework}}">
                      <input type="number" name="coursework" min="0" max="{{$coursework-$mark}}" class="form-control" id="coursework" required/>
                      <span class="bmd-help" id="mark_record">The {{$question}} of coursework is {{$coursework}}%, It already insert {{$mark}}%.</span>
                      <span class="bmd-help" id="mark_record_2">So, It Cannot insert over {{$coursework-$mark}}% of coursework.</span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-1 align-self-center" style="padding: 25px 0px 0px 2%;">
                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                    <i class="fa fa-folder" aria-hidden="true" style="font-size: 18px;"></i>
                </p>
            </div>
            <div id="not_empty" class="col-11" style="padding-left: 20px;">
              <div id="not_add_edit">
                  <div class="form-group">
                    <label for="subject_type" class="label" style="font-size: 12px;">(Q & S),and Result Store in</label>
                    <!-- <select class="selectpicker form-control" name="stored" data-width="100%" id="sample" title="Choose one"required>
                    </select> -->
                    <input type="text" name="stored" class="form-control" id="stored_edit" readonly/>
                  </div>
              </div>
              <!-- <div class="col-1 align-self-center" style="padding: 25px 0px 0px 2%;">
                <button type="button" class="btn btn-raised btn-success edit_add"><i class="fa fa-plus" aria-hidden="true"></i></button>
                <button type="button" class="btn btn-raised btn-danger edit_remove" style="display: none;"><i class="fa fa-times" aria-hidden="true"></i></button>
              </div> -->
            </div>
            <input type="hidden" value="" id="selected">
            <input type="hidden" name="main_tf" id="main_tf">
        </div>
        <br>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-raised btn-secondary" data-dismiss="modal">Close</button>
        &nbsp;
        <input type="submit" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;margin: 0px!important;" value="Save Changes">
      </div>
      </form>
    </div>
  </div>
</div>
@endsection