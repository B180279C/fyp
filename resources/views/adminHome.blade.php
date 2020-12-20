<?php
    $title = "Home";
    $option0 = "id='selected-sidebar'";
?>
@extends('layouts.nav')
   
@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js" charset="utf-8"></script>
<script src="https://cdn.jsdelivr.net/gh/emn178/chartjs-plugin-labels/src/chartjs-plugin-labels.js"></script>
<script>
    var url = "{{url('admin/chartProgramme')}}";
    var programmes = new Array();
    var count = new Array();
    var color = new Array();
    $(document).ready(function(){
        $.get(url, function(response){
        response.forEach(function(data){
            
            programmes.push(data.name);
            count.push(data.count);
        });
        for(var i=0;i<=40;i++){
            var r = Math.floor(Math.random() * 255);
            var g = Math.floor(Math.random() * 255);
            var b = Math.floor(Math.random() * 255);
            var c = 'rgb(' + r + ', ' + g + ', ' + b + ')';
            color.push(c);
        }
        var ctx = document.getElementById("canvas_programmes").getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels:['Bachelor in Accounting (Honours)','Diploma in Accountancy','Bachelor BA (Hons) in Finance & Investment','Diploma in Financial Analysis','Bachelor of Software Engineering (Hons)','Diploma in Information Technology','Bachelor of Electronic Engineering with Honours','Diploma in Electrical & Electronic Engineering','BA (Hons) in Chinese Studies'],
                     datasets: [{
                        label: 'Number of Students',
                        data:[10,20,30,10,30,20,10,30,20],
                        backgroundColor:[
                            '#cc33ff',
                            '#0000ff',
                            '#0099cc',
                            '#ffff00',
                            '#ff5050',
                            '#33cc33',
                            '#ff9900',
                            '#00ffcc',
                            '#ff0066',
                            '#669999',
                        ],
                        borderColor: "#fff",
                        borderWidth: [1, 1, 1, 1, 1,1,1]
                    }],
                },
                options: {
                    plugins: {
                        labels: {
                            precision: 0,
                            fontSize:10,
                            fontColor: '#fff',
                            fontStyle: 'bold',
                            position: 'default',
                            textMargin: 4,
                        },
                    },
                    responsive : true,
                    title: {
                        display: true,
                        position: "top",
                        text: "",
                        fontSize: 18,
                        fontColor: "#111"
                    },
                    legend: {
                        display: true,
                        position: "bottom",
                        labels: {
                            fontColor: "#333",
                            fontSize: 10
                        }
                    }
                }
            });
        });
    });

    var url2 = "{{url('admin/chartStudent')}}";
    var semester_name = new Array();
    var count_student = new Array();
    $(document).ready(function(){
        $.get(url2, function(response){
        response.forEach(function(data){
            semester_name.push(data.name);
            count_student.push(data.count);
        });
        var ctx_student = document.getElementById("canvas_student").getContext('2d');
            var myChart = new Chart(ctx_student, {
                type: 'bar',
                data: {
                    labels:['2018_C','2019_A','2019_B','2019_C','2020_A','2020_B','2020_C'],
                     datasets: [{
                        label: 'Number of Students',
                        data:[300,199,402,370,150,278,306],
                        backgroundColor:[
                            '#cc33ff',
                            '#0000ff',
                            '#0099cc',
                            '#ffff00',
                            '#ff5050',
                            '#33cc33',
                            '#ff9900',
                            '#00ffcc',
                            '#ff0066',
                            '#669999',
                        ],
                        borderWidth: 1
                    }],
                },
                options: {
                    legend: {
                        display: false
                    },
                    plugins: {
                        labels: {
                            render:'value'
                        }
                    },
                    scales: {
                        yAxes: [{
                            ticks: { 
                                beginAtZero: true,
                            }
                         }],
                         xAxes: [{
                            ticks: {
                                fontSize : 12,
                            }
                        }],
                    }
                }
            });
        });
    });
    </script>
<style type="text/css">
    .list_data{
        color: black;
        font-weight: bold;
        margin: 0px;
        padding: 2px;
    }
    .list_data_name{
        display: block;
        width: 100%;
        padding:0px 10px;    
    }
</style>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">Home</p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 5px 18px;margin: 10px 0px 0px 0px;">
        <div class="col-md-3" style="padding: 2px;">
            <a href="/staff_list" class="row list_data" id="course_list" style="border:5px solid #a6b3ff;">
                <div class="list_data_name" style="border:2px solid #a6b3ff;">
                    <i class="fa fa-users" aria-hidden="true" style="font-size: 50px;padding:10px 0px;float: right;color:#3352ff;"></i>
                    <span style="font-size: 22px;display: block;padding: 5px 0px 0px 0px;color: #3352ff;">Staffs</span> 
                    <span style="font-size: 18px;display: block;padding:0px;color: #3352ff;">{{count($staffs)}}</span>
                </div>
            </a>
        </div>
        <div class="col-md-3" style="padding: 2px;">
            <a href="/student_list" class="row list_data" id="course_list" style="border:5px solid #ff5991;">
                <div class="list_data_name" style="border:2px solid #ff5991;">
                    <i class="fa fa-graduation-cap" aria-hidden="true" style="font-size: 50px;padding:10px 0px;float: right;color:#ff0055;"></i>
                    <span style="font-size: 22px;display: block;padding: 5px 0px 0px 0px;color:#ff0055;">Students</span> 
                    <span style="font-size: 18px;display: block;padding:0px;color:#ff0055;">{{count($student)}}</span>
                </div>
            </a>
            
        </div>
        <div class="col-md-3" style="padding: 2px;">
            <a href="/semester_list" class="row list_data" id="course_list" style="border:5px solid #ffc970;">
                <div class="list_data_name" style="border:2px solid #ffc970;">
                    <i class="fa fa-calendar" aria-hidden="true" style="font-size: 50px;padding:10px 0px;float: right;color:#ff9d00;"></i>
                    <span style="font-size: 22px;display: block;padding: 5px 0px 0px 0px;color:#ff9d00;">Current Semester</span> 
                    <span style="font-size: 18px;display: block;padding:0px;color:#ff9d00;">{{$last_semester->semester_name}}</span>
                </div>
            </a>
            
        </div>
        <div class="col-md-3" style="padding: 2px;">
            <a href="/faculty_list" class="row list_data" id="course_list" style="border:5px solid #64dfb6;">
                <div class="list_data_name" style="border:2px solid #64dfb6;">
                    <i class="fa fa-folder-open" aria-hidden="true" style="font-size: 50px;padding:10px 0px;float: right;color:#1e946d;"></i>
                    <span style="font-size: 22px;display: block;padding: 5px 0px 0px 0px;color:#1e946d;">Faculty</span> 
                    <span style="font-size: 18px;display: block;padding:0px;color:#1e946d;">{{count($faculty)}}</span>
                </div>
            </a>
        </div>
    </div>
    <hr style="margin: 10px 10px 5px 20px;background-color: #d9d9d9;">
    <div class="row" style="padding: 0px 10px 10px 5px;margin:0px;">
        <div class="col-md-6" style="border-right:2px solid #d9d9d9;">
            <p class="page_title" style="position: relative;left: 5px ;top: -5px;">Each semester of Num of Students.</p>
            <canvas id="canvas_student" height="100px" width="100%;"></canvas>
        </div>
        <div class="col-md-6">
            <p class="page_title" style="position: relative;left: 5px ;top: -5px;">Newest Semester of Students in Each Programme.</p>
            <canvas id="canvas_programmes" height="80px" width="100%;"></canvas>
        </div>
    </div>
    </div>
</div>
@endsection
