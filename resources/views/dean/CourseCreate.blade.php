@extends('layouts.app')

@section('content')
<script type="text/javascript">
    $(function () {
        $("#form_sub").hide();
        $("#form_year").hide();
        $("#form_tch").hide();
        $("#form_lct").hide();
        $("#form_other_lct").hide();

        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        
        $('#programme').change(function(){
            var value = $('#programme').val();
            $.ajax({
               type:'POST',
               url:'/courseSubject',
               data:{value:value},

               success:function(data){
                    $("#subject").html(data);
                    $('#subject').selectpicker('refresh');
                    $("#form_sub").show();
               }
            });
        });
        $('#inlineRadio1').change(function(){
            $("#form_other_lct").hide();
            $("#form_lct").show();
            var value = $('#lecturer1').val();
            document.getElementById('lecturer').value = value;
        });

        $('#inlineRadio2').change(function(){
            $("#form_other_lct").show();
            $("#form_lct").hide();
            var value = $('#lecturer2').val();
            document.getElementById('lecturer').value = value;
        });

        $('#lecturer1').change(function(){
            var value = $('#lecturer1').val();
            document.getElementById('lecturer').value = value;
        });

        $('#lecturer2').change(function(){
            var value = $('#lecturer2').val();
            document.getElementById('lecturer').value = value;
        });

        $('#subject').change(function(){
            $("#form_year").show();
            $("#form_tch").show();
            $("#form_lct").show();
        });
    });
</script>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card">
                <div class="card-header">{{ __('Add Course') }}</div>
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

                        <form method="post" action="{{route('course.submit')}}">
                        {{csrf_field()}}
                        <div class="form-group row">
                            <label for="department" class="col-md-4 col-form-label text-md-right">{{ __('Programme : ') }}</label>
                            <div class="col-md-6">
                                <select class="selectpicker" name="programme" id="programme" data-width="100%" title="Choose one" data-live-search="true">
                                    @foreach($programme as $row)
                                        <option  value="{{ $row->programme_id }}">{{$row->short_form_name}} : {{$row->programme_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row" id="form_sub">
                            <label for="subject" class="col-md-4 col-form-label text-md-right">{{ __('Subject : ') }}</label>
                            <div class="col-md-6">
                                <select class="selectpicker" name="subject" data-width="100%" title="Choose one" data-live-search="true" id="subject">
                                </select>
                            </div>
                        </div>

                        <div class="form-group row" id="form_year">
                            <label for="full_name" class="col-md-4 col-form-label text-md-right">{{ __('Year/Semester : ') }}</label>
                            <div class="col-md-2">
                                <select class="selectpicker" name="year" id="year" data-width="100%" title="Year">
                                    <option value="<?php echo date('y')-1?>"><?php echo date('Y')-1?></option>
                                    <option value="<?php echo date('y')?>"><?php echo date('Y')?></option>
                                    <option value="<?php echo date('y')+1?>"><?php echo date('Y')+1?></option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="selectpicker" name="semester" id="semester" data-width="100%" title="Semester">
                                    <option value="A">Semester 1</option>
                                    <option value="B">Semester 2</option>
                                    <option value="C">Semester 3</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group row" id="form_tch">
                            <label for="subject" class="col-md-4 col-form-label text-md-right">{{ __('Lecturer : ') }}</label>
                            <div class="col-md-8 col-form-label text-md-left">
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1" checked>
                                  <label class="form-check-label" for="inlineRadio1">Own Faculty</label>
                                </div>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
                                  <label class="form-check-label" for="inlineRadio2">Other Faculty</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row" id="form_lct">
                            <label for="full_name" class="col-md-4 col-form-label text-md-right"></label>
                            <div class="col-md-6">
                                <select class="selectpicker" id="lecturer1" data-width="100%" title="Lecturer" data-live-search="true">
                                    @foreach($staffs as $row_staff)
                                        <option  value="{{$row_staff->staff_id}}">{{$row_staff->name}} ({{$row_staff->staff_id}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row" id="form_other_lct">
                            <label for="full_name" class="col-md-4 col-form-label text-md-right"></label>
                            <div class="col-md-6">
                                <select class="selectpicker" id="lecturer2" data-width="100%" title="Lecturer" data-live-search="true">
                                    @foreach($academic as $row_academic)
                                    <optgroup label="{{ $row_academic['academic_name']}}">
                                        @foreach($lct as $row)
                                            @if($row_academic['academic_id']==$row->academic_id)
                                                <option  value="{{$row_staff->staff_id}}">{{$row_staff->name}} ({{$row_staff->staff_id}})</option>
                                            @endif
                                        @endforeach
                                    </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="lecturer" id="lecturer">
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