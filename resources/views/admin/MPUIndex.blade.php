<?php
$title = "Programme";
$option6 = "id='selected-sidebar'";
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
            pageLength: 5,
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
    function w3_open() {
      document.getElementById("action_sidebar").style.display = "block";
      document.getElementById("button_open").style.display = "none";
    }
    function w3_close() {
      document.getElementById("action_sidebar").style.display = "none";
      document.getElementById("button_open").style.display = "block";
    }
</script>
<div style="background-color:white;">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">General Studies</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <span class="now_page"> General Studies </span>/
        </p>
        <hr style="margin: -10px 10px;">
    </div>
<div class="row" style="padding: 10px 10px 10px 10px;">

        <div class="col-md-12">
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
            @if($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <Strong>{{$message}}</Strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            <div style="overflow-x:auto;box-shadow: 0px 2px 5px #aaaaaa;">
                <table id="dtBasicExample" style="border:none;width: 100%;">
                    <thead style="background-color: #0d2f81!important; color: gold;">
                        <tr style="height: 60px;text-align: left;">
                            <th style="padding-left: 10px;">No. </th>
                            <th style="padding-left: 10px;">Level</th>
                            <th style="padding-left: 10px;">Course</th>
                        </tr>
                    </thead>
                <?php
                $i = 1; 
                ?>
                <tbody>
                @foreach($programmes as $row)
                    <tr style="height: 60px;">
                        <td><?php echo $i++?></td>
                        <td>{{$row->level}}</td>
                        <td><a href="{{action('MPUController@create', $row->level)}}">MPU Subject List</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
