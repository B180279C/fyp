@extends('layouts.app')

@section('content');
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Add Department') }}</div>
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

                        <form method="post" action="{{route('department.submit')}}">
                        {{csrf_field()}}
                        
                        <div class="form-group row">
                            <label for="full_name" class="col-md-4 col-form-label text-md-right">{{ __('Department Name : ') }}</label>
                            <div class="col-md-6">
                                <input type="text" name="department_name" class="form-control" placeholder="Name">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="position" class="col-md-4 col-form-label text-md-right">{{ __('Academic : ') }}</label>
                            <div class="col-md-6">
                                <select class="selectpicker" name="academic" data-width="100%" title="Choose one">
                                    @foreach($academic as $row)
                                        <option value="{{ $row['academic_id'] }}">{{$row['academic_name']}}</option>
                                     @endforeach
                                </select>
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