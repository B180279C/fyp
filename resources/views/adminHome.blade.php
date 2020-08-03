<?php
    $title = "Home";
    $option0 = "id='selected-sidebar'";
?>
@extends('layouts.nav')
   
@section('content')
<br>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Dashboard</div>
                <div class="card-body">
                    You are Admin.
                    <br>
                    <a href="{{route('admin.semester_list.index')}}">Semester List</a>
                    <br>
                    <a href="{{route('admin.student_list.index')}}">Student List</a>
                    <br>
                    <a href="{{route('admin.staff_list.index')}}">Staff List</a>
                    <br>
                    <a href="{{route('admin.programme_list.index')}}">Programme List</a>
                    <br>
                    <a href="{{route('admin.faculty_list.index')}}">Faculty List</a>
                    <br>
                    <a href="{{route('admin.department_list.index')}}">Department List</a>
                    <br>
                    <a href="{{route('admin.mpu_list.index')}}">General Studies</a>
                </div>
            </div>
        </div>
    </div>
<br>
@endsection
