<?php
$title = "Add Programme";
$option5 = "id='selected-sidebar'";
?>
@extends('layouts.nav')

@section('content')
<script type="text/javascript">
    $(function () {
        $("#form_dep").hide();
        $('#department').prop('disabled', true);
        $('#department').selectpicker('refresh');
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
                    if(data!="null"){
                        $("#department").html(data);
                        $('#department').prop('disabled', false);
                        $('#department').selectpicker('refresh');
                        $("#form_dep").show();
                    }else{
                        $('#department').prop('disabled', true);
                        $('#department').selectpicker('refresh');
                    }
               }
            });
        });
    });
</script>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">Add New Programme</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/programme_list">Programme </a>/
            <span class="now_page">Add Programme</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="col-md-12">
        <div class="details" style="padding: 20px 5px 5px 5px;">
            <p class="page_title" style="position: relative;left: 0px ;top: -5px;">Programme Details</p>
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

                @if(\Session::has('failed'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                      <Strong>{{\Session::get('failed')}}</Strong>
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                @endif

                        <form method="post" action="{{route('programme.submit')}}">
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
                                    <input type="text" name="programme_name" class="form-control" id="input" required>
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
                                    <input type="text" name="short_form_name" class="form-control" id="input" required>
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
                                        <option value="Degree" class="option">Degree</option>
                                        <option value="Diploma" class="option">Diploma</option>
                                        <option value="Master" class="option">Master</option>
                                        <option value="Foundation" class="option">Foundation</option>
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
                                            <option value="{{ $row['faculty_id'] }}" class="option">{{$row['faculty_name']}}</option>
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