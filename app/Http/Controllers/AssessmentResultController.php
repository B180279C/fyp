<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Image;
use App\Staff;
use App\AssessmentResult;
use App\AssessmentResultStudent;
use App\Imports\syllabusRead;

class AssessmentResultController extends Controller
{
   public function viewAssessmentResult($id)
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

        $group = DB::table('assessment_results')
                 ->select('assessment_results.*')
                 ->where('course_id', '=', $id)
                 ->groupBy('assessment')
                 ->get();

        $assessment_results = DB::table('assessment_results')
                 ->select('assessment_results.*')
                 ->where('course_id', '=', $id)
                 ->get();

        if(count($course)>0){
            return view('dean.AssessmentResult.viewAssessmentResult',compact('course','assessment_results','group'));
        }else{
            return redirect()->back();
        }
   }

   public function openSubmissionForm(Request $request)
   {
        $course_id           = $request->get('course_id');
        $assessment          = $request->get('assessment');
        $submission_name     = $request->get('submission_name');

        $assessment_result = new AssessmentResult([
            'course_id'         =>  $course_id,
            'assessment'        =>  $assessment,
            'submission_name'   =>  $submission_name,
            'status'            =>  'Active',
        ]);
        $assessment_result->save();

        return redirect()->back()->with('success','New Submission Added Successfully');
   }

   public function submissionFormEdit(Request $request)
    {
        $ass_rs_id = $request->get('value');
        $result = AssessmentResult::find($ass_rs_id);
        return $result;
    }

    public function updateSubmissionForm(Request $request)
    {
        $ass_rs_id   = $request->get('ass_rs_id');
        $assessment_result = AssessmentResult::where('ass_rs_id', '=', $ass_rs_id)->firstOrFail();
        $assessment_result->assessment       = $request->get('assessment_model');
        $assessment_result->submission_name  = $request->get('submission_name');
        $assessment_result->save();
        return redirect()->back()->with('success','Edit Submission Form Successfully');
    }

    public function uploadFiles(Request $request) 
    {
        $image = $request->file('file');
        $imageName = $image->getClientOriginalName();
        $image->storeAs('fake','/assessment_result/'.$imageName, 'private');
        return response()->json(['success'=>$imageName]);  
    }

    public function destroyFiles(Request $request)
    {
        $filename =  $request->get('filename');
        Storage::disk('private')->delete('fake/assessment_result/'.$filename);
        return $filename;  
    }

    public function storeFiles(Request $request)
    {
        $ass_rs_id = $request->get('ass_rs_id');
        $assessment_result = AssessmentResult::where('ass_rs_id', '=', $ass_rs_id)->firstOrFail();
        $ass_name = $assessment_result['submission_name'];
        $count     = $request->get('count'.$ass_rs_id);
        for($i=1;$i<=$count;$i++){
            $student_id = $request->get($ass_rs_id.'form'.$i);
            $ext  = $request->get($ass_rs_id.'ext'.$i);
            $fake = $request->get($ass_rs_id.'fake'.$i);

            $count_student_document = DB::table('assessment_result_students')
                 ->select('assessment_result_students.*')
                 ->where('ass_rs_id', '=', $ass_rs_id)
                 ->where('student_id','=',$student_id)
                 ->get();
            $name = $student_id."_".$ass_name."_".(count($count_student_document)+1);

            $result = new AssessmentResultStudent([
                'ass_rs_id'              =>  $ass_rs_id,
                'student_id'             =>  $student_id,
                'submitted_by'           =>  'Lecturer',
                'document_name'          =>  $name,
                'document'               =>  $fake,
                'status'                 =>  'Active',
            ]);
            $result->save();
            $fake_place = Storage::disk('private')->get("fake/assessment_result/".$fake);
            Storage::disk('private')->put('Assessment_Result/'.$fake, $fake_place); 
            Storage::disk('private')->delete("fake/assessment_result/".$fake);
        }
        return redirect()->back()->with('success','New Result Added Successfully');
    }
}
