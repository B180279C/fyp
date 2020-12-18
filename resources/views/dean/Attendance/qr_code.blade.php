<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
    
<div class="visible-print text-center">
	<h1></h1>
    <!-- {!! QrCode::size(250)->generate('http://127.0.0.1:8000'.$character."/Attendance/Student/".$attendance_id."/".$code); !!} -->
    <div style="vertical-align: middle;height: auto;">
	    <center>{!! QrCode::size(250)->generate('http://127.0.0.1:8000/Attendance/1'); !!}</center>
	    <center><p>Scan by QR scanning.</p></center>
	    <center><p>http://127.0.0.1:8000{{$character}}/Attendance/Student/login/{{$attendance_id}}/{{$code}}</p></center>
    </div>
</div>
</body>
</html>