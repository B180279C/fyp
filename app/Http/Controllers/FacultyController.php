<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;
use App\Faculty;
use App\Exports\FacultyExport;
use Maatwebsite\Excel\Facades\Excel;

class FacultyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $facultys = Faculty::where('status_faculty','=','Active')->get();
        return view('admin.FacultyIndex', ['facultys' => $facultys]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.FacultyCreate');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'faculty_name'          =>  'required',
        ]);

        $faculty = new Faculty([
            'faculty_name'       => $request->get('faculty_name'),
        ]);

        $faculty->save();
        $faculty_id = $faculty->faculty_id;

        $path = public_path().'/f_Portfolio/' . $faculty_id;
        File::makeDirectory($path, $mode = 0777, true, true);
        return redirect()->route('admin.faculty_list.index')->with('success','Data Added');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $faculty = Faculty::where('faculty_id', '=', $id)->firstOrFail();
        return view('admin.FacultyEdit', compact('faculty', 'id'));
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
        $this->validate($request, [
            'faculty_name'    =>  'required',
        ]);

        $faculty               = Faculty::where('faculty_id', '=', $id)->firstOrFail();
        $faculty->faculty_name = $request->get('faculty_name');
        $faculty->save(); 

        if(auth()->user()){
            if(auth()->user()->position == "admin"){
                return redirect()->route('admin.faculty_list.index')->with('success','Data Updated');
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
        return Excel::download(new FacultyExport, 'Faculty.xlsx');
    }

    public function removeActiveFaculty($id){
        $faculty = Faculty::where('faculty_id', '=', $id)->firstOrFail();
        $faculty->status_faculty  = "Remove";
        $faculty->save();
        return redirect()->back()->with('success','Remove Successfully');
    }
}

