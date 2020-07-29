<?php
$title = "Department";
$option2 = "id='selected-sidebar'";
?>
@extends('layouts.nav_dean')

@section('content')
<script type="text/javascript">
    function w3_open() {
      document.getElementById("action_sidebar").style.display = "block";
      document.getElementById("button_open").style.display = "none";
    }
    function w3_close() {
      document.getElementById("action_sidebar").style.display = "none";
      document.getElementById("button_open").style.display = "block";
    }
    $(document).ready(function(){  
        $(document).on('click', '#open_folder', function(){
            $('#openFolderModal').modal('show');
        });
    });  
</script>
<div style="background-color: #f2f2f2">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">Faculty PortFolio</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/FacultyPortFolio"> Faculty PortFolio </a>/
            @if($faculty_portfolio->portfolio_place=="Faculty")
                <span class="now_page">{{$faculty_portfolio->portfolio_name}}</span>/
            @else
                <?php
                    $place = explode(',,,',($faculty_portfolio->portfolio_place));
                    $place_name = explode(',,,',($data));
                    $i=1;
                    while(isset($place[$i])!=""){
                        echo "<a href='/faculty_portfolio/folder/$place[$i]'>".$place_name[$i]."</a>/";
                        $i++;
                    }
                ?>
                <span class="now_page">{{$faculty_portfolio->portfolio_name}}</span>/
            @endif
        </p>
        <hr style="margin: -10px 10px;">
    </div>
    <div class="row" style="padding: 10px 10px 10px 10px;">
        <div class="col-md-12">
            <button onclick="w3_open()" class="button_open" id="button_open" style="float: right;margin-top: 10px;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                <div id="action_sidebar" class="w3-animate-right" style="display: none">
                    <div style="text-align: right;padding:10px;">
                        <button onclick="w3_close()" class="button_close"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                  <ul class="sidebar-action-ul">
                      <a id="open_folder"><li class="sidebar-action-li"><i class="fa fa-folder" style="padding: 0px 10px;" aria-hidden="true"></i>Make a new Folder</li></a>
                      <a href=""><li class="sidebar-action-li"><i class="fa fa-upload" style="padding: 0px 10px;" aria-hidden="true"></i>Upload Files</li></a>
                  </ul>
            </div>
            <br>
            <br>
            @if(\Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <Strong>{{\Session::get('success')}}</Strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            <div class="details" style="padding: 10px 5px 5px 5px;">
                <div class="row">
                    <?php
                    $i=0;
                    ?>
                    @foreach($faculty_portfolio_list as $row)
                        <div class="col-md-3">
                            <center>
                                <a href="/faculty_portfolio/folder/{{$row->fp_id}}" style="border: 1px solid #cccccc;padding:50px;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                                @if($row->portfolio_type=="folder")
                                    <img src="{{url('image/folder2.png')}}" width="85px" height="90px" />
                                @else

                                @endif
                                <br>
                                <p style="padding-top: 10px;color: #0d2f81;">{{$row->portfolio_name}}</p>
                                </a>
                            </center>
                        </div>
                    <?php
                    $i++;
                    ?>
                    @endforeach
                </div>
                <?php
                if($i==0){
                ?>
                <div style="display: block;border:1px solid black;padding: 50px">
                        <center>Empty</center>
                </div>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="openFolderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Open New Folder</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="post" action="{{action('F_PortFolioController@openNewFolder')}}">
        {{csrf_field()}}
      <div class="modal-body">
        <div id="message"></div>
        <br>
        <div class="row">
            <div class="col-md-1 align-self-center" style="padding: 15px 0px 0px 2%;">
                <p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;">
                    <i class="fa fa-folder" aria-hidden="true" style="font-size: 18px;"></i>
                </p>
            </div>
                <div class="col-11" style="padding-left: 20px;">
                <div class="form-group">
                      <label for="subject_type" class="bmd-label-floating">Folder Name</label>
                      <input type="text" name="folder_name" class="form-control" required/>
                      <input type="hidden" name="folder_place" value="{{$faculty_portfolio->portfolio_place}},,,{{$faculty_portfolio->fp_id}}">
                </div>
            </div>
        </div>
        <br>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-raised btn-secondary" data-dismiss="modal">Close</button>
        &nbsp;
        <input type="submit" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;margin: 0px!important;" value="Save Changes">
      </div>
      </form>
    </div>
  </div>
</div>
@endsection
