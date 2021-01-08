@extends('layouts.Attendance_login')

@section('content')
<style type="text/css">
    #button_link:hover{
        text-decoration: none;
    }
    @media only screen and (max-width: 600px) {
        #title_side{
            height: 300px;
        }
    }
    @media only screen and (min-width: 600px) {
        #title_side{
            height: 500px;
        }
    }
</style>
<br>
<div class="container" style="box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);">
    <div class="row align-self-center">
        <div class="col-md-4 row" style="padding:0px;box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);background-color: #0d2f81;color: white;width: 100%; margin-left: 0px;" id="title_side">
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
                    <center><h5 style="color: #0d2f81">Sign In</h5></center>
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
                        <form method="post" action="{{ route('attendance.submit') }}">
                        @csrf
                            <input type="hidden" name="attendance_id" value="{{$attendance_id}}">
                            <input type="hidden" name="code" value="{{$code}}">
                            <div class="row">
                                <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                    <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                        <i class="fa fa-user" aria-hidden="true" style="font-size: 20px;"></i>
                                    </p>
                                </div>
                                <div class="col-11" style="padding-left: 20px;">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1" class="bmd-label-floating">Email address</label>
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                    <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                        <i class="fa fa-key" aria-hidden="true"></i>
                                    </p>
                                </div>
                                <div class="col-11" style="padding-left: 20px;">
                                    <div class="form-group">
                                        <label for="exampleInputPassword" class="bmd-label-floating">{{ __('Password') }}</label>
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                                    </div>
                                </div>
                            </div>
                            
                            <br>
                            <div class="form-group">
                                <button type="submit" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;">
                                        {{ __('Login') }}
                                </button>
                                @if (Route::has('password.request'))
                                        <a class="btn btn-link" href="{{ route('password.request') }}" id="button_link" style="color: #008075;">
                                            {{ __('Forgot Your Password?') }}
                                        </a>
                                @endif
                            </div>
                    </form>
                    <br>
                </div>
        </div>
        <div class="col-md-1"></div>
    </div>
</div>
@endsection
