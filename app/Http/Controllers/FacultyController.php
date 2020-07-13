<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Faculty;

class FacultyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $facultys = Faculty::all()->toArray();
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
}
