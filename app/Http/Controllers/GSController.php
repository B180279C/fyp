<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Programme;
use App\GeneralStudies;
use App\Academic;
use App\Department;

class GSController extends Controller
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
        return view('admin.GSIndex', ['programmes' => $programmes]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($level)
    {
        $subjects = DB::table('general_studies')
                    ->select('general_studies.*')
                    ->where('general_studies.level', '=', $level)
                    ->get();
        $group = DB::table('general_studies')
                    ->select('general_studies.*')
                    ->groupBy('subject_type')
                    ->orderBy('gs_id')
                    ->where('general_studies.level', '=', $level)
                    ->get();
        return view('admin.GSCreate', compact('subjects','group', 'level'));
    }

    public function view($level)
    {
        $subjects = DB::table('general_studies')
                    ->select('general_studies.*')
                    ->where('general_studies.level', '=', $level)
                    ->get();
        $group = DB::table('general_studies')
                    ->select('general_studies.*')
                    ->groupBy('subject_type')
                    ->orderBy('gs_id')
                    ->where('general_studies.level', '=', $level)
                    ->get();
        return view('admin.GSView', compact('subjects','group', 'level'));
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

                if($subject_code!="" && $subject_name!=""){
                    $subject = new GeneralStudies([
                        'level'                =>  $level,
                        'subject_type'         =>  $type,
                        'subject_code'         =>  $subject_code,
                        'subject_name'         =>  $subject_name,
                    ]);
                    $subject->save();
                }
            }
        }
        return redirect()->route('admin.gs_list.index')->with('success','Data Added');
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
        $gs_id = $request->get('value');
        $subject = GeneralStudies::find($gs_id);
        return $subject;
    }

    public function generalStudiesUpdateModal(Request $request)
    {
        $gs_id = $request->get('gs_id');

        $gs = GeneralStudies::where('gs_id', '=', $gs_id)->firstOrFail();
        $gs->subject_code  = $request->get('subject_code');
        $gs->subject_name  = $request->get('subject_name');
        $gs->save();

        $level = $gs->level;

        $subjects = DB::table('general_studies')
                    ->select('general_studies.*')
                    ->where('general_studies.level', '=', $level)
                    ->get();
        $group = DB::table('general_studies')
                    ->select('general_studies.*')
                    ->groupBy('subject_type')
                    ->orderBy('gs_id')
                    ->where('general_studies.level', '=', $level)
                    ->get();
        return redirect()->route('generalStudies.create', compact('subjects','group', 'level'))->with('success','Data Updated');
    }

    public function generalStudiesTypeUpdateModal(Request $request)
    {
        $level   = $request->get('level');
        $same    = $request->get('same');

        $gs = GeneralStudies::where([
                ['level', '=', $level],
                ['subject_type', '=', $same],
            ])->get();

        foreach($gs as $row){
            $gs_list = GeneralStudies::where('gs_id', '=', $row->gs_id)->firstOrFail();
            $gs_list->subject_type = $request->get('subject_type');
            $gs_list->save();
        }

        $subjects = DB::table('general_studies')
                    ->select('general_studies.*')
                    ->where('general_studies.level', '=', $level)
                    ->get();
        $group = DB::table('general_studies')
                    ->select('general_studies.*')
                    ->groupBy('subject_type')
                    ->orderBy('gs_id')
                    ->where('general_studies.level', '=', $level)
                    ->get();
        return redirect()->route('generalStudies.create', compact('subjects','group', 'level'))->with('success','Data Updated');
    }
}
