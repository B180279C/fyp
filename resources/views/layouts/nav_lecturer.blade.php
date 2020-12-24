<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>UCMS</title>


    <!-- Scripts -->
    <script
      src="https://code.jquery.com/jquery-3.4.1.min.js"
      integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
      crossorigin="anonymous"></script>
    <script src="https://unpkg.com/popper.js@1.12.6/dist/umd/popper.js" integrity="sha384-fA23ZRQ3G/J53mElWqVJEGJzU0sTs+SvzG8fXVWP+kJQ1lwFAOkcUOysnlKJC33U" crossorigin="anonymous"></script>
    

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


    <!-- Latest compiled and minified CSS -->
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css"> -->
    <link rel="stylesheet" href="{{ asset('bootstrap-select.min.css') }}">
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script type="text/javascript">
        $(function () {
            $('.selectpicker').selectpicker();
        });
    </script>

    <!-- MDB -->
    <script src="https://unpkg.com/bootstrap-material-design@4.1.1/dist/js/bootstrap-material-design.js" integrity="sha384-CauSuKpEqAFajSpkdjv3z9t8E7RlpJ1UP0lKM/+NdtSarroVKu069AlsRPKkFBz9" crossorigin="anonymous"></script>
    <script>$(document).ready(function() { $('body').bootstrapMaterialDesign(); });</script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons">
    <link rel="stylesheet" href="https://unpkg.com/bootstrap-material-design@4.1.1/dist/css/bootstrap-material-design.min.css" integrity="sha384-wXznGJNEXNG1NFsbm0ugrLFMQPWswR3lds2VeinahP8N0zJw9VWSopbjv2x7WCvX" crossorigin="anonymous">


    <!-- dropzone -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/dropzone.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/dropzone.css">

    <!-- datatables -->
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css"> -->
    <link rel="stylesheet" href="{{ asset('jquery.dataTables.min.css')}}">
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.10.15/pagination/input.js"></script>

    <!-- Editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

    <!-- lightbox Image -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.min.js"></script>
    <link rel="stylesheet" href="{{ asset('ekko-lightbox.css')}}">
    <script src = "https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.js" crossorigin="anonymous"></script>
    <!-- <script type="text/javascript">
        $().ready(function() {
           $('[data-toggle="lightbox"]').click(function(event) {
             event.preventDefault();
                 $(this).ekkoLightbox({
                   type: 'image',
                   onContentLoaded: function() {
                     var container = $('.ekko-lightbox-container');
                     var content = $('.modal-content');
                     var backdrop = $('.modal-backdrop');
                     var overlay = $('.ekko-lightbox-nav-overlay');
                     var modal = $('.modal');
                     var image = container.find('img');
                     var windowHeight = $(window).height();
                     var dialog = container.parents('.modal-dialog');
                     var data_header = $('.modal-header');
                     var data_title = $('.modal-title');
                     var body = $('.modal-body');
                     console.log(image.width());

                     if((image.width() > 380) && (image.width() < 441)){
                        dialog.css('max-width','700px');
                        image.css('height','900px');
                        image.css('width','700px');
                        overlay.css('height','900px');
                     }else{
                        overlay.css('height','100%');
                     }
                     // backdrop.css('opacity','1');
                     data_header.css('background-color','white');
                     data_header.css('padding','10px');
                     data_header.css('margin','0px 24px');
                     data_header.css('border-bottom','1px solid black');
                     data_title.css('font-size','18px');

                     body.css('padding-top','0px');
                     body.css('padding-bottom','0px');
                     body.css('margin', "0px 25px");
                     body.css('background-color', "white");
                     content.css('background', "none");
                     content.css('-webkit-box-shadow', "0 5px 15px rgba(0,0,0,0)");
                     content.css('-moz-box-shadow', "0 5px 15px rgba(0,0,0,0)");
                     content.css('-o-box-shadow', "0 5px 15px rgba(0,0,0,0)");
                     content.css('box-shadow', "0 5px 15px rgba(0,0,0,0)");
                   }
                 });
           });
         });
    </script> -->

    <!-- OCR function -->
    <script src='https://unpkg.com/tesseract.js@2.1.3/dist/tesseract.min.js'></script>
    <!-- // const exampleImage = 'https://tesseract.projectnaptha.com/img/eng_bw.png';
    // const worker = Tesseract.createWorker({
    //   logger: m => console.log(m)
    // });
    // Tesseract.setLogging(true);
    // work();

    // async function work() {
    //   await worker.load();
    //   await worker.loadLanguage('eng');
    //   await worker.initialize('eng');

    //   let result = await worker.detect(exampleImage);
    //   console.log(result.data);

    //   result = await worker.recognize(exampleImage);
    //   console.log(result.data);

    //   await worker.terminate();
    // } -->


    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <style type="text/css">
        .w3-sidebar{height:100%;width:200px;background-color:#fff;position:fixed!important;z-index:1;overflow:auto}
        .w3-animate-left{position:relative;animation:animateleft 0.4s}@keyframes animateleft{from{left:-300px;opacity:0} to{left:0;opacity:1}}
        .w3-animate-top{position:absolute;animation:animatetop 0.4s}@keyframes animatetop{from{top:-300px;opacity:0} to{top:0;opacity:1}}
        .w3-animate-right{position:relative;animation:animateright 0.4s}@keyframes animateright{from{right:-300px;opacity:0} to{right:0;opacity:1}}
    </style>

    <!-- sidebar -->
    <script>
    function getWidth() {
        return Math.max(
            document.body.scrollWidth,
            document.documentElement.scrollWidth,
            document.body.offsetWidth,
            document.documentElement.offsetWidth,
            document.documentElement.clientWidth
        );
    }
    $(window).resize(function() {
      var width = $(window).width();
      // var height = $(window).height();
      if(width<600){
        $("#sidebar").hide();
        $("#sidebar-title").hide();
        $("#sidebar-table").hide();
        $("#main").attr('class', 'col-md-12');
        $("#nav_main").attr('class', 'col-md-12');
        document.getElementById('openOclose').value = "open";
      }else{
        $("#main").attr('class', 'col-md-9');
        $("#nav_main").attr('class', 'col-md-9');
        $("#sidebar").show();
        $("#sidebar-table").show();
        $("#sidebar-title").show();
        document.getElementById('openOclose').value = "close";
      }
    })
    $(document).ready(function(){
        $("#button_side").click(function(){
          var width = getWidth();
          var value = document.getElementById('openOclose').value;
            if(value=="open"){
                $("#sidebar").show();
                $("#sidebar-title").show();
                $("#main").attr('class', 'col-md-9');
                $("#nav_main").attr('class', 'col-md-9');
                $(".navbar-brand-second").hide();
                document.getElementById("openOclose").value = "close";
            }else{
                $("#sidebar").hide();
                $("#sidebar-title").hide();
                $("#main").attr('class', 'col-md-12');
                $("#nav_main").attr('class', 'col-md-12');
                $("#main_container").attr('class', 'col-md-12');
                $(".navbar-brand-second").show();
                document.getElementById("openOclose").value = "open";
            }
        });

        // $.ajaxSetup({
        //   headers: {
        //     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //   }
        // });
        // var value = $('#user_id').val();
        // $.ajax({
        //     type:'POST',
        //     url:'/deanDetails',
        //     data:{value:value},
        //     success:function(data){
        //         if(data!="null"){
        //             document.getElementById("myImage").src = "{{URL::asset('/staffImage/')}}"+"/"+data;
        //         }else{
        //             document.getElementById("myImage").src = "{{URL::asset('/image/user.png')}}";
        //         }
        //     }
        // });
    });
    </script>
</head>
<body>
    <div id="app">
        <div class="row" style="padding: 0px; margin: 0px;">
            <div class="col-md-3 w3-animate-left" id="sidebar-title" style="margin: 0px;padding:0px;background-color: #0d2f81;">
                <center>
                <table>
                    <tr>    
                        <td style="padding:10px;">
                            <a class="navbar-brand" href="/admin/home">University Content Management System</a>
                        </td>
                    </tr>
                </table>
                </center>
            </div>
            <div class="col-md-9" id="nav_main" style="background-color: white;">
                <div style="float: left;padding:10px 10px 10px 0px;">
                <input type="hidden" id="openOclose" value="">
                    <button id="button_side" style="display: inline-block;">
                        <i class="fa fa-bars" aria-hidden="true" style="color: black;"></i>
                    </button>
                    <a class="navbar-hide" href="">UCMS</a>
                    <a class="navbar-brand-second w3-animate-top" style="display: none" href="">University Content Management System</a>
                </div>
                <div class="nav-item dropdown" style="float: right;padding:7px 0px 7px 7px;">
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('student.create') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
                @else
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre style="color: black!important;">
                        <!-- {{ Auth::user()->name }}  -->
                        <i class="fa fa-user-circle" aria-hidden="true"></i>
                        <span class="caret"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('lecturer.Profile') }}">
                            Profile
                        </a>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
                @endguest
            </div>
        </div>
        <div class="row" style="height: 101%;padding: 0px; margin: 0px;">
            <div class="col-md-3 w3-animate-left" id="sidebar">
            <!-- <input type="hidden" id="smallthan" value="0">  -->
<!--                <hr style="margin: 0px;background-color: #0d2f81;"> -->
                    <ul class="sidebar-ul">
                        <a href="/lecturer/home">
                            <li class="sidebar-li" <?php if(isset($option0)){ echo $option0;};?>>
                                <i class="fa fa-home sidebar-icon" aria-hidden="true"></i>
                                <span style="padding-left: 20px;font-weight: bold;">Home</span>
                            </li>
                        </a>
                        <a href="/lecturer/course_list">
                            <li class="sidebar-li" <?php if(isset($option1)){ echo $option1;};?>>
                                    <i class="fa fa-book sidebar-icon" aria-hidden="true"></i>
                                    <span style="padding-left: 20px;font-weight: bold;">My Courses</span>
                            </li>
                        </a>
                    </ul>
                    <p style="padding:0px 0px 5px 10px;margin: 0px;color: #e5e7e8;">Moderation Function</p>
                    <ul class="sidebar-ul">
                        <a href="/lecturer/Moderator">
                            <li class="sidebar-li" <?php if(isset($option3)){ echo $option3;};?>>
                                <i class="fa fa-info sidebar-icon" aria-hidden="true" style="padding-left: 11px;"></i>
                                <span style="padding-left: 20px;font-weight: bold;">Moderator</span>
                            </li>
                        </a>
                    </ul>
                    <input type="hidden" name="user_id" id="user_id" value="{{Auth::user()->user_id}}">
                    <table id="sidebar-table" style="background-color: #e5e7e8; border:none;width: 100.1%;padding:0px;margin: 0px; position: absolute; bottom: 0px;">
                        <tr>
                            <td>&nbsp;</td>
                            <td id="sidebar-img">
                                <center>
                                    <img src="{{ action('HomeController@lecturerDetails', Auth::user()->user_id ) }}" height="50px;" style="border-radius: 50%; border:2px solid white;margin-left: auto;margin-right: auto;width: 60%;" id="myImage">        
                                </center>
                            </td>
                            <?php
                                $email = Auth::user()->email;
                                $dean_id = explode("@", $email);
                            ?>
                            <td>
                                <p id="slidebar-details" style="border-bottom: 1px solid white;color: black;"><b>
                                    {{Auth::user()->name}} ( <?php echo $dean_id[0]?> )</b></p>
                                <p id="slidebar-details" style="color: grey"><b>Lecturer</b></p>
                            </td>
                        <tr>
                    </table>
            </div>
            <div class="col-md-9" id="main" style="border:0px solid black;padding: 0px;margin: 0px;">
                <br>
                <div class="container" id="main_container" style="border:0px solid black;">
                    @yield('content')
                </div>
                <br>
            </div>
        </div>
</body>
</html>
