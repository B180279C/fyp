@extends('layouts.app')

@section('content');
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('MPU Subject Information') }}</div>
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

                        <form method="post" action="{{action('GSController@store', $level)}}">
                        {{csrf_field()}}
                        <!-- <div style="text-align: right;">
                            <button type="button" name="add" id="subject_type" class="btn btn-warning">Add Subject List</button>
                        </div> -->
                        <div class="form-group row">
                            <label for="level" class="col-md-4 col-form-label text-md-right">{{ __('Level : ') }}</label>
                            <div class="col-md-6 col-form-label text-md-left">
                                <strong>{{$level}}</strong>
                            </div>
                        </div>
                        <hr>
                        <?php
                        $i = 1;
                        ?>
                        <div id="type">
                        @foreach($group as $row_group)
                            <div id="dynamic_field<?php echo $i?>">
                                <div class="form-group row">
                                    <label for="subject" class="col-md-4 col-form-label text-md-right" ><?php echo $i?>) Subject : </label>
                                    <div class="col-md-4" style="padding: 0px">
                                        <input type="text" name="subject_type<?php echo $i?>" placeholder="Subject Type" value="{{$row_group->subject_type}}" class="form-control" id="subject_type<?php echo $i?>" readonly/>
                                    </div>
                                </div>
                                <?php
                                $m = 1;
                                ?>
                                @foreach($subjects as $row)
                                  @if($row_group->subject_type == $row->subject_type)
                                  <div class="form-group row list_row">
                                      <label for="subject" class="col-md-4 col-form-label text-md-right"></label>
                                      <div class="col-md-8 row">
                                          <div class="col-md-3" style="padding: 1px">
                                              <input type="text" name="<?php echo $i?>subject_code<?php echo $m?>" placeholder="Code" class="form-control" value="{{$row->subject_code}}" disabled/>
                                          </div>
                                          <div class="col-md-7" style="padding: 1px">
                                              <input type="text" name="<?php echo $i?>subject_name<?php echo $m?>" placeholder="Subject Name" class="form-control" value="{{$row->subject_name}}" disabled/>
                                          </div>
                                      </div>
                                  </div>
                                  <?php
                                    $m++;
                                  ?>
                                  @endif
                                @endforeach
                                <input type="hidden" name="count_list<?php echo $i?>" id="count_list<?php echo $i?>" value="<?php echo ($m-1)?>">
                            </div>
                            <br>
                          <hr>
                          <?php 
                            $i++;
                          ?>
                        @endforeach
                        </div>
                        <input type="hidden" name="count" id="count" value='<?php echo ($i-1)?>'>
                        <br>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
