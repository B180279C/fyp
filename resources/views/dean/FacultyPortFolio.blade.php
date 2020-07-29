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
        $(document).on('click', '#open_document', function(){
            $('#openDocumentModal').modal('show');
        });

        $(document).on('keyup', '.filename', function(){  
           var button_id = $(this).attr("id");   
           
        });
    });
    var i = 0;
    Dropzone.options.dropzoneFile =
    {
        acceptedFiles: ".pdf,.xlsx,.docx",
        addRemoveLinks: true,
        timeout: 50000,
        init: function() {
            this.on("maxfilesexceeded", function(file) {
                    this.removeAllFiles();
                    this.addFile(file);
            });
            this.on("addedfile", function(file) {
              i++;
              file._captionLabel = Dropzone.createElement("<div class='changelabel'><label class='label' style='font-size:13px'>File Name</label></div>")
              file._captionBox = Dropzone.createElement("<div class='changeName'><input id='"+i+"' type='text' name='caption' value="+file.name+" class='form-control filename' size='30%'></div>");
              file.previewElement.appendChild(file._captionLabel);
              file.previewElement.appendChild(file._captionBox);
              writeInput(i,file.name);
            });
        },
        accept: function(file, done) {
            switch (file.type) {
              case 'application/pdf':
                $(file.previewElement).find(".dz-image img").attr("src", "{{url('image/pdf.png')}}");
                break;
              case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                $(file.previewElement).find(".dz-image img").attr("src", "{{url('image/docs.png')}}");
                break;
              case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                $(file.previewElement).find(".dz-image img").attr("src", "{{url('image/excel.png')}}");
                 break;
            }
            done();
        },
        removedfile: function(file)
        {
            var name = file.upload.filename;
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                type: 'POST',
                url: '{{ url("/destoryFiles") }}',
                data: {filename: name},
                success: function (data){
                    console.log("File has been successfully removed!!");
                    $("#staff_CV").val("");
                },
                error: function(e) {
                    console.log(e);
                }
            });
            var fileRef;
            return (fileRef = file.previewElement) != null ? 
            fileRef.parentNode.removeChild(file.previewElement) : void 0;
        },
        success: function(file, response) {

        },
        error: function(file, response) {
            alert(response);
        }
    };  
    function writeInput(num,name){
        $(document).ready(function(){  
            $("#writeInput").append("<input type='hidden' id='form"+num+"' value='"+name+"'>");
        });
    }
</script>
<style type="text/css">
.dropzone .dz-preview{
  border-bottom: 1px solid black;
  padding-left: 10px;
  padding-top: 10px;
  padding-bottom: -30px!important;
  width: 95%;
}
.dropzone .dz-preview .dz-filename {
  display: none;
}
.dropzone .dz-preview .dz-size {
  display: none;
}
.dropzone .dz-preview .dz-remove {
  text-align: left;
  padding-left: 25px;
  display: inline-block;
}
.changelabel{
    border:0px solid black;
    margin-left: 20%;
    position: relative;
    top: -105px;
    margin-bottom: -10px;
}
.changeName{
    border:0px solid black;
    margin-left: 20%;
    position: relative;
    top: -105px;
    margin-bottom: -50px;
}
@media only screen and (max-width: 600px) {
    .changelabel{
        border:0px solid black;
        margin-left: 40%;
        position: relative;
        top: -105px;
        margin-bottom: -10px;
    }
    .changeName{
        border:0px solid black;
        margin-left: 40%;
        position: relative;
        top: -105px;
        margin-bottom: -50px;
    }
}
</style>
<div style="background-color: #f2f2f2">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">Faculty Portfolio</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <span class="now_page">Faculty Portfolio </span>/
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
                      <a id="open_document"><li class="sidebar-action-li"><i class="fa fa-upload" style="padding: 0px 10px;" aria-hidden="true"></i>Upload Files</li></a>
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
            <div class="details" style="padding: 10px 0px;">
                <div class="row">
                    <div class="col-md-3">
                        <center>
                            <a href="/FacultyPortFolio/CVdepartment" style="border: 1px solid #cccccc;padding:50px;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <img src="{{url('image/cv.png')}}" width="96px" height="90px" />
                            <br>
                            <p style="padding-top: 10px;color: #0d2f81;">Lecturer CV</p>
                            </a>
                        </center>
                    </div>
                    <div class="col-md-3">
                        <center>
                            <a href="/FacultyPortFolio/" style="border: 1px solid #cccccc;padding:50px;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <img src="{{url('image/syllabus.png')}}" width="70px" height="90px" />
                            <br>
                            <p style="padding-top: 10px;color: #0d2f81;">Syllabus</p>
                            </a>
                        </center>
                    </div>
                    @foreach($faculty_portfolio as $row)
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
                    @endforeach
                </div>
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
                      <input type="hidden" name="folder_place" value="Faculty">
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

<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="openDocumentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Upload Files</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="post" action="{{route('dropzone.uploadFiles')}}" enctype="multipart/form-data"
        class="dropzone" id="dropzoneFile" style="margin: 20px;font-size: 20px;color:#a6a6a6;border-style: double;">
        @csrf
      </form>
      <form method="post" action="{{action('F_PortFolioController@openNewFolder')}}">
        {{csrf_field()}}
        <div id="writeInput"></div>
        <div class="modal-footer">
        <button type="button" class="btn btn-raised btn-secondary" data-dismiss="modal">Close</button>
        &nbsp;
        <input type="submit" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;margin-right: 13px;" value="Save Changes">
        </div>
      </form>
    </div>
  </div>
</div>
@endsection