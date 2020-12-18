@extends('layouts.Attendance_login')

@section('content')
<br>
<div class="container" style="box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);">
    <div class="row align-self-center">
        <div class="col-md-4 row" style="padding:0px;box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);background-color: #0d2f81;color: white;width: 100%; margin-left: 0px;height: 500px;">
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
            <div class="col-md-2"></div>
                <div class="col-md-8 align-self-center">
                    <br>
                    <center><h5 style="color: #0d2f81"></h5></center>
                        @if(\Session::has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-top: 10px;">
                          <Strong>{{\Session::get('success')}}</Strong>
                          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        @endif
                        @if(\Session::has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-top: 10px;">
                          <Strong>{{\Session::get('error')}}</Strong>
                          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        @endif
                        <div style="border:0px solid black;text-align: center;">
                            <img src="{{url('image/success.png')}}" width="150px" height="150px">
                            <p style="font-size: 22px;">You ({{$student_id[0]}}) are take the Attendance <span style="color: green;font-weight: bold;">Successfully.</span></p>
                        </div>
                    <br>
                </div>
        </div>
        <div class="col-md-1"></div>
    </div>
</div>
@endsection
