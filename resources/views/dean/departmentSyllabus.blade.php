<?php
$title = "Department";
$option2 = "id='selected-sidebar'";
?>
@extends('layouts.nav_dean')

@section('content')
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$faculty->faculty_name}}</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/FacultyPortFolio"> Faculty PortFolio </a>/
            <span class="now_page">Department ( Syllabus )</span>/
        </p>
        <hr style="margin: -10px 10px;">
    </div>
    <div class="row" style="padding: 10px 10px 10px 10px;">
        <div class="col-md-12">
            <p style="display: inline;font-size: 25px;position: relative;top: 5px;left: 10px;color: #0d2f81;">Department</p>
            <div class="details" style="padding: 10px 5px 5px 5px;">
                <div class="row">
                    <?php
                    $i=0;
                    ?>
                    @foreach($departments as $row)
                    <div class="col-md-3" style="margin-bottom: 20px">
                        <center>
                            <a href="/FacultyPortFolio/SyllabusProgramme/<?php echo $row->department_id?>" style="border: 1px solid #cccccc;padding:40px;display: inline-block;height: 225px;width: 100%;border-radius: 10px;font-weight: bold;" id="download_link">
                                @if($i%2==0)
                                <i class="fa fa-building" aria-hidden="true" style="font-size: 72px;color: #0d2f81;"></i>
                                @else
                                <i class="fa fa-building-o" aria-hidden="true" style="font-size: 72px;color: #0d2f81;">
                                </i>
                                @endif
                                <br>
                                <br>
                                <p style="color: #2C2C2C;">{{$row->department_name}}</p>
                            </a>
                        </center>
                    </div>
                    <?php
                    $i++;
                    ?>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
