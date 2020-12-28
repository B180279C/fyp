<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Semester;
use App\Exports\SemesterExport;
use Maatwebsite\Excel\Facades\Excel;

class SemesterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $semesters = Semester::orderByDesc('semester_name')->where('status_sem','=','Active')->get();
        return view('admin.SemesterIndex', ['semesters' => $semesters]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.SemesterCreate');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $semester_name = '20'.$request->get('year')."_".$request->get('semester');
        $semester = new Semester([
            'year'           => $request->get('year'),
            'semester'       => $request->get('semester'),
            'semester_name'  => $semester_name,
            'startDate'      => $request->get('start_date'),
            'endDate'        => $request->get('end_date'),
        ]);
        $semester->save();
        return redirect()->route('admin.semester_list.index')->with('success','Data Added');
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
        $semester = Semester::where('semester_id', '=', $id)->firstOrFail();
        return view('admin.SemesterEdit', compact('semester', 'id'));
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

        $semester                = Semester::where('semester_id', '=', $id)->firstOrFail();
        $semester->year          = $request->get('year');
        $semester->semester      = $request->get('semester');
        $semester->semester_name = '20'.$request->get('year')."_".$request->get('semester');
        $semester->startDate     = $request->get('start_date');
        $semester->endDate       = $request->get('end_date');
        $semester->save(); 

        if(auth()->user()){
            if(auth()->user()->position == "admin"){
                return redirect()->route('admin.semester_list.index')->with('success','Data Updated');
            }
        }
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

    public function downloadExcel()
    {
        return Excel::download(new SemesterExport, 'semester.xlsx');
    }

    public function removeActiveSemester($id){
        $semester = Semester::where('semester_id', '=', $id)->firstOrFail();
        $semester->status_sem  = "Remove";
        $semester->save();
        return redirect()->back()->with('success','Remove Successfully');
    }
}
