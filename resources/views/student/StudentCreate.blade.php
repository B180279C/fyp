@extends('layouts.app')

@section('content')
<div class="container" style="box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);">
    <div class="row align-self-center">
        <div class="col-md-4 row" style="padding:0px;box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);background-color: #0d2f81;color: white;width: 100%; margin-left: 0px;height:645px;">
            <div class="col-md-1"></div>
            <div class="col-md-10 align-self-center">
                <table>
                    <tr>
                        <td colspan="2"><b style="font-size:34px;color: gold;font-family: times">University Content Management System</b></td>
                    </tr>
                    <tr>
                        <td><hr></td>
                    </tr>
                    <tr>
                       <td>
                           <img src="{{ url('/image/book2.png') }}" alt="" title="" width="50px" height="30px"/>
                           <b style="color:gold;">Store and Search More Easily.</b><br><span style="font-size: 13px;">No need uses more paper storage. Student and Lecturer can working more comfortable.</span>
                       </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-1"></div>
        </div>
<div class="col-md-8 row" style="margin-left: 0px;">
    <div class="col-md-1"></div>
        <div class="col-md-10 align-self-center">
                <br>
                <center><h5 style="color: #0d2f81">Sign Up</h5></center>
                    @if(count($errors) > 0)
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul>
                                @foreach($errors->all() as $error)
                                    @if($error=="The programme must be a string.")
                                        <li>The programme cannot be empty.</li>
                                    @elseif($error=="The year must be a string.")
                                        <li>The year cannot be empty.</li>
                                    @elseif($error=="The semester must be a string.")
                                        <li>The semester cannot be empty.</li>
                                    @elseif($error=="The intake must be a string.")
                                        <li>The intake cannot be empty.</li>
                                    @else
                                        <li>{{$error}}</li>
                                    @endif
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
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
                    
                        <form method="post" action="{{ route('student.register.submit') }}">
                        {{csrf_field()}}
                        <input type="hidden" name="student_image" value="">
                        <div class="row">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-user" aria-hidden="true" style="font-size: 20px;"></i>
                                </p>
                            </div>
                            <div class="col-10" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="full_name" class="bmd-label-floating">Full Name</label>
                                    <input type="text" name="name" class="form-control" id="input" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-briefcase" aria-hidden="true" style="font-size: 17px;"></i>
                                </p>
                            </div>
                            <div class="col-10" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="Programme" class="label">Programme</label>
                                    <select class="selectpicker form-control" name="programme" id="programme" data-width="100%"data-live-search="true" title="Choose One" required >
                                        @foreach($faculty as $row_faculty)
                                        <optgroup label="{{ $row_faculty['faculty_name']}}">
                                            @foreach($programme as $row)
                                                @if($row_faculty['faculty_id']==$row->faculty_id)
                                                    <option value="{{ $row->programme_id }}" class="option-group">{{$row->short_form_name}} : {{$row->programme_name}}</option>
                                                @endif
                                            @endforeach
                                        </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-calendar" aria-hidden="true" style="font-size: 20px;"></i>
                                </p>
                            </div>
                            <div class="col-10" style="padding-left: 20px;">
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1" class="label">Semester</label>
                                            <select class="selectpicker form-control" name="semester" data-width="100%" title="Choose One" required>
                                                    @foreach($semester as $row_semester)
                                                        <option value="{{ $row_semester->semester_id }}" class="option">{{$row_semester->semester_name}}</option>
                                                    @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1" class="label">Intake</label>
                                            <select class="selectpicker form-control" name="intake" id="programme" data-width="100%" title="Choose One" required>
                                                    <option value="1" class="option">First Year</option>
                                                    <option value="2" class="option">Second Year</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-id-badge" aria-hidden="true" style="font-size: 18px;"></i>
                                </p>
                            </div>
                            <div class="col-10" style="padding-left: 20px;">
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1" class="bmd-label-floating">Student ID</label>
                                            <input type="text" name="student_id" class="form-control student_id" placeholder="" id="input" required onkeyup="myFunction()">
                                        </div>
                                    </div>
                                    <div class="col align-self-end" style="padding: 0px;">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">@sc.edu.my</label></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-lock" aria-hidden="true"></i>
                                </p>
                            </div>
                            <div class="col-10" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="exampleInputEmail1" class="bmd-label-floating">Password</label>
                                    <input type="password" name="password" class="form-control password" id="input" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-lock" aria-hidden="true"></i>
                                </p>
                            </div>
                            <div class="col-10" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="exampleInputEmail1" class="bmd-label-floating">Confirm Password</label>
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" style="color: black;" required onkeyup="check_password()">
                                    <!-- <span class="bmd-help">Please enter again your correct pasword.</span> -->
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="form-group">
                            <input type="submit" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;float:left;">
                        </div>
                    </form>
               <br>
                </div>
        </div>
        <div class="col-md-1"></div>
    </div>
</div>
@endsection
