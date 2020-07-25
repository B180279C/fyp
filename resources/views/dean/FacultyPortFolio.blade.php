<?php
$title = "Department";
$option2 = "id='selected-sidebar'";
?>
@extends('layouts.nav_dean')

@section('content')
<div style="background-color: #f2f2f2">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">Faculty Portfolio</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <span class="now_page">Faculty Portfolio </span>/
        </p>
        <hr style="margin: -10px 10px;">
    </div>
    <div class="row" style="padding: 10px 10px 10px 10px;">
        <div class="col-md-12">
            <div class="details" style="padding: 10px 5px 5px 5px;">
                <h5 style="color: #0d2f81;">Faculty Normal Info</h5>
                <hr style="margin: 0px 0px 15px 0px;">
                <div class="row">
                    <div class="col-md-3">
                        <center><a href="/FacultyPortFolio/CVdepartment" style="border: 1px solid black;padding:50px;display: inline-block;width: 100%">Lecturer CV</a></center>
                    </div>
                    <div class="col-md-3">
                        <center><a href="/FacultyPortFolio/" style="border: 1px solid black;padding:50px;display: inline-block;width: 100%">Syllabus</a></center>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
