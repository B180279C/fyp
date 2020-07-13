@extends('layouts.app')

@section('content');
<script type="text/javascript">
    $(function () {
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });

        $('#faculty').change(function(){
            var value = $('#faculty').val();
            $.ajax({
               type:'POST',
               url:'/staffFaculty',
               data:{value:value},

               success:function(data){
                  $("#department").html(data);
                  $('#department').selectpicker('refresh');
               }
            });
        });
    });
</script>

<?php
foreach($department as $row){
    if($programme->department_id==$row['department_id']){
        $faculty_id = $row['faculty_id'];
    }
}
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Edit Programme Information') }}</div>
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

                        <form method="post" action="{{action('ProgrammeController@update', $id)}}">
                        {{csrf_field()}}
                        <div class="form-group row">
                            <label for="programme_name" class="col-md-4 col-form-label text-md-right">{{ __('Programme Name : ') }}</label>
                            <div class="col-md-6">
                                <input type="text" name="programme_name" value="{{$programme->programme_name}}" class="form-control" placeholder="Bachelor/Doploma/Foundation of ...">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="short_form_name" class="col-md-4 col-form-label text-md-right">{{ __('Short Form Name : ') }}</label>
                            <div class="col-md-6">
                                <input type="text" name="short_form_name" value="{{$programme->short_form_name}}" class="form-control" placeholder="XXX">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="level" class="col-md-4 col-form-label text-md-right">{{ __('level : ') }}</label>
                            <div class="col-md-6">
                                <select class="selectpicker" name="level" id="level" data-width="100%" title="Choose one">
                                    <option <?php if($programme->level == "Degree"){ echo 'selected';}?> value="Degree">Degree</option>
                                    <option <?php if($programme->level == "Diploma"){ echo 'selected';}?> value="Diploma">Diploma</option>
                                    <option <?php if($programme->level == "Master"){ echo 'selected';}?> value="Master">Master</option>
                                    <option <?php if($programme->level == "Foundation"){ echo 'selected';}?> value="Foundation">Foundation</option>
                                </select>
                            </div>
                        </div>
 
                        <div class="form-group row">
                            <label for="position" class="col-md-4 col-form-label text-md-right">{{ __('Faculty : ') }}</label>
                            <div class="col-md-6">
                                <select class="selectpicker" name="faculty" id="faculty" data-width="100%" title="Choose one">
                                    @foreach($faculty as $row)
                                        @if($faculty_id==$row['faculty_id'])
                                            <option selected value="{{ $row['faculty_id'] }}">{{$row['faculty_name']}}</option>
                                        @else
                                            <option value="{{ $row['faculty_id'] }}">{{$row['faculty_name']}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="department" class="col-md-4 col-form-label text-md-right">{{ __('Department : ') }}</label>
                            <div class="col-md-6">
                                <select class="selectpicker" name="department" data-width="100%" title="Choose one" data-live-search="true" id="department"> 
                                    @foreach($faculty as $row_faculty)
                                        <optgroup label="{{ $row_faculty['faculty_name']}}">
                                            @foreach($department as $row)
                                                @if($row['faculty_id']==$row_faculty['faculty_id'])
                                                    @if($programme->department_id==$row['department_id'])
                                                        <option selected value="{{ $row['department_id'] }}">{{$row['department_name']}}</option>
                                                    @else
                                                        <option  value="{{ $row['department_id'] }}">{{$row['department_name']}}</option>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </optgroup>
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
