<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Semester;

class SemesterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $semesters = DB::table('semesters')
                    ->select('semesters.*')
                    ->orderByDesc('semesters.year')
                    ->orderByDesc('semesters.semester')
                    ->get();
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
        $semester = new Semester([
            'semester'       => $request->get('semester'),
            'year'           => $request->get('year'),
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

        $semester               = Semester::where('semester_id', '=', $id)->firstOrFail();
        $semester->semester     = $request->get('semester');
        $semester->year         = $request->get('year');
        $semester->startDate    = $request->get('start_date');
        $semester->endDate      = $request->get('end_date');
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
}
