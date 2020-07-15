@extends('layouts.app')

@section('content')
<br>
<div class="container" style="box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);">
    <div class="row align-self-center">
        <div class="col-md-4 row" style="padding:0px;box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);background-color: #0d2f81;color: white;width: 100%; margin-left: 0px;height: 645px;">
            <div class="col-md-1"></div>
            <div class="col-md-10 align-self-center">
                <table>
                    <tr>
                        <td colspan="2"><b style="font-size:32px;color: gold;">University Content Management System</b></td>
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
                        @if(\Session::has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-top: 10px;"">
                          <Strong>{{\Session::get('error')}}</Strong>
                          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        @endif
                        <form method="POST" action="{{ route('login') }}">
                        @csrf
                            <div class="form-group">
                                <label for="exampleInputEmail1" class="bmd-label-floating">Email address</label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword" class="bmd-label-floating">{{ __('Password') }}</label>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                            </div>
                            <br>
                            <div class="form-group">
                                <button type="submit" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;">
                                        {{ __('Login') }}
                                </button>
                                @if (Route::has('password.request'))
                                        <a class="btn btn-link" href="{{ route('password.request') }}">
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
