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
	<p style="padding: 0px 0px;margin: 0px 10px;display: inline;font-size: 20px;position: relative;top: 8px;">{{$checkARID->student_id}} : {{$assessments->assessment_name}} ( {{$submitted_by}} )</p>
	<p style="padding: 0px;margin: 0px 30px;float: right;display: inline;border-radius: 50%;"><i class="fa fa-download" style="color: black!important;font-size: 20px;padding: 10px 11px;border-radius: 50%;" aria-hidden="true" id="download"></i></p>
</div>
<br>
<br>
<br>
<br>
<?php
$i=1;
?>
@foreach($assessment_result_list as $row)
<?php
    $ext = "";
    if($row->document!=""){
       $ext = explode(".", $row->document);
   	}
?>
@if(($ext[1] != "pdf")&&($ext[1] != "docx")&&($ext[1] != "xlsx")&&($ext[1] != "pptx"))
<center>
<?php
if(isset($string)){
?>
<img src="{{$character}}/CourseList/PastYear/images/AssessmentResult/{{$string[0]}}-{{$row->document}}" width="600px" height="auto">
<?php
}else{
?>
<img src="{{$character}}/CourseList/images/AssessmentResult/{{$row->document}}" width="600px" height="auto">
<?php
}
?>
<br>
<?php
// echo $i;
?>
</center>
<?php
$i++;
?>
@endif
@endforeach
<br>
</body>
</html>