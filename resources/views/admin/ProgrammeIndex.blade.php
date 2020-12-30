<?php
$title = "Programme";
$option5 = "id='selected-sidebar'";
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

    $(document).on('click', '.edit_action', function(){
        var id = $(this).attr("id");
        var num = id.split("_");
        window.location = "/programme/"+num[2];
        return false;
    });

    $(document).on('click', '.remove_action', function(){
        var id = $(this).attr("id");
        var num = id.split("_");
        if(confirm('Are you sure want to remove it')){
          window.location = "/programme/remove/"+num[2];
        }
        return false;
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
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">Programme Listing</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <span class="now_page">Programme </span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 10px 10px;">
        <div class="col-md-12">
            <!-- Page Content -->
            <p class="page_title">Programme</p>
            <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
            <div id="action_sidebar" class="w3-animate-right" style="display: none">
                <div style="text-align: right;padding:10px;">
                    <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                </div>
              <ul class="sidebar-action-ul">
                  <a href="/programme/create"><li class="sidebar-action-li"><i class="fa fa-plus-circle" style="padding: 0px 10px;" aria-hidden="true"></i>Add New Programme</li></a>
                  <a href="/programme/excel/download/"><li class="sidebar-action-li"><i class="fa fa-file-excel-o" style="padding: 0px 10px;" aria-hidden="true"></i>Export Excel File</li></a>
              </ul>
            </div>
            <br>
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
                            <th style="border-left:1px solid #e6e6e6;border-bottom: 1px solid #d9d9d9;text-align: center;">Programme Name</th>
                            <th style="border-left:1px solid #e6e6e6;border-bottom: 1px solid #d9d9d9;text-align: center;">Level</th>
                            <th style="border-left:1px solid #e6e6e6;border-bottom: 1px solid #d9d9d9;text-align: center;">Subject</th>
                            <th style="border-left:1px solid #e6e6e6;border-bottom: 1px solid #d9d9d9;text-align: center;">MPU Subject</th>
                            <th style="border-left:1px solid #e6e6e6;border-bottom: 1px solid #d9d9d9;text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <?php
                    $i = 1; 
                    ?>
                    <tbody>
                    @foreach($programmes as $row)
                        <tr style="height: 60px;">
                            <td  style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;"><?php echo $i++?></td>
                            <td  style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: left;">{{$row->programme_name}}, ({{$row->short_form_name}})</td>
                            <td  style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">{{$row->level}}</td>
                            <td  style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;"><a href="{{action('SubjectController@create', $row->programme_id)}}" id="show_image_link">Subject</a></td>
                            <td  style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;"><a href="{{action('MPUController@view', $row->level)}}" id="show_image_link">MPU Subject</a></td>
                            <!-- <td  style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;"><a href="{{action('ProgrammeController@edit', $row->programme_id)}}">Edit</a></td> -->
                            <td style="border-left:1px solid #d9d9d9;border-bottom: 1px solid #d9d9d9;text-align: center;">
                                <i class="fa fa-wrench edit_action" aria-hidden="true" id="edit_button_{{$row->programme_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>&nbsp;
                                <i class="fa fa-times remove_action" aria-hidden="true" id="remove_button_{{$row->programme_id}}" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
