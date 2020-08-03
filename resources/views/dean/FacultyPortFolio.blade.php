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
           var id = $(this).attr("id");
           var value = document.getElementById(id).value;
           $("#form"+id).val(value);
        });

        $(document).on('click', '.action_button_file', function(){  
           var active = $('#active_dropdownlist').val();
           var id = $(this).attr("id");
           var num = id.split("_");
           if(active!=""){
              if($('#dropdown_list'+num[3]).css('display') === 'block')
               {
                document.getElementById('dropdown_list'+num[3]).style.display = "none";
                document.getElementById('active_dropdownlist').value = "";
               }else{
                document.getElementById('dropdown_list'+num[3]).style.display = "block";
                document.getElementById('active_dropdownlist').value = num[3];
               }
               document.getElementById('dropdown_list'+active).style.display = "none";
           }else{
              if($('#dropdown_list'+num[3]).css('display') === 'block')
               {
                document.getElementById('dropdown_list'+num[3]).style.display = "none";
                document.getElementById('active_dropdownlist').value = "";
               }else{
                document.getElementById('dropdown_list'+num[3]).style.display = "block";
                document.getElementById('active_dropdownlist').value = num[3];
               }
           }
           return false;
        });

        $(document).on('click', '.edit_button_file', function(){
          var id = $(this).attr("id");
          var num = id.split("_");
          $.ajax({
            type:'POST',
            url:'/folderNameEdit',
            data:{value : num[3]},
            success:function(data){
              document.getElementById('fp_id').value = num[3];
              document.getElementById('folder_name').value = data.portfolio_name;
            } 
          });
          $('#folderNameEdit').modal('show');
          return false;
        });

        $(document).on('click', '.remove_button_file', function(){
          var id = $(this).attr("id");
          var num = id.split("_");
          if(confirm('Are you sure you want to remove the it?')) {
            window.location = "/FacultyPortFolio/remove/"+num[3];
          }
          return false;
        });
    });
    var i = 0;
    var file_up_names = [0];
    Dropzone.options.dropzoneFile =
    {
        acceptedFiles: ".pdf,.xlsx,.docx",
        addRemoveLinks: true,
        timeout: 50000,
        renameFile: function(file) {
            var re = /(?:\.([^.]+))?$/;
            var ext = re.exec(file.name)[1];
            var newName = new Date().getTime() +"___"+file.name;
            file_up_names.push(newName);
            return newName;
        },
        init: function() {
            this.on("addedfile", function(file){
              i++;
              var re = /(?:\.([^.]+))?$/;
              var ext = re.exec(file.upload.filename)[0];
              var filename = file.upload.filename.split(ext);
              var name_without_time = filename[0].split("___");
              file._captionLabel = Dropzone.createElement("<div class='changelabel'><label class='label' style='font-size:13px'>File Name</label></div>")
              file._captionBox = Dropzone.createElement("<div class='changeName'><input id='"+i+"' type='text' name='caption' value='"+name_without_time[1]+"' class='form-control filename'></div>");
              file.previewElement.appendChild(file._captionLabel);
              file.previewElement.appendChild(file._captionBox);
              writeInput(i,name_without_time[1],name_without_time[0],ext,file.upload.filename);
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
            for(var i=0;i<file_up_names.length;i++){
                if(file_up_names[i]==name){
                    var id = i;
                    document.getElementById("form"+id).value = "";
                    document.getElementById("time"+id).value = "";
                    document.getElementById("ext"+id).value = "";
                    document.getElementById("fake"+id).value = "";
                }
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                type: 'POST',
                url: '{{ url("/destoryFiles") }}',
                data: {filename: name},
                success: function (data){
                    console.log("File has been successfully removed!!");
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
            document.getElementById('count').value = i;
        },
        error: function(file, response) {
            alert(response);
        }
    };  
    function writeInput(num,name,time,ext,fake){
        $(document).ready(function(){  
            $("#writeInput").append("<input type='hidden' id='form"+num+"' name='form"+num+"' value='"+name+"'><input type='hidden' id='time"+num+"' name='time"+num+"' value='"+time+"'><input type='hidden' id='ext"+num+"' name='ext"+num+"' value='"+ext+"'><input type='hidden' id='fake"+num+"' name='fake"+num+"' value='"+fake+"'>");
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
</style>
<div style="background-color: #f2f2f2">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$faculty->faculty_name}}</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <span class="now_page">Faculty Portfolio </span>/
        </p>
        <hr style="margin: -10px 10px;">
    </div>
    <div class="row" style="padding: 10px 10px 10px 10px;">
        <div class="col-md-12">
             <p style="display: inline;font-size: 25px;position: relative;top: 5px;left: 10px;">Faculty Portfolio</p>
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
            <div class="details" style="position: relative;top: -10px">
                <div class="row">
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="/FacultyPortFolio/CVdepartment" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/cv.png')}}" width="96px" height="90px" style="margin-top: 50px;"/>
                            <br>
                            <p style="padding-top: 10px;color: #0d2f81;">Lecturer CV</p>
                            </center>
                        </a>
                    </div>
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <a href="/FacultyPortFolio/SyllabusDepartment" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link">
                            <center>
                            <img src="{{url('image/syllabus.png')}}" width="70px" height="90px" style="margin-top: 50px;"/>
                            <br>
                            <p style="padding-top: 10px;color: #0d2f81;">Syllabus</p>
                            </center>
                        </a>
                        
                    </div>
                    <?php
                    $i=1;
                    ?>
                    @foreach($faculty_portfolio as $row)
                    <div class="col-md-3" style="margin-bottom: 20px">
                            @if($row->portfolio_type=="folder")
                            <a href="/faculty_portfolio/folder/{{$row->fp_id}}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;"   id="download_link" class="file_listing">
                                <p class="file_action_block">
                                  <!-- <i class="fa fa-times" aria-hidden="true" id="remove_button_file"></i> -->
                                  <i class="fa fa-caret-down action_button_file" aria-hidden="true" id="action_button_file_<?php echo $i?>"></i>
                                  <div class="dropdown_list w3-animate-top" id="dropdown_list<?php echo $i?>">
                                    <i class="fa fa-wrench edit_button_file" aria-hidden="true" id="edit_button_file_{{$row->fp_id}}"></i><br>
                                    <i class="fa fa-times remove_button_file" aria-hidden="true" id="remove_button_file_{{$row->fp_id}}"></i>
                                  </div>
                                </p>
                                <center>
                                <img src="{{url('image/folder2.png')}}" width="85px" height="90px" style="margin-top: 35px;" />
                            @else
                            <?php
                                $filename = "";
                                if($row->portfolio_file!=""){
                                    $filename = explode("___", $row->portfolio_file);
                                }
                            ?>
                            <a download="<?php echo $filename[1]?>" href="{{ asset('f_Portfolio/'.$row->faculty_id.'/'.$row->portfolio_file) }}" style="border: 1px solid #cccccc;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" id="download_link" class="file_listing">
                              <p class="file_action_block">
                                  <i class="fa fa-caret-down action_button_file" aria-hidden="true" id="action_button_file_<?php echo $i?>"></i>
                                  <div class="dropdown_list w3-animate-top" id="dropdown_list<?php echo $i?>">
                                    <i class="fa fa-times remove_button_file" aria-hidden="true" id="remove_button_file_{{$row->fp_id}}"></i>
                                  </div>
                                </p>
                              <center>
                                <?php
                                    $ext = "";
                                    if($row->portfolio_file!=""){
                                        $ext = explode(".", $row->portfolio_file);
                                    }
                                ?>
                                @if($ext!="")
                                    @if($ext[1]=="pdf")
                                    <img src="{{url('image/pdf.png')}}" width="85px" height="90px" style="margin-top: 35px;" />
                                    @elseif($ext[1]=="docx")
                                    <img src="{{url('image/docs.png')}}" width="85px" height="90px" style="margin-top: 35px;" />
                                    @elseif($ext[1]=="xlsx")
                                    <img src="{{url('image/excel.png')}}" width="85px" height="90px" style="margin-top: 35px;" />
                                    @endif   
                                @endif
                            @endif
                            <br>
                            <p style="padding: 10px 10px 0px 10px;color: #0d2f81;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;">{{$row->portfolio_name}}</p>
                            </center>
                            </a>
                        
                    </div>
                    <?php
                    $i++;
                    ?>
                    @endforeach
                    <input type="hidden" id="active_dropdownlist" value="">
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
<div class="modal fade bd-example-modal-lg" id="folderNameEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit Folder Name</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="post" action="{{action('F_PortFolioController@updateFolderName')}}">
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
                      <label for="subject_type" class="label">Folder Name</label>
                      <input type="hidden" name="fp_id" id="fp_id" value="">
                      <input type="text" name="folder_name" class="form-control" id="folder_name" placeholder="Folder" required/>
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
      <form method="post" action="{{action('F_PortFolioController@storeFiles')}}">
        {{csrf_field()}}
        <input type="hidden" name="count" value="0" id="count">
        <input type="hidden" name="file_place" value="Faculty">
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