<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Programme;
use App\Subject;
use App\Academic;
use App\Department;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $programme = Programme::where('programme_id', '=', $id)->firstOrFail();
        $subjects = DB::table('subjects')
                    ->join('programmes', 'subjects.programme_id', '=', 'programmes.programme_id')
                    ->select('subjects.*', 'programmes.programme_name','programmes.short_form_name')
                    ->where('subjects.programme_id', '=', $id)
                    ->get();
        $group = DB::table('subjects')
                    ->select('subjects.*')
                    ->where('programme_id', '=', $id)
                    ->groupBy('subject_type')
                    ->get();
        return view('admin.SubjectCreate', compact('programme','subjects','group', 'id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        $count = $request->get('count');
    
        for($i=1;$i<=$count;$i++){
            $type = $request->get('subject_type'.$i);
            $count_list = $request->get('count_list'.$i);
            for ($m=1; $m <= $count_list; $m++) { 
                $subject_code = $request->get($i.'subject_code'.$m);
                $subject_name = $request->get($i.'subject_name'.$m);

                if($subject_code!="" && $subject_name!=""){
                    $subject = new Subject([
                        'programme_id'         =>  $id,
                        'subject_type'         =>  $type,
                        'subject_code'         =>  $subject_code,
                        'subject_name'         =>  $subject_name,
                    ]);
                    $subject->save();
                }
            }
        }
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

    public function subjectEditModal(Request $request)
    {
        $subject_id = $request->get('value');
        $subject = Subject::find($subject_id);
        return $subject;
    }
        
    public function subjectUpdateModal(Request $request)
    {
        $subject_id   = $request->get('subject_id');

        $subject = Subject::where('subject_id', '=', $subject_id)->firstOrFail();
        $subject->subject_code  = $request->get('subject_code');
        $subject->subject_name  = $request->get('subject_name');
        $subject->save();

        $id = $subject->programme_id;
        $programme = Programme::where('programme_id', '=', $id)->firstOrFail();
        $subjects = DB::table('subjects')
                    ->join('programmes', 'subjects.programme_id', '=', 'programmes.programme_id')
                    ->select('subjects.*', 'programmes.programme_name','programmes.short_form_name')
                    ->where('subjects.programme_id', '=', $id)
                    ->get();
        $group = DB::table('subjects')
                    ->select('subjects.*')
                    ->where('programme_id', '=', $id)
                    ->groupBy('subject_type')
                    ->get();
        return redirect()->route('subject.create', compact('programme','subjects','group', 'id'))->with('success','Data Updated');
    }

    public function subjectTypeUpdateModal(Request $request)
    {
        $programme_id   = $request->get('programme_id');
        $same           = $request->get('same');

        $subject = Subject::where([
                        ['programme_id', '=', $programme_id],
                        ['subject_type', '=', $same],
                    ])->get();

        foreach($subject as $row){
            $subject_list = Subject::where('subject_id', '=', $row->subject_id)->firstOrFail();
            $subject_list->subject_type = $request->get('subject_type');
            $subject_list->save();
        }

        $id = $programme_id;
        $programme = Programme::where('programme_id', '=', $id)->firstOrFail();
        $subjects = DB::table('subjects')
                    ->join('programmes', 'subjects.programme_id', '=', 'programmes.programme_id')
                    ->select('subjects.*', 'programmes.programme_name','programmes.short_form_name')
                    ->where('subjects.programme_id', '=', $id)
                    ->get();
        $group = DB::table('subjects')
                    ->select('subjects.*')
                    ->where('programme_id', '=', $id)
                    ->groupBy('subject_type')
                    ->get();
        return redirect()->route('subject.create', compact('programme','subjects','group', 'id'))->with('success','Data Updated');
    }


}
