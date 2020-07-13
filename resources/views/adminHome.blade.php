@extends('layouts.app')
   
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>
                <div class="card-body">
                    You are Admin.
                    <br>
                    <a href="{{route('admin.student_list.index')}}">Student List</a>
                    <br>
                    <a href="{{route('admin.staff_list.index')}}">Staff List</a>
                    <br>
                    <a href="{{route('admin.programme_list.index')}}">Programme List</a>
                    <br>
                    <a href="{{route('admin.academic_list.index')}}">Academic List</a>
                    <br>
                    <a href="{{route('admin.department_list.index')}}">Department List</a>
                    <br>
                    <a href="{{route('admin.gs_list.index')}}">General Studies</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
