<?php
$title = "Add Semester";
$option7 = "id='selected-sidebar'";
?>
@extends('layouts.nav')

@section('content')
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">Add New Semester</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/semester_list">Semester </a>/
            <span class="now_page">Add Semester</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="col-md-12">
        <div class="details" style="padding: 10px 0px 5px 0px;">
            <p class="page_title" style="position: relative;left: 5px;">Semester Details</p>
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
                        <form method="post" action="{{route('semester.submit')}}">
                        {{csrf_field()}}
                        <div class="row">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-calendar" aria-hidden="true" style="font-size: 20px;"></i>
                                </p>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="exampleInputEmail1" class="label">Year</label>
                                    <select class="selectpicker form-control" name="year" id="year" data-width="100%" title="Choose One" required>
                                            <option value="<?php echo date('y')-5?>" class="option"><?php echo date('Y')-5?></option>
                                            <option value="<?php echo date('y')-4?>" class="option"><?php echo date('Y')-4?></option>
                                            <option value="<?php echo date('y')-3?>" class="option"><?php echo date('Y')-3?></option>
                                            <option value="<?php echo date('y')-2?>" class="option"><?php echo date('Y')-2?></option>
                                            <option value="<?php echo date('y')-1?>" class="option"><?php echo date('Y')-1?></option>
                                            <option value="<?php echo date('y')?>" class="option"><?php echo date('Y')?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="exampleInputEmail1" class="label">Semester</label>
                                    <select class="selectpicker form-control" name="semester" id="programme" data-width="100%" title="Choose One" required>
                                            <option value="A" class="option">Semester 1</option>
                                            <option value="B" class="option">Semester 2</option>
                                            <option value="C" class="option">Semester 3</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-step-backward" aria-hidden="true" style="font-size: 20px;"></i>
                                </p>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="exampleInputEmail1" class="label">Start Date</label>
                                    <input type="date" class="form-control" name="start_date">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-step-forward" aria-hidden="true" style="font-size: 20px;"></i>
                                </p>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="exampleInputEmail1" class="label">End Date</label>
                                    <input type="date" class="form-control" name="end_date">
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