<?php
if(auth()->user()->position=="Dean"){
   	$layout = 'layouts.nav_dean';
}else if(auth()->user()->position=="HoD"){
   	$layout = 'layouts.nav_hod';
}
?>
@extends($layout)

