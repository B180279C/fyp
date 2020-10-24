<?php
$title = "Add Faculty";
$option3 = "id='selected-sidebar'";
?>
@extends('layouts.nav')

@section('content')
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">Edit Faculty</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/faculty_list">Faculty </a>/
            <span class="now_page">Edit Faculty</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="col-md-12">
        <div class="details" style="padding: 20px 5px 5px 5px;">
            <h5 style="color: #0d2f81;">Edit Faculty Information</h5>
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

                        <form method="post" action="{{action('FacultyController@update', $id)}}">
                        {{csrf_field()}}
                        <div class="row">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-home" aria-hidden="true" style="font-size: 20px;"></i>
                                </p>
                            </div>
                            <div class="col-11" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="faculty" class="bmd-label-floating">{{ __('Faculty Name') }}</label>
                                    <input type="text" name="faculty_name" class="form-control" value="{{$faculty->faculty_name}}" id="input">
                                </div>
                            </div>
                        </div>
                        <div class="form-group" style="text-align: right;margin: 0px!important;">
                            <input type="submit" class="btn btn-raised btn-warning" style="background-color: #3C5AFF;color: white;margin: 0px!important;" value="Edit">
                        </div>
                    </form>
                </div>
            </div>
</div>
@endsection