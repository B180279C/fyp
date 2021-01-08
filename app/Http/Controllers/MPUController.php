<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Programme;
use App\Subject_MPU;
use App\Faculty;
use App\Department;
use App\Exports\MPUSubjectExport;
use Maatwebsite\Excel\Facades\Excel;


class MPUController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $programmes = DB::table('programmes')
                    ->select('programmes.*')
                    ->groupBy('level')
                    ->where('programmes.status_programme','=','Active')
                    ->get();
        return view('admin.MPUIndex', ['programmes' => $programmes]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($level)
    {
        $subjects = DB::table('subjects_mpu')
                    ->select('subjects_mpu.*')
                    ->where('subjects_mpu.level', '=', $level)
                    ->where('subjects_mpu.status_subject','=','Active')
                    ->get();
        $group = DB::table('subjects_mpu')
                    ->select('subjects_mpu.*')
                    ->groupBy('subject_type')
                    ->orderBy('mpu_id')
                    ->where('subjects_mpu.level', '=', $level)
                    ->where('subjects_mpu.status_subject','=','Active')
                    ->get();
        return view('admin.MPUCreate', compact('subjects','group', 'level'));
    }

    public function view($level)
    {
        $subjects = DB::table('subjects_mpu')
                    ->select('subjects_mpu.*')
                    ->where('subjects_mpu.level', '=', $level)
                    ->get();
        $group = DB::table('subjects_mpu')
                    ->select('subjects_mpu.*')
                    ->groupBy('subject_type')
                    ->orderBy('mpu_id')
                    ->where('subjects_mpu.level', '=', $level)
                    ->get();
        return view('admin.MPUView', compact('subjects','group', 'level'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $level)
    {
        $count = $request->get('count');
        $failed = "";
        for($i=1;$i<=$count;$i++){
            $type = $request->get('subject_type'.$i);
            $count_list = $request->get('count_list'.$i);
            for ($m=1; $m <= $count_list; $m++) { 
                $subject_code = $request->get($i.'subject_code'.$m);
                $subject_name = $request->get($i.'subject_name'.$m);
                $syllabus     = $request->get($i.'syllabus'.$m);
                $file         = $request->get($i.'full_syllabus'.$m);
                $already      = $request->get($i.'already'.$m);
                if($already=="No"){
                    if($subject_code!="" && $subject_name!="" && $syllabus!=""){
                        $subject = new Subject_MPU([
                            'level'                =>  $level,
                            'subject_type'         =>  $type,
                            'subject_code'         =>  $subject_code,
                            'subject_name'         =>  $subject_name,
                            'syllabus'             =>  $file,
                            'syllabus_name'        =>  $syllabus,
                        ]);

                        $fake_place = Storage::disk('private')->get("fake/syllabus/".$file);
                        Storage::disk('private')->put('syllabus/'.$file, $fake_place); 
                        Storage::disk('private')->delete('fake/syllabus/'.$file);

                        $subject->save();
                    }else if($syllabus=="" || $file==""){
                        $failed .= $subject_code." ".$subject_name." of syllabus cannot be empty";
                    }
                }
            }
        }
        $subjects = DB::table('subjects_mpu')
                    ->select('subjects_mpu.*')
                    ->where('subjects_mpu.level', '=', $level)
                    ->get();
        $group = DB::table('subjects_mpu')
                    ->select('subjects_mpu.*')
                    ->groupBy('subject_type')
                    ->orderBy('mpu_id')
                    ->where('subjects_mpu.level', '=', $level)
                    ->get();
        if($failed==""){
            return redirect()->route('MPU.create', compact('subjects','group', 'level'))->with('success','Data Added');
        }else{
            return redirect()->route('MPU.create', compact('subjects','group', 'level'))->with('failed',$failed);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function generalStudiesEditModal(Request $request)
    {
        $mpu_id = $request->get('value');
        $subject = Subject_MPU::find($mpu_id);
        return $subject;
    }

    public function generalStudiesUpdateModal(Request $request)
    {
        $mpu_id = $request->get('mpu_id');
        $fake   = $request->get('fake');

        $filename = $fake;
        $mpu = Subject_MPU::where('mpu_id', '=', $mpu_id)->firstOrFail();
        $mpu->subject_code  = $request->get('subject_code');
        $mpu->subject_name  = $request->get('subject_name');

        if($fake!=""){
            if($mpu->syllabus!=""){
                Storage::disk('private')->delete('/syllabus/'.$mpu->syllabus);
            }
            $mpu->syllabus = $filename;

            $fake_place = Storage::disk('private')->get("fake/syllabus/".$fake);
            Storage::disk('private')->put('syllabus/'.$filename, $fake_place); 
            Storage::disk('private')->delete('fake/syllabus/'.$fake);
            
            $mpu->syllabus_name = $request->get('form');
        }else{
            $mpu->syllabus_name = $request->get('syllabus');
        }

        $mpu->save();
        $level = $mpu->level;
        $subjects = DB::table('subjects_mpu')
                    ->select('subjects_mpu.*')
                    ->where('subjects_mpu.level', '=', $level)
                    ->get();
        $group = DB::table('subjects_mpu')
                    ->select('subjects_mpu.*')
                    ->groupBy('subject_type')
                    ->orderBy('mpu_id')
                    ->where('subjects_mpu.level', '=', $level)
                    ->get();
        return redirect()->route('MPU.create', compact('subjects','group', 'level'))->with('success','Data Updated');
    }

    public function generalStudiesTypeUpdateModal(Request $request)
    {
        $level   = $request->get('level');
        $same    = $request->get('same');

        $mpu = Subject_MPU::where([
                ['level', '=', $level],
                ['subject_type', '=', $same],
            ])->get();

        foreach($mpu as $row){
            $gs_list = Subject_MPU::where('mpu_id', '=', $row->mpu_id)->firstOrFail();
            $gs_list->subject_type = $request->get('subject_type');
            $gs_list->save();
        }

        $subjects = DB::table('subjects_mpu')
                    ->select('subjects_mpu.*')
                    ->where('subjects_mpu.level', '=', $level)
                    ->get();
        $group = DB::table('subjects_mpu')
                    ->select('subjects_mpu.*')
                    ->groupBy('subject_type')
                    ->orderBy('mpu_id')
                    ->where('subjects_mpu.level', '=', $level)
                    ->get();
        return redirect()->route('MPU.create', compact('subjects','group', 'level'))->with('success','Data Updated');
    }

    public function downloadSyllabus($id)
    {
        $subject = Subject_MPU::where('mpu_id', '=', $id)->firstOrFail();
        $syllabus = $subject->syllabus;
        $name = $subject->syllabus_name;
        $ext = "";
        if($subject->syllabus!=""){
            $ext = explode(".", $subject->syllabus);
        }
        return Storage::disk('private')->download('/syllabus/'.$syllabus,$name.'.'.$ext[1]);
    }

    public function downloadExcel($level)
    {
        return Excel::download(new MPUSubjectExport($level), 'MPUSubject.xlsx');
    }

    public function removeActiveSubject($id){
        $subject = Subject_MPU::where('mpu_id', '=', $id)->firstOrFail();
        $subject->status_subject  = "Remove";
        $subject->save();
        Storage::disk('private')->delete('/syllabus/'.$subject->syllabus);
        return redirect()->back()->with('success','Remove Successfully');
    }
}
