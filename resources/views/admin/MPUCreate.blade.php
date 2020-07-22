<?php
$title = "Add MPU Subject";
$option6 = "id='selected-sidebar'";
?>
@extends('layouts.nav')

@section('content')
<script type="text/javascript">
    $(document).ready(function(){  
      var i=document.getElementById("count").value;
      $('#subject_type').click(function(){  
           i++;
           document.getElementById("count").value = i;
           $('#type').append('<div id="dynamic_field'+i+'"><div><label class="col-md-11 align-self-center" style="padding-left: 0px;">'+i+') Subject Classification</label><button type="button" name="remove" id="'+i+'" class="col-md-1 btn btn-raised btn-danger btn_remove">Remove</button></div> <div class="row"><div class="col-md-2 align-self-center"></div><div class="col-md-8 align-self-center" style="padding: 15px 0px 0px 0px;"><div class="form-group" style=""><label for="subject" style="font-size:12px" class="label">Subject Type: </label><input type="text" name="subject_type'+i+'" class="form-control" placeholder="Category of subject" required/></div></div><div class="col-md-2 align-self-center" style="padding: 20px 0px 0px 5px;"><a class="btn btn-raised btn-success btn_add_list" name="add" id="'+i+'" ><i class="fa fa-plus" style="color:white;"></i></a></div></div><div class="row"><div class="col-md-2 align-self-center"></div><div class="col-md-8 row align-self-center"><div class="form-group col-md-3"><label for="subject" style="font-size:12px" class="label">Code: </label><input type="text" name="'+i+'subject_code1" class="form-control" placeholder="Subject Code" required/></div><div class="form-group col-md-7"><label for="subject" style="font-size:12px" class="label">Name: </label><input type="text" name="'+i+'subject_name1" class="form-control" placeholder="Subject Name" required/></div></div></div><input type="hidden" name="count_list'+i+'" id="count_list'+i+'" value="1"></div></div></div></div><div id="hr'+i+'"><br><hr></div>');  
      });
      $(document).on('click', '.btn_remove', function(){  
           var button_id = $(this).attr("id");   
           $('#dynamic_field'+button_id+'').remove(); 
           $('#hr'+button_id).remove(); 
      });

      $(document).on('click', '.btn_add_list', function(){ 
           var button_id = $(this).attr("id");
           var count_list = document.getElementById('count_list'+button_id).value;
           count_list++;
           document.getElementById('count_list'+button_id).value = count_list;
           $('#dynamic_field'+button_id).append('<div class="row" id="list_row'+button_id+'"><div class="col-md-2 align-self-center"></div><div class="col-md-8 row align-self-center"><div class="form-group col-md-3"><label for="subject" style="font-size:12px" class="label">Code: </label><input type="text" name="'+button_id+'subject_code'+count_list+'" class="form-control" placeholder="Subject Code" required/></div><div class="form-group col-md-7"><label for="subject" style="font-size:12px" class="label">Name: </label><input type="text" name="'+button_id+'subject_name'+count_list+'" class="form-control" placeholder="Subject Name" required/></div><div class="col-md-2 align-self-center" style="padding: 20px 0px 0px 5px;"><button type="button" name="remove" id="'+button_id+'" class="btn btn-raised btn-danger btn_remove_list"><i class="fa fa-times" aria-hidden="true" style="color:white;"></button></div></div></div>');  
      });
      $(document).on('click', '.btn_remove_list', function(){  
           var button_id = $(this).attr("id");   
           $('#list_row'+button_id).remove();  
      });

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $(document).on('click', '.open-modal', function(){
    var gs_id = $(this).attr("id");

    $.ajax({
      type:'POST',
      url:'/generalStudiesEditModal',
      data:{value : gs_id},

      success:function(data){
         document.getElementById('gs_id_modal').value = gs_id;
         document.getElementById('subject_code_modal').value = data.subject_code;
         document.getElementById('subject_name_modal').value = data.subject_name;
      }
    });
    $('#subjectModal').modal('show');
  });

  $(document).on('click', '.open-modal2', function(){
    var id = $(this).attr("id");
    var subject_type = document.getElementById('subject_type'+id).value;
    document.getElementById('subject_type_modal').value = subject_type;
    document.getElementById('same').value = subject_type;
    $('#subjectTypeModal').modal('show');
  });
 });  
</script>
<div style="background-color: #f2f2f2">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">Add New Subject</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/mpu_list">General Studies</a>/
            <span class="now_page">Add MPU Subject</span>/
        </p>
        <hr style="margin: 0px 10px;">
    </div>
    <div class="col-md-12">
        <div class="details" style="padding: 10px 5px 5px 5px;">
            <h5 style="color: #0d2f81;">Add MPU Subject Information</h5>
            <hr style="margin: 0px;">
                    @if(count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{$error}}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if(\Session::has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                      <Strong>{{\Session::get('success')}}</Strong>
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    @endif

                        <form method="post" action="{{action('MPUController@store', $level)}}">
                        {{csrf_field()}}
                        <div style="text-align: right;margin-top: 10px;">
                            <button type="button" name="add" id="subject_type" class="btn btn-raised btn-primary" style="margin: 0px!important;">Add Subject List</button>
                        </div>
                        <div class="row">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-tasks" aria-hidden="true" style="font-size: 18px;"></i>
                                </p>
                            </div>
                            <div class="col-11" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="programme_name" class="bmd-label-floating">Level</label>
                                    <input type="text" name="programme_name" value="{{$level}}" class="form-control" id="input" readonly style="font-weight: bold;">
                                </div>
                            </div>
                        </div>
                        <hr>
                        <?php
                        $i = 1;
                        ?>
                        <div id="type">
                        @foreach($group as $row_group)
                            <div id="dynamic_field<?php echo $i?>">
                                <label class="col-12 align-self-center" style="padding-left: 0px;"><?php echo $i?>) Subject Classification</label>
                              <div class="row">
                                  <div class="col-md-2 align-self-center"></div>
                                  <div class="col-md-8 align-self-center" style="padding: 15px 0px 0px 0px;">
                                    <div class="form-group" style="">
                                        <label for="subject" class="bmd-label-floating">Subject Type: </label>
                                        <input type="text" name="subject_type<?php echo $i?>" value="{{$row_group->subject_type}}" class="form-control" id="subject_type<?php echo $i?>" readonly/>
                                    </div>
                                  </div>
                                  <div class="col-md-2 align-self-center" style="padding: 20px 0px 0px 5px;">
                                        <a class="btn btn-raised btn-info open-modal2" id="<?php echo $i?>"><i class="fa fa-pencil" aria-hidden="true" style="color:white;"></i>
                                        </a>
                                        <a class="btn btn-raised btn-success btn_add_list" name="add" id="<?php echo $i?>" ><i class="fa fa-plus" style="color:white;"></i></a>
                                  </div>
                                </div>
                                <?php
                                $m = 1;
                                ?>
                                @foreach($subjects as $row)
                                  @if($row_group->subject_type == $row->subject_type)
                                  <div class="row list_row">
                                    <div class="col-md-2 align-self-center"></div>
                                      <div class="col-md-8 row align-self-center">
                                          <div class="form-group col-md-3">
                                              <label for="subject" class="bmd-label-floating" style="padding-left: 15px">Code: </label>
                                              <input type="text" name="<?php echo $i?>subject_code<?php echo $m?>" placeholder="Code" class="form-control" value="{{$row->subject_code}}" disabled/>
                                          </div>
                                          <div class="form-group col-md-7">
                                              <label for="subject" class="bmd-label-floating" style="padding-left: 15px">Subject Name: </label>
                                              <input type="text" name="<?php echo $i?>subject_name<?php echo $m?>" placeholder="Subject Name" class="form-control" value="{{$row->subject_name}}" disabled/>
                                          </div>
                                          <div class="col-md-2 align-self-center" style="padding: 20px 0px 0px 5px;">
                                            <a class="btn btn-raised btn-primary open-modal " id="{{$row->mpu_id}}"><i class="fa fa-pencil" aria-hidden="true" style="color:white;"></i>
                                            </a>
                                          </div>
                                      </div>
                                  </div>
                                  <?php
                                    $m++;
                                  ?>
                                  @endif
                                @endforeach
                                <input type="hidden" name="count_list<?php echo $i?>" id="count_list<?php echo $i?>" value="<?php echo ($m-1)?>">
                            </div>
                            <br>
                          <hr>
                          <?php 
                            $i++;
                          ?>
                        @endforeach
                        </div>
                        <input type="hidden" name="count" id="count" value='<?php echo ($i-1)?>'>
                        <div class="form-group" style="text-align: right;margin: 0px!important;">
                            <input type="submit" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;margin: 0px!important;">
                        </div>
                    </form>
                </div>
            </div>
</div>

<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="subjectModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit Subject Information</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="post" action="{{action('MPUController@generalStudiesUpdateModal')}}">
        {{csrf_field()}}
      <div class="modal-body">
        <div id="message"></div>
        <br>
        <div class="row">
            <div class="col-md-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                    <i class="fa fa-sticky-note" aria-hidden="true" style="font-size: 18px;"></i>
                </p>
            </div>
            <input type="hidden" name="mpu_id" id="gs_id_modal">
              <div class="col-md-11 row" style="padding-left: 20px;">
                <div class="form-group col-md-4">
                    <label for="subject" class="label" style="padding-left: 15px">Code: </label>
                    <input type="text" name="subject_code" placeholder="Code" class="form-control" id="subject_code_modal" required/>
                </div>
                <div class="form-group col-md-8">
                    <label for="subject" class="label" style="padding-left: 15px">Subject Name: </label>
                    <input type="text" name="subject_name" id="subject_name_modal" placeholder="Subject Name" class="form-control" value="" required/>
                </div>
            </div>
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
<div class="modal fade bd-example-modal-lg" id="subjectTypeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit Subject Information</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="post" action="{{action('MPUController@generalStudiesTypeUpdateModal')}}">
        {{csrf_field()}}
      <div class="modal-body">
        <div id="message_type"></div>
        <br>
        <div class="row">
            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                    <i class="fa fa-sticky-note-o" aria-hidden="true" style="font-size: 18px;"></i>
                </p>
            </div>
              <div class="col-11" style="padding-left: 20px;">
                <div class="form-group">
                      <label for="subject_type" class="label">Subject Type</label>
                      <input type="text" name="subject_type" id="subject_type_modal" class="form-control" placeholder="Category of Subject" required/>
                      <input type="hidden" name="same" id="same">
                      <input type="hidden" name="level" value="<?php echo $level?>">
                </div>
            </div>
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
