<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'University Content Management System') }}</title>


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

    


    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <style type="text/css">
        .w3-sidebar{height:100%;width:200px;background-color:#fff;position:fixed!important;z-index:1;overflow:auto}
        .w3-animate-left{position:relative;animation:animateleft 0.4s}@keyframes animateleft{from{left:-300px;opacity:0} to{left:0;opacity:1}}
        .w3-animate-top{position:relative;animation:animatetop 0.4s}@keyframes animatetop{from{top:-300px;opacity:0} to{top:0;opacity:1}}
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

    function logout(){
        if(confirm('Are you sure want to Logout?')){
            event.preventDefault();
            document.getElementById('logout-form').submit();
        }
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
        $('.tooltiptext').hide();
        $(".tooltip_hover").hover(function(){
          $('.tooltiptext').show();
          }, function(){
          $('.tooltiptext').hide();
        });
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
                $(".navbar-brand-second").show();
                document.getElementById("openOclose").value = "open";
            }
        });
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
                        <a class="dropdown-item" onclick="logout()">
                            <i class="fa fa-sign-out" aria-hidden="true"></i>&nbsp;&nbsp;{{ __('Logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
                @endguest
            </div>
        </div>
        <div class="row" style="height: 111%;padding: 0px; margin: 0px;overflow-y:;">
            <div class="col-md-3 w3-animate-left" id="sidebar">
            <!-- <input type="hidden" id="smallthan" value="0">  -->
<!--                <hr style="margin: 0px;background-color: #0d2f81;"> -->
                    <ul class="sidebar-ul">
                        <a href="/admin/home">
                            <li class="sidebar-li" <?php if(isset($option0)){ echo $option0;};?>>
                                <i class="fa fa-home sidebar-icon" aria-hidden="true"></i>
                                <span style="padding-left: 20px;font-weight: bold;">Home</span>
                            </li>
                        </a>
                    </ul>
                    <p style="padding:0px 0px 5px 10px;margin: 0px;color: #e5e7e8;">Action</p>
                    <ul class="sidebar-ul">
                        <a href="/semester_list">
                            <li class="sidebar-li" <?php if(isset($option7)){ echo $option7;};?>>
                                    <i class="fa fa-calendar sidebar-icon" aria-hidden="true"></i>
                                    <span style="padding-left: 20px;font-weight: bold;">Semester</span>
                            </li>
                        </a>
                        <a href="/courses">
                            <li class="sidebar-li" <?php if(isset($option8)){ echo $option8;};?>>
                                    <i class="fa fa-book sidebar-icon" aria-hidden="true"></i>
                                    <span style="padding-left: 20px;font-weight: bold;">Courses</span>
                            </li>
                        </a>
                    </ul>
                    <p style="padding:0px 0px 5px 10px;margin: 0px;color: #e5e7e8;">Management</p>
                    <ul class="sidebar-ul">
                        
                        <a href="/staff_list">
                            <li class="sidebar-li" <?php if(isset($option1)){ echo $option1;};?>>
                                    <i class="fa fa-briefcase sidebar-icon" aria-hidden="true"></i>
                                    <span style="padding-left: 20px;font-weight: bold;">Staff</span>
                            </li>
                        </a>
                        <a href="/student_list">
                            <li class="sidebar-li" <?php if(isset($option2)){ echo $option2;};?>>
                                <i class="fa fa-graduation-cap sidebar-icon" aria-hidden="true" style="padding-left: 8px;"></i>
                                <span style="padding-left: 20px;font-weight: bold;">Student</span>
                            </li>
                        </a>
                        <a href="/faculty_list">
                            <li class="sidebar-li" <?php if(isset($option3)){ echo $option3;};?>>
                                <i class="fa fa-folder-open sidebar-icon" aria-hidden="true" style="padding-left: 11px;"></i>
                                <span style="padding-left: 20px;font-weight: bold;">Faculty</span>
                            </li>
                        </a>
                        <a href="/department_list">
                            <li class="sidebar-li" <?php if(isset($option4)){ echo $option4;};?>>
                                <i class="fa fa-info sidebar-icon" aria-hidden="true" style="padding-left: 11px;"></i>
                                <span style="padding-left: 20px;font-weight: bold;">Department</span>
                            </li>
                        </a>
                        <a href="/programme_list">
                            <li class="sidebar-li" <?php if(isset($option5)){ echo $option5;};?>>
                                <i class="fa fa-address-book-o sidebar-icon" aria-hidden="true" style="padding-left: 11px;"></i>
                                <span style="padding-left: 20px;font-weight: bold;">Programme</span>
                            </li>
                        </a>
                        <a href="/mpu_list">
                            <li class="sidebar-li" <?php if(isset($option6)){ echo $option6;};?>>
                                <i class="fa fa-address-book sidebar-icon" aria-hidden="true" style="padding-left: 11px;"></i>
                                <span style="padding-left: 20px;font-weight: bold;">General Studies</span>
                            </li>
                        </a>
                        
                    </ul>
                    <table id="sidebar-table" style="background-color: #e5e7e8; border:none;width: 100.1%;padding:0px;margin: 0px; position: absolute; bottom: 0px;">
                        <tr>
                            <td>&nbsp;</td>
                            <td id="sidebar-img">
                                <center>
                                    <!-- <i class="fa fa-user" aria-hidden="true" style="font-size: 55px;"></i> -->
                                    <img src="{{ url('/image/weijun.jpg') }}" width=60% height="50px;" style="border-radius: 50%; border:2px solid white;">        
                                </center>
                            </td>
                            <td>
                                <p id="slidebar-details" style="border-bottom: 1px solid white;"><b>Lam Wei Chun (B180279C)</b></p>
                                <p id="slidebar-details" style="color: grey"><b>Adminstrator</b></p>
                            </td>
                        <tr>
                    </table>
            </div>
            <div class="col-md-9" id="main">
                <br>
                <div class="container">
                    @yield('content')
                </div>
                <br>
            </div>
        </div>
</body>
</html>
