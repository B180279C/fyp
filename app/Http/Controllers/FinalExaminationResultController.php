<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Image;
use App\Staff;
use App\AssFinal;
use App\AssessmentFinalResult;
use App\Imports\syllabusRead;
use ZipArchive;
use File;

class FinalExaminationResultController extends Controller
{
    public function viewFinalResult($id)
    {
    	$user_id       = auth()->user()->user_id;
        $staff_dean    = Staff::where('user_id', '=', $user_id)->firstOrFail();
        $faculty_id    = $staff_dean->faculty_id;

        $course = DB::table('courses')
                 ->join('subjects', 'courses.subject_id', '=', 'subjects.subject_id')
                 ->join('semesters', 'courses.semester', '=', 'semesters.semester_id')
                 ->select('courses.*','subjects.*','semesters.*')
                 ->where('lecturer', '=', $staff_dean->id)
                 ->where('course_id', '=', $id)
                 ->get();

        $lecturer_result = DB::table('assessment_result_students')
                 ->join('students','students.student_id', '=', 'assessment_result_students.student_id')
                 ->join('users','users.user_id', '=', 'students.user_id')
                 ->select('assessment_result_students.*','students.*','users.*')
                 ->where('assessment_result_students.ass_id', '=', $ass_id)
                 ->where('assessment_result_students.submitted_by','=', 'Lecturer')
                 ->where('assessment_result_students.status','=','Active')
                 ->groupBy('assessment_result_students.student_id')
                 ->get();

        $student_result = DB::table('assessment_result_students')
                 ->join('students','students.student_id', '=', 'assessment_result_students.student_id')
                 ->join('users','users.user_id', '=', 'students.user_id')
                 ->select('assessment_result_students.*','students.*','users.*')
                 ->where('assessment_result_students.ass_id', '=', $ass_id)
                 ->where('assessment_result_students.submitted_by','=', 'Students')
                 ->where('assessment_result_students.status','=','Active')
                 ->groupBy('assessment_result_students.student_id')
                 ->get();

        if((count($course)>0)&&(count($final)>0)){
            return view('dean.FinalExamResult.viewFinalResult',compact('course','final'));
        }else{
            return redirect()->back();
        }
    }

    public function uploadFiles(Request $request) 
    {
        $image = $request->file('file');
        $imageName = $image->getClientOriginalName();
        $image->storeAs('fake','/final_result/'.$imageName, 'private');
        return response()->json(['success'=>$imageName]);  
    }

    public function destroyFiles(Request $request)
    {
        $filename =  $request->get('filename');
        Storage::disk('private')->delete('fake/final_result/'.$filename);
        return $filename;  
    }

    public function storeFiles(Request $request)
    {
        $fx_id = $request->get('fx_id');

        $final = AssFinal::where('fx_id', '=', $fx_id)->firstOrFail();

        $ass_name  = $final['assessment_name'];

        $count     = $request->get('count'.$fx_id);

        for($i=1;$i<=$count;$i++){
            $student_id = $request->get($fx_id.'form'.$i);
            $ext  = $request->get($fx_id.'ext'.$i);
            $fake = $request->get($fx_id.'fake'.$i);

            if($student_id!=""){

                $count_student_document = DB::table('assessment_final_result')
                     ->select('assessment_final_result.*')
                     ->where('fx_id', '=', $fx_id)
                     ->where('student_id','=',$student_id)
                     ->where('submitted_by','=','Lecturer')
                     ->get();

                $name = $student_id."_".$ass_name."_".(count($count_student_document)+1);

                $result = new AssessmentFinalResult([
                    'fx_id'                  =>  $fx_id,
                    'student_id'             =>  $student_id,
                    'submitted_by'           =>  'Lecturer',
                    'document_name'          =>  $name,
                    'document'               =>  $fake,
                    'status'                 =>  'Active',
                ]);

                $result->save();
                $fake_place = Storage::disk('private')->get("fake/final_result/".$fake);
                Storage::disk('private')->put('Final_Assessment_Result/'.$fake, $fake_place); 
                Storage::disk('private')->delete("fake/final_result/".$fake);
            }
        }
        return redirect()->back()->with('success','New Result Added Successfully');
    }
}
