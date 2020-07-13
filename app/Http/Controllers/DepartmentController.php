<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Department;
use App\Academic;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departments = DB::table('departments')
                    ->join('academic', 'departments.academic_id', '=', 'academic.academic_id')
                    ->select('departments.*', 'academic.academic_name')
                    ->get();
        return view('admin.DepartmentIndex', ['departments' => $departments]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $academic = Academic::all()->toArray();
        return view('admin.DepartmentCreate', compact('academic'));
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
            'academic'             =>  'string',
            'department_name'      =>  'required'
        ]);


        $department = new Department([
            'academic_id'        => $request->get('academic'),
            'department_name'   => $request->get('department_name'),
        ]);

        $department->save();
        return redirect()->route('admin.department_list.index')->with('success','Data Added');
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
        $department = Department::where('department_id', '=', $id)->firstOrFail();
        $academic = Academic::all()->toArray();
        return view('admin.DepartmentEdit', compact('department' ,'academic', 'id'));
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
            'department_name'       =>  'required',
            'academic'              =>  'string',
        ]);

        $department                  = Department::where('department_id', '=', $id)->firstOrFail();
        $department->department_name = $request->get('department_name');
        $department->academic_id     = $request->get('academic');
        $department->save(); 

        if(auth()->user()){
            if(auth()->user()->position == "admin"){
                return redirect()->route('admin.department_list.index')->with('success','Data Updated');
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
