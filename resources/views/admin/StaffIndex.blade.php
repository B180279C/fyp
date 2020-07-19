<?php
$title = "Staff";
$option1 = "id='selected-sidebar'";
?>
@extends('layouts.nav')

@section('content')

<script type="text/javascript">
    $(document).ready(function() {

        oTable = $('#dtBasicExample').DataTable(
        {
            "bLengthChange" : false,
            "bInfo": false,
            pagingType: 'input',
            pageLength: 10,
            language: {
                oPaginate: {
                   sNext: '<i class="fa fa-forward"></i>',
                   sPrevious: '<i class="fa fa-backward"></i>',
                   sFirst: '<i class="fa fa-step-backward"></i>',
                   sLast: '<i class="fa fa-step-forward"></i>'
                }
            }
        });
        $('#input').keyup(function(){
              oTable.search($(this).val()).draw();
        });
    });
</script>
<div style="background-color: #f2f2f2">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">Staff Listing</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <span class="now_page">Staff </span>/
        </p>
        <hr style="margin: 0px 10px;">
        
    </div>
    <div class="row" style="padding: 10px 10px 10px 10px;">
        <div class="col-md-12">
            @if($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <Strong>{{$message}}</Strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            <div class="col-md-6 row" style="padding:0px 20px;position: relative;top: -10px;">
                <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                    <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                        <i class="fa fa-search" aria-hidden="true" style="font-size: 20px;"></i>
                    </p>
                </div>
                <div class="col-11" style="padding-left: 20px;">
                    <div class="form-group">
                        <label for="full_name" class="bmd-label-floating">Search</label>
                        <input type="text" name="search" class="form-control" id="input" style="font-size: 18px;">
                    </div>
                </div>
            </div>
            <!-- <a href="{{route('staff.create')}}" class="btn btn-primary" id="button-add">
                <i class="fa fa-plus-circle" aria-hidden="true" style="font-size: 50px;padding:0px!important;"></i>
            </a> -->
            <div style="overflow-x:auto;">
                <table id="dtBasicExample" style="border:none;width: 100%;">
                    <thead style="background-color: #0d2f81!important; color: gold;">
                        <tr style="height: 60px;text-align: left;">
                            <th style="padding-left: 10px;">No. </th>
                            <th style="padding-left: 10px;">Name</th>
                            <th style="padding-left: 10px;">Staff Id</th>
                            <th style="padding-left: 10px;">Position</th>
                            <th style="padding-left: 10px;">Email</th>
                            <th style="padding-left: 10px;">Action</th>
                        </tr>
                    </thead>
                    <?php
                    $i = 1; 
                    ?>
                    <tbody>
                        @foreach($staffs as $row)
                        <tr style="height: 60px;">
                            <td><?php echo $i++?></td>
                            <td>{{$row->name}}</td>
                            <td>{{$row->staff_id}}</td>
                            <td>{{$row->position}} , {{$row->department_name}}</td>
                            <td>{{$row->email}}</td>
                            <td><a href="{{action('StaffController@edit', $row->staff_id)}}">Edit</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
