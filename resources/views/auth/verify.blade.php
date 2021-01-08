@extends('layouts.app')

@section('content')
<style type="text/css">
    #button_link:hover{
        text-decoration: none;
    }
</style>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8" style="margin-left: 0px;">
            <div class="align-self-center">
                <div class="card">
                    <div class="card-header">{{ __('Verify Your Email Address') }}</div>
                    <div class="card-body">
                        @if (session('resent'))
                            <div class="alert alert-success" role="alert">
                                {{ __('A fresh verification link has been sent to your email address.') }}
                            </div>
                        @endif

                        {{ __('Before proceeding, please check your email for a verification link.') }}
                        <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                            @csrf
                            <button type="submit" class="btn btn-link p-0 m-0 align-baseline" style="color: #008075;" id="button_link">{{ __('Click here to request email') }}</button>.
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
