@extends('layouts.app')

@section('content');
<script type="text/javascript">
    $(document).ready(function(){  
      var i=document.getElementById("count").value;
      $('#subject_type').click(function(){  
           i++;
           document.getElementById("count").value = i;
           $('#type').append('<div id="dynamic_field'+i+'"><div style="text-align:right;"><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">Remove</button><div class="form-group row"><label for="subject" class="col-md-4 col-form-label text-md-right">'+i+') Subject : </label><div class="col-md-4" style="padding: 0px"><input type="text" name="subject_type'+i+'" placeholder="Subject Type" class="form-control" required/></div><div class="col-md-1"><a class="btn btn-success btn_add_list" name="add" id="'+i+'" ><i class="fa fa-plus" style="color:white;"></i></a></div></div><div class="form-group row list_row"><label for="subject" class="col-md-4 col-form-label text-md-right"></label><div class="col-md-8 row"><div class="col-md-3" style="padding: 1px"><input type="text" name="'+i+'subject_code1" placeholder="Code" class="form-control" required/></div><div class="col-md-7" style="padding: 1px"><input type="text" name="'+i+'subject_name1" placeholder="Subject Name" class="form-control" required/></div><input type="hidden" name="count_list'+i+'" id="count_list'+i+'" value="1"></div></div></div></div><div id="hr'+i+'"><br><hr></div>');  
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
           $('#dynamic_field'+button_id).append('<div class="form-group row" id="list_row'+button_id+'"><label for="subject" class="col-md-4 col-form-label text-md-right"></label><div class="col-md-8 row"><div class="col-md-3" style="padding: 1px"><input type="text" name="'+button_id+'subject_code'+count_list+'" placeholder="Code" class="form-control" required/></div><div class="col-md-7" style="padding: 1px"><input type="text" name="'+button_id+'subject_name'+count_list+'" placeholder="Subject Name" class="form-control" required/></div><div class="col-md-1" style="padding: 1px"><button type="button" name="remove" id="'+button_id+'" class="btn btn-danger btn_remove_list"><i class="fa fa-times" aria-hidden="true" style="color:white;"></button></div></div></div>');  
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
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card">
                <div class="card-header">{{ __('Add MPU Subject Information') }}</div>
                <div class="card-body">
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
                        <div style="text-align: right;">
                            <button type="button" name="add" id="subject_type" class="btn btn-warning">Add Subject List</button>
                        </div>
                        <div class="form-group row">
                            <label for="level" class="col-md-4 col-form-label text-md-right">{{ __('Level : ') }}</label>
                            <div class="col-md-6 col-form-label text-md-left">
                                <strong>{{$level}}</strong>
                            </div>
                        </div>
                        <hr>
                        <?php
                        $i = 1;
                        ?>
                        <div id="type">
                        @foreach($group as $row_group)
                            <div id="dynamic_field<?php echo $i?>">
                                <div class="form-group row">
                                    <label for="subject" class="col-md-4 col-form-label text-md-right" ><?php echo $i?>) Subject : </label>
                                    <div class="col-md-4" style="padding: 0px">
                                        <input type="text" name="subject_type<?php echo $i?>" placeholder="Subject Type" value="{{$row_group->subject_type}}" class="form-control" id="subject_type<?php echo $i?>" readonly/>
                                    </div>
                                    <div class="col-md-3 row">
                                      <div class="col-md-3">
                                        <a class="btn btn-secondary open-modal2 " id="<?php echo $i?>"><i class="fa fa-pencil" aria-hidden="true" style="color:white;"></i>
                                        </a>
                                      </div>
                                      <div class="col-md-1">
                                        <a class="btn btn-success btn_add_list" name="add" id="<?php echo $i?>" ><i class="fa fa-plus" style="color:white;"></i></a>
                                      </div>
                                    </div>
                                </div>
                                <?php
                                $m = 1;
                                ?>
                                @foreach($subjects as $row)
                                  @if($row_group->subject_type == $row->subject_type)
                                  <div class="form-group row list_row">
                                      <label for="subject" class="col-md-4 col-form-label text-md-right"></label>
                                      <div class="col-md-8 row">
                                          <div class="col-md-3" style="padding: 1px">
                                              <input type="text" name="<?php echo $i?>subject_code<?php echo $m?>" placeholder="Code" class="form-control" value="{{$row->subject_code}}" disabled/>
                                          </div>
                                          <div class="col-md-7" style="padding: 1px">
                                              <input type="text" name="<?php echo $i?>subject_name<?php echo $m?>" placeholder="Subject Name" class="form-control" value="{{$row->subject_name}}" disabled/>
                                          </div>
                                          <div class="col-md-1" style="padding: 1px">
                                            <a class="btn btn-secondary open-modal " id="{{$row->mpu_id}}"><i class="fa fa-pencil" aria-hidden="true" style="color:white;"></i>
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
                        <br>
                        <div class="form-group" style="text-align: center;">
                                <input type="submit" class="btn btn-primary" value="Submit">
                        </div>
                    </form>
                </div>
            </div>
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
            <div class="col-md-3" style="text-align: right;">
              <label for="subject" class="col-form-label">Subject : </label>
            </div>
            <input type="hidden" name="mpu_id" id="gs_id_modal">
            <div class="col-md-8 row">
              <div class="col-md-4" style="padding: 1px">
                <input type="text" name="subject_code" id="subject_code_modal" placeholder="Code" class="form-control" value="" required/>
              </div>
              <div class="col-md-8" style="padding: 1px">
                <input type="text" name="subject_name" id="subject_name_modal" placeholder="Subject Name" class="form-control" value="" required/>
              </div>
          </div>
        </div>
        <br>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <input type="submit" class="btn btn-primary" value="Sava Changes">
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
            <div class="col-md-3" style="text-align: right;">
              <label for="subject" class="col-form-label">Subject : </label>
            </div>
            <div class="col-md-5 row">
              <input type="hidden" name="same" id="same">
              <input type="hidden" name="level" value="<?php echo $level?>">
              <input type="text" name="subject_type" id="subject_type_modal" placeholder="type" class="form-control" value="" required/>
          </div>
        </div>
        <br>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <input type="submit" class="btn btn-primary" value="Sava Changes">
      </div>
      </form>
    </div>
  </div>
</div>

@endsection
