<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Programme;
use App\Subject_MPU;
use App\Faculty;
use App\Department;


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
                    ->get();
        $group = DB::table('subjects_mpu')
                    ->select('subjects_mpu.*')
                    ->groupBy('subject_type')
                    ->orderBy('mpu_id')
                    ->where('subjects_mpu.level', '=', $level)
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
    
        for($i=1;$i<=$count;$i++){
            $type = $request->get('subject_type'.$i);
            $count_list = $request->get('count_list'.$i);
            for ($m=1; $m <= $count_list; $m++) { 
                $subject_code = $request->get($i.'subject_code'.$m);
                $subject_name = $request->get($i.'subject_name'.$m);
                $syllabus     = $request->get($i.'syllabus'.$m);
                $file         = $request->get($i.'full_syllabus'.$m);

                if($subject_code!="" && $subject_name!=""){
                    $subject = new Subject_MPU([
                        'level'                =>  $level,
                        'subject_type'         =>  $type,
                        'subject_code'         =>  $subject_code,
                        'subject_name'         =>  $subject_name,
                        'syllabus'             =>  $file,
                        'syllabus_name'        =>  $syllabus,
                    ]);
                    $fake_place = "fake/syllabus/".$file;
                    rename($fake_place, 'syllabus/'.$file);
                    $subject->save();
                }
            }
        }
        return redirect()->route('admin.mpu_list.index')->with('success','Data Added');
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
                $path = public_path().'/syllabus/'.$mpu->syllabus;
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            $mpu->syllabus = $filename;
            $fake_place = "fake/syllabus/".$fake;
            rename($fake_place, 'syllabus/'.$filename);
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
}
