<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Academic;

class AcademicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $academic = Academic::all()->toArray();
        return view('admin.AcademicIndex', ['academic' => $academic]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.AcademicCreate');
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
            'academic_name'          =>  'required',
        ]);

        $academic = new Academic([
            'academic_name'       => $request->get('academic_name'),
        ]);
        $academic->save();
        return redirect()->route('admin.academic_list.index')->with('success','Data Added');
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
        $academic = Academic::where('academic_id', '=', $id)->firstOrFail();
        return view('admin.AcademicEdit', compact('academic', 'id'));
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
            'academic_name'    =>  'required',
        ]);

        $academic                = Academic::where('academic_id', '=', $id)->firstOrFail();
        $academic->academic_name = $request->get('academic_name');
        $academic->save(); 

        if(auth()->user()){
            if(auth()->user()->position == "admin"){
                return redirect()->route('admin.academic_list.index')->with('success','Data Updated');
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
