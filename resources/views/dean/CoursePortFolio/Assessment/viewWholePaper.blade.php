<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<style type="text/css">
		body{
			background-color: black;
			margin: 0px;
			padding: 0px;
		}
		.question_title{
			background-color: white;
			padding: 10px;
			margin: 0px;
			position: fixed;
			display: block;
			z-index: 9999;
			width: 100%;
			box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
		}
		#download:hover{
			background-color: #ccc!important;
		}
	</style>
</head>
<body>
<div class="question_title">
	<p style="padding: 0px 0px;margin: 0px 10px;display: inline;font-size: 20px;position: relative;top: 8px;">{{$assessment_list[0]->semester_name}} : {{$question}} / {{$assessments->assessment_name}}</p>
	<p style="padding: 0px;margin: 0px 30px;float: right;display: inline;border-radius: 50%;"><i class="fa fa-download" style="color: black!important;font-size: 20px;padding: 10px 11px;border-radius: 50%;" aria-hidden="true" id="download"></i></p>
</div>
<br>
<br>
<br>
<br>
<?php
$i=1;
?>
@foreach($assessment_list as $row)
<center>
<img src="{{$character}}/CourseList/images/assessment/{{$row->ass_document}}" width="600px" height="auto">
<br>
<?php
// echo $i;
?>
</center>
<?php
$i++;
?>
@endforeach
<br>
</body>
</html>