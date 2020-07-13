@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card">
                <div class="card-header">{{ __('Student Registration') }}</div>
                <div class="card-body">
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
                        <div class="form-group row">
                            <label for="batch" class="col-md-4 col-form-label text-md-right">{{ __('Name : ') }}</label>
                            <div class="col-md-6">
                                <input type="text" name="name" class="form-control" placeholder="Full Name">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="department" class="col-md-4 col-form-label text-md-right">{{ __('Programme : ') }}</label>
                            <div class="col-md-6">
                                <select class="selectpicker" name="programme" id="programme" data-width="100%" title="Choose one" data-live-search="true">
                                    @foreach($academic as $row_academic)
                                    <optgroup label="{{ $row_academic['academic_name']}}">
                                        @foreach($programme as $row)
                                            @if($row_academic['academic_id']==$row->academic_id)
                                                <option  value="{{ $row->programme_id }}">{{$row->short_form_name}} : {{$row->programme_name}}</option>
                                            @endif
                                        @endforeach
                                    </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="full_name" class="col-md-4 col-form-label text-md-right">{{ __('Year/Intake : ') }}</label>
                            <div class="col-md-2">
                                <select class="selectpicker" name="year" id="programme" data-width="100%" title="Year">
                                    <option value="<?php echo date('y')-5?>"><?php echo date('Y')-5?></option>
                                    <option value="<?php echo date('y')-4?>"><?php echo date('Y')-4?></option>
                                    <option value="<?php echo date('y')-3?>"><?php echo date('Y')-3?></option>
                                    <option value="<?php echo date('y')-2?>"><?php echo date('Y')-2?></option>
                                    <option value="<?php echo date('y')-1?>"><?php echo date('Y')-1?></option>
                                    <option value="<?php echo date('y')?>"><?php echo date('Y')?></option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="selectpicker" name="semester" id="programme" data-width="100%" title="Semester">
                                    <option value="A">Semester 1</option>
                                    <option value="B">Semester 2</option>
                                    <option value="C">Semester 3</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="selectpicker" name="intake" id="programme" data-width="100%" title="Intake">
                                    <option value="1">First Year</option>
                                    <option value="2">Second Year</option>
                                </select>
                            </div>
                        </div>

                        <hr>
                        
                        <div class="form-group row">
                            <label for="student_id" class="col-md-4 col-form-label text-md-right">{{ __('Email : ') }}</label>
                            <div class="col-md-4">
                                <input type="text" name="student_id" class="form-control" placeholder="Student ID">
                            </div>
                            <span class="col-md-4 col-form-label text-md-left">@sc.edu.my</span>
                        </div>
                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password : ') }}</label>
                            <div class="col-md-6">
                                <input type="password" name="password" class="form-control" placeholder="Student Account Password">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password : ') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Type Password Again">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-8 offset-md-4">
                                <input type="submit" class="btn btn-primary">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
