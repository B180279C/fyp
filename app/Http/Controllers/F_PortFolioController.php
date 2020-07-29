<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Staff;
use App\Department;
use App\Faculty;
use App\Faculty_Portfolio;

class F_PortFolioController extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->user_id;
        $staff_dean     = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id = $staff_dean->faculty_id;
        $faculty_portfolio = DB::table('faculty_portfolio')
                    ->select('faculty_portfolio.*')
                    ->where('faculty_id', '=', $faculty_id)
                    ->where('portfolio_place', '=', 'Faculty')
                    ->orderBy('faculty_portfolio.fp_id')
                    ->get();
        return view('dean.FacultyPortFolio', compact('faculty_portfolio'));
    }

    public function CVdepartment()
    {
        if(auth()->user()->position == "Dean"){
            $user_id = auth()->user()->user_id;
            $staff_dean     = Staff::where('user_id', '=', $user_id)->firstOrFail();
            $faculty_id = $staff_dean->faculty_id;
            $departments = DB::table('departments')
                    ->select('departments.*')
                    ->where('faculty_id', '=', $faculty_id)
                    ->orderBy('departments.department_id')
                    ->get();
        }
        return view('dean.departmentCV', compact('departments'));
    }

    public function lecturerCV($department)
    {
        if(auth()->user()->position == "Dean"){
        	$user_id = auth()->user()->user_id;
        	$staff_dean     = Staff::where('user_id', '=', $user_id)->firstOrFail();
        	$faculty_id = $staff_dean->faculty_id;
        	$faculty_staff = DB::table('staffs')
        			->join('users', 'staffs.user_id', '=', 'users.user_id')
                    ->select('staffs.*','users.*')
                    ->where('staffs.faculty_id', '=', $faculty_id)
                    ->where('staffs.department_id','=', $department)
                    ->orderByDesc('staffs.lecturer_CV')
                    ->get();
            $departments = Department::where('department_id', '=', $department)->firstOrFail();
            if($departments->faculty_id!=$faculty_id){
                return redirect()->back();
            }
        }
        return view('dean.LecturerCV', compact('faculty_staff','departments'));
    }

    public function searchLecturerCV(Request $request){
        $value = $request->get('value');
        $department = $request->get('department');
        $result = "";
        if($value!=""){
            $faculty_staff = DB::table('staffs')
                    ->join('users', 'staffs.user_id', '=', 'users.user_id')
                    ->select('staffs.*','users.*')
                    ->where('staffs.lecturer_CV','LIKE','%'.$value.'%')
                    ->where('staffs.department_id','=', $department)
                    ->orderByDesc('staffs.lecturer_CV')
                    ->get();
            if ($faculty_staff->count()) {
                foreach($faculty_staff as $row){
                    $ext = explode(".", $row->lecturer_CV);
                    $result .= '<div class="col-md-3">';
                    $result .= '<center>';
                    $result .= '<a href="'.asset("staffCV/".$row->lecturer_CV).'" style="border: 1px solid #cccccc;padding:40px;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" download id="download_link">';
                    if($ext[1]=="pdf"){
                      $result .= '<img src="'.url("image/pdf.png").'"/>';
                    }else if($ext[1]=="docx"){
                        $result .= '<img src="'.url("image/docs.png").'"/>';
                    }else if($ext[1]=="xlsx"){
                        $result .= '<img src="'.url("image/excel.png").'"/>';
                    }
                    $result .= '<p>'.$row->lecturer_CV.'</p>';
                    $result .= '</a></center></div>';
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
                    ->where('staffs.department_id','=', $department)
                    ->orderByDesc('staffs.lecturer_CV')
                    ->get();
            foreach($faculty_staff as $row){
                $ext = explode(".", $row->lecturer_CV);
                $result .= '<div class="col-md-3">';
                $result .= '<center>';
                $result .= '<a href="'.asset("staffCV/".$row->lecturer_CV).'" style="border: 1px solid #cccccc;padding:40px;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" download id="download_link">';
                if($ext[1]=="pdf"){
                    $result .= '<img src="'.url("image/pdf.png").'"/>';
                }else if($ext[1]=="docx"){
                    $result .= '<img src="'.url("image/docs.png").'"/>';
                }else if($ext[1]=="xlsx"){
                    $result .= '<img src="'.url("image/excel.png").'"/>';
                }
                $result .= '<p>'.$row->lecturer_CV.'</p>';
                $result .= '</a></center></div>';
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

    public function folder_view($folder_id)
    {
        $user_id = auth()->user()->user_id;
        $staff_dean     = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id = $staff_dean->faculty_id;
        $faculty_portfolio = Faculty_Portfolio::where('fp_id', '=', $folder_id)->firstOrFail();

        $place_name = explode(',,,',($faculty_portfolio->portfolio_place));
        $i=1;
        $data = "Faculty";
        while(isset($place_name[$i])!=""){
            $name = Faculty_Portfolio::where('fp_id', '=', $place_name[$i])->firstOrFail();
            $data .= ",,,".$name->portfolio_name;
            $i++;
        }

        $place = $faculty_portfolio->portfolio_place.",,,".$faculty_portfolio->fp_id;
        $faculty_portfolio_list = DB::table('faculty_portfolio')
                    ->select('faculty_portfolio.*')
                    ->where('faculty_id', '=', $faculty_id)
                    ->where('portfolio_place', '=', $place)
                    ->orderBy('faculty_portfolio.fp_id')
                    ->get();
        return view('dean.folder_view', compact('faculty_portfolio','faculty_portfolio_list','data'));
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
}
