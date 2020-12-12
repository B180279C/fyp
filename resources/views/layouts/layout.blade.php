<?php
if(auth()->user()->position=="Dean"){
   	$layout = 'layouts.nav_dean';
}else if(auth()->user()->position=="HoD"){
   	$layout = 'layouts.nav_hod';
}else if(auth()->user()->position=="Lecturer"){
   	$layout = 'layouts.nav_lecturer';
}else if(auth()->user()->position=="student"){
   	$layout = 'layouts.nav_student';
}
?>
@extends($layout)

