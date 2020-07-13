@extends('layouts.app')

@section('content');

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card">
                <div class="card-header">{{ __('Edit Student Information') }}</div>
                <div class="card-body">
                    @if(count($errors) > 0)
                        <div class="alert alert-danger">
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

                        <form method="post" action="{{action('StudentController@update', $id)}}">
                        {{csrf_field()}}
                        <div class="form-group row">
                            <label for="student_id" class="col-md-4 col-form-label text-md-right">{{ __('Email : ') }}</label>
                            <div class="col-md-4">
                                <input type="text" name="student_id" class="form-control" placeholder="Student ID" value="{{$student->student_id}}">
                            </div>
                            <span class="col-md-4 col-form-label text-md-left">@sc.edu.my</span>
                        </div>
                        <hr>
                        <input type="hidden" name="_method" value="post" />
                        <div class="form-group row">
                            <label for="full_name" class="col-md-4 col-form-label text-md-right">{{ __('Name : ') }}</label>
                            <div class="col-md-6">
                                <input type="text" name="name" value="{{$user->name}}" class="form-control" placeholder="Full Name">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="programme" class="col-md-4 col-form-label text-md-right">{{ __('Programme : ') }}</label>
                            <div class="col-md-6">
                                <select class="selectpicker" name="programme" id="programme" data-width="100%" title="Choose one" data-live-search="true">
                                    @foreach($faculty as $row_faculty)
                                    <optgroup label="{{ $row_faculty['faculty_name']}}">
                                        @foreach($programme as $row)
                                            @if($row_faculty['faculty_id']==$row->faculty_id)
                                                @if($student->programme_id == $row->programme_id)
                                                    <option selected value="{{ $row->programme_id }}">{{$row->short_form_name}} : {{$row->programme_name}}</option>
                                                @else
                                                    <option value="{{ $row->programme_id }}">{{$row->short_form_name}} : {{$row->programme_name}}</option>
                                                @endif
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
                                    <option <?php if($student->year==(date('y')-5)){ echo "selected";}?> value="<?php echo date('y')-5?>"><?php echo date('Y')-5?></option>
                                    <option <?php if($student->year==(date('y')-4)){ echo "selected";}?> value="<?php echo date('y')-4?>"><?php echo date('Y')-4?></option>
                                    <option <?php if($student->year==(date('y')-3)){ echo "selected";}?> value="<?php echo date('y')-3?>"><?php echo date('Y')-3?></option>
                                    <option <?php if($student->year==(date('y')-2)){ echo "selected";}?> value="<?php echo date('y')-2?>"><?php echo date('Y')-2?></option>
                                    <option <?php if($student->year==(date('y')-1)){ echo "selected";}?> value="<?php echo date('y')-1?>"><?php echo date('Y')-1?></option>
                                    <option <?php if($student->year==date('y')){ echo "selected";}?> value="<?php echo date('y')?>"><?php echo date('Y')?></option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="selectpicker" name="semester" id="programme" data-width="100%" title="Semester">
                                    <option <?php if($student->semester=="A"){ echo "selected";}?> value="A">Semester 1</option>
                                    <option <?php if($student->semester=="B"){ echo "selected";}?> value="B">Semester 2</option>
                                    <option <?php if($student->semester=="C"){ echo "selected";}?> value="C">Semester 3</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="selectpicker" name="intake" id="programme" data-width="100%" title="Intake">
                                    <option <?php if($student->intake=="1"){ echo "selected";}?> value="1">First Year</option>
                                    <option <?php if($student->intake=="2"){ echo "selected";}?> value="2">Second Year</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-8 offset-md-4">
                                <input type="submit" class="btn btn-warning" value="Edit">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
