<?php
$title = "Edit Department";
$option4 = "id='selected-sidebar'";
?>
@extends('layouts.nav')

@section('content')
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">Edit Department</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/department_list">Department </a>/
            <span class="now_page">Edit Department</span>/
        </p>
        <hr style="margin: 0px 10px;">
    </div>
    <div class="col-md-12">
        <div class="details" style="padding: 10px 5px 5px 5px;">
            <h5 style="color: #0d2f81;">Edit Department Information</h5>
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

                        <form method="post" action="{{action('DepartmentController@update', $id)}}">
                        {{csrf_field()}}
                        <div class="row">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-address-book" aria-hidden="true" style="font-size: 17px;padding-left: 1px;"></i>
                                </p>
                            </div>
                            <div class="col-11" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="department" class="bmd-label-floating">{{ __('Department ') }}</label>
                                    <input type="text" name="department_name" class="form-control" value="{{$department->department_name}}" id="input">
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
                                        @if($department->faculty_id==$row['faculty_id'])
                                            <option selected value="{{ $row['faculty_id'] }}" class="option">{{$row['faculty_name']}}</option>
                                        @else
                                            <option value="{{ $row['faculty_id'] }}" class="option">{{$row['faculty_name']}}</option>
                                        @endif
                                     @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group" style="text-align: right;margin: 0px!important;">
                            <input type="submit" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;margin: 0px!important;" value="Edit">
                        </div>
                    </form>
                </div>
            </div>
</div>
@endsection