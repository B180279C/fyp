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
<style type="text/css">
    #show_image_link:hover{
        text-decoration-line: none;
    }
@media only screen and (min-width: 600px) {
  .tooltiptext{
    width:300px;
    background-color:#e6e6e6;
    color: black;
    text-align: left;
    border-radius: 6px;
    border:1px solid black;
    padding: 5px 10px;
    position: absolute;
    z-index: 1;
    top:38%;
    left:103%;
  }
}
</style>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">General Studies</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <span class="now_page"> General Studies </span>/
        </p>
        <hr class="separate_hr">
    </div>
<div class="row" style="padding: 10px 10px 10px 10px;">
        <div class="col-md-12">
            <p class="page_title">General Studies</p>
            <div class="col-md-6 row" style="padding:0px 20px;position: relative;top: -10px;">
                <div class="col-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                    <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                        <i class="fa fa-search" aria-hidden="true" style="font-size: 20px;"></i>
                    </p>
                </div>
                <div class="col-11" style="padding-left: 20px;">
                    <div class="form-group">
                        <label for="full_name" class="bmd-label-floating">Search</label>
                        <input type="text" name="search" class="form-control tooltip_hover" id="input" style="font-size: 18px;">
                        <span class="tooltiptext">
                            <span>
                                <i class="fa fa-info-circle" style="color: #0d2f81;" aria-hidden="true"></i> Important : 
                            </span>
                            <hr style="background-color: #d9d9d9;margin: 3px 0px;">
                            <span>1. All Data in table</span>
                        </span>
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
            <hr style="margin-top: 0px;">
            <div style="overflow-x:auto;">
                <table style="text-align: left;box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);border:none;" id="dtBasicExample">
                    <thead style="background-color: #0d2f81!important;">
                        <tr style="background-color: #d9d9d9;">
                            <th style="border-left:1px solid #e6e6e6;border-bottom: 1px solid #d9d9d9;text-align: center;">No. </th>
                            <th style="border-left:1px solid #e6e6e6;border-bottom: 1px solid #d9d9d9;text-align: center;">Level</th>
                            <th style="border-left:1px solid #e6e6e6;border-bottom: 1px solid #d9d9d9;text-align: center;">Course</th>
                        </tr>
                    </thead>
                <?php
                $i = 1; 
                ?>
                <tbody>
                @foreach($programmes as $row)
                    <tr style="height: 60px;">
                        <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;"><?php echo $i++?></td>
                        <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">{{$row->level}}</td>
                        <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;"><a href="{{action('MPUController@create', $row->level)}}" id="show_image_link">MPU Subject List</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
