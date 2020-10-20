<?php
$title = "Edit Programme";
$option5 = "id='selected-sidebar'";
?>
@extends('layouts.nav')

@section('content')
<script type="text/javascript">
    $(function () {
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });

        $('#faculty').change(function(){
            var value = $('#faculty').val();
            $.ajax({
               type:'POST',
               url:'/staffFaculty',
               data:{value:value},

               success:function(data){
                  $("#department").html(data);
                  $('#department').selectpicker('refresh');
               }
            });
        });
    });
</script>

<?php
foreach($department as $row){
    if($programme->department_id==$row['department_id']){
        $faculty_id = $row['faculty_id'];
    }
}
?>
<div style="background-color:white;">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">Edti Programme</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/programme_list">Programme </a>/
            <span class="now_page">Edit Programme</span>/
        </p>
        <hr style="margin: 0px 10px;">
    </div>
    <div class="col-md-12">
        <div class="details" style="padding: 10px 5px 5px 5px;">
            <h5 style="color: #0d2f81;">Edit Programme Details</h5>
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

                        <form method="post" action="{{action('ProgrammeController@update', $id)}}">
                        {{csrf_field()}}

                        <div class="row">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-tasks" aria-hidden="true" style="font-size: 18px;"></i>
                                </p>
                            </div>
                            <div class="col-11" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="programme_name" class="bmd-label-floating">Programme Name</label>
                                    <input type="text" name="programme_name" class="form-control" id="input" value="{{$programme->programme_name}}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-thumb-tack" aria-hidden="true" style="font-size: 20px;"></i>
                                </p>
                            </div>
                            <div class="col-11" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="short_form_name" class="bmd-label-floating">Short Form Name</label>
                                    <input type="text" name="short_form_name" value="{{$programme->short_form_name}}" class="form-control" id="input" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-list-ol" aria-hidden="true" style="font-size: 18px;"></i>
                                </p>
                            </div>
                            <div class="col-11" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="level" class="label">Level</label>
                                    <select class="selectpicker form-control" name="level" id="level" data-width="100%" title="Choose one" required>
                                        <option <?php if($programme->level == "Degree"){ echo 'selected';}?> value="Degree" class="option">Degree</option>
                                        <option <?php if($programme->level == "Diploma"){ echo 'selected';}?> value="Diploma" class="option">Diploma</option>
                                        <option <?php if($programme->level == "Master"){ echo 'selected';}?> value="Master" class="option">Master</option>
                                        <option <?php if($programme->level == "Foundation"){ echo 'selected';}?> value="Foundation" class="option">Foundation</option>
                                    </select>
                                </div>
                            </div>
                        </div>
 
                        <div class="row">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-home" aria-hidden="true" style="font-size: 20px;"></i>
                                </p>
                            </div>
                            <div class="col-11" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="faculty" class="label">{{ __('Faculty') }}</label>
                                    <select class="selectpicker form-control" name="faculty" id="faculty" data-width="100%" title="Choose one" required>
                                        @foreach($faculty as $row)
                                        @if($faculty_id==$row['faculty_id'])
                                            <option selected value="{{ $row['faculty_id'] }}" class="option">{{$row['faculty_name']}}</option>
                                        @else
                                            <option value="{{ $row['faculty_id'] }}" class="option">{{$row['faculty_name']}}</option>
                                        @endif
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-address-book" aria-hidden="true" style="font-size: 17px;padding-left: 1px;"></i>
                                </p>
                            </div>
                            <div class="col-11" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="department" class="label">{{ __('Department ') }}</label>
                                    <select class="selectpicker form-control" name="department" data-width="100%" title="Choose one" data-live-search="true" id="department" required>
                                        @foreach($faculty as $row_faculty)
                                        <optgroup label="{{ $row_faculty['faculty_name']}}">
                                            @foreach($department as $row)
                                                @if($row['faculty_id']==$row_faculty['faculty_id'])
                                                    @if($programme->department_id==$row['department_id'])
                                                        <option selected value="{{ $row['department_id'] }}" class="option">{{$row['department_name']}}</option>
                                                    @else
                                                        <option  value="{{ $row['department_id'] }}" class="option">{{$row['department_name']}}</option>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group" style="text-align: right;margin: 0px!important;">
                            <input type="submit" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;margin: 0px!important;">
                        </div>
                    </form>
                </div>
            </div>
</div>
@endsection
