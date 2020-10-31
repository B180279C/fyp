<?php
$title = "View MPU Subject";
$option5 = "id='selected-sidebar'";
?>
@extends('layouts.nav')
@section('content')
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">MPU Subject</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/programme_list">Programme</a>/
            <span class="now_page">MPU Subject ( {{$level}} )</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="col-md-12">
        <div class="details" style="padding: 20px 5px 5px 5px;">
            <p class="page_title" style="position: relative;left: 0px ;top: -5px;">MPU Subject Information</p>
            <hr style="margin: 0px;">
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

                        <form method="post">
                        {{csrf_field()}}
                        <div class="row">
                            <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                                    <i class="fa fa-list-ol" aria-hidden="true" style="font-size: 18px;"></i>
                                </p>
                            </div>
                            <div class="col-11" style="padding-left: 20px;">
                                <div class="form-group">
                                    <label for="programme_name" class="bmd-label-floating">Level</label>
                                    <input type="text" name="programme_name" value="{{$level}}" class="form-control" id="input" readonly style="font-weight: bold;">
                                </div>
                            </div>
                        </div>
                        <hr>
                        <?php
                        $i = 1;
                        ?>
                        <div id="type">
                        @foreach($group as $row_group)
                            <div id="dynamic_field<?php echo $i?>">
                                <label class="col-12 align-self-center" style="padding-left: 0px;"><?php echo $i?>) Subject Classification</label>

                                <div class="row">
                                  <div class="col-md-1 align-self-center"></div>
                                  <div class="col-md-9 align-self-center" style="padding: 15px 0px 0px 0px;">
                                    <div class="form-group" style="">
                                        <label for="subject" class="bmd-label-floating">Subject Type: </label>
                                        <input type="text" name="subject_type<?php echo $i?>" value="{{$row_group->subject_type}}" class="form-control" id="subject_type<?php echo $i?>" readonly/>
                                    </div>
                                  </div>
                                </div>
                                <?php
                                $m = 1;
                                ?>
                                @foreach($subjects as $row)
                                  @if($row_group->subject_type == $row->subject_type)
                                  <div class="row list_row">
                                    <div class="col-md-1 align-self-center"></div>
                                      <div class="col-md-2 align-self-center" style="padding: 0px;">
                                        <center>
                                          <div id="download">
                                              <a href="{{ action('MPUController@downloadSyllabus',$row->mpu_id) }}" style="background-color: none;">
                                                  <img src="{{url('image/excel.png')}}" width="100px" height="100px" style="border-radius:10%;"/>
                                                  <br>
                                                <p style="font-size: 14px;color: #009697;padding-bottom: 5px;">Download</a>
                                              </a>
                                          </div>
                                        </center>
                                      </div>
                                      <div class="col-md-9 row align-self-center">
                                         <div class="form-group col-md-10">
                                              <label for="subject" class="label" style="padding-left: 15px">Syllabus: </label>
                                              <input type="text" name="<?php echo $i?>syllabus<?php echo $m?>" placeholder="Syllabus" class="form-control" id="<?php echo $i?>syllabus<?php echo $m?>" value="{{$row->syllabus_name}}" disabled/>
                                              <input type="hidden" name="<?php echo $i?>full_syllabus<?php echo $m?>" id="<?php echo $i?>full_syllabus<?php echo $m?>">
                                          </div>
                                          <div class="form-group col-md-3">
                                              <label for="subject" class="bmd-label-floating" style="padding-left: 15px">Code: </label>
                                              <input type="text" name="<?php echo $i?>subject_code<?php echo $m?>" placeholder="Code" class="form-control" value="{{$row->subject_code}}" disabled/>
                                          </div>
                                          <div class="form-group col-md-7">
                                              <label for="subject" class="bmd-label-floating" style="padding-left: 15px">Subject Name: </label>
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
@endsection
