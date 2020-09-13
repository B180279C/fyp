<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Staff;
use App\Department;
use App\Programme;
use App\Faculty;
use App\Faculty_Portfolio;

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
        return view('dean.FacultyPortFolio', compact('faculty_portfolio','faculty'));
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
                    if($row->portfolio_type=="folder"){
                        $result .= '<a href="/faculty_portfolio/folder/'.$row->fp_id.'" class="col-md-12 align-self-center" id="course_list">';
                        $result .= '<div class="col-md-12 row" style="padding:10px;color:#0d2f81;">';
                        $result .= '<div class="col-1" style="padding-top: 3px;">';
                        $result .= '<img src="'.url("image/folder2.png").'" width="25px" height="25px"/>';
                        $result .= '</div>';
                        $result .= '<div class="col" id="course_name">';
                        $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>'.$row->portfolio_name.'</b></p>';
                        $result .= '</div>';
                        $result .= '<div class="col-3" id="course_action_two">';
                        $result .= '<i class="fa fa-wrench edit_button_file" aria-hidden="true" id="edit_button_file_'.$row->fp_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>&nbsp;&nbsp;';
                        $result .= '<i class="fa fa-times remove_button_file" aria-hidden="true" id="remove_button_file_'.$row->fp_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>
                            </div>';
                        $result .= '</div></a>';
                    }else{
                        $ext = "";
                        if($row->portfolio_file!=""){
                            $ext = explode(".", $row->portfolio_file);
                        }
                        $result .= '<a download="'.$row->portfolio_name.$ext[1].'" href="'.asset('f_Portfolio/'.$row->faculty_id.'/'.$row->portfolio_file).'" class="col-md-12 align-self-center" id="course_list">';
                        $result .= '<div class="col-md-12 row" style="padding:10px;color:#0d2f81;">';
                        $result .= '<div class="col-1" style="padding-top: 3px;">';
                        if($ext[1]=="pdf"){
                            $result .= '<img src="'.url('image/pdf.png').'" width="25px" height="25px"/>';
                        }else if($ext[1]=="docx"){
                            $result .= '<img src="'.url('image/docs.png').'" width="25px" height="25px"/>';
                        }else if($ext[1]=="xlsx"){
                            $result .= '<img src="'.url('image/excel.png').'" width="25px" height="25px"/>';
                        }else if($ext[1]=="pptx"){
                            $result .= '<img src="'.url('image/pptx.png').'" width="25px" height="25px"/>';
                        }

                        $result .= '</div>';
                        $result .= '<div class="col" id="course_name">';
                        $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$row->portfolio_name.'</b></p>';
                        $result .= '</div>';
                        $result .= '<div class="col-1" id="course_action">';
                        $result .= '<i class="fa fa-times remove_button_file" aria-hidden="true" id="remove_button_file_'.$row->fp_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>
                                </div>';
                        $result .= '</div></a>';
                    }   
                }
            }else{
                    $result .= '<div class="col-md-12">';
                    $result .= '<p>Not Found</p>';
                    $result .= '</div>';
            }
        }else{
            if($place=="Faculty"){
                $result .= '<a href="/FacultyPortFolio/CVdepartment" class="col-md-12 align-self-center" id="course_list">
                              <div class="col-md-12 row" style="padding:10px;color:#0d2f81;">
                                <div class="col-1" style="padding-top: 3px;">
                                  <img src="'.url('image/cv.png').'" width="25px" height="25px"/>
                                </div>
                                <div class="col" id="course_name">
                                  <p style="margin: 0px;"><b>Lecturer CV</b></p>
                                </div>
                              </div>
                            </a>';
                $result .= '<a href="/FacultyPortFolio/SyllabusDepartment" class="col-md-12 align-self-center" id="course_list">
                              <div class="col-md-12 row" style="padding:10px;color:#0d2f81;">
                                <div class="col-1" style="padding-top: 3px;">
                                  <img src="'.url('image/syllabus.png').'" width="25px" height="25px"/>
                                </div>
                                <div class="col" id="course_name">
                                  <p style="margin: 0px;"><b>Syllabus</b></p>
                                </div>
                              </div>
                            </a>';
            }
            $faculty_portfolio = DB::table('faculty_portfolio')
                    ->select('faculty_portfolio.*')
                    ->where('faculty_id', '=', $faculty_id)
                    ->where('portfolio_place', '=', $place)
                    ->where('status', '=', 'Active')
                    ->orderByDesc('faculty_portfolio.portfolio_type')
                    ->get();
            if ($faculty_portfolio->count()) {
                foreach($faculty_portfolio as $row){
                    if($row->portfolio_type=="folder"){
                        $result .= '<a href="/faculty_portfolio/folder/'.$row->fp_id.'" class="col-md-12 align-self-center" id="course_list">';
                        $result .= '<div class="col-md-12 row" style="padding:10px;color:#0d2f81;">';
                        $result .= '<div class="col-1" style="padding-top: 3px;">';
                        $result .= '<img src="'.url("image/folder2.png").'" width="25px" height="25px"/>';
                        $result .= '</div>';
                        $result .= '<div class="col" id="course_name">';
                        $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name_two"><b>'.$row->portfolio_name.'</b></p>';
                        $result .= '</div>';
                        $result .= '<div class="col-3" id="course_action_two">';
                        $result .= '<i class="fa fa-wrench edit_button_file" aria-hidden="true" id="edit_button_file_'.$row->fp_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:green;background-color: white;width: 28px;"></i>&nbsp;&nbsp;';
                        $result .= '<i class="fa fa-times remove_button_file" aria-hidden="true" id="remove_button_file_'.$row->fp_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>
                            </div>';
                        $result .= '</div></a>';
                    }else{
                        $ext = "";
                        if($row->portfolio_file!=""){
                            $ext = explode(".", $row->portfolio_file);
                        }

                        $result .= '<a download="'.$row->portfolio_name.$ext[1].'" href="'.asset('f_Portfolio/'.$row->faculty_id.'/'.$row->portfolio_file).'" class="col-md-12 align-self-center" id="course_list">';
                        $result .= '<div class="col-md-12 row" style="padding:10px;color:#0d2f81;">';
                        $result .= '<div class="col-1" style="padding-top: 3px;">';

                        if($ext[1]=="pdf"){
                            $result .= '<img src="'.url('image/pdf.png').'" width="25px" height="25px"/>';
                        }else if($ext[1]=="docx"){
                            $result .= '<img src="'.url('image/docs.png').'" width="25px" height="25px"/>';
                        }else if($ext[1]=="xlsx"){
                            $result .= '<img src="'.url('image/excel.png').'" width="25px" height="25px"/>';
                        }else if($ext[1]=="pptx"){
                            $result .= '<img src="'.url('image/pptx.png').'" width="25px" height="25px"/>';
                        }    

                        $result .= '</div>';
                        $result .= '<div class="col" id="course_name">';
                        $result .= '<p style="margin: 0px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;" id="file_name"><b>'.$row->portfolio_name.'</b></p>';
                        $result .= '</div>';
                        $result .= '<div class="col-1" id="course_action">';
                        $result .= '<i class="fa fa-times remove_button_file" aria-hidden="true" id="remove_button_file_'.$row->fp_id.'" style="border: 1px solid #cccccc;padding:5px;border-radius: 50%;color:red;background-color: white;width: 28px;text-align: center;"></i>
                                </div>';
                        $result .= '</div></a>';
                    }   
                }
            }else{
                
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
        return view('dean.LecturerCV', compact('faculty_staff','faculty'));
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
                    ->get();
        }
        return view('dean.Syllabus', compact('subjects','faculty'));
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
                          $query->orWhere('staffs.lecturer_CV','LIKE','%'.$value.'%')
                            ->orWhere('departments.department_name','LIKE','%'.$value.'%');
                    })
                    ->where('staffs.lecturer_CV', '!=', null)
                    ->orderByDesc('staffs.lecturer_CV')
                    ->get();
            $result .= '<div class="col-md-12">';
            $result .= '<p style="font-size: 18px;margin:0px 0px 0px 10px;display: inline-block;">Search Filter : '.$value.'</p>';
            $result .= '</div>';
            if ($faculty_staff->count()) {
                foreach($faculty_staff as $row){
                    $ext = explode(".", $row->lecturer_CV);
                    $result .= '<a href="'.asset("staffCV/".$row->lecturer_CV).'" class="col-md-12 align-self-center" id="course_list" download>';
                    $result .= '<div class="col-md-12 row" style="padding:10px;color:#0d2f81;">';
                    $result .= '<div class="col-1" style="padding-top: 3px;">';
                        if($ext[1]=="pdf"){
                          $result .= '<img src="'.url("image/pdf.png").'" width="25px" height="25px"/>';
                        }else if($ext[1]=="docx"){
                            $result .= '<img src="'.url("image/docs.png").'" width="25px" height="25px"/>';
                        }else if($ext[1]=="xlsx"){
                            $result .= '<img src="'.url("image/excel.png").'" width="25px" height="25px"/>';
                        }
                    $result .= '</div>';
                    $result .= '<div class="col" id="course_name">';
                    $result .= '<p style="margin: 0px;"><b>'.$row->lecturer_CV.'</b></p>';
                    $result .= '</div></div></a>';
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
            $result .= '<div class="col-md-12">';
            $result .= '<p style="font-size: 18px;margin:0px 0px 0px 10px;display: inline-block;">Lecturer CV In Faculty</p>';
            $result .= '</div>';
            foreach($faculty_staff as $row){
                    $ext = explode(".", $row->lecturer_CV);
                    $result .= '<a href="'.asset("staffCV/".$row->lecturer_CV).'" class="col-md-12 align-self-center" id="course_list" download>';
                    $result .= '<div class="col-md-12 row" style="padding:10px;color:#0d2f81;">';
                    $result .= '<div class="col-1" style="padding-top: 3px;">';
                        if($ext[1]=="pdf"){
                          $result .= '<img src="'.url("image/pdf.png").'" width="25px" height="25px"/>';
                        }else if($ext[1]=="docx"){
                            $result .= '<img src="'.url("image/docs.png").'" width="25px" height="25px"/>';
                        }else if($ext[1]=="xlsx"){
                            $result .= '<img src="'.url("image/excel.png").'" width="25px" height="25px"/>';
                        }
                    $result .= '</div>';
                    $result .= '<div class="col" id="course_name">';
                    $result .= '<p style="margin: 0px;"><b>'.$row->lecturer_CV.'</b></p>';
                    $result .= '</div></div></a>';
            }
        }
        return $result;
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
                          $query->orWhere('subjects.syllabus_name','LIKE','%'.$value.'%')
                            ->orWhere('programmes.programme_name','LIKE','%'.$value.'%')
                            ->orWhere('departments.department_name','LIKE','%'.$value.'%');
                    })
                    ->where('subjects.syllabus', '!=', "")
                    ->get();
            $result .= '<div class="col-md-12">';
            $result .= '<p style="font-size: 18px;margin:0px 0px 0px 10px;display: inline-block;">Search Filter : '.$value.'</p>';
            $result .= '</div>';
            if ($subjects->count()) {
                foreach($subjects as $row){
                    $result .= '<a href="'.asset("syllabus/".$row->syllabus).'" class="col-md-12 align-self-center" id="course_list" download="'.$row->syllabus_name.'".xlsx">';
                    $result .= '<div class="col-md-12 row" style="padding:10px;color:#0d2f81;">';
                    $result .= '<div class="col-1" style="padding-top: 3px;">';
                    $result .= '<img src="'.url("image/excel.png").'" width="25px" height="25px"/>';
                    $result .= '</div>';
                    $result .= '<div class="col" id="course_name">';
                    $result .= '<p style="margin: 0px;"><b>'.$row->syllabus_name.'</b></p>';
                    $result .= '</div></div></a>';
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
                    ->get();
            $result .= '<div class="col-md-12">';
            $result .= '<p style="font-size: 18px;margin:0px 0px 0px 10px;display: inline-block;">Syllabus In Faculty</p>';
            $result .= '</div>';
            foreach($subjects as $row){
                $result .= '<a href="'.asset("syllabus/".$row->syllabus).'" class="col-md-12 align-self-center" id="course_list" download="'.$row->syllabus_name.'".xlsx">';
                $result .= '<div class="col-md-12 row" style="padding:10px;color:#0d2f81;">';
                $result .= '<div class="col-1" style="padding-top: 3px;">';
                $result .= '<img src="'.url("image/excel.png").'" width="25px" height="25px"/>';
                $result .= '</div>';
                $result .= '<div class="col" id="course_name">';
                $result .= '<p style="margin: 0px;"><b>'.$row->syllabus_name.'</b></p>';
                $result .= '</div></div></a>';
            }
        }
        return $result;
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
        return view('dean.folder_view', compact('faculty','portfolio_place','faculty_portfolio','faculty_portfolio_list','data'));
    }

    public function uploadFiles(Request $request) 
    {
        $image = $request->file('file');
        $imageName = $image->getClientOriginalName();
        $image->move(public_path('/fake/faculty_portfolio/'),$imageName);
        return response()->json(['success'=>$imageName]);  
    }
    public function destroyFiles(Request $request)
    {
        $filename =  $request->get('filename');
        $path = public_path().'/fake/faculty_portfolio/'.$filename;
        if (file_exists($path)) {
            unlink($path);
        }
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

            $faculty_portfolio = new Faculty_Portfolio([
                'faculty_id'             =>  $faculty_id,
                'portfolio_name'         =>  $name,
                'portfolio_type'         =>  'document',
                'portfolio_place'        =>  $place,
                'portfolio_file'         =>  $fake,
                'status'                 =>  'Active',
            ]);
            $faculty_portfolio->save();
            $fake_place = "fake/faculty_portfolio/".$fake;
            rename($fake_place, 'f_Portfolio/'.$faculty_id.'/'.$fake);
        }
        return redirect()->back()->with('success','New Document Added Successfully');
    }

    public function removeActiveFile($id){
        $faculty_portfolio = Faculty_Portfolio::where('fp_id', '=', $id)->firstOrFail();
        $faculty_portfolio->status  = "Remove";
        $faculty_portfolio->save();
        return redirect()->back()->with('success','Remove Successfully');
    }
}
