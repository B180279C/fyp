<?php
$title = "Course";
$option1 = "id='selected-sidebar'";
?>
@extends('layouts.nav_dean')

@section('content')

<style type="text/css">
.short-div{
    height: 200px;
}
.editor{
    height: 100px;
}
.editor_t{
    height: 100px;
}
.editor_a{
    height: 100px;
}
.editor_r{
    height: 100px;
}
#topic_sub{
    width:95%;
    padding:0px 0px 20px 0px;
    border-bottom: 1px solid black;
}
.topic_remove{
    text-align: right;
    padding:0px 40px 0px 0px;
}
@media only screen and (max-width: 600px) {
    #topic_sub{
        margin:0px;
        padding:0px 0px 20px 0px;
        width: 100%;
    }
    .topic_remove{
        text-align: right;
        padding:0px;
    }
}
</style>
<div id="all">
    <div>
        <p style="margin: 0px;padding:10px 20px;font-size: 30px;">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</p>
        <p class="pass_page">
            <a href="/home" class="first_page"> Home </a>/
            <a href="/course_list">Courses </a>/
            <a href="/course/action/{{$course[0]->course_id}}">{{$course[0]->semester_name}} : {{$course[0]->subject_code}} {{$course[0]->subject_name}}</a>/
            <a href="/teachingPlan/{{$course[0]->course_id}}">Teaching Plan</a>/
            <span class="now_page">Manage Assessment Method</span>/
        </p>
        <hr class="separate_hr">
    </div>
    <div class="row" style="padding: 10px 10px 0px 10px;">
        <div class="col-md-12">
            <p style="display: inline;font-size: 25px;position: relative;top: 5px;left: 10px;color: #0d2f81;">Methods of Assessment</p>
            <hr style="margin-top: 5px;margin-bottom: 0px;padding: 0px;">
            @if(\Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <Strong>{{\Session::get('success')}}</Strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            <div class="details" style="padding: 0px 5px 0px 5px;">
                <div class="row" style="padding:0px;"> 
                    <div class="col-md-12" style="padding:0px;">
                        <form method="post" action="{{action('TeachingPlanController@storeTPAss', $course[0]->course_id)}}" id="form">
                            {{csrf_field()}}
                            <input type="hidden" id="course_id" value="{{$course[0]->course_id}}">
                        </form>         
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $(document).on('click', '.week', function(){  
            var id = $(this).attr("id");
            $('#plan_detail_'+id).slideToggle("slow", function(){
                if($('#plan_detail_'+id).is(":visible")){
                    $('#icon_'+id).removeClass('fa fa-plus');
                    $('#icon_'+id).addClass('fa fa-minus');
                }else{
                    $('#icon_'+id).removeClass('fa fa-minus');
                    $('#icon_'+id).addClass('fa fa-plus');
                }
            });
        });
    });

    $(document).ready(function(){
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        var course_id = $('#course_id').val();
        $.ajax({
            type:'POST',
            url:'/teachingPlan/getSyllabusData',
            data:{course_id:course_id},
            success:function(response){
                // alert(response[1]);

                var table = document.getElementById("table");
                var m = 1;
                var p = 0;
                var k = 0;
                var array = [];
                var array_data = [];
                var array_f_data = [];
                for(var i = 0;i<=(response.length-1);i++){
                    if(response[i][2]!=null){
                        var str = response[i][2].toString();
                        if((str.includes("CLO"))&&(response[i][1]==null)&&(response[i][3]!=null)&&(response[i][15]==null)){
                            var data = response[i][3];
                            var value = data.split("(").splice(-1);
                            value = value.toString();
                            var level = value.split(",");
                            // console.log(level[1].replace(')',''));
                            array[p] = level[1].replace(')','');
                            var plo = array[p].replace('PLO','');
                            var int = parseInt(plo);
                            array[p] = int;
                            p++;
                        }
                    }
                    if(response[i][8]!=null){
                        if((response[i][8]=== parseInt(response[i][8], 10))&&(response[i][9]!=null)){
                            // console.log(response[i]);
                            array_data[k] = response[i][9];
                            k++;
                        }  
                    }
                }
                var a = 0;
                for(var q = 0; q<array.length;q++){
                    const sorted = array.sort((a,b)=>a-b);
                    if(q!=0){
                        if(sorted[q]==sorted[q-1]){
                            array_f_data[q] = array_f_data[a];
                        }else{
                            a++;
                            array_f_data[q] = sorted[q]+"///"+array_data[a];
                        }
                    }else{
                        array_f_data[q] = sorted[q]+"///"+array_data[q];
                    }
                    console.log(array_f_data[q]);
                }
                for(var i = 0;i<=(response.length-1);i++){
                    // console.log(response[i]);
                    $('.selectpicker').selectpicker();
                    if(response[i][2]=="Continuous Assessment"){
                        var count = i;
                    }
                    if(response[i][2]!=null){
                        var str = response[i][2].toString();
                        if((str.includes("CLO"))&&(response[i][1]==null)&&(response[i][3]!=null)&&(response[i][15]==null)){

                            var data = response[i][3];
                            var value = data.split("(").splice(-1);
                            value = value.toString();

                            var clo = data.split('('+value);
                            var level = value.split(",");
                            var plo = level[1].replace(')','').replace('PLO','');
                            var int = parseInt(plo);
                            
                            for(var q = 0; q<array_f_data.length;q++){
                                var po = array_f_data[q].split("///");
                                if(po[0]==int){
                                    var po_data = po[1];
                                }
                            }
                            $('#form').append('<p class="col-12 align-self-center week" id="'+m+'" style="padding:10px 10px;font-size: 20px;margin: 0px;"><i class="fa fa-plus" id="icon_'+m+'" aria-hidden="true" style="font-size: 20px;color: #0d2f81"></i> CLO '+m+'</p><div class="teachingPlan" style="border-bottom: 1px solid grey;padding:0px 20px;"><div class="row plan" id="plan_detail_'+m+'" style="padding: 0px 20px;display: none;"0><div class="col-md-12 row" style="padding:0px; margin: 0px;display: inline-block;"><div class="col-md-12 row"><div class="col-1 align-self-center" style="padding: 15px 0px 0px 0px;"><p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;"><i class="fa fa-tag" aria-hidden="true" style="font-size: 18px;"></i></p></div><div class="col-11" style="position:relative;"><div class="form-group"><label class="label" style="font-size:12px">Course Learning Outcomes ( CLO )</label><input type="text" name="CLO_'+m+'" class="form-control" placeholder="" value="'+clo[0]+'"></div></div></div><div class="col-md-12 row"><div class="col-1 align-self-center" style="padding: 15px 0px 0px 0px;"><p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;"><i class="fa fa-thumb-tack" aria-hidden="true" style="font-size: 18px;"></i></p></div><div class="col-11"><div class="form-group"><label class="label" style="font-size:12px">Programme Outcomes ( PO )</label><input type="text" name="PO_'+m+'" class="form-control" placeholder="" value="'+po_data+'"></div></div></div><div class="col-md-12 row"><div class="col-1 align-self-center" style="padding: 15px 0px 0px 0px;"><p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;"><i class="fa fa-list" aria-hidden="true" style="font-size: 18px;"></i></p></div><div class="col-5"><div class="form-group"><label class="label" style="font-size:12px">Domain & Taxonomy Level</label><input type="text" name="domain_level_'+m+'" class="form-control" placeholder="e.g. A2/C3" value="'+level[0]+'"></div></div><div class="col-1 align-self-center" style="padding: 15px 0px 0px 0px;"><p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;"><i class="fa fa-code-fork" aria-hidden="true" style="font-size: 18px;"></i></p></div><div class="col-5"><div class="form-group"><label class="label" style="font-size:12px">Teaching Methods</label><select class="selectpicker form-control" multiple name="method_'+m+'[]" data-width="100%" title="Mutiple Choose"><option class="option">Lecture</option><option class="option">Tutorial</option><option class="option">Practical</option><option class="option">Others</option></select></div></div></div><div class="col-md-12 row"><div class="col-1 align-self-center" style="padding: 15px 0px 0px 0px;"><p class="text-center align-self-center" style="margin: 0px;padding:0px;font-size: 20px;width: 30px!important;border-radius: 50%;background-color: #0d2f81;color: gold;"><i class="fa fa-table" aria-hidden="true" style="font-size: 18px;"></i></p></div><label class="label col-11" style="padding:20px 0px 0px 15px;">Assessment Methods & Mark Breakdown</label></div></div><div class="col-md-12 row"><div class="col-1 align-self-center" style="padding: 15px 0px 0px 0px;"></div><div class="col-11"><table border="1" width="100%" id="table_'+m+'" style="text-align:center;"></table><br></div></div>          </div></div></div>');
                                var table = document.getElementById("table_"+m);
                                var row = table.insertRow(0);
                                var row1 = table.insertRow(1);
                                var row2 = table.insertRow(2);
                                var num = 0;
                                var assessment = "";
                                var assessment_num = "";
                                for(var k = 0;k<=(response.length-1);k++){
                                    if(Number.isInteger(response[k][2])){
                                        assessment += response[k][3]+",";
                                        assessment_num += response[k][9]+",";
                                        // console.log(response[0][k]);
                                        var cell = row.insertCell(num);
                                        var cell1 = row1.insertCell(num);
                                        var cell2 = row2.insertCell(num);
                                        cell.innerHTML  = response[k][3];
                                        cell1.innerHTML  = response[k][9]+"%";
                                        cell2.innerHTML  = "<input type='checkbox' name='assessment_"+m+"_"+num+"' value='yes'>";
                                        num++;
                                    }
                                }
                            m++;
                        }
                    }
                }
                $('#form').append('<input type="hidden" name="num" value="'+num+'"><input type="hidden" name="assessment_name" value="'+assessment+'"><input type="hidden" name="assessment_num" value="'+assessment_num+'"><input type="hidden" name="count" value="'+(m-1)+'"><div class="form-group" style="text-align: right;margin: 0px!important;padding-top: 20px;padding-right: 20px;"><input type="submit" class="btn btn-raised btn-primary" style="background-color: #3C5AFF;color: white;margin: 0px!important;" value="Save Change">&nbsp;</div>');
            }
        });
    });
</script>
@endsection