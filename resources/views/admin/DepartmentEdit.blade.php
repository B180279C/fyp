@extends('layouts.app')

@section('content');

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card">
                <div class="card-header">{{ __('Edit Department Information') }}</div>
                <div class="card-body">
                    @if(count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{$error}}</li>
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

                        <form method="post" action="{{action('DepartmentController@update', $id)}}">
                        {{csrf_field()}}
                        <div class="form-group row">
                            <label for="department" class="col-md-4 col-form-label text-md-right">{{ __('Department : ') }}</label>
                            <div class="col-md-6">
                                <input type="text" name="department_name" value="{{$department->department_name}}" class="form-control" placeholder="Department">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="batch" class="col-md-4 col-form-label text-md-right">{{ __('Faculty : ') }}</label>
                            <div class="col-md-6">
                                <select class="selectpicker" name="faculty" data-width="100%" title="Choose one">
                                     @foreach($faculty as $row)
                                        @if($department->faculty_id==$row['faculty_id'])
                                            <option selected value="{{ $row['faculty_id'] }}">{{$row['faculty_name']}}</option>
                                        @else
                                            <option value="{{ $row['faculty_id'] }}">{{$row['faculty_name']}}</option>
                                        @endif
                                     @endforeach
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