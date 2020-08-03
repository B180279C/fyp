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

    public function CVdepartment()
    {
        if(auth()->user()->position == "Dean"){
            $user_id = auth()->user()->user_id;
            $staff_dean     = Staff::where('user_id', '=', $user_id)->firstOrFail();
            $faculty_id = $staff_dean->faculty_id;
            $faculty    = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
            $departments = DB::table('departments')
                    ->select('departments.*')
                    ->where('faculty_id', '=', $faculty_id)
                    ->orderBy('departments.department_id')
                    ->get();
        }
        return view('dean.departmentCV', compact('departments','faculty'));
    }


    public function SyllabusDepartment()
    {
        if(auth()->user()->position == "Dean"){
            $user_id = auth()->user()->user_id;
            $staff_dean     = Staff::where('user_id', '=', $user_id)->firstOrFail();
            $faculty_id = $staff_dean->faculty_id;
            $faculty    = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
            $departments = DB::table('departments')
                    ->select('departments.*')
                    ->where('faculty_id', '=', $faculty_id)
                    ->orderBy('departments.department_id')
                    ->get();
        }
        return view('dean.departmentSyllabus', compact('departments','faculty'));
    }


    public function lecturerCV($department)
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
                    ->where('staffs.department_id','=', $department)
                    ->orderByDesc('staffs.lecturer_CV')
                    ->get();
            $departments = Department::where('department_id', '=', $department)->firstOrFail();
            if($departments->faculty_id!=$faculty_id){
                return redirect()->back();
            }
        }
        return view('dean.LecturerCV', compact('faculty_staff','departments','faculty'));
    }

    public function SyllabusProgramme($department)
    {
        if(auth()->user()->position == "Dean"){
            $user_id = auth()->user()->user_id;
            $staff_dean     = Staff::where('user_id', '=', $user_id)->firstOrFail();
            $faculty_id = $staff_dean->faculty_id;
            $faculty    = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
            $programmes = DB::table('programmes')
                    ->join('departments', 'programmes.department_id', '=', 'departments.department_id')
                    ->select('departments.*','programmes.*')
                    ->where('departments.faculty_id', '=', $faculty_id)
                    ->where('programmes.department_id', '=', $department)
                    ->get();
            $departments = Department::where('department_id', '=', $department)->firstOrFail();
            if($departments->faculty_id!=$faculty_id){
                return redirect()->back();
            }
        }
        return view('dean.programmeSyllabus', compact('programmes','departments','faculty'));
    }

    public function Syllabus($programme)
    {
        if(auth()->user()->position == "Dean"){
            $user_id = auth()->user()->user_id;
            $staff_dean     = Staff::where('user_id', '=', $user_id)->firstOrFail();
            $faculty_id = $staff_dean->faculty_id;
            $faculty    = Faculty::where('faculty_id', '=', $faculty_id)->firstOrFail();
            $subjects = DB::table('subjects')
                    ->select('subjects.*')
                    ->where('subjects.syllabus', '!=', "")
                    ->where('subjects.programme_id', '=', $programme)
                    ->get();
            $programme  = Programme::where('programme_id', '=', $programme)->firstOrFail();
            $department_id = $programme->department_id;
            $departments = Department::where('department_id', '=', $department_id)->firstOrFail();
            if($departments->faculty_id!=$faculty_id){
                return redirect()->back();
            }
        }
        return view('dean.Syllabus', compact('subjects','programme','departments','faculty'));
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
                    $result .= '<div class="col-md-3" style="margin-bottom: 20px">';
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
                $result .= '<div class="col-md-3" style="margin-bottom: 20px">';
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

    public function searchSyllabus(Request $request){
        $value = $request->get('value');
        $programme = $request->get('programme');
        $result = "";
        if($value!=""){
            $subjects = DB::table('subjects')
                    ->select('subjects.*')
                    ->where('subjects.syllabus_name','LIKE','%'.$value.'%')
                    ->where('subjects.programme_id', '=', $programme)
                    ->get();
            if ($subjects->count()) {
                foreach($subjects as $row){
                    $result .= '<div class="col-md-3" style="margin-bottom: 20px">';
                    $result .= '<center>';
                    $result .= '<a href="'.asset("syllabus/".$row->syllabus).'" style="border: 1px solid #cccccc;padding:40px;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" download="'.$row->syllabus_name.'".xlsx" id="download_link">';
                    $result .= '<img src="'.url("image/excel.png").'"/>';
                    $result .= '<p>'.$row->syllabus_name.'</p>';
                    $result .= '</a></center></div>';
                }
            }else{
                    $result .= '<div class="col-md-12">';
                    $result .= '<p>Not Found</p>';
                    $result .= '</div>';
            }
        }else{
            $subjects = DB::table('subjects')
                    ->select('subjects.*')
                    ->where('subjects.programme_id', '=', $programme)
                    ->get();
            foreach($subjects as $row){
                $result .= '<div class="col-md-3" style="margin-bottom: 20px">';
                $result .= '<center>';
                $result .= '<a href="'.asset("syllabus/".$row->syllabus).'" style="border: 1px solid #cccccc;padding:40px;display: inline-block;height: 225px;width: 100%;border-radius: 10px;color: black;font-weight: bold;" download="'.$row->syllabus_name.'".xlsx" id="download_link">';
                $result .= '<img src="'.url("image/excel.png").'"/>';
                $result .= '<p>'.$row->syllabus_name.'</p>';
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

        $place = $faculty_portfolio->portfolio_place.",,,".$faculty_portfolio->fp_id;
        $faculty_portfolio_list = DB::table('faculty_portfolio')
                    ->select('faculty_portfolio.*')
                    ->where('faculty_id', '=', $faculty_id)
                    ->where('portfolio_place', '=', $place)
                    ->where('status', '=', 'Active')
                    ->orderBy('faculty_portfolio.fp_id')
                    ->get();
        return view('dean.folder_view', compact('faculty','faculty_portfolio','faculty_portfolio_list','data'));
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
            $time = $request->get('time'.$i);
            $name = $request->get('form'.$i);
            $ext = $request->get('ext'.$i);
            $fake = $request->get('fake'.$i);

            $filename = $time."___".$name.$ext;
            $faculty_portfolio = new Faculty_Portfolio([
                'faculty_id'             =>  $faculty_id,
                'portfolio_name'         =>  $name,
                'portfolio_type'         =>  'document',
                'portfolio_place'        =>  $place,
                'portfolio_file'         =>  $filename,
                'status'                 =>  'Active',
            ]);
            $faculty_portfolio->save();
            $fake_place = "fake/faculty_portfolio/".$fake;
            rename($fake_place, 'f_Portfolio/'.$faculty_id.'/'.$filename);
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
