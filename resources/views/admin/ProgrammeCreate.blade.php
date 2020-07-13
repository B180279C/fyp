@extends('layouts.app')

@section('content')
<script type="text/javascript">
    $(function () {
        $("#form_dep").hide();

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
                    if(data!="null"){
                        $("#department").html(data);
                        $('#department').selectpicker('refresh');
                        $("#form_dep").show();
                    }else{
                        $("#form_dep").hide();
                    }
               }
            });
        });
    });
</script>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Add Programme') }}</div>
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

                        <form method="post" action="{{route('programme.submit')}}">
                        {{csrf_field()}}
                        
                        <div class="form-group row">
                            <label for="programme_name" class="col-md-4 col-form-label text-md-right">{{ __('Programme Name : ') }}</label>
                            <div class="col-md-6">
                                <input type="text" name="programme_name" class="form-control" placeholder="Bachelor/Doploma/Foundation of ...">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="short_form_name" class="col-md-4 col-form-label text-md-right">{{ __('Short Form Name : ') }}</label>
                            <div class="col-md-6">
                                <input type="text" name="short_form_name" class="form-control" placeholder="XXX">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="level" class="col-md-4 col-form-label text-md-right">{{ __('Level : ') }}</label>
                            <div class="col-md-6">
                                <select class="selectpicker" name="level" id="level" data-width="100%" title="Choose one">
                                    <option value="Degree">Degree</option>
                                    <option value="Diploma">Diploma</option>
                                    <option value="Master">Master</option>
                                    <option value="Foundation">Foundation</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="faculty" class="col-md-4 col-form-label text-md-right">{{ __('Faculty : ') }}</label>
                            <div class="col-md-6">
                                <select class="selectpicker" name="faculty" id="faculty" data-width="100%" title="Choose one">
                                    @foreach($faculty as $row)
                                        <option value="{{ $row['faculty_id'] }}">{{$row['faculty_name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row" id="form_dep">
                            <label for="department" class="col-md-4 col-form-label text-md-right">{{ __('Department : ') }}</label>
                            <div class="col-md-6">
                                <select class="selectpicker" name="department" data-width="100%" title="Choose one" data-live-search="true" id="department">
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