<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Programme;
use App\Subject;
use App\Faculty;
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
        $failed = "";
        for($i=1;$i<=$count;$i++){
            $type = $request->get('subject_type'.$i);
            $count_list = $request->get('count_list'.$i);
            for ($m=1; $m <= $count_list; $m++) { 
                $subject_code = $request->get($i.'subject_code'.$m);
                $subject_name = $request->get($i.'subject_name'.$m);
                $syllabus     = $request->get($i.'syllabus'.$m);
                $file         = $request->get($i.'full_syllabus'.$m);
                $already      = $request->get($i.'already'.$m);
                if($already=="No"){
                    if($subject_code!="" && $subject_name!="" && $syllabus!=""){
                        $subject = new Subject([
                            'programme_id'         =>  $id,
                            'subject_type'         =>  $type,
                            'subject_code'         =>  $subject_code,
                            'subject_name'         =>  $subject_name,
                            'syllabus'             =>  $file,
                            'syllabus_name'        =>  $syllabus,
                        ]);
                        $fake_place = "fake/syllabus/".$file;
                        rename($fake_place, 'syllabus/'.$file);
                        $subject->save();
                    }else if($syllabus=="" || $file==""){
                        $failed .= $subject_code." ".$subject_name." of syllabus cannot be empty";
                    }
                }
            }
        }
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
        if($failed==""){
            return redirect()->route('subject.create', compact('programme','subjects','group', 'id'))->with('success','Data Added');
        }else{
            return redirect()->route('subject.create', compact('programme','subjects','group', 'id'))->with('failed',$failed);
        }
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
        $subject_id        = $request->get('subject_id');
        $fake              = $request->get('fake');

        $filename = $fake;
        $subject = Subject::where('subject_id', '=', $subject_id)->firstOrFail();
        $subject->subject_code  = $request->get('subject_code');
        $subject->subject_name  = $request->get('subject_name');

        if($fake!=""){
            if($subject->syllabus!=""){
                $path = public_path().'/syllabus/'.$subject->syllabus;
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            $subject->syllabus = $filename;
            $fake_place = "fake/syllabus/".$fake;
            rename($fake_place, 'syllabus/'.$filename);
            $subject->syllabus_name = $request->get('form');
        }else{
            $subject->syllabus_name = $request->get('syllabus');
        }
        
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

    public function postUpload(Request $request){
        $image = $request->file('file');
        $imageName = $image->getClientOriginalName();
        $image->move(public_path('/fake/syllabus/'),$imageName);
        return response()->json(['success'=>$imageName]); 
    }

    public function syllabusDestory(Request $request)
    {
        $filename =  $request->get('filename');
        $path = public_path().'/fake/syllabus/'.$filename;
        if (file_exists($path)) {
            unlink($path);
        }
        return $filename;  
    }
}
