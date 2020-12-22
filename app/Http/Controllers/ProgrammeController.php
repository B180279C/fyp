<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Department;
use App\Faculty;
use App\Programme;
use Illuminate\Support\Facades\DB;
use App\Exports\ProgrammeExport;
use Maatwebsite\Excel\Facades\Excel;

class ProgrammeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $programmes = DB::table('programmes')
                    ->join('departments', 'programmes.department_id', '=', 'departments.department_id')
                    ->join('faculty', 'departments.faculty_id', '=', 'faculty.faculty_id')
                    ->select('programmes.*', 'departments.department_name', 'faculty.faculty_name')
                    ->orderBy('faculty.faculty_id')
                    ->get();
        return view('admin.ProgrammeIndex', ['programmes' => $programmes]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $departments = Department::all()->toArray();
        $faculty = Faculty::all()->toArray();
        return view('admin.ProgrammeCreate', compact('departments', 'faculty'));
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
            'programme_name'       =>  'required',
            'short_form_name'      =>  'required',
            'department'           =>  'string',
            'level'                =>  'string',
        ]);

        $programme = new Programme([
            'programme_name'       =>  $request->get('programme_name'),
            'short_form_name'      =>  $request->get('short_form_name'),
            'department_id'        =>  $request->get('department'),
            'level'                =>  $request->get('level'),
        ]);

        $programme->save();
        return redirect()->route('admin.programme_list.index')->with('success','Data Added');
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
        $programme = Programme::where('programme_id', '=', $id)->firstOrFail();
        $department = Department::all()->toArray();
        $faculty = Faculty::all()->toArray();
        return view('admin.ProgrammeEdit', compact('programme','department' ,'faculty', 'id'));
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
            'programme_name'       =>  'required',
            'short_form_name'      =>  'required',
            'department'           =>  'string',
            'level'                =>  'string',
        ]);

        $programme                   = Programme::where('programme_id', '=', $id)->firstOrFail();
        $programme->programme_name   = $request->get('programme_name');
        $programme->short_form_name  = $request->get('short_form_name');
        $programme->department_id    = $request->get('department');
        $programme->level            = $request->get('level');

        $programme->save(); 

        if(auth()->user()){
            if(auth()->user()->position == "admin"){
                return redirect()->route('admin.programme_list.index')->with('success','Data Updated');
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
        return Excel::download(new ProgrammeExport, 'Programme.xlsx');
    }
}
