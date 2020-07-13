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
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Edit Staff Information') }}</div>
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

                    @if(\Session::has('failed'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                      <Strong>{{\Session::get('failed')}}</Strong>
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    @endif

                        <form method="post" action="{{action('StaffController@update', $id)}}">
                        {{csrf_field()}}
                        <div class="form-group row">
                            <label for="staff_id" class="col-md-4 col-form-label text-md-right">{{ __('Email : ') }}</label>
                            <div class="col-md-4">
                                <input type="text" name="staff_id" class="form-control" placeholder="Staff ID" value="{{$staff->staff_id}}">
                            </div>
                            <span class="col-md-4 col-form-label text-md-left">@sc.edu.my</span>
                        </div>
                        <hr>
                        <input type="hidden" name="_method" value="post" />
                        <div class="form-group row">
                            <label for="full_name" class="col-md-4 col-form-label text-md-right">{{ __('Name : ') }}</label>
                            <div class="col-md-6">
                                <input type="text" name="name" value="{{$user->name}}" class="form-control" placeholder="Username">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="batch" class="col-md-4 col-form-label text-md-right">{{ __('Position : ') }}</label>
                            <div class="col-md-6">
                                <select class="form-control" name="position" id="position" title="Choose one">
                                    <option <?php if($user->position==='Teacher'){ echo 'selected'; }?> value="Teacher">Teacher</option>
                                    <option <?php if($user->position==='HoD'){ echo 'selected'; }?> value="HoD">Head of Department</option>
                                    <option <?php if($user->position==='Dean'){ echo 'selected'; }?> value="Dean">Dean</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="position" class="col-md-4 col-form-label text-md-right">{{ __('Faculty : ') }}</label>
                            <div class="col-md-6">
                                <select class="selectpicker" name="faculty" id="faculty" data-width="100%" title="Choose one">
                                    @foreach($faculty as $row)
                                        @if($staff->faculty_id==$row['faculty_id'])
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
                                            @foreach($departments as $row)
                                                @if($row['faculty_id']==$row_faculty['faculty_id'])
                                                    @if($staff->department_id==$row['department_id'])
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
