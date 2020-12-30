<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Image;
use App\Staff;
use App\Department;
use App\Programme;
use App\Faculty;
use App\Faculty_Portfolio;
use App\Subject;
use ZipArchive;
use File;

class F_PortFolioController extends Controller
{
    public function index()
    {
        $user_id    = auth()->user()->user_id;
        $staff_dean = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id = $staff_dean->faculty_id;
        $faculty    = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
        $faculty_portfolio = DB::table('faculty_portfolio')
                    ->select('faculty_portfolio.*')
                    ->where('faculty_id', '=', $faculty_id)
                    ->where('portfolio_place', '=', 'Faculty')
                    ->where('status', '=', 'Active')
                    ->orderByDesc('faculty_portfolio.portfolio_type')
                    ->get();
        return view('dean.FacultyPortFolio.FacultyPortFolio', compact('faculty_portfolio','faculty'));
    }

    public function searchFiles(Request $request)
    {
        $user_id    = auth()->user()->user_id;
        $staff_dean = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id = $staff_dean->faculty_id;
        $faculty    = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();

        $value = $request->get('value');
        $place = $request->get('place');
        $result = "";
        if($value!=""){
            $faculty_portfolio = DB::table('faculty_portfolio')
                    ->select('faculty_portfolio.*')
                    ->Where(function($query) use ($value) {
                          $query->orWhere('portfolio_name','LIKE','%'.$value.'%')
                            ->orWhere('portfolio_file','LIKE','%'.$value.'%');
                    })
                    ->where('faculty_id', '=', $faculty_id)
                    ->where('status', '=', 'Active')
                    ->orderByDesc('faculty_portfolio.portfolio_type')
                    ->get();
            if ($faculty_portfolio->count()) {
                foreach($faculty_portfolio as $row){
                    $data = "";
                    if($row->portfolio_place != "Faculty"){
                        $i=1;
                        $place = explode(',,,',$row->portfolio_place);
                        $data = "";
                        while(isset($place[$i])!=""){
                            $name = Faculty_Portfolio::where('fp_id', '=', $place[$i])->firstOrFail();
                            if($data==""){
                                $data .= $name->portfolio_name." / " ;
                            }else{
                                $data .= $name->portfolio_name." / ";
                            }
                            $i++;
                        }
                    }
                    if($row->portfolio_type=="folder"){
                        $result .= '<div class="col-12 row align-self-center" id="course_list">';
                        $result .= '<div class="col-9 row align-self-center">';
                        $result .= '<div class="checkbox_style align-self-center">';
                        $result .= '<input type="checkbox" value="'.$row->fp_id.'" class="group_download_list">';
                        $result .= '</div>';
                        $result .= '<a href="/faculty_portfolio/folder/'.$row->fp_id.'" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
                        $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                        $result .= '<img src="'.url('image/folder2.png').'" width="25px" height="25px"/>';
                        $result .= '</div>';
                        $result .= '<div class="col-10" id="course_name">';
                        $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$data.$row->portfolio_name.'</b></p>'; 
                        $result .= '</div>';
                        $result .= '</a>';
                        $result .= '</div>';
                        $result .= '<div class="col-3" id="course_action_two">';
                            $result .= '<i class="fa fa-wrench edit_button" aria-hidden="true" id="edit_button_'.$row->fp_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>&nbsp;&nbsp;';
                            $result .= '<i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->fp_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
                        $result .= '</div>';
                        $result .= '</div>';
                    }else{
                        $ext = "";
                        if($row->portfolio_file!=""){
                            $ext = explode(".", $row->portfolio_file);
                        }
                        if(($ext[1] == "pdf")||($ext[1] == "docx")||($ext[1] == "xlsx")||($ext[1] == "pptx")||($ext[1] == "ppt")){
                            $result .= '<div class="col-12 row align-self-center" id="course_list">';
                            $result .= '<div class="col-9 row align-self-center">';
                            $result .= '<div class="checkbox_style align-self-center">';
                            $result .= '<input type="checkbox" value="'.$row->fp_id.'" class="group_download_list">';
                            $result .= '</div>';
                            $result .= '<a href="/faculty/portfolio/'.$row->fp_id.'" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
                            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                            if($ext[1]=="pdf"){
                                $result .= '<img src="'.url('image/pdf.png').'" width="25px" height="25px"/>';
                            }elseif($ext[1]=="docx"){
                                $result .= '<img src="'.url('image/docs.png').'" width="25px" height="25px"/>';
                            }elseif($ext[1]=="xlsx"){
                                $result .= '<img src="'.url('image/excel.png').'" width="25px" height="25px"/>';
                            }elseif($ext[1]=="pptx"){
                                $result .= '<img src="'.url('image/pptx.png').'" width="25px" height="25px"/>';
                            }elseif($ext[1]=="ppt"){
                                $result .= '<img src="'.url('image/pptx.png').'" width="25px" height="25px"/>';
                            }
                            $result .= '</div>';
                            $result .= '<div class="col-10" id="course_name">';
                            $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>'.$data.$row->portfolio_name.'</b></p>';
                            $result .= '</div>';
                            $result .= '</a>';
                            $result .= '</div>';
                            $result .= '<div class="col-3" id="course_action_two">';
                            $result .= '<i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->fp_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
                            $result .= '</div>';
                            $result .= '</div>'; 
                        }else{
                            $result .= '<div class="col-12 row align-self-center" id="course_list">';
                            $result .= '<div class="col-9 row align-self-center">';
                            $result .= '<div class="checkbox_style align-self-center">';
                            $result .= '<input type="checkbox" value="'.$row->fp_id.'" class="group_download_list">';
                            $result .= '</div>';
                            $result .= '<a href="/images/faculty_portfolio/'.$row->fp_id.'/'.$row->portfolio_file.'" data-toggle="lightbox" data-gallery="example-gallery_student" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$row->portfolio_name.'">';
                            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                            $result .= '<img src="'.url('image/img_icon.png').'" width="25px" height="20px"/>';
                            $result .= '</div>';
                            $result .= '<div class="col-10" id="course_name">';
                            $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$data.$row->portfolio_name.'</b></p>';
                            $result .= '</div>';
                            $result .= '</a>';
                            $result .= '</div>';
                            $result .= '<div class="col-3" id="course_action_two">';
                            $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$row->fp_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>&nbsp;&nbsp;<i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->fp_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
                            $result .= '</div>';
                            $result .= '</div>';
                        }
                    }   
                }
            }else{
                    $result .= '<div class="col-md-12">';
                    $result .= '<p>Not Found</p>';
                    $result .= '</div>';
            }
        }else{
            $faculty_portfolio = DB::table('faculty_portfolio')
                    ->select('faculty_portfolio.*')
                    ->where('faculty_id', '=', $faculty_id)
                    ->where('portfolio_place', '=', $place)
                    ->where('status', '=', 'Active')
                    ->orderByDesc('faculty_portfolio.portfolio_type')
                    ->get();

            if ($faculty_portfolio->count()) {
                foreach($faculty_portfolio as $row){
                    $data = "";
                    if($row->portfolio_place != "Faculty"&&$row->portfolio_place != $place){
                        $i=1;
                        $place = explode(',,,',$row->portfolio_place);
                        $data = "";
                        while(isset($place[$i])!=""){
                            $name = Faculty_Portfolio::where('fp_id', '=', $place[$i])->firstOrFail();
                            if($data==""){
                                $data .= $name->portfolio_name." / " ;
                            }else{
                                $data .= $name->portfolio_name." / ";
                            }
                            $i++;
                        }
                    }
                    if($row->portfolio_type=="folder"){
                        $result .= '<div class="col-12 row align-self-center" id="course_list">';
                        $result .= '<div class="col-9 row align-self-center">';
                        $result .= '<div class="checkbox_style align-self-center">';
                        $result .= '<input type="checkbox" value="'.$row->fp_id.'" class="group_download_list">';
                        $result .= '</div>';
                        $result .= '<a href="/faculty_portfolio/folder/'.$row->fp_id.'" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
                        $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                        $result .= '<img src="'.url('image/folder2.png').'" width="25px" height="25px"/>';
                        $result .= '</div>';
                        $result .= '<div class="col-10" id="course_name">';
                        $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$data.$row->portfolio_name.'</b></p>'; 
                        $result .= '</div>';
                        $result .= '</a>';
                        $result .= '</div>';
                        $result .= '<div class="col-3" id="course_action_two">';
                            $result .= '<i class="fa fa-wrench edit_button" aria-hidden="true" id="edit_button_'.$row->fp_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>&nbsp;&nbsp;';
                            $result .= '<i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->fp_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
                        $result .= '</div>';
                        $result .= '</div>';
                    }else{
                        $ext = "";
                        if($row->portfolio_file!=""){
                            $ext = explode(".", $row->portfolio_file);
                        }
                        if(($ext[1] == "pdf")||($ext[1] == "docx")||($ext[1] == "xlsx")||($ext[1] == "pptx")||($ext[1] == "ppt")){
                            $result .= '<div class="col-12 row align-self-center" id="course_list">';
                            $result .= '<div class="col-9 row align-self-center">';
                            $result .= '<div class="checkbox_style align-self-center">';
                            $result .= '<input type="checkbox" value="'.$row->fp_id.'" class="group_download_list">';
                            $result .= '</div>';
                            $result .= '<a href="/faculty/portfolio/'.$row->fp_id.'" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
                            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                            if($ext[1]=="pdf"){
                                $result .= '<img src="'.url('image/pdf.png').'" width="25px" height="25px"/>';
                            }elseif($ext[1]=="docx"){
                                $result .= '<img src="'.url('image/docs.png').'" width="25px" height="25px"/>';
                            }elseif($ext[1]=="xlsx"){
                                $result .= '<img src="'.url('image/excel.png').'" width="25px" height="25px"/>';
                            }elseif($ext[1]=="pptx"){
                                $result .= '<img src="'.url('image/pptx.png').'" width="25px" height="25px"/>';
                            }elseif($ext[1]=="ppt"){
                                $result .= '<img src="'.url('image/pptx.png').'" width="25px" height="25px"/>';
                            }
                            $result .= '</div>';
                            $result .= '<div class="col-10" id="course_name">';
                            $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>'.$data.$row->portfolio_name.'</b></p>';
                            $result .= '</div>';
                            $result .= '</a>';
                            $result .= '</div>';
                            $result .= '<div class="col-3" id="course_action_two">';
                            $result .= '<i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->fp_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
                            $result .= '</div>';
                            $result .= '</div>'; 
                        }else{
                            $result .= '<div class="col-12 row align-self-center" id="course_list">';
                            $result .= '<div class="col-9 row align-self-center">';
                            $result .= '<div class="checkbox_style align-self-center">';
                            $result .= '<input type="checkbox" value="'.$row->fp_id.'" class="group_download_list">';
                            $result .= '</div>';
                            $result .= '<a href="/images/faculty_portfolio/'.$row->fp_id.'/'.$row->portfolio_file.'" data-toggle="lightbox" data-gallery="example-gallery_student" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;" id="show_image_link" data-title="'.$row->portfolio_name.'">';
                            $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                            $result .= '<img src="'.url('image/img_icon.png').'" width="25px" height="20px"/>';
                            $result .= '</div>';
                            $result .= '<div class="col-10" id="course_name">';
                            $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$data.$row->portfolio_name.'</b></p>';
                            $result .= '</div>';
                            $result .= '</a>';
                            $result .= '</div>';
                            $result .= '<div class="col-3" id="course_action_two">';
                            $result .= '<i class="fa fa-download download_button" aria-hidden="true" id="download_button_'.$row->fp_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:blue;background-color: white;width: 28px;"></i>&nbsp;&nbsp;<i class="fa fa-times remove_button" aria-hidden="true" id="remove_button_'.$row->fp_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>';
                            $result .= '</div>';
                            $result .= '</div>';
                        }
                    }   
                }
            }else{
                $result .= '<div style="display: block;border:1px solid black;padding: 50px;width: 100%;margin: 0px 20px 10px 20px;">';
                $result .= '<center>Empty</center>';
                $result .= '</div>';
            }
        }
        return $result;
    }

    public function lecturerCV()
    {
        if(auth()->user()->position == "Dean"){
        	$user_id = auth()->user()->user_id;
        	$staff_dean     = Staff::where('user_id', '=', $user_id)->firstOrFail();
        	$faculty_id = $staff_dean->faculty_id;
            $faculty    = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
        	$faculty_staff = DB::table('staffs')
        			->join('users', 'staffs.user_id', '=', 'users.user_id')
                    ->select('staffs.*','users.*')
                    ->where('staffs.faculty_id', '=', $faculty_id)
                    ->where('staffs.lecturer_CV', '!=', null)
                    ->orderByDesc('staffs.lecturer_CV')
                    ->get();
        }
        return view('dean.FacultyPortFolio.LecturerCV', compact('faculty_staff','faculty'));
    }

    public function Syllabus()
    {
        if(auth()->user()->position == "Dean"){
            $user_id = auth()->user()->user_id;
            $staff_dean     = Staff::where('user_id', '=', $user_id)->firstOrFail();
            $faculty_id = $staff_dean->faculty_id;
            $faculty    = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
            $subjects = DB::table('subjects')
                    ->join('programmes', 'subjects.programme_id', '=', 'programmes.programme_id')
                    ->join('departments', 'programmes.department_id', '=', 'departments.department_id')
                    ->select('subjects.*','programmes.*','departments.*')
                    ->where('departments.faculty_id', '=', $faculty_id)
                    ->where('subjects.syllabus', '!=', "")
                    ->orderBy('programmes.programme_id')
                    ->get();
        }
        return view('dean.FacultyPortFolio.Syllabus', compact('subjects','faculty'));
    }

    public function downloadCV($id)
    {
        $staff = Staff::where('staff_id', '=', $id)->firstOrFail();
        $staffFaculty = $staff->faculty_id;

        $user_id    = auth()->user()->user_id;
        $checkFaculty = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $user_faculty = $checkFaculty->faculty_id;

        if($staffFaculty == $user_faculty){
            $CV = $staff->lecturer_CV;
            $ext = "";
            if($staff->lecturer_CV!=""){
                $ext = explode(".", $staff->lecturer_CV);
            }
            return Storage::disk('private')->download('/staffCV/'.$CV,$id.'.'.$ext[1]);
        }else{
            return redirect()->route('login');
        }
    }

    public function downloadSyllabus($id)
    {
        $user_id    = auth()->user()->user_id;
        $checkFaculty = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $user_faculty = $checkFaculty->faculty_id;

        $subject = Subject::where('subject_id', '=', $id)->firstOrFail();
        $programme_id = $subject->programme_id;
        $programmes = Programme::where('programme_id', '=', $programme_id)->firstOrFail();
        $department_id = $programmes->department_id;
        $departments = Department::where('department_id', '=', $department_id)->firstOrFail();
        $faculty_id = $departments->faculty_id;

        if($user_faculty==$faculty_id){
            $syllabus = $subject->syllabus;
            $name = $subject->syllabus_name;
            $ext = "";
            if($subject->syllabus!=""){
                $ext = explode(".", $subject->syllabus);
            }
            return Storage::disk('private')->download('/syllabus/'.$syllabus,$name.'.'.$ext[1]);
        }else{
            return redirect()->route('login');
        }
    }

    public function searchLecturerCV(Request $request){
        $user_id    = auth()->user()->user_id;
        $staff_dean = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id = $staff_dean->faculty_id;
        $value = $request->get('value');
        $result = "";
        if($value!=""){
            $faculty_staff = DB::table('staffs')
                    ->join('users', 'staffs.user_id', '=', 'users.user_id')
                    ->join('departments','departments.department_id', '=', 'staffs.department_id')
                    ->select('staffs.*','users.*','departments.*')
                    ->where('staffs.faculty_id', '=', $faculty_id)
                    ->Where(function($query) use ($value) {
                          $query->orWhere('staffs.staff_id','LIKE','%'.$value.'%')
                            ->orWhere('users.name','LIKE','%'.$value.'%')
                            ->orWhere('departments.department_name','LIKE','%'.$value.'%');
                    })
                    ->where('staffs.lecturer_CV', '!=', null)
                    ->orderByDesc('staffs.lecturer_CV')
                    ->get();
            $result .= '<div class="col-12 row" style="padding: 0px 0px 5px 10px;margin:0px;">';
            $result .= '<div class="checkbox_group_style align-self-center">';
            $result .= '<input type="checkbox" name="group_lecturer" class="group_checkbox">';
            $result .= '</div>';
            $result .= '<p style="font-size: 18px;margin:0px 0px 0px 5px;display: inline-block;">';
            $result .= 'Search Filter : '.$value;
            $result .= '</p>';
            $result .= '</div>';
            if ($faculty_staff->count()) {
                foreach($faculty_staff as $row){
                    $ext = explode(".", $row->lecturer_CV);
                    $result .= '<div class="col-12 row align-self-center" id="course_list">';
                    $result .= '<div class="col-12 row align-self-center">';
                    $result .= '<div class="checkbox_style align-self-center">';
                    $result .= '<input type="checkbox" value="'.$row->staff_id.'" class="group_download_list">';
                    $result .= '</div>';
                    $result .= '<a href="/dean/staff/CV/'.$row->staff_id.'" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
                    $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                    if($ext[1]=="pdf"){
                        $result .= '<img src="'.url('image/pdf.png').'" width="25px" height="25px"/>';
                    }elseif($ext[1]=="docx"){
                        $result .= '<img src="'.url('image/docs.png').'" width="25px" height="25px"/>';
                    }elseif($ext[1]=="xlsx"){
                        $result .= '<img src="'.url('image/excel.png').'" width="25px" height="25px"/>';
                    }elseif($ext[1]=="pptx"){
                        $result .= '<img src="'.url('image/pptx.png').'" width="25px" height="25px"/>';
                    }elseif($ext[1]=="ppt"){
                        $result .= '<img src="'.url('image/pptx.png').'" width="25px" height="25px"/>';
                    }
                    $result .= '</div>';
                    $result .= '<div class="col-10" id="course_name">';
                    $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>'.$row->staff_id.'_'.$row->name.'</b></p>';
                    $result .= '</div>';
                    $result .= '</a>';
                    $result .= '</div>';
                    $result .= '</div>'; 
                }
            }else{
                    $result .= '<div class="col-md-12">';
                    $result .= '<p>Not Found</p>';
                    $result .= '</div>';
            }
        }else{
            $faculty_staff = DB::table('staffs')
                    ->join('users', 'staffs.user_id', '=', 'users.user_id')
                    ->select('staffs.*','users.*')
                    ->where('staffs.faculty_id', '=', $faculty_id)
                    ->where('staffs.lecturer_CV', '!=', null)
                    ->orderByDesc('staffs.lecturer_CV')
                    ->get();
            $result .= '<div class="col-12 row" style="padding: 0px 0px 5px 10px;margin:0px;">';
            $result .= '<div class="checkbox_group_style align-self-center">';
            $result .= '<input type="checkbox" name="group_lecturer" class="group_checkbox">';
            $result .= '</div>';
            $result .= '<p style="font-size: 18px;margin:0px 0px 0px 5px;display: inline-block;">';
            $result .= 'Listing of CV';
            $result .= '</p>';
            $result .= '</div>';
            if ($faculty_staff->count()) {
                foreach($faculty_staff as $row){
                    $ext = explode(".", $row->lecturer_CV);
                    $result .= '<div class="col-12 row align-self-center" id="course_list">';
                    $result .= '<div class="col-12 row align-self-center">';
                    $result .= '<div class="checkbox_style align-self-center">';
                    $result .= '<input type="checkbox" value="'.$row->staff_id.'" class="group_download_list">';
                    $result .= '</div>';
                    $result .= '<a href="/dean/staff/CV/'.$row->staff_id.'" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
                    $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                    if($ext[1]=="pdf"){
                        $result .= '<img src="'.url('image/pdf.png').'" width="25px" height="25px"/>';
                    }elseif($ext[1]=="docx"){
                        $result .= '<img src="'.url('image/docs.png').'" width="25px" height="25px"/>';
                    }elseif($ext[1]=="xlsx"){
                        $result .= '<img src="'.url('image/excel.png').'" width="25px" height="25px"/>';
                    }elseif($ext[1]=="pptx"){
                        $result .= '<img src="'.url('image/pptx.png').'" width="25px" height="25px"/>';
                    }elseif($ext[1]=="ppt"){
                        $result .= '<img src="'.url('image/pptx.png').'" width="25px" height="25px"/>';
                    }
                    $result .= '</div>';
                    $result .= '<div class="col-10" id="course_name">';
                    $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>'.$row->staff_id.'_'.$row->name.'</b></p>';
                    $result .= '</div>';
                    $result .= '</a>';
                    $result .= '</div>';
                    $result .= '</div>'; 
                }
            }else{
                $result .= '<div style="display: block;border:1px solid black;padding: 50px;width: 100%;margin: 0px 20px 10px 20px;">';
                $result .= '<center>Empty</center>';
                $result .= '</div>';
            }
        }
        return $result;
    }


    public function zipFileCV($staff_id)
    {
        $string = explode('---',$staff_id);
        $user_id    = auth()->user()->user_id;
        $checkFaculty = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $user_faculty = $checkFaculty->faculty_id;
        $faculty    = Faculty::where('faculty_id', '=', $user_faculty)->firstOrFail();

        $name = "Lecturer CV (".$faculty->faculty_name.")";
        $zip = new ZipArchive;
        $fileName = storage_path('private/staffCV/Zip_Files/'.$name.'.zip');
        $zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $files = File::files(storage_path('/private/staffCV/'));

        for($i=0;$i<(count($string)-1);$i++){
            $staff = Staff::where('staff_id', '=', $string[$i])->firstOrFail();
            foreach ($files as $key => $value) {
                $relativeNameInZipFile = basename($value);
                if($staff->lecturer_CV==$relativeNameInZipFile){
                    $ext = explode('.',$relativeNameInZipFile);
                    $zip->addFile($value,$staff->staff_id.'.'.$ext[1]);
                }
            }
        }
        $zip->close();
        return response()->download($fileName);
    }

    public function searchSyllabus(Request $request){
        $user_id    = auth()->user()->user_id;
        $staff_dean = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id = $staff_dean->faculty_id;
        $value = $request->get('value');
        $result = "";
        if($value!=""){
            $subjects = DB::table('subjects')
                    ->join('programmes', 'subjects.programme_id', '=', 'programmes.programme_id')
                    ->join('departments', 'programmes.department_id', '=', 'departments.department_id')
                    ->select('subjects.*','programmes.*','departments.*')
                    ->where('departments.faculty_id', '=', $faculty_id)
                    ->Where(function($query) use ($value) {
                          $query->orWhere('subjects.subject_code','LIKE','%'.$value.'%')
                            ->orWhere('subjects.subject_name','LIKE','%'.$value.'%')
                            ->orWhere('programmes.short_form_name','LIKE','%'.$value.'%')
                            ->orWhere('programmes.programme_name','LIKE','%'.$value.'%')
                            ->orWhere('departments.department_name','LIKE','%'.$value.'%');
                    })
                    ->where('subjects.syllabus', '!=', "")
                    ->orderBy('programmes.programme_id')
                    ->get();
            $result .= '<div class="col-12 row" style="padding: 0px 0px 5px 10px;margin:0px;">';
            $result .= '<div class="checkbox_group_style align-self-center">';
            $result .= '<input type="checkbox" name="group_lecturer" class="group_checkbox">';
            $result .= '</div>';
            $result .= '<p style="font-size: 18px;margin:0px 0px 0px 5px;display: inline-block;">';
            $result .= 'Search Filter : '.$value;
            $result .= '</p>';
            $result .= '</div>';
            if ($subjects->count()) {
                foreach($subjects as $row){
                    $result .= '<div class="col-12 row align-self-center" id="course_list">';
                    $result .= '<div class="col-12 row align-self-center">';
                    $result .= '<div class="checkbox_style align-self-center">';
                    $result .= '<input type="checkbox" value="'.$row->subject_id.'" class="group_download_list">';
                    $result .= '</div>';
                    $result .= '<a href="/dean/syllabusDownload/'.$row->subject_id.'" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
                    $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                    $result .= '<img src="'.url('image/excel.png').'" width="25px" height="25px"/>';
                    $result .= '</div>';
                    $result .= '<div class="col-10" id="course_name">';
                    $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>'.$row->short_form_name." / ".$row->subject_code.' '.$row->subject_name.'</b></p>';
                    $result .= '</div>';
                    $result .= '</a>';
                    $result .= '</div>';
                    $result .= '</div>'; 
                }
            }else{
                    $result .= '<div class="col-md-12">';
                    $result .= '<p>Not Found</p>';
                    $result .= '</div>';
            }
        }else{
            $subjects = DB::table('subjects')
                    ->join('programmes', 'subjects.programme_id', '=', 'programmes.programme_id')
                    ->join('departments', 'programmes.department_id', '=', 'departments.department_id')
                    ->select('subjects.*','programmes.*','departments.*')
                    ->where('departments.faculty_id', '=', $faculty_id)
                    ->where('subjects.syllabus', '!=', "")
                    ->orderBy('programmes.programme_id')
                    ->get();

            $result .= '<div class="col-12 row" style="padding: 0px 0px 5px 10px;margin:0px;">';
            $result .= '<div class="checkbox_group_style align-self-center">';
            $result .= '<input type="checkbox" name="group_lecturer" class="group_checkbox">';
            $result .= '</div>';
            $result .= '<p style="font-size: 18px;margin:0px 0px 0px 5px;display: inline-block;">';
            $result .= 'Listing of Syllabus';
            $result .= '</p>';
            $result .= '</div>';
            if($subjects->count()){
                foreach($subjects as $row){
                    $result .= '<div class="col-12 row align-self-center" id="course_list">';
                    $result .= '<div class="col-12 row align-self-center">';
                    $result .= '<div class="checkbox_style align-self-center">';
                    $result .= '<input type="checkbox" value="'.$row->subject_id.'" class="group_download_list">';
                    $result .= '</div>';
                    $result .= '<a href="/dean/syllabusDownload/'.$row->subject_id.'" id="show_image_link" class="col-11 row" style="padding:10px 0px;margin-left:-10px;color:#0d2f81;border:0px solid black;">';
                    $result .= '<div class="col-1" style="position: relative;top: -2px;">';
                    $result .= '<img src="'.url('image/excel.png').'" width="25px" height="25px"/>';
                    $result .= '</div>';
                    $result .= '<div class="col-10" id="course_name">';
                    $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>'.$row->short_form_name." / ".$row->subject_code.' '.$row->subject_name.'</b></p>';
                    $result .= '</div>';
                    $result .= '</a>';
                    $result .= '</div>';
                    $result .= '</div>'; 
                }
            }else{
                $result .= '<div style="display: block;border:1px solid black;padding: 50px;width: 100%;margin: 0px 20px 10px 20px;">';
                $result .= '<center>Empty</center>';
                $result .= '</div>';
            }
        }
        return $result;
    }

    public function zipFileSyllabus($subject_id)
    {
        $string = explode('---',$subject_id);
        $user_id    = auth()->user()->user_id;
        $checkFaculty = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $user_faculty = $checkFaculty->faculty_id;
        $faculty    = Faculty::where('faculty_id', '=', $user_faculty)->firstOrFail();

        $name = "Syllabus (".$faculty->faculty_name.")";
        $zip = new ZipArchive;
        $fileName = storage_path('private/syllabus/Zip_Files/'.$name.'.zip');
        $zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $files = File::files(storage_path('/private/syllabus/'));

        for($i=0;$i<(count($string)-1);$i++){
            $subject = Subject::where('subject_id', '=', $string[$i])->firstOrFail();
            $programmes = Programme::where('programme_id', '=', $subject->programme_id)->firstOrFail();
            foreach ($files as $key => $value) {
                $relativeNameInZipFile = basename($value);
                if($subject->syllabus==$relativeNameInZipFile){
                    $ext = explode('.',$relativeNameInZipFile);
                    $zip->addFile($value,$programmes->short_form_name." : ".$subject->subject_code." ".$subject->subject_name.'.'.$ext[1]);
                }
            }
        }
        $zip->close();
        return response()->download($fileName);
    }

    public function openNewFolder(Request $request){
        $user_id      = auth()->user()->user_id;
        $staff_dean   = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id   = $staff_dean->faculty_id;

        $folder_name  = $request->get('folder_name');
        $type         = "folder";
        $place        = $request->get('folder_place');

        $faculty_portfolio = new Faculty_Portfolio([
            'faculty_id'             =>  $faculty_id,
            'portfolio_name'         =>  $folder_name,
            'portfolio_type'         =>  $type,
            'portfolio_place'        =>  $place,
            'status'                 =>  'Active',
        ]);
        $faculty_portfolio->save();

        return redirect()->back()->with('success','New Folder Added Successfully');
    }

    public function folderNameEdit(Request $request){
        $folder_id = $request->get('value');
        $folder = Faculty_Portfolio::find($folder_id);
        return $folder;
    }

    public function updateFolderName(Request $request){
        $fp_id   = $request->get('fp_id');
        $faculty_portfolio = Faculty_Portfolio::where('fp_id', '=', $fp_id)->firstOrFail();
        $faculty_portfolio->portfolio_name  = $request->get('folder_name');
        $faculty_portfolio->save();
        return redirect()->back()->with('success','Edit Folder Name Successfully');
    }

    public function folder_view($folder_id)
    {
        $user_id = auth()->user()->user_id;
        $staff_dean     = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id = $staff_dean->faculty_id;
        $faculty    = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
        $faculty_portfolio = Faculty_Portfolio::where('fp_id', '=', $folder_id)->firstOrFail();

        $place_name = explode(',,,',($faculty_portfolio->portfolio_place));
        $i=1;
        $data = "Faculty";
        while(isset($place_name[$i])!=""){
            $name = Faculty_Portfolio::where('fp_id', '=', $place_name[$i])->firstOrFail();
            $data .= ",,,".$name->portfolio_name;
            $i++;
        }

        $portfolio_place = $faculty_portfolio->portfolio_place.",,,".$faculty_portfolio->fp_id;
        $faculty_portfolio_list = DB::table('faculty_portfolio')
                    ->select('faculty_portfolio.*')
                    ->where('faculty_id', '=', $faculty_id)
                    ->where('portfolio_place', '=', $portfolio_place)
                    ->where('status', '=', 'Active')
                    ->orderBy('faculty_portfolio.fp_id')
                    ->get();
        return view('dean.FacultyPortFolio.folder_view', compact('faculty','portfolio_place','faculty_portfolio','faculty_portfolio_list','data'));
    }

    public function uploadFiles(Request $request) 
    {
        $image = $request->file('file');
        $imageName = $image->getClientOriginalName();
        $image->storeAs('fake','/faculty_portfolio/'.$imageName, 'private');
        return response()->json(['success'=>$imageName]);  
    }
    public function destroyFiles(Request $request)
    {
        $filename =  $request->get('filename');
        Storage::disk('private')->delete('fake/faculty_portfolio/'.$filename);
        return $filename;  
    }

    public function storeFiles(Request $request){
        $user_id      = auth()->user()->user_id;
        $staff_dean   = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id   = $staff_dean->faculty_id;   

        $count = $request->get('count');
        $place = $request->get('file_place');
        for($i=1;$i<=$count;$i++){
            $name = $request->get('form'.$i);
            $ext = $request->get('ext'.$i);
            $fake = $request->get('fake'.$i);
            if($name!=""){
                $faculty_portfolio = new Faculty_Portfolio([
                    'faculty_id'             =>  $faculty_id,
                    'portfolio_name'         =>  $name,
                    'portfolio_type'         =>  'document',
                    'portfolio_place'        =>  $place,
                    'portfolio_file'         =>  $fake,
                    'status'                 =>  'Active',
                ]);
                $faculty_portfolio->save();
                $fake_place = Storage::disk('private')->get("fake/faculty_portfolio/".$fake);
                Storage::disk('private')->put('f_Portfolio/'.$faculty_id.'/'.$fake, $fake_place); 
                Storage::disk('private')->delete("fake/faculty_portfolio/".$fake);
            }
        }
        return redirect()->back()->with('success','New Document Added Successfully');
    }

    public function removeActiveFile($id){
        $faculty_portfolio = Faculty_Portfolio::where('fp_id', '=', $id)->firstOrFail();
        if($faculty_portfolio->portfolio_type=="folder"){
            $faculty_portfolio_list = Faculty_Portfolio::where('portfolio_place', 'LIKE', $faculty_portfolio->portfolio_place.",,,".$id)->update(['status' => 'Remove']);

            $faculty_portfolio_list_2 = Faculty_Portfolio::where('portfolio_place', 'LIKE', $faculty_portfolio->portfolio_place.",,,".$id.",,,".'%')->update(['status' => 'Remove']);
        }
        $faculty_portfolio->status  = "Remove";
        $faculty_portfolio->save();
        return redirect()->back()->with('success','Remove Successfully');
    }

    public function FPImage($fp_id,$image_name)
    {
        $user_id    = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $staff_faculty = $staff_dean->faculty_id;

        $checkCourseId = Faculty_Portfolio::where('fp_id', '=', $fp_id)->firstOrFail();
        $faculty_id = $checkCourseId->faculty_id;

        if($staff_faculty==$faculty_id){
            $storagePath = storage_path('/private/f_Portfolio/'.$faculty_id.'/'.$image_name);
            return Image::make($storagePath)->response();
        }else{
            return redirect()->route('login');
        }
    }

    public function downloadFP($id)
    {
        $faculty_portfolio = Faculty_Portfolio::where('fp_id', '=', $id)->firstOrFail();
        $faculty_id = $faculty_portfolio->faculty_id;

        $user_id    = auth()->user()->user_id;
        $checkFaculty = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $user_faculty = $checkFaculty->faculty_id;

        if($faculty_id == $user_faculty){
            $ext = "";
            if($faculty_portfolio->portfolio_file!=""){
                $ext = explode(".", $faculty_portfolio->portfolio_file);
            }
            return Storage::disk('private')->download('f_Portfolio/'.$faculty_id.'/'.$faculty_portfolio->portfolio_file, $faculty_portfolio->portfolio_name.'.'.$ext[1]);
        }else{
            return redirect()->route('login');
        }
    }

    public function zipFileDownload($fp_id,$download){

        $string = explode('---',$fp_id);
        $user_id    = auth()->user()->user_id;
        $checkFaculty = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $user_faculty = $checkFaculty->faculty_id;
        $faculty    = Faculty::where('faculty_id', '=', $user_faculty)->firstOrFail();

        $name = "Faculty Portfolio (".$faculty->faculty_name.")";
        $zip = new ZipArchive;
        $fileName = storage_path('private/f_Portfolio/Zip_Files/'.$name.'.zip');
        $zip->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $files = File::files(storage_path('/private/f_Portfolio/'.$user_faculty.'/'));

        if($download == "checked"){
            for($i=0;$i<(count($string)-1);$i++){
                $portfolio = Faculty_Portfolio::where('fp_id', '=', $string[$i])->firstOrFail();
                if($portfolio->portfolio_type == "document"){
                    foreach ($files as $key => $value) {
                        $relativeNameInZipFile = basename($value);
                        if($portfolio->portfolio_file==$relativeNameInZipFile){
                            $ext = explode('.',$relativeNameInZipFile);
                            if($portfolio->portfolio_place=="Faculty"){
                                $zip->addFile($value,$portfolio->portfolio_name.'.'.$ext[1]);
                            }else{
                                $m=1;
                                $place = explode(',,,',$portfolio->portfolio_place);
                                $data = "";
                                while(isset($place[$m])!=""){
                                    $name = Faculty_Portfolio::where('fp_id', '=', $place[$m])->firstOrFail();
                                    if($data==""){
                                        $data .= $name->portfolio_name;
                                    }else{
                                        $data .= "/".$name->portfolio_name;
                                    }
                                    $m++;
                                }
                                $zip->addFile($value,$data.'/'.$portfolio->portfolio_name.'.'.$ext[1]);
                            }
                        }
                    }
                }else{
                    if($portfolio->portfolio_place=="Faculty"){
                        $zip->addEmptyDir($portfolio->portfolio_name);
                    }else{
                        $m=1;
                        $place = explode(',,,',$portfolio->portfolio_place);
                        $data = "";
                        while(isset($place[$m])!=""){
                            $name = Faculty_Portfolio::where('fp_id', '=', $place[$m])->firstOrFail();
                            if($data==""){
                                $data .= $name->portfolio_name;
                            }else{
                                $data .= "/".$name->portfolio_name;
                            }
                            $m++;
                        }
                        $zip->addEmptyDir($data.'/'.$portfolio->portfolio_name);
                    }
                    $check = $portfolio->portfolio_place.",,,".$portfolio->fp_id;
                    $next_check = $portfolio->portfolio_place.",,,".$portfolio->fp_id.",,,";
                    $faculty_portfolio = DB::table('faculty_portfolio')
                                    ->select('faculty_portfolio.*')
                                    ->Where(function($query) use ($check,$next_check) {
                                      $query->orWhere('portfolio_place','LIKE','%'.$check)
                                            ->orWhere('portfolio_place','LIKE','%'.$next_check.'%');
                                    })
                                    ->where('faculty_portfolio.status', '=', 'Active')
                                    ->where('faculty_portfolio.faculty_id','=',$user_faculty)
                                    ->orderByDesc('faculty_portfolio.portfolio_type')
                                    ->get();
                    foreach($faculty_portfolio as $row){
                        if($row->portfolio_type == "document"){
                            foreach ($files as $key => $value) {
                                $relativeNameInZipFile = basename($value);
                                if($row->portfolio_file==$relativeNameInZipFile){
                                    $ext = explode('.',$relativeNameInZipFile);
                                    if($row->portfolio_place=="Faculty"){
                                        $zip->addFile($value,$row->portfolio_name.'.'.$ext[1]);
                                    }else{
                                        $m=1;
                                        $place = explode(',,,',$row->portfolio_place);
                                        $data = "";
                                        while(isset($place[$m])!=""){
                                            $name = Faculty_Portfolio::where('fp_id', '=', $place[$m])->firstOrFail();
                                            if($data==""){
                                                $data .= $name->portfolio_name;
                                            }else{
                                                $data .= "/".$name->portfolio_name;
                                            }
                                            $m++;
                                        }
                                        $zip->addFile($value,$data.'/'.$row->portfolio_name.'.'.$ext[1]);
                                    }
                                }
                            }
                        }else{
                            if($row->portfolio_place=="Faculty"){
                                    $zip->addEmptyDir($row->portfolio_name);
                            }else{
                                $m=1;
                                $place = explode(',,,',$row->portfolio_place);
                                $data = "";
                                while(isset($place[$m])!=""){
                                    $name = Faculty_Portfolio::where('fp_id', '=', $place[$m])->firstOrFail();
                                    if($data==""){
                                        $data .= $name->portfolio_name;
                                    }else{
                                        $data .= "/".$name->portfolio_name;
                                    }
                                    $m++;
                                }
                                $zip->addEmptyDir($data.'/'.$row->portfolio_name);
                            }
                        }
                    }
                }
            }
        }
        $zip->close();
        return response()->download($fileName);
    }
}
