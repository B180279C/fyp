<?php
$character = "/hod";
Route::get($character.'/home', 'HomeController@hodHome')->name('hod.home');

Route::get($character.'/images/home_image/{user_id}', [
	     'as'         => 'home_image',
	     'uses'       => 'HomeController@hodDetails',
	     'middleware' => 'auth',
]);

Route::post($character.'/notification/getNum', 'Dean\NotificationController@getNum');

Route::get($character.'/profile/', 'Dean\ProfileController@profile')->name('hod.Profile');

Route::get($character.'/images/profile/{image_name}', [
	'as'         => 'profile_image',
	'uses'       => 'Dean\ProfileController@profileImage',
	'middleware' => 'auth',
]);
Route::get($character.'/sign/profile/{image_name}', [
	'as'         => 'profile_sign',
	'uses'       => 'Dean\ProfileController@profileSign',
	'middleware' => 'auth',
]);

Route::get($character.'/profile/CV/{id}','Dean\ProfileController@ProfileDownloadCV')->name('hod.downloadCV');
Route::post($character.'/staffUploadImage', 'Dean\ProfileController@uploadImages')->name('hod.dropzone.uploadStaffImage');
Route::post($character.'/staffDestoryImage', 'Dean\ProfileController@destroyImage')->name('hod.dropzone.destoryStaffImage');
Route::post($character.'/staffUploadCV', 'Dean\ProfileController@uploadCV')->name('hod.dropzone.uploadStaffCV');
Route::post($character.'/staffDestoryCV', 'Dean\ProfileController@destroyCV')->name('hod.dropzone.destoryStaffCV');
Route::post($character.'/staffUploadSign', 'Dean\ProfileController@uploadSign')->name('hod.dropzone.uploadStaffSign');
Route::post($character.'/staffDestorySign', 'Dean\ProfileController@destroySign')->name('hod.dropzone.destoryStaffSign');
Route::post($character.'/profile/store', 'Dean\ProfileController@store')->name('hod.staff.submit');



//Course List
Route::get($character.'/CourseList', 'Dean\C_PortFolioController@index')->name('dean.C_potrfolio.index');
Route::post($character.'/searchCPCourse', 'Dean\C_PortFolioController@searchCourse');
Route::get($character.'/CourseList/action/{id}','Dean\C_PortFolioController@CourseListAction');


//All Course Action
//Student list
Route::get($character.'/CourseList/assign/student/{id}','Dean\Course\C_StudentListController@StudentList');
// Route::post('/searchCourseListStudent', 'Dean\Course\C_StudentListController@searchStudent');
//Lecture Note
Route::get($character.'/CourseList/lectureNote/{id}','Dean\Course\C_LectureNoteController@LectureNote');
Route::post($character.'/CourseList/lectureNote/searchFiles', 'Dean\Course\C_LectureNoteController@searchLN');
Route::get($character.'/CourseList/lectureNote/folder/{ln_id}','Dean\Course\C_LectureNoteController@LNFolderView');
//Teaching Plan
Route::get($character.'/CourseList/teachingPlan/{id}','Dean\Course\C_TeachingPlanController@TeachingPlan');
// Route::post('/CourseList/teachingPlan/approve/','Dean\Dean\D_TeachingPlanController@D_TP_VerifyAction');

//Assessment
Route::get($character.'/CourseList/assessment/{id}','Dean\Course\C_AssessmentController@Assessment');
Route::post($character.'/CourseList/assessment/getSyllabusData', 'Dean\Course\C_AssessmentController@getSyllabusData');
Route::get($character.'/CourseList/assessment/create/{id}/question/{coursework}/{question}', [
    'as' => 'hod.C_createQuestion', 'uses' => 'Dean\Course\C_AssessmentController@create_question']);
Route::get($character.'/CourseList/assessment/view_list/{ass_id}', 'Dean\Course\C_AssessmentController@assessment_list_view');
Route::get($character.'/CourseList/assessment/view/whole_paper/{ass_id}', 'Dean\Course\C_AssessmentController@view_wholePaper');
Route::get($character.'/CourseList/images/assessment/{image_name}', [
	'as'         => 'hod.C_assessment_image',
	'uses'       => 'Dean\Course\C_AssessmentController@assessmentImage',
	'middleware' => 'auth',
]);
Route::post($character.'/CourseList/assessment/searchKey/', 'Dean\Course\C_AssessmentController@searchKey');
Route::post($character.'/CourseList/assessment/searchAssessmentList/', 'Dean\Course\C_AssessmentController@searchAssessmentList');
Route::get($character.'/CourseList/assessment/download/{ass_li_id}', 'Dean\Course\C_AssessmentController@downloadFiles');

//Assessment Result
Route::get($character.'/CourseList/AssessmentResult/{id}/question/{question}', [
'as' => 'hod.C_viewAssessmentStudentResult', 'uses' => 'Dean\Course\C_AssessmentResultController@viewAssessmentStudentResult']
);
Route::get($character.'/CourseList/AssessmentResult/studentResult/{ass_id}/', [
'as' => 'hod.C_viewstudentlist', 'uses' => 'Dean\Course\C_AssessmentResultController@viewstudentlist']);
Route::get($character.'/CourseList/AssessmentResult/view/student/{ar_stu_id}/', [
'as' => 'hod.C_viewStudentResult', 'uses' => 'Dean\Course\C_AssessmentResultController@viewStudentResult']);
Route::post($character.'/CourseList/AssessmentResult/searchAssessmentForm/', 'Dean\Course\C_AssessmentResultController@searchAssessmentForm');
Route::post($character.'/CourseList/AssessmentResult/searchStudentList/', 'Dean\Course\C_AssessmentResultController@searchStudentList');
Route::get($character.'/CourseList/images/AssessmentResult/{image_name}', [
	'as'         => 'hod.C_assessmentResult_image',
	'uses'       => 'Dean\Course\C_AssessmentResultController@assessmentResult_image',
    'middleware' => 'auth',
]);
Route::get($character.'/CourseList/AssessmentResult/view/whole_paper/{ar_stu_id}', 'Dean\Course\C_AssessmentResultController@view_wholePaper');
Route::get($character.'/CourseList/AssessmentResult/result/{ar_stu_id}','Dean\Course\C_AssessmentResultController@downloadDocument');
	
//Final Assessment
Route::get($character.'/CourseList/FinalExamination/{id}/', [
    'as' => 'hod.C_FinalExamination', 'uses' => 'Dean\Course\C_FinalExamController@viewFinalExamination']);
Route::post($character.'/CourseList/FinalExamination/getSyllabusData', 'Dean\Course\C_FinalExamController@getSyllabusData');
Route::get($character.'/CourseList/FinalExamination/question/{coursework}/{id}/', [
    'as' => 'hod.C_createQuestion', 'uses' => 'Dean\Course\C_FinalExamController@create_question']);
Route::get($character.'/CourseList/FinalExamination/view_list/{fx_id}', 'Dean\Course\C_FinalExamController@final_assessment_list_view');
Route::get($character.'/CourseList/images/final_assessment/{image_name}', [
	     'as'         => 'hod.C_assessment_final_image',
	     'uses'       => 'Dean\Course\C_FinalExamController@FinalAssessmentImage',
	     'middleware' => 'auth',
]);
Route::post($character.'/CourseList/FinalExamination/searchAssessmentList/', 'Dean\Course\C_FinalExamController@searchAssessmentList');
Route::post($character.'/CourseList/FinalExamination/searchKey/', 'Dean\Course\C_FinalExamController@searchKey');
Route::get($character.'/CourseList/final_assessment/view/whole_paper/{fx_id}', 'Dean\Course\C_FinalExamController@view_wholePaper');
Route::get($character.'/CourseList/FinalExamination/download/{ass_fx_id}', 'Dean\Course\C_FinalExamController@downloadFiles');
	
//Final Assessment
Route::get($character.'/CourseList/FinalResult/{id}', [
    'as' => 'hod.C_viewFinalResult', 'uses' => 'Dean\Course\C_FinalExamResultController@viewFinalResult']);
Route::get($character.'/CourseList/FinalResult/view/student/{fxr_id}/', [
    'as' => 'hod.C_viewFinalStudentResult', 'uses' => 'Dean\Course\C_FinalExamResultController@viewFinalStudentResult']);
Route::get($character.'/CourseList/FinalResult/result/{fxr_id}','Dean\Course\C_FinalExamResultController@downloadDocument');
Route::get($character.'/CourseList/images/FinalResult/{image_name}', [
	     'as'         => 'hod.C_FinalResult_image',
	     'uses'       => 'Dean\Course\C_FinalExamResultController@FinalResult_image',
	     'middleware' => 'auth',
]);
Route::get($character.'/CourseList/FinalResult/view/whole_paper/{fxr_id}', 'Dean\Course\C_FinalExamResultController@view_wholePaper');
Route::post($character.'/CourseList/FinalResult/searchStudentList/', 'Dean\Course\C_FinalExamResultController@searchStudentList');

//Course List E_Portfolio
Route::get($character.'/CourseList/E_Portfolio/{id}', [
    'as' => 'hod.C_viewE_Portfolio', 'uses' => 'Dean\Course\E_PortfolioController@viewE_Portfolio']);

//Course List Timetable
Route::get($character.'/CourseList/timetable/{id}', [
    'as' => 'hod.viewTimetable', 'uses' => 'Dean\Course\C_TimetableController@viewTimetable']);

//Course List Attendance
Route::get($character.'/CourseList/Attendance/{id}','Dean\Course\C_AttendanceController@viewAttendance');
Route::get($character.'/CourseList/Attendance/{id}/student_list/{date}', 'Dean\Course\C_AttendanceController@viewStudentList');


//Course List Past Year CA Question
Route::get($character.'/CourseList/PastYear/assessment/{id}','Dean\Course\C_PastYearController@PastYearAssessment');
Route::get($character.'/CourseList/PastYear/assessment/{id}/assessment_name/{course_id}','Dean\Course\C_PastYearController@PastYearAssessmentName');
Route::get($character.'/CourseList/PastYear/assessment/{id}/list/{ass_id}/','Dean\Course\C_PastYearController@PastYearAssessmentList');
Route::post($character.'/CourseList/PastYear/assessment/searchAssessment/', 'Dean\Course\C_PastYearController@searchAssessment');
Route::post($character.'/CourseList/PastYear/assessment/name/searchAssessmentName/', 'Dean\Course\C_PastYearController@searchAssessmentName');
Route::post($character.'/CourseList/PastYear/assessment/list/searchAssessmentlist/', 'Dean\Course\C_PastYearController@searchAssessmentlist');
Route::get($character.'/CourseList/PastYear/assessment/view/whole_paper/{ass_id}', 'Dean\Course\C_PastYearController@view_wholePaper');
Route::get($character.'/CourseList/PastYear/images/assessment/{image_name}', [
	'as'         => 'C_assessment_image',
	'uses'       => 'Dean\Course\C_PastYearController@assessmentImage',
	'middleware' => 'auth',
]);
Route::get($character.'/CourseList/PastYear/assessment/download/{ass_li_id}', 
		'Dean\Course\C_PastYearController@downloadFiles');

//Course List Past year Final question
Route::get($character.'/CourseList/PastYear/FinalAssessment/{id}',
	'Dean\Course\C_PastYearFinalController@PastYearAssessment');
Route::get($character.'/CourseList/PastYear/FinalAssessment/{id}/assessment_name/{course_id}','Dean\Course\C_PastYearFinalController@PastYearAssessmentName');
Route::get($character.'/CourseList/PastYear/FinalAssessment/{id}/list/{fx_id}/','Dean\Course\C_PastYearFinalController@PastYearAssessmentList');
Route::post($character.'/CourseList/PastYear/FinalAssessment/searchAssessment/', 'Dean\Course\C_PastYearFinalController@searchAssessment');
Route::post($character.'/CourseList/PastYear/FinalAssessment/name/searchAssessmentName/', 'Dean\Course\C_PastYearFinalController@searchAssessmentName');
Route::post($character.'/CourseList/PastYear/FinalAssessment/list/searchAssessmentlist/', 'Dean\Course\C_PastYearFinalController@searchAssessmentlist');
Route::get($character.'/CourseList/PastYear/FinalAssessment/download/{ass_fx_id}', 'Dean\Course\C_PastYearFinalController@downloadFiles');
Route::get($character.'/CourseList/PastYear/images/final_assessment/{image_name}', [
	'as'         => 'C_assessment_final_image',
	'uses'       => 'Dean\Course\C_PastYearFinalController@FinalAssessmentImage',
	'middleware' => 'auth',
]);
Route::get($character.'/CourseList/PastYear/final_assessment/view/whole_paper/{fx_id}', 'Dean\Course\C_PastYearFinalController@view_wholePaper');


//Course List Past year CA Result
Route::get($character.'/CourseList/PastYear/sampleResult/{id}/previous/{course_id}/{search}','Dean\Course\C_PastYearController@PastYearResultAssessmentList');
Route::get($character.'/CourseList/PastYear/sampleResult/{id}/name/{ass_id}/{search}','Dean\Course\C_PastYearController@PastYearStudentList');
Route::get($character.'/CourseList/PastYear/sampleResult/{id}/result/{ar_stu_id}','Dean\Course\C_PastYearController@PastYearResultList');
Route::post($character.'/CourseList/PastYear/assessment/sampleResult/searchSampleResult/', 'Dean\Course\C_PastYearController@searchAssessmentSampleResult');
Route::post($character.'/CourseList/PastYear/result/searchAssessmentResult/', 'Dean\Course\C_PastYearController@searchAssessmentResult');
Route::post($character.'/CourseList/PastYear/assessment/sampleResult/searchStudentList/', 'Dean\Course\C_PastYearController@searchStudentList');
Route::get($character.'/CourseList/PastYear/images/AssessmentResult/{image_name}', [
	'as'         => 'M_assessmentResult_image',
	'uses'       => 'Dean\Course\C_PastYearController@assessmentResult_image',
	'middleware' => 'auth',
]);
Route::get($character.'/CourseList/PastYear/AssessmentResult/view/whole_paper/{ar_stu_id}', 'Dean\Course\C_PastYearController@view_wholePaperResult');
Route::get($character.'/CourseList/PastYear/AssessmentResult/result/{ar_stu_id}','Dean\Course\C_PastYearController@downloadDocument');

//Course List Past Year FInal Result
Route::get($character.'/CourseList/PastYear/FinalSampleResult/{id}/previous/{course_id}/{search}','Dean\Course\C_PastYearFinalController@PastYearStudentList');
Route::get($character.'/CourseList/PastYear/FinalSampleResult/{id}/result/{fxr_id}','Dean\Course\C_PastYearFinalController@PastYearResultList');
Route::post($character.'/CourseList/PastYear/FinalSampleResult/searchAssessmentResult/', 'Dean\Course\C_PastYearFinalController@searchAssessmentResult');
Route::post($character.'/CourseList/PastYear/FinalSampleResult/searchStudentList/', 'Dean\Course\C_PastYearFinalController@searchStudentList');
Route::get($character.'/CourseList/PastYear/images/FinalResult/{image_name}', [
	'as'         => 'C_FinalResult_image',
	'uses'       => 'Dean\Course\C_PastYearFinalController@FinalResult_image',
	'middleware' => 'auth',
]);
Route::get($character.'/CourseList/PastYear/FinalResult/view/whole_paper/{fxr_id}', 'Dean\Course\C_PastYearFinalController@view_wholePaperResult');
Route::get($character.'/CourseList/PastYear/FinalSampleResult/download/{fxr_id}', 'Dean\Course\C_PastYearFinalController@downloadFilesResult');


//Course List Past Year Lecturer Note
Route::get($character.'/CourseList/PastYearNote/{id}','Dean\Course\C_PastYearNoteController@PastYearNote');
Route::get($character.'/CourseList/PastYearNote/{id}/{view}/{view_id}','Dean\Course\C_PastYearNoteController@PastYearNoteViewIn');
Route::post($character.'/CourseList/PastYear/lectureNote/searchFiles', 'Dean\Course\C_PastYearNoteController@searchLecturerNote');
Route::post($character.'/CourseList/PastYear/lectureNote/searchPreviousFiles', 'Dean\Course\C_PastYearNoteController@searchLecturerNotePrevious');
Route::get($character.'/CourseList/PastYear/images/lectureNote/{ln_id}/{image_name}', [
	'as'         => 'M_lectureNote_image',
	'uses'       => 'Dean\Course\C_PastYearNoteController@LectureNoteImage',
	'middleware' => 'auth',
]);
Route::get($character.'/CourseList/PastYear/lectureNote/download/{id}','Dean\Course\C_PastYearNoteController@downloadLN');

//Course List Past Year TP
Route::get($character.'/CourseList/PastYearTP/{id}','Dean\Course\C_PastYearTPController@PastYearTP');
Route::get($character.'/CourseList/PastYearTP/{id}/course/{view_id}','Dean\Course\C_PastYearTPController@PastYearTPDownload');
Route::post($character.'/CourseList/PastYearTP/searchFiles', 'Dean\Course\C_PastYearTPController@searchPastYearTP');


//My Course
Route::get($character.'/course_list','Dean\CourseController@index');
Route::post($character.'/searchTeachCourse', 'Dean\CourseController@searchTeachCourse');
Route::get($character.'/course/action/{id}','Dean\CourseController@courseAction');

//Assign Student
Route::get($character.'/assign/student/{id}','Dean\AssignStudentController@viewAssignStudent');
Route::post($character.'/searchAssignStudent', 'Dean\AssignStudentController@searchAssignStudent');
Route::post($character.'/showStudent','Dean\AssignStudentController@showStudent');
Route::post($character.'/storeStudent', 'Dean\AssignStudentController@storeStudent');
Route::post($character.'/uploadAssignStudent', 'Dean\AssignStudentController@importExcelStudent');
Route::post($character.'/assignStudent/excel/create', 'Dean\AssignStudentController@storeAssignStudent');
Route::get($character.'/assignStudent/remove/{id}','Dean\AssignStudentController@removeActiveStudent');

//Note
Route::get($character.'/lectureNote/{id}','Dean\LectureNoteController@viewLectureNote');
Route::post($character.'/lectureNote/searchFiles', 'Dean\LectureNoteController@searchFiles');
Route::get($character.'/lectureNote/folder/{folder_id}', 'Dean\LectureNoteController@folder_view');
Route::post($character.'/lectureNote/openNewFolder', 'Dean\LectureNoteController@openNewFolder');
Route::post($character.'/lectureNote/folderNameEdit', 'Dean\LectureNoteController@folderNameEdit');
Route::post($character.'/lectureNote/SelectPreviousSemester', 'Dean\LectureNoteController@SelectPreviousSemester');
Route::post($character.'/lectureNote/SelectFolderSemester', 'Dean\LectureNoteController@SelectFolderSemester');
Route::post($character.'/lectureNote/SelectFolderPlace', 'Dean\LectureNoteController@SelectFolderPlace');
Route::post($character.'/lectureNote/SelectFolder', 'Dean\LectureNoteController@SelectFolder');
Route::post($character.'/lectureNote/GetUsedSemester', 'Dean\LectureNoteController@GetUsedSemester');
Route::post($character.'/lectureNote/updateFolderName', 'Dean\LectureNoteController@updateFolderName');
Route::get($character.'/lectureNote/remove/{id}', 'Dean\LectureNoteController@removeActive');
Route::get($character.'/images/lectureNote/{ln_id}/{image_name}', [
	'as'         => 'hod.lectureNote_image',
	'uses'       => 'Dean\LectureNoteController@LectureNoteImage',
	'middleware' => 'auth',
]);
Route::post($character.'/note_uploadFiles', 'Dean\LectureNoteController@uploadFiles');
Route::post($character.'/note_destoryFiles', 'Dean\LectureNoteController@destroyFiles');
Route::post($character.'/note_storeFiles', 'Dean\LectureNoteController@storeFiles');
Route::post($character.'/note_storePreviousFiles', 'Dean\LectureNoteController@storePreviousFiles');
Route::get($character.'/lectureNote/download/{id}','Dean\LectureNoteController@downloadLN');
Route::get($character.'/lectureNote/download/zipFiles/{course_id}/{download}','Dean\LectureNoteController@zipFileDownload');

//TP
Route::get($character.'/teachingPlan/{id}','Dean\TeachingPlanController@viewTeachingPlan');
Route::get($character.'/teachingPlan/create/weekly/{id}','Dean\TeachingPlanController@createTeachingPlan');
Route::post($character.'/teachingPlan/create/weekly/{id}', 'Dean\TeachingPlanController@storeTP');
Route::get($character.'/teachingPlan/create/previous/weekly/{id}','Dean\TeachingPlanController@createPreviousTP');
Route::post($character.'/removeTopic', 'Dean\TeachingPlanController@removeTopic');
Route::post($character.'/teachingPlan/searchPlan', 'Dean\TeachingPlanController@searchPlan');
Route::post($character.'/teachingPlan/getSyllabusData', 'Dean\TeachingPlanController@getSyllabusData');
Route::get($character.'/teachingPlan/create/assessment/{id}','Dean\TeachingPlanController@createTPAss');
Route::get($character.'/teachingPlan/create/new/assessment/{id}','Dean\TeachingPlanController@createNewTPAss');
Route::get($character.'/teachingPlan/create/previous/assessment/{id}','Dean\TeachingPlanController@createPreviousTPAss');
Route::post($character.'/teachingPlan/create/assessment/{id}', 'Dean\TeachingPlanController@storeTPAss');
Route::get($character.'/teachingPlan/create/CQI/{id}','Dean\TeachingPlanController@createTPCQI');
Route::post($character.'/teachingPlan/store/CQI/', 'Dean\TeachingPlanController@storeTPCQI');
Route::post($character.'/teachingPlan/CQI/Edit/', 'Dean\TeachingPlanController@CQIEdit');
Route::post($character.'/teachingPlan/CQIUpdate/', 'Dean\TeachingPlanController@CQIUpdate');
Route::get($character.'/teachingPlan/CQIRemove/{id}', 'Dean\TeachingPlanController@removeActive');
Route::get($character.'/teachingPlan/report/{id}', 'Dean\TeachingPlanController@TPDownload');
Route::get($character.'/teachingPlan/Action/Submit/{id}', 'Dean\TeachingPlanController@TPSubmitAction');

//Assessment
Route::get($character.'/assessment/{id}','Dean\AssessmentController@viewAssessment');
Route::post($character.'/assessment/getSyllabusData', 'Dean\AssessmentController@getSyllabusData');
Route::get($character.'/assessment/create/{id}/question/{coursework}/{question}', [
    'as' => 'hod.createQuestion', 'uses' => 'Dean\AssessmentController@create_question']);
Route::post($character.'/assessment/openNewAssessment', 'Dean\AssessmentController@openNewAssessment');
Route::post($character.'/assessment/AssessmentNameEdit', 'Dean\AssessmentController@AssessmentNameEdit');
Route::post($character.'/assessment/updateAssessmentName', 'Dean\AssessmentController@updateAssessmentName');
Route::get($character.'/assessment/view_list/{ass_id}', 'Dean\AssessmentController@assessment_list_view');
Route::get($character.'/assessment/remove/{id}', 'Dean\AssessmentController@removeActive');
Route::get($character.'/assessment/remove/list/{id}', 'Dean\AssessmentController@removeActiveList');
Route::post($character.'/ass_uploadFiles', 'Dean\AssessmentController@uploadFiles');
Route::post($character.'/ass_destoryFiles', 'Dean\AssessmentController@destroyFiles');
Route::post($character.'/ass_storeFiles', 'Dean\AssessmentController@storeFiles');
Route::get($character.'/images/assessment/{image_name}', [
	     'as'         => 'hod.assessment_image',
	     'uses'       => 'Dean\AssessmentController@assessmentImage',
	     'middleware' => 'auth',
	]);
Route::get($character.'/assessment/view/whole_paper/{ass_id}', 'Dean\AssessmentController@view_wholePaper');
Route::get($character.'/assessment/download/{ass_li_id}', 'Dean\AssessmentController@downloadFiles');
Route::post($character.'/assessment/searchKey/', 'Dean\AssessmentController@searchKey')->name('hod.searchKey');
Route::post($character.'/assessment/searchAssessmentList/', 'Dean\AssessmentController@searchAssessmentList')->name('hod.searchAssessmentList');
Route::get($character.'/assessment/AllZipFiles/{id}/{download}','Dean\AssessmentController@AllZipFileDownload');
Route::get($character.'/assessment/download/zipFiles/{ass_id}/{download}','Dean\AssessmentController@zipFileDownload');
Route::get($character.'/assessment/Action/Submit/{id}', 'Dean\AssessmentController@AssessmentSubmitAction');
Route::post($character.'/assessment/Action/HOD/', 'Dean\AssessmentController@SubmitSelf_D_Form')->name('hod.CA.submit_for_verify');
Route::get($character.'/Assessment/report/{actionCA_id}','Dean\AssessmentController@ModerationFormReport');
Route::get($character.'/assessment/create/previous/{id}/{question}','Dean\AssessmentController@createPreviousAss');


    // Continuous Assessment Student Result
Route::get($character.'/AssessmentResult/{id}/question/{question}', [
    'as' => 'hod.viewAssessmentStudentResult', 'uses' => 'Dean\AssessmentResultController@viewAssessmentStudentResult']);
Route::post($character.'/ass_rs_uploadFiles', 'Dean\AssessmentResultController@uploadFiles');
Route::post($character.'/ass_rs_destoryFiles', 'Dean\AssessmentResultController@destroyFiles');
Route::post($character.'/ass_rs_storeFiles', 'Dean\AssessmentResultController@storeFiles');
Route::get($character.'/AssessmentResult/studentResult/{ass_id}/', [
    'as' => 'hod.viewstudentlist', 'uses' => 'Dean\AssessmentResultController@viewstudentlist']);
Route::get($character.'/AssessmentResult/view/student/{ar_stu_id}/', [
    'as' => 'hod.viewStudentResult', 'uses' => 'Dean\AssessmentResultController@viewStudentResult']);
Route::get($character.'/AssessmentResult/result/{ar_stu_id}','Dean\AssessmentResultController@downloadDocument');
Route::post($character.'/AssessmentResult/searchAssessmentForm/', 'Dean\AssessmentResultController@searchAssessmentForm')->name('hod.searchAssessmentForm');
Route::post($character.'/AssessmentResult/searchStudentList/', 'Dean\AssessmentResultController@searchStudentList')->name('hod.searchStudentList');
Route::get($character.'/images/AssessmentResult/{image_name}', [
	'as'         => 'hod.assessmentResult_image',
	'uses'       => 'Dean\AssessmentResultController@assessmentResult_image',
	'middleware' => 'auth',
]);
Route::get($character.'/AssessmentResult/view/whole_paper/{ar_stu_id}', 'Dean\AssessmentResultController@view_wholePaper');
Route::get($character.'/AssessmentResult/remove/{id}', 'Dean\AssessmentResultController@removeActive');
Route::get($character.'/AssessmentResultStudent/remove/{ar_stu_id}', 'Dean\AssessmentResultController@removeStudentActive');
Route::get($character.'/AssessmentResult/AllZipFiles/{id}/{download}','Dean\AssessmentResultController@AllZipFileDownload');
Route::get($character.'/AssessmentResult/download/zipFiles/{ass_id}/{download}','Dean\AssessmentResultController@zipFileDownload');
Route::get($character.'/AssessmentResult/Student/{student_id}/download/zipFiles/{ass_id}/{download}', [
    'as' => 'hod.zipFileDownloadStudent', 'uses' => 'Dean\AssessmentResultController@zipFileDownloadStudent']);

// FinalExamination
Route::get($character.'/FinalExamination/{id}/', [
    'as' => 'hod.viewFinalExamination', 'uses' => 'Dean\FinalExaminationController@viewFinalExamination']);
Route::post($character.'/FinalExamination/getSyllabusData', 'Dean\FinalExaminationController@getSyllabusData');
Route::get($character.'/FinalExamination/question/{coursework}/{id}/', [
    'as' => 'hod.createQuestion', 'uses' => 'Dean\FinalExaminationController@create_question']);
Route::post($character.'/FinalExamination/openNewAssessment', 'Dean\FinalExaminationController@openNewAssessment');
Route::post($character.'/FinalExamination/AssessmentNameEdit', 'Dean\FinalExaminationController@AssessmentNameEdit');
Route::post($character.'/FinalExamination/updateAssessmentName', 'Dean\FinalExaminationController@updateAssessmentName');
Route::get($character.'/FinalExamination/view_list/{fx_id}', 'Dean\FinalExaminationController@final_assessment_list_view');
Route::get($character.'/FinalExamination/remove/{id}', 'Dean\FinalExaminationController@removeActive');
Route::get($character.'/FinalExamination/remove/list/{id}', 'Dean\FinalExaminationController@removeActiveList');
Route::post($character.'/FinalExamination/uploadFiles', 'Dean\FinalExaminationController@uploadFiles');
Route::post($character.'/FinalExamination/destoryFiles', 'Dean\FinalExaminationController@destroyFiles');
Route::post($character.'/FinalExamination/storeFiles', 'Dean\FinalExaminationController@storeFiles');
Route::get($character.'/images/final_assessment/{image_name}', [
	     'as'         => 'hod.assessment_final_image',
	     'uses'       => 'Dean\FinalExaminationController@FinalAssessmentImage',
	     'middleware' => 'auth',
	]);
Route::post($character.'/FinalExamination/searchAssessmentList/', 'Dean\FinalExaminationController@searchAssessmentList')->name('hod.final.searchAssessmentList');
Route::post($character.'/FinalExamination/searchKey/', 'Dean\FinalExaminationController@searchKey')->name('hod.final.searchKey');
Route::get($character.'/final_assessment/view/whole_paper/{fx_id}', 'Dean\FinalExaminationController@view_wholePaper');
Route::get($character.'/FinalExamination/download/{ass_fx_id}', 'Dean\FinalExaminationController@downloadFiles');
Route::get($character.'/FinalExamination/AllZipFiles/{id}/{download}','Dean\FinalExaminationController@AllZipFileDownload');
Route::get($character.'/FinalExamination/download/zipFiles/{fx_id}/{download}','Dean\FinalExaminationController@zipFileDownload');
Route::get($character.'/FinalExamination/Action/Submit/{id}','Dean\FinalExaminationController@FASubmitAction');
Route::post($character.'/FinalExamination/Action/HOD/', 'Dean\FinalExaminationController@SubmitSelf_D_Form')->name('hod.FA.submit_for_verify');
Route::get($character.'/FinalExamination/report/{actionFA_id}','Dean\FinalExaminationController@ModerationFormReport');
Route::get($character.'/FinalExamination/create/previous/{id}/','Dean\FinalExaminationController@createPreviousAss');

// Final Examination Result
Route::get($character.'/FinalResult/{id}', [
    'as' => 'hod.viewFinalResult', 'uses' => 'Dean\FinalExaminationResultController@viewFinalResult']);
Route::post($character.'/final_rs_uploadFiles', 'Dean\FinalExaminationResultController@uploadFiles');
Route::post($character.'/final_rs_destoryFiles', 'Dean\FinalExaminationResultController@destroyFiles');
Route::post($character.'/final_rs_storeFiles', 'Dean\FinalExaminationResultController@storeFiles');
Route::get($character.'/FinalResult/view/student/{fxr_id}/', [
    'as' => 'hod.viewFinalStudentResult', 'uses' => 'Dean\FinalExaminationResultController@viewFinalStudentResult']);
Route::get($character.'/FinalResult/result/{fxr_id}','Dean\FinalExaminationResultController@downloadDocument');
Route::get($character.'/images/FinalResult/{image_name}', [
	'as'         => 'hod.FinalResult_image',
	'uses'       => 'Dean\FinalExaminationResultController@FinalResult_image',
	'middleware' => 'auth',
]);
Route::get($character.'/FinalResult/view/whole_paper/{fxr_id}', 'Dean\FinalExaminationResultController@view_wholePaper');
Route::get($character.'/FinalResult/remove/{fxr_id}', 'Dean\FinalExaminationResultController@removeStudentActive');
Route::post($character.'/FinalResult/searchStudentList/', 'Dean\FinalExaminationResultController@searchStudentList')->name('hod.final.searchStudentList');
Route::get($character.'/FinalResult/download/zipFiles/{course_id}/{download}','Dean\FinalExaminationResultController@zipFileDownload');
Route::get($character.'/FinalResult/Student/{student_id}/download/zipFiles/{course_id}/{download}', [
    'as' => 'hod.zipFileDownloadFinalResult', 'uses' => 'Dean\FinalExaminationResultController@zipFileDownloadStudent']);

//E_Portfolio
Route::get($character.'/E_Portfolio/{id}', [
    'as' => 'hod.viewE_Portfolio', 'uses' => 'Dean\E_PortfolioController@viewE_Portfolio']);
Route::get($character.'/E_Portfolio/report/{id}', [
    'as' => 'hod.Download_E_Portfolio', 'uses' => 'Dean\E_PortfolioController@Download_E_Portfolio']);
Route::get($character.'/E_Portfolio/course/List/', [
    'as' => 'hod.E_Portfolio_List', 'uses' => 'Dean\E_PortfolioController@E_Portfolio_List']);
Route::post($character.'/E_Portfolio/searchCourse/', 'Dean\E_PortfolioController@searchCourse');
Route::get($character.'/E_Portfolio/download/zipFiles/{course_id}/{checked}','Dean\E_PortfolioController@downloadZipFiles');
Route::get($character.'/E_Portfolio/list/{id}', [
    'as' => 'hod.viewListE_Portfolio', 'uses' => 'Dean\E_PortfolioController@viewListE_Portfolio']);

//Timetable
Route::get($character.'/Timetable/{id}', [
    'as' => 'hod.viewTimetable', 'uses' => 'Dean\TimetableController@viewTimetable']);

//Attendance
Route::get($character.'/Attendance/{id}', 'Dean\AttendanceController@viewAttendance');
Route::get($character.'/Attendance/{id}/student_list/{date}', 'Dean\AttendanceController@viewStudentList');
Route::post($character.'/Attendance/store/', 'Dean\AttendanceController@storeAttendance')->name('hod.storeAttendance');
Route::post($character.'/Attendance/edit/', 'Dean\AttendanceController@editAttendance')->name('hod.editAttendance');
Route::post($character.'/Attendance/openQR_Code/', 'Dean\AttendanceController@openQR_Code');
Route::get($character.'/Attendance/QR_code/{attendance_id}/{code}', 'Dean\AttendanceController@QR_Code');
Route::get($character.'/Attendance/excel/download/{id}','Dean\AttendanceController@downloadExcel');


//Past Year CA Question
Route::get($character.'/PastYear/assessment/{id}','Dean\PastYearController@PastYearAssessment')->name('dean.pastYear');
Route::get($character.'/PastYear/assessment/{id}/assessment_name/{course_id}','Dean\PastYearController@PastYearAssessmentName');
Route::get($character.'/PastYear/assessment/{id}/list/{ass_id}/','Dean\PastYearController@PastYearAssessmentList');
Route::get($character.'/PastYear/assessment/download/zipFiles/{course_id}/{download}','Dean\PastYearController@zipFileDownload');
Route::get($character.'/PastYear/assessment/name/download/zipFiles/{course_id}/{download}','Dean\PastYearController@zipFileDownloadName');
Route::get($character.'/PastYear/assessment/list/download/zipFiles/{ass_id}/{download}','Dean\PastYearController@zipFileDownloadList');
Route::post($character.'/PastYear/assessment/searchAssessment/', 'Dean\PastYearController@searchAssessment')->name('hod.PY.searchAssessment');
Route::post($character.'/PastYear/assessment/name/searchAssessmentName/', 'Dean\PastYearController@searchAssessmentName')->name('hod.PY.searchAssessmentName');
Route::post($character.'/PastYear/assessment/list/searchAssessmentlist/', 'Dean\PastYearController@searchAssessmentlist')->name('hod.PY.searchAssessmentlist');
Route::get($character.'/PastYear/assessment/download/{ass_li_id}', 'Dean\PastYearController@downloadFiles');
Route::get($character.'/PastYear/assessment/view/whole_paper/{ass_id}', 'Dean\PastYearController@view_wholePaper');
Route::get($character.'/PastYear/images/assessment/{image_name}', [
     'as'         => 'M_assessment_image',
     'uses'       => 'Dean\PastYearController@assessmentImage',
     'middleware' => 'auth',
]);


//Past year Final question
Route::get($character.'/PastYear/FinalAssessment/{id}','Dean\PastYearFinalController@PastYearAssessment');
Route::get($character.'/PastYear/FinalAssessment/{id}/assessment_name/{course_id}','Dean\PastYearFinalController@PastYearAssessmentName');
Route::get($character.'/PastYear/FinalAssessment/{id}/list/{fx_id}/','Dean\PastYearFinalController@PastYearAssessmentList')->name('dean.pastYearASSList');
Route::get($character.'/PastYear/FinalAssessment/download/zipFiles/{course_id}/{download}','Dean\PastYearFinalController@zipFileDownload');
Route::get($character.'/PastYear/FinalAssessment/name/download/zipFiles/{course_id}/{download}','Dean\PastYearFinalController@zipFileDownloadName');
Route::get($character.'/PastYear/FinalAssessment/list/download/zipFiles/{fx_id}/{download}','Dean\PastYearFinalController@zipFileDownloadList');
Route::post($character.'/PastYear/FinalAssessment/searchAssessment/', 'Dean\PastYearFinalController@searchAssessment')->name('hod.PY.final.searchAssessment');
Route::post($character.'/PastYear/FinalAssessment/name/searchAssessmentName/', 'Dean\PastYearFinalController@searchAssessmentName')->name('hod.PY.final.searchAssessmentName');
Route::post($character.'/PastYear/FinalAssessment/list/searchAssessmentlist/', 'Dean\PastYearFinalController@searchAssessmentlist')->name('hod.PY.final.searchAssessmentlist');
Route::get($character.'/PastYear/FinalAssessment/download/{ass_fx_id}', 'Dean\PastYearFinalController@downloadFiles');
Route::get($character.'/PastYear/images/final_assessment/{image_name}', [
	'as'         => 'assessment_final_image',
	'uses'       => 'Dean\PastYearFinalController@FinalAssessmentImage',
	'middleware' => 'auth',
]);
Route::get($character.'/PastYear/final_assessment/view/whole_paper/{fx_id}', 'Dean\PastYearFinalController@view_wholePaper');

//Past year CA Result
Route::get($character.'/PastYear/sampleResult/{id}/previous/{course_id}/{search}','Dean\PastYearController@PastYearResultAssessmentList')->name('dean.PastYearResultAssessmentList');
Route::get($character.'/PastYear/sampleResult/{id}/name/{ass_id}/{search}','Dean\PastYearController@PastYearStudentList')->name('dean.PastYearStudentList');
Route::get($character.'/PastYear/sampleResult/{id}/result/{ar_stu_id}','Dean\PastYearController@PastYearResultList')->name('dean.PastYearResultList');
Route::get($character.'/PastYear/assessment/sampleResult/download/zipFiles/{course_id}/{download}','Dean\PastYearController@zipFileDownloadResult');
Route::get($character.'/PastYear/sampleResult/list/download/zipFiles/{course_id}/{download}','Dean\PastYearController@zipFileDownloadResultList');
Route::get($character.'/PastYear/sampleResult/download/zipFiles/{ass_id}/{download}','Dean\PastYearController@zipFileDownloadStudent');
Route::get($character.'/PastYear/Student/{student_id}/download/zipFiles/{ass_id}/{download}', [
    'as' => 'zipFileDownloadDocument', 'uses' => 'Dean\PastYearController@zipFileDownloadDocument']);
Route::post($character.'/PastYear/assessment/sampleResult/searchSampleResult/', 'Dean\PastYearController@searchAssessmentSampleResult')->name('hod.PY.searchSampleResult');
Route::post($character.'/PastYear/result/searchAssessmentResult/', 'Dean\PastYearController@searchAssessmentResult')->name('hod.PY.searchAssessmentResult');
Route::post($character.'/PastYear/assessment/sampleResult/searchStudentList/', 'Dean\PastYearController@searchStudentList')->name('hod.PY.searchStudentList');
Route::get($character.'/PastYear/images/AssessmentResult/{image_name}', [
	'as'         => 'M_assessmentResult_image',
	'uses'       => 'Dean\PastYearController@assessmentResult_image',
	'middleware' => 'auth',
]);
Route::get($character.'/PastYear/AssessmentResult/view/whole_paper/{ar_stu_id}', 'Dean\PastYearController@view_wholePaperResult');
Route::get($character.'/PastYear/AssessmentResult/result/{ar_stu_id}','Dean\PastYearController@downloadDocument');


//Past Year FInal Result
Route::get($character.'/PastYear/FinalSampleResult/{id}/previous/{course_id}/{search}','Dean\PastYearFinalController@PastYearStudentList')->name('dean.PastYearStudentList');
Route::get($character.'/PastYear/FinalSampleResult/{id}/result/{fxr_id}','Dean\PastYearFinalController@PastYearResultList')->name('dean.PastYearResultList');
Route::get($character.'/PastYear/FinalSampleResult/download/zipFiles/{course_id}/{download}','Dean\PastYearFinalController@zipFileDownloadResult');
Route::get($character.'/PastYear/FinalSampleResult/Student/{student_id}/download/zipFiles/{course_id}/{download}', [
    'as' => 'zipFileDownloadDocument', 'uses' => 'Dean\PastYearFinalController@zipFileDownloadDocument']);
Route::get($character.'/PastYear/FinalSampleResult/student/download/zipFiles/{course_id}/{download}','Dean\PastYearFinalController@zipFileDownloadStudent');
Route::post($character.'/PastYear/FinalSampleResult/searchAssessmentResult/', 'Dean\PastYearFinalController@searchAssessmentResult')->name('hod.PY.final.searchAssessmentResult');
Route::post($character.'/PastYear/FinalSampleResult/searchStudentList/', 'Dean\PastYearFinalController@searchStudentList')->name('hod.PY.final.searchStudentList');
Route::get($character.'/PastYear/images/FinalResult/{image_name}', [
	'as'         => 'FinalResult_image',
	'uses'       => 'Dean\PastYearFinalController@FinalResult_image',
	'middleware' => 'auth',
]);
Route::get($character.'/PastYear/FinalResult/view/whole_paper/{fxr_id}', 'Dean\PastYearFinalController@view_wholePaperResult');
Route::get($character.'/PastYear/FinalSampleResult/download/{fxr_id}', 'Dean\PastYearFinalController@downloadFilesResult');


//Past Year Lecturer Note
Route::get($character.'/PastYearNote/{id}','Dean\PastYearNoteController@PastYearNote');
Route::get($character.'/PastYearNote/{id}/{view}/{view_id}','Dean\PastYearNoteController@PastYearNoteViewIn');
Route::post($character.'/PastYear/lectureNote/searchFiles', 'Dean\PastYearNoteController@searchLecturerNote');
Route::post($character.'/PastYear/lectureNote/searchPreviousFiles', 'Dean\PastYearNoteController@searchLecturerNotePrevious');
Route::get($character.'/PastYearNote/download/zipFiles/{course_id}/{download}','Dean\PastYearNoteController@zipFileDownload');
Route::get($character.'/PastYear/images/lectureNote/{ln_id}/{image_name}', [
     'as'         => 'lectureNote_image',
     'uses'       => 'Dean\PastYearNoteController@LectureNoteImage',
     'middleware' => 'auth',
]);
Route::get($character.'/PastYear/lectureNote/download/{id}','Dean\PastYearNoteController@downloadLN');

//Past Year TP
Route::get($character.'/PastYearTP/{id}','Dean\PastYearTPController@PastYearTP')->name('dean.pastYearTP');
Route::get($character.'/PastYearTP/{id}/course/{view_id}','Dean\PastYearTPController@PastYearTPDownload');
Route::get($character.'/PastYearTP/download/zipFiles/{course_id}/{checked}','Dean\PastYearTPController@downloadZipFiles');
Route::post($character.'/PastYearTP/searchFiles', 'Dean\PastYearTPController@searchPastYearTP');

//Moderator
Route::get($character.'/Moderator','Dean\Moderator\M_CourseController@index');
Route::post($character.'/searchModeratorCourse', 'Dean\Moderator\M_CourseController@searchModeratorCourse');
Route::get($character.'/Moderator/course/{id}','Dean\Moderator\M_CourseController@ModeratorAction');
//Moderator Student list
Route::get($character.'/Moderator/assign/student/{id}','Dean\Moderator\M_StudentListController@ModeratorStudent');
Route::post($character.'/searchModeratorStudent', 'Dean\Moderator\M_StudentListController@searchModeratorStudent');
//Moderator Lecture Note
Route::get($character.'/Moderator/lectureNote/{id}','Dean\Moderator\M_LectureNoteController@ModeratorLectureNote');
Route::post($character.'/Moderator/lectureNote/searchFiles', 'Dean\Moderator\M_LectureNoteController@searchModeratorLN');
Route::get($character.'/Moderator/lectureNote/folder/{ln_id}','Dean\Moderator\M_LectureNoteController@ModeratorLNFolderView');
Route::get($character.'/Moderator/images/lectureNote/{ln_id}/{image_name}', [
	'as'         => 'hod.lectureNote_image',
	'uses'       => 'Dean\Moderator\M_LectureNoteController@LectureNoteImage',
	'middleware' => 'auth',
]);
Route::get($character.'/Moderator/lectureNote/download/{id}','Dean\Moderator\M_LectureNoteController@downloadLN');

//Moderator Teaching Plan
Route::get($character.'/Moderator/teachingPlan/{id}','Dean\Moderator\M_TeachingPlanController@ModeratorTeachingPlan');
Route::post($character.'/Moderator/teachingPlan/verify/','Dean\Moderator\M_TeachingPlanController@M_TP_VerifyAction')->name('hod.tp_verify_form');
Route::get($character.'/Moderator/teachingPlan/report/{id}', 'Dean\Moderator\M_TeachingPlanController@TPDownload');
//Moderator Assessment
Route::get($character.'/Moderator/viewAssessment/{id}','Dean\Moderator\M_AssessmentController@viewAssessment');
Route::post($character.'/Moderator/assessment/getSyllabusData', 'Dean\Moderator\M_AssessmentController@getSyllabusData');
Route::get($character.'/Moderator/assessment/create/{id}/question/{coursework}/{question}', [
    'as' => 'hod.M_V_Question', 'uses' => 'Dean\Moderator\M_AssessmentController@create_question']);
Route::get($character.'/Moderator/assessment/view_list/{ass_id}', 'Dean\Moderator\M_AssessmentController@assessment_list_view');
Route::get($character.'/Moderator/assessment/view/whole_paper/{ass_id}', 'Dean\Moderator\M_AssessmentController@view_wholePaper');
Route::get($character.'/Moderator/images/assessment/{image_name}', [
	     'as'         => 'hod.M_assessment_image',
	     'uses'       => 'Dean\Moderator\M_AssessmentController@assessmentImage',
	     'middleware' => 'auth',
	]);
Route::post($character.'/Moderator/assessment/searchKey/', 'Dean\Moderator\M_AssessmentController@searchKey')->name('hod.moderator.searchKey');
Route::post($character.'/Moderator/assessment/searchAssessmentList/', 'Dean\Moderator\M_AssessmentController@searchAssessmentList')->name('hod.moderator.searchAssessmentList');
Route::get($character.'/Moderator/assessment/download/{ass_li_id}', 'Dean\Moderator\M_AssessmentController@downloadFiles');

//Assessment Result
Route::get($character.'/Moderator/AssessmentResult/{id}/question/{question}', [
    'as' => 'hod.M_viewAssessmentStudentResult', 'uses' => 'Dean\Moderator\M_AssessmentResultController@viewAssessmentStudentResult']);
Route::get($character.'/Moderator/AssessmentResult/studentResult/{ass_id}/', [
    'as' => 'hod.M_viewstudentlist', 'uses' => 'Dean\Moderator\M_AssessmentResultController@viewstudentlist']);
Route::get($character.'/Moderator/AssessmentResult/view/student/{ar_stu_id}/', [
    'as' => 'hod.M_viewStudentResult', 'uses' => 'Dean\Moderator\M_AssessmentResultController@viewStudentResult']);
Route::post($character.'/Moderator/AssessmentResult/searchAssessmentForm/', 'Dean\Moderator\M_AssessmentResultController@searchAssessmentForm')->name('hod.moderator.searchAssessmentForm');
Route::post($character.'/Moderator/AssessmentResult/searchStudentList/', 'Dean\Moderator\M_AssessmentResultController@searchStudentList')->name('hod.moderator.searchStudentList');
Route::get($character.'/Moderator/images/AssessmentResult/{image_name}', [
	'as'         => 'hod.M_assessmentResult_image',
	'uses'       => 'Dean\Moderator\M_AssessmentResultController@assessmentResult_image',
	'middleware' => 'auth',
]);
Route::get($character.'/Moderator/AssessmentResult/view/whole_paper/{ar_stu_id}', 'Dean\Moderator\M_AssessmentResultController@view_wholePaper');
Route::get($character.'/Moderator/AssessmentResult/result/{ar_stu_id}','Dean\Moderator\M_AssessmentResultController@downloadDocument');
//Moderator Assessment
Route::get($character.'/Moderator/Assessment/{id}','Dean\Moderator\M_AssessmentController@ModeratorAssessment');
Route::post($character.'/Moderator/Assessment/Moderation/','Dean\Moderator\M_AssessmentController@M_Ass_Moderate_Action')->name('hod.create.CAModerationForm');
Route::get($character.'/Moderator/Assessment/report/{actionCA_id}','Dean\Moderator\M_AssessmentController@ModerationFormReport');

//Final Assessment
Route::get($character.'/Moderator/FinalExam/{id}/', [
    'as' => 'hod.M_FinalExamination', 'uses' => 'Dean\Moderator\M_FinalExamController@viewFinalExamination']);
Route::post($character.'/Moderator/FinalExamination/getSyllabusData', 'Dean\Moderator\M_FinalExamController@getSyllabusData');
Route::get($character.'/Moderator/FinalExamination/question/{coursework}/{id}/', [
    'as' => 'hod.M_FX_Question', 'uses' => 'Dean\Moderator\M_FinalExamController@create_question']);
Route::get($character.'/Moderator/images/final_assessment/{image_name}', [
	     'as'         => 'hod.M_assessment_final_image',
	     'uses'       => 'Dean\Moderator\M_FinalExamController@FinalAssessmentImage',
	     'middleware' => 'auth',
]);
Route::get($character.'/Moderator/FinalExamination/view_list/{fx_id}', 'Dean\Moderator\M_FinalExamController@final_assessment_list_view');
Route::post($character.'/Moderator/FinalExamination/searchAssessmentList/', 'Dean\Moderator\M_FinalExamController@searchAssessmentList')->name('hod.moderator.Final_searchAssessmentList');
Route::post($character.'/Moderator/FinalExamination/searchKey/', 'Dean\Moderator\M_FinalExamController@searchKey')->name('hod.m.Final.searchKey');
Route::get($character.'/Moderator/final_assessment/view/whole_paper/{fx_id}', 'Dean\Moderator\M_FinalExamController@view_wholePaper');
Route::get($character.'/Moderator/FinalExamination/download/{ass_fx_id}', 'Dean\Moderator\M_FinalExamController@downloadFiles');

//Final Assessment Result
Route::get($character.'/Moderator/FinalResult/{id}', [
    'as' => 'hod.M_viewFinalResult', 'uses' => 'Dean\Moderator\M_FinalExamResultController@viewFinalResult']);
Route::get($character.'/Moderator/FinalResult/view/student/{fxr_id}/', [
    'as' => 'hod.M_viewFinalStudentResult', 'uses' => 'Dean\Moderator\M_FinalExamResultController@viewFinalStudentResult']);
Route::get($character.'/Moderator/FinalResult/result/{fxr_id}','Dean\Moderator\M_FinalExamResultController@downloadDocument');
Route::get($character.'/Moderator/images/FinalResult/{image_name}', [
	     'as'         => 'hod.M_FinalResult_image',
	     'uses'       => 'Dean\Moderator\M_FinalExamResultController@FinalResult_image',
	     'middleware' => 'auth',
]);
Route::get($character.'/Moderator/FinalResult/view/whole_paper/{fxr_id}', 'Dean\Moderator\M_FinalExamResultController@view_wholePaper');
Route::post($character.'/Moderator/FinalResult/searchStudentList/', 'Dean\Moderator\M_FinalExamResultController@searchStudentList')->name('hod.m.final.searchStudentList');

//Moderator Final Assessment
Route::get($character.'/Moderator/FinalExamination/{id}','Dean\Moderator\M_FinalExamController@ModeratorFinalExam');
Route::post($character.'/Moderator/FinalExamination/Moderation/','Dean\Moderator\M_FinalExamController@M_FX_Moderate_Action')->name('hod.create.FA_ModerationForm');
Route::get($character.'/Moderator/FinalExamination/report/{actionFA_id}','Dean\Moderator\M_FinalExamController@ModerationFormReport');
//Moderator E_PortFolio
Route::get($character.'/Moderator/E_Portfolio/{id}','Dean\Moderator\E_PortfolioController@viewE_Portfolio');
Route::get($character.'/Moderator/E_Portfolio/report/{id}','Dean\Moderator\E_PortfolioController@Download_E_Portfolio');

//Moderator Timetable
Route::get($character.'/Moderator/timetable/{id}', [
	'as' => 'hod.M_timetable', 'uses' => 'Dean\Moderator\M_TimetableController@viewTimetable'
]);

//Moderator Attendance
Route::get($character.'/Moderator/Attendance/{id}','Dean\Moderator\M_AttendanceController@viewAttendance');
Route::get($character.'/Moderator/Attendance/{id}/student_list/{date}', 'Dean\Moderator\M_AttendanceController@viewStudentList');

//Moderator Past Year CA Question
Route::get($character.'/Moderator/PastYear/assessment/{id}','Dean\Moderator\M_PastYearController@PastYearAssessment');
Route::get($character.'/Moderator/PastYear/assessment/{id}/assessment_name/{course_id}','Dean\Moderator\M_PastYearController@PastYearAssessmentName');
Route::get($character.'/Moderator/PastYear/assessment/{id}/list/{ass_id}/','Dean\Moderator\M_PastYearController@PastYearAssessmentList');
Route::post($character.'/Moderator/PastYear/assessment/searchAssessment/', 'Dean\Moderator\M_PastYearController@searchAssessment')->name('hod.m.PY.searchAssessment');
Route::post($character.'/Moderator/PastYear/assessment/name/searchAssessmentName/', 'Dean\Moderator\M_PastYearController@searchAssessmentName')->name('hod.m.PY.searchAssessmentName');
Route::post($character.'/Moderator/PastYear/assessment/list/searchAssessmentlist/', 'Dean\Moderator\M_PastYearController@searchAssessmentlist')->name('hod.m.PY.searchAssessmentlist');
Route::get($character.'/Moderator/PastYear/assessment/view/whole_paper/{ass_id}', 'Dean\Moderator\M_PastYearController@view_wholePaper');
Route::get($character.'/Moderator/PastYear/images/assessment/{image_name}', [
	'as'         => 'M_assessment_image',
	'uses'       => 'Dean\Moderator\M_PastYearController@assessmentImage',
	'middleware' => 'auth',
]);
Route::get($character.'/Moderator/PastYear/assessment/download/{ass_li_id}', 
		'Dean\Moderator\M_PastYearController@downloadFiles');

//Moderator Past year Final question
Route::get($character.'/Moderator/PastYear/FinalAssessment/{id}','Dean\Moderator\M_PastYearFinalController@PastYearAssessment');
Route::get($character.'/Moderator/PastYear/FinalAssessment/{id}/assessment_name/{course_id}','Dean\Moderator\M_PastYearFinalController@PastYearAssessmentName');
Route::get($character.'/Moderator/PastYear/FinalAssessment/{id}/list/{fx_id}/','Dean\Moderator\M_PastYearFinalController@PastYearAssessmentList');
Route::post($character.'/Moderator/PastYear/FinalAssessment/searchAssessment/', 'Dean\Moderator\M_PastYearFinalController@searchAssessment')->name('hod.m.PY.final.searchAssessment');
Route::post($character.'/Moderator/PastYear/FinalAssessment/name/searchAssessmentName/', 'Dean\Moderator\M_PastYearFinalController@searchAssessmentName')->name('hod.m.PY.final.searchAssessmentName');
Route::post($character.'/Moderator/PastYear/FinalAssessment/list/searchAssessmentlist/', 'Dean\Moderator\M_PastYearFinalController@searchAssessmentlist')->name('hod.m.PY.final.searchAssessmentlist');
Route::get($character.'/Moderator/PastYear/FinalAssessment/download/{ass_fx_id}', 'Dean\Moderator\M_PastYearFinalController@downloadFiles');
Route::get($character.'/Moderator/PastYear/images/final_assessment/{image_name}', [
	'as'         => 'M_assessment_final_image',
	'uses'       => 'Dean\Moderator\M_PastYearFinalController@FinalAssessmentImage',
	'middleware' => 'auth',
]);
Route::get($character.'/Moderator/PastYear/final_assessment/view/whole_paper/{fx_id}', 'Dean\Moderator\M_PastYearFinalController@view_wholePaper');


//Moderator Past year CA Result
Route::get($character.'/Moderator/PastYear/sampleResult/{id}/previous/{course_id}/{search}','Dean\Moderator\M_PastYearController@PastYearResultAssessmentList');
Route::get($character.'/Moderator/PastYear/sampleResult/{id}/name/{ass_id}/{search}','Dean\Moderator\M_PastYearController@PastYearStudentList');
Route::get($character.'/Moderator/PastYear/sampleResult/{id}/result/{ar_stu_id}','Dean\Moderator\M_PastYearController@PastYearResultList');
Route::post($character.'/Moderator/PastYear/assessment/sampleResult/searchSampleResult/', 'Dean\Moderator\M_PastYearController@searchAssessmentSampleResult')->name('hod.m.PY.searchSampleResult');
Route::post($character.'/Moderator/PastYear/result/searchAssessmentResult/', 'Dean\Moderator\M_PastYearController@searchAssessmentResult')->name('hod.m.PY.searchAssessmentResult');
Route::post($character.'/Moderator/PastYear/assessment/sampleResult/searchStudentList/', 'Dean\Moderator\M_PastYearController@searchStudentList')->name('hod.m.PY.searchStudentList');
Route::get($character.'/Moderator/PastYear/images/AssessmentResult/{image_name}', [
	'as'         => 'M_assessmentResult_image',
	'uses'       => 'Dean\Moderator\M_PastYearController@assessmentResult_image',
	'middleware' => 'auth',
]);
Route::get($character.'/Moderator/PastYear/AssessmentResult/view/whole_paper/{ar_stu_id}', 'Dean\Moderator\M_PastYearController@view_wholePaperResult');
Route::get($character.'/Moderator/PastYear/AssessmentResult/result/{ar_stu_id}','Dean\Moderator\M_PastYearController@downloadDocument');

//Moderator Past Year FInal Result
Route::get($character.'/Moderator/PastYear/FinalSampleResult/{id}/previous/{course_id}/{search}','Dean\Moderator\M_PastYearFinalController@PastYearStudentList');
Route::get($character.'/Moderator/PastYear/FinalSampleResult/{id}/result/{fxr_id}','Dean\Moderator\M_PastYearFinalController@PastYearResultList');
Route::post($character.'/Moderator/PastYear/FinalSampleResult/searchAssessmentResult/', 'Dean\Moderator\M_PastYearFinalController@searchAssessmentResult')->name('hod.m.PY.final.searchAssessmentResult');
Route::post($character.'/Moderator/PastYear/FinalSampleResult/searchStudentList/', 'Dean\Moderator\M_PastYearFinalController@searchStudentList')->name('hod.m.PY.final.searchAssessmentResult');
Route::get($character.'/Moderator/PastYear/images/FinalResult/{image_name}', [
	'as'         => 'M_FinalResult_image',
	'uses'       => 'Dean\Moderator\M_PastYearFinalController@FinalResult_image',
	'middleware' => 'auth',
]);
Route::get($character.'/Moderator/PastYear/FinalResult/view/whole_paper/{fxr_id}', 'Dean\Moderator\M_PastYearFinalController@view_wholePaperResult');
Route::get($character.'/Moderator/PastYear/FinalSampleResult/download/{fxr_id}', 'Dean\Moderator\M_PastYearFinalController@downloadFilesResult');


//Moderator Past Year Lecturer Note
Route::get($character.'/Moderator/PastYearNote/{id}','Dean\Moderator\M_PastYearNoteController@PastYearNote');
Route::get($character.'/Moderator/PastYearNote/{id}/{view}/{view_id}','Dean\Moderator\M_PastYearNoteController@PastYearNoteViewIn');
Route::post($character.'/Moderator/PastYear/lectureNote/searchFiles', 'Dean\Moderator\M_PastYearNoteController@searchLecturerNote');
Route::post($character.'/Moderator/PastYear/lectureNote/searchPreviousFiles', 'Dean\Moderator\M_PastYearNoteController@searchLecturerNotePrevious');
Route::get($character.'/Moderator/PastYear/images/lectureNote/{ln_id}/{image_name}', [
	'as'         => 'M_lectureNote_image',
	'uses'       => 'Dean\Moderator\M_PastYearNoteController@LectureNoteImage',
	'middleware' => 'auth',
]);
Route::get($character.'/Moderator/PastYear/lectureNote/download/{id}','Dean\Moderator\M_PastYearNoteController@downloadLN');


//Moderator Past Year TP
Route::get($character.'/Moderator/PastYearTP/{id}','Dean\Moderator\M_PastYearTPController@PastYearTP');
Route::get($character.'/Moderator/PastYearTP/{id}/course/{view_id}','Dean\Moderator\M_PastYearTPController@PastYearTPDownload');
Route::post($character.'/Moderator/PastYearTP/searchFiles', 'Dean\Moderator\M_PastYearTPController@searchPastYearTP');


//Reviewer
Route::get($character.'/Reviewer','Dean\Dean\D_CourseController@index');
Route::post($character.'/searchCourse', 'Dean\Dean\D_CourseController@searchCourse');
Route::get($character.'/Reviewer/course/{id}','Dean\Dean\D_CourseController@DeanAction');

//Reviewer Student list
Route::get($character.'/Reviewer/assign/student/{id}','Dean\Dean\D_StudentListController@DeanStudent');
Route::post($character.'/searchDeanStudent', 'Dean\Dean\D_StudentListController@searchDeanStudent');
//Reviewer Lecture Note
Route::get($character.'/Reviewer/lectureNote/{id}','Dean\Dean\D_LectureNoteController@DeanLectureNote');
Route::post($character.'/Reviewer/lectureNote/searchFiles', 'Dean\Dean\D_LectureNoteController@searchDeanLN');
Route::get($character.'/Reviewer/lectureNote/folder/{ln_id}','Dean\Dean\D_LectureNoteController@DeanLNFolderView');
Route::get($character.'/Reviewer/images/lectureNote/{ln_id}/{image_name}', [
	'as'         => 'hod.lectureNote_image',
	'uses'       => 'Dean\Dean\D_LectureNoteController@LectureNoteImage',
	'middleware' => 'auth',
]);
Route::get($character.'/Reviewer/lectureNote/download/{id}','Dean\Dean\D_LectureNoteController@downloadLN');
//Reviewer Teaching Plan
Route::get($character.'/Reviewer/teachingPlan/{id}','Dean\Dean\D_TeachingPlanController@DeanTeachingPlan');
Route::post($character.'/Reviewer/teachingPlan/approve/','Dean\Dean\D_TeachingPlanController@D_TP_VerifyAction')->name('hod.tp_approve_form');
Route::get($character.'/Reviewer/teachingPlan/report/{id}', 'Dean\Dean\D_TeachingPlanController@TPDownload');

//Reviewer Assessment
Route::get($character.'/Reviewer/viewAssessment/{id}','Dean\Dean\D_AssessmentController@viewAssessment');
Route::post($character.'/Reviewer/assessment/getSyllabusData', 'Dean\Dean\D_AssessmentController@getSyllabusData');
Route::get($character.'/Reviewer/assessment/create/{id}/question/{coursework}/{question}', [
    'as' => 'createQuestion', 'uses' => 'Dean\Dean\D_AssessmentController@create_question']);
Route::get($character.'/Reviewer/assessment/view_list/{ass_id}', 'Dean\Dean\D_AssessmentController@assessment_list_view')->name('dean.ass.assessment_list_view');
Route::get($character.'/Reviewer/assessment/view/whole_paper/{ass_id}', 'Dean\Dean\D_AssessmentController@view_wholePaper');
Route::get($character.'/Reviewer/images/assessment/{image_name}', [
	     'as'         => 'assessment_image',
	     'uses'       => 'Dean\Dean\D_AssessmentController@assessmentImage',
	     'middleware' => 'auth',
]);
Route::post($character.'/Reviewer/assessment/searchKey/', 'Dean\Dean\D_AssessmentController@searchKey')->name('hod.r.searchKey');
Route::post($character.'/Reviewer/assessment/searchAssessmentList/', 'Dean\Dean\D_AssessmentController@searchAssessmentList')->name('hod.r.searchAssessmentList');
Route::get($character.'/Reviewer/assessment/download/{ass_li_id}', 'Dean\Dean\D_AssessmentController@downloadFiles');

//Assessment Result
Route::get($character.'/Reviewer/AssessmentResult/{id}/question/{question}', [
'as' => 'viewAssessmentStudentResult', 'uses' => 'Dean\Dean\D_AssessmentResultController@viewAssessmentStudentResult'
]);
Route::get($character.'/Reviewer/AssessmentResult/studentResult/{ass_id}/', [
'as' => 'viewstudentlist', 'uses' => 'Dean\Dean\D_AssessmentResultController@viewstudentlist']);
Route::get($character.'/Reviewer/AssessmentResult/view/student/{ar_stu_id}/', [
'as' => 'viewStudentResult', 'uses' => 'Dean\Dean\D_AssessmentResultController@viewStudentResult']);
Route::post($character.'/Reviewer/AssessmentResult/searchAssessmentForm/', 'Dean\Dean\D_AssessmentResultController@searchAssessmentForm')->name('hod.r.searchAssessmentForm');
Route::post($character.'/Reviewer/AssessmentResult/searchStudentList/', 'Dean\Dean\D_AssessmentResultController@searchStudentList')->name('hod.r.searchStudentList');
Route::get($character.'/Reviewer/images/AssessmentResult/{image_name}', [
	'as'         => 'assessmentResult_image',
	'uses'       => 'Dean\Dean\D_AssessmentResultController@assessmentResult_image',
	'middleware' => 'auth',
]);
Route::get($character.'/Reviewer/AssessmentResult/view/whole_paper/{ar_stu_id}', 'Dean\Dean\D_AssessmentResultController@view_wholePaper');
Route::get($character.'/Reviewer/AssessmentResult/result/{ar_stu_id}','Dean\Dean\D_AssessmentResultController@downloadDocument');

Route::get($character.'/Reviewer/Assessment/{id}','Dean\Dean\D_AssessmentController@DeanAssessment');
Route::post($character.'/Reviewer/Assessment/approve/','Dean\Dean\D_AssessmentController@D_Ass_Verify_Action')->name('hod.CA.verify_form');
Route::get($character.'/Reviewer/Assessment/report/{actionCA_id}','Dean\Dean\D_AssessmentController@ModerationFormReport');

//Dean Final Assessment
Route::get($character.'/Reviewer/FinalExam/{id}/', [
    'as' => 'hod.D_FinalExamination', 'uses' => 'Dean\Dean\D_FinalExamController@viewFinalExamination']);
Route::post($character.'/Reviewer/FinalExamination/getSyllabusData', 'Dean\Dean\D_FinalExamController@getSyllabusData');
Route::get($character.'/Reviewer/FinalExamination/question/{coursework}/{id}/', [
'as' => 'hod.D_createQuestion', 'uses' => 'Dean\Dean\D_FinalExamController@create_question']);
Route::get($character.'/Reviewer/FinalExamination/view_list/{fx_id}', 'Dean\Dean\D_FinalExamController@final_assessment_list_view');
Route::get($character.'/Reviewer/images/final_assessment/{image_name}', [
	     'as'         => 'hod.D_assessment_final_image',
	     'uses'       => 'Dean\Dean\D_FinalExamController@FinalAssessmentImage',
	     'middleware' => 'auth',
]);
Route::post($character.'/Reviewer/FinalExamination/searchAssessmentList/', 'Dean\Dean\D_FinalExamController@searchAssessmentList')->name('hod.r.final.searchAssessmentList');
Route::post($character.'/Reviewer/FinalExamination/searchKey/', 'Dean\Dean\D_FinalExamController@searchKey')->name('hod.r.final.searchKey');
Route::get($character.'/Reviewer/final_assessment/view/whole_paper/{fx_id}', 'Dean\Dean\D_FinalExamController@view_wholePaper');
Route::get($character.'/Reviewer/FinalExamination/download/{ass_fx_id}', 'Dean\Dean\D_FinalExamController@downloadFiles');

//Final Assessment Result
Route::get($character.'/Reviewer/FinalResult/{id}', [
    'as' => 'hod.D_viewFinalResult', 'uses' => 'Dean\Dean\D_FinalExamResultController@viewFinalResult']);
Route::get($character.'/Reviewer/FinalResult/view/student/{fxr_id}/', [
    'as' => 'hod.D_viewFinalStudentResult', 'uses' => 'Dean\Dean\D_FinalExamResultController@viewFinalStudentResult']);
Route::get($character.'/Reviewer/FinalResult/result/{fxr_id}','Dean\Dean\D_FinalExamResultController@downloadDocument');
Route::get($character.'/Reviewer/images/FinalResult/{image_name}', [
	     'as'         => 'hod.D_FinalResult_image',
	     'uses'       => 'Dean\Dean\D_FinalExamResultController@FinalResult_image',
	     'middleware' => 'auth',
]);
Route::get($character.'/Reviewer/FinalResult/view/whole_paper/{fxr_id}', 'Dean\Dean\D_FinalExamResultController@view_wholePaper');
Route::post($character.'/Reviewer/FinalResult/searchStudentList/', 'Dean\Dean\D_FinalExamResultController@searchStudentList')->name('hod.r.final.searchStudentList');
	
Route::get($character.'/Reviewer/FinalExamination/{id}','Dean\Dean\D_FinalExamController@DeanFinalExam');	
Route::post($character.'/Reviewer/FinalExamination/verify/','Dean\Dean\D_FinalExamController@D_FX_Verify_Action')->name('hod.FA.verify_form');
Route::get($character.'/Reviewer/FinalExamination/report/{actionFA_id}','Dean\Dean\D_FinalExamController@ModerationFormReport');

//Dean E_PortFolio
Route::get($character.'/Reviewer/E_Portfolio/{id}', [
'as' => 'viewE_Portfolio', 'uses' => 'Dean\Dean\E_PortfolioController@viewE_Portfolio']);
Route::get($character.'/Reviewer/E_Portfolio/report/{id}', [
'as' => 'Download_E_Portfolio', 'uses' => 'Dean\Dean\E_PortfolioController@Download_E_Portfolio']);

//Dean Timetable
Route::get($character.'/Reviewer/timetable/{id}', [
    'as' => 'hod.view_Timetable', 'uses' => 'Dean\Dean\D_TimetableController@viewTimetable'
]);

//Dean Attendance
Route::get($character.'/Reviewer/Attendance/{id}','Dean\Dean\D_AttendanceController@viewAttendance');
Route::get($character.'/Reviewer/Attendance/{id}/student_list/{date}', 'Dean\Dean\D_AttendanceController@viewStudentList');

//Reviewer Past Year CA Question
Route::get($character.'/Reviewer/PastYear/assessment/{id}','Dean\Dean\D_PastYearController@PastYearAssessment');
Route::get($character.'/Reviewer/PastYear/assessment/{id}/assessment_name/{course_id}','Dean\Dean\D_PastYearController@PastYearAssessmentName');
Route::get($character.'/Reviewer/PastYear/assessment/{id}/list/{ass_id}/','Dean\Dean\D_PastYearController@PastYearAssessmentList');
Route::post($character.'/Reviewer/PastYear/assessment/searchAssessment/', 'Dean\Dean\D_PastYearController@searchAssessment')->name('hod.r.PY.searchAssessment');
Route::post($character.'/Reviewer/PastYear/assessment/name/searchAssessmentName/', 'Dean\Dean\D_PastYearController@searchAssessmentName')->name('hod.r.PY.searchAssessmentName');
Route::post($character.'/Reviewer/PastYear/assessment/list/searchAssessmentlist/', 'Dean\Dean\D_PastYearController@searchAssessmentlist')->name('hod.r.PY.searchAssessmentlist');
Route::get($character.'/Reviewer/PastYear/assessment/view/whole_paper/{ass_id}', 'Dean\Dean\D_PastYearController@view_wholePaper');
Route::get($character.'/Reviewer/PastYear/images/assessment/{image_name}', [
	'as'         => 'M_assessment_image',
	'uses'       => 'Dean\Dean\D_PastYearController@assessmentImage',
	'middleware' => 'auth',
]);
Route::get($character.'/Reviewer/PastYear/assessment/download/{ass_li_id}', 
		'Dean\Dean\D_PastYearController@downloadFiles');

//Reviewer Past year Final question
Route::get($character.'/Reviewer/PastYear/FinalAssessment/{id}','Dean\Dean\D_PastYearFinalController@PastYearAssessment');
Route::get($character.'/Reviewer/PastYear/FinalAssessment/{id}/assessment_name/{course_id}','Dean\Dean\D_PastYearFinalController@PastYearAssessmentName');
Route::get($character.'/Reviewer/PastYear/FinalAssessment/{id}/list/{fx_id}/','Dean\Dean\D_PastYearFinalController@PastYearAssessmentList');
Route::post($character.'/Reviewer/PastYear/FinalAssessment/searchAssessment/', 'Dean\Dean\D_PastYearFinalController@searchAssessment')->name('hod.r.PY.final.searchAssessment');
Route::post($character.'/Reviewer/PastYear/FinalAssessment/name/searchAssessmentName/', 'Dean\Dean\D_PastYearFinalController@searchAssessmentName')->name('hod.r.PY.final.searchAssessmentName');
Route::post($character.'/Reviewer/PastYear/FinalAssessment/list/searchAssessmentlist/', 'Dean\Dean\D_PastYearFinalController@searchAssessmentlist')->name('hod.r.PY.final.searchAssessmentlist');
Route::get($character.'/Reviewer/PastYear/FinalAssessment/download/{ass_fx_id}', 'Dean\Dean\D_PastYearFinalController@downloadFiles');
Route::get($character.'/Reviewer/PastYear/images/final_assessment/{image_name}', [
	'as'         => 'M_assessment_final_image',
	'uses'       => 'Dean\Dean\D_PastYearFinalController@FinalAssessmentImage',
	'middleware' => 'auth',
]);
Route::get($character.'/Reviewer/PastYear/final_assessment/view/whole_paper/{fx_id}', 'Dean\Dean\D_PastYearFinalController@view_wholePaper');


//Reviewer Past year CA Result
Route::get($character.'/Reviewer/PastYear/sampleResult/{id}/previous/{course_id}/{search}','Dean\Dean\D_PastYearController@PastYearResultAssessmentList');
Route::get($character.'/Reviewer/PastYear/sampleResult/{id}/name/{ass_id}/{search}','Dean\Dean\D_PastYearController@PastYearStudentList');
Route::get($character.'/Reviewer/PastYear/sampleResult/{id}/result/{ar_stu_id}','Dean\Dean\D_PastYearController@PastYearResultList');
Route::post($character.'/Reviewer/PastYear/assessment/sampleResult/searchSampleResult/', 'Dean\Dean\D_PastYearController@searchAssessmentSampleResult')->name('hod.r.PY.searchSampleResult');
Route::post($character.'/Reviewer/PastYear/result/searchAssessmentResult/', 'Dean\Dean\D_PastYearController@searchAssessmentResult')->name('hod.r.PY.searchAssessmentResult');
Route::post($character.'/Reviewer/PastYear/assessment/sampleResult/searchStudentList/', 'Dean\Dean\D_PastYearController@searchStudentList')->name('hod.r.PY.searchStudentList');
Route::get($character.'/Reviewer/PastYear/images/AssessmentResult/{image_name}', [
	'as'         => 'M_assessmentResult_image',
	'uses'       => 'Dean\Dean\D_PastYearController@assessmentResult_image',
	'middleware' => 'auth',
]);
Route::get($character.'/Reviewer/PastYear/AssessmentResult/view/whole_paper/{ar_stu_id}', 'Dean\Dean\D_PastYearController@view_wholePaperResult');
Route::get($character.'/Reviewer/PastYear/AssessmentResult/result/{ar_stu_id}','Dean\Dean\D_PastYearController@downloadDocument');

//Reviewer Past Year FInal Result
Route::get($character.'/Reviewer/PastYear/FinalSampleResult/{id}/previous/{course_id}/{search}','Dean\Dean\D_PastYearFinalController@PastYearStudentList');
Route::get($character.'/Reviewer/PastYear/FinalSampleResult/{id}/result/{fxr_id}','Dean\Dean\D_PastYearFinalController@PastYearResultList');
Route::post($character.'/Reviewer/PastYear/FinalSampleResult/searchAssessmentResult/', 'Dean\Dean\D_PastYearFinalController@searchAssessmentResult')->name('hod.r.PY.final.searchAssessmentResult');
Route::post($character.'/Reviewer/PastYear/FinalSampleResult/searchStudentList/', 'Dean\Dean\D_PastYearFinalController@searchStudentList')->name('hod.r.PY.final.searchStudentList');
Route::get($character.'/Reviewer/PastYear/images/FinalResult/{image_name}', [
	'as'         => 'M_FinalResult_image',
	'uses'       => 'Dean\Dean\D_PastYearFinalController@FinalResult_image',
	'middleware' => 'auth',
]);
Route::get($character.'/Reviewer/PastYear/FinalResult/view/whole_paper/{fxr_id}', 'Dean\Dean\D_PastYearFinalController@view_wholePaperResult');
Route::get($character.'/Reviewer/PastYear/FinalSampleResult/download/{fxr_id}', 'Dean\Dean\D_PastYearFinalController@downloadFilesResult');


//Reviewer Past Year Lecturer Note
Route::get($character.'/Reviewer/PastYearNote/{id}','Dean\Dean\D_PastYearNoteController@PastYearNote');
Route::get($character.'/Reviewer/PastYearNote/{id}/{view}/{view_id}','Dean\Dean\D_PastYearNoteController@PastYearNoteViewIn');
Route::post($character.'/Reviewer/PastYear/lectureNote/searchFiles', 'Dean\Dean\D_PastYearNoteController@searchLecturerNote');
Route::post($character.'/Reviewer/PastYear/lectureNote/searchPreviousFiles', 'Dean\Dean\D_PastYearNoteController@searchLecturerNotePrevious');
Route::get($character.'/Reviewer/PastYear/images/lectureNote/{ln_id}/{image_name}', [
	'as'         => 'M_lectureNote_image',
	'uses'       => 'Dean\Dean\D_PastYearNoteController@LectureNoteImage',
	'middleware' => 'auth',
]);
Route::get($character.'/Reviewer/PastYear/lectureNote/download/{id}','Dean\Dean\D_PastYearNoteController@downloadLN');

//Reviewer Past Year TP
Route::get($character.'/Reviewer/PastYearTP/{id}','Dean\Dean\D_PastYearTPController@PastYearTP');
Route::get($character.'/Reviewer/PastYearTP/{id}/course/{view_id}','Dean\Dean\D_PastYearTPController@PastYearTPDownload');
Route::post($character.'/Reviewer/PastYearTP/searchFiles', 'Dean\Dean\D_PastYearTPController@searchPastYearTP');


Route::get($character.'/report/course/List/','Dean\ReportController@ReportAction');

Route::get($character.'/report/TP/course/List/','Dean\ReportController@TPReport');
Route::post($character.'/report/TP/searchCourse/','Dean\ReportTPController@searchCourse');
Route::get($character.'/report/TP/view/{id}','Dean\ReportTPController@viewTPDetail');
Route::get($character.'/report/TP/download/{id}','Dean\ReportTPController@DownloadTPReport');
Route::get($character.'/report/TP/download/zipFiles/{id}/{download}','Dean\ReportTPController@ZipFilesDownloadTPReport');

Route::get($character.'/report/assessment/','Dean\ReportController@AssessmentReport');
Route::post($character.'/report/CA/searchCourse/','Dean\ReportAssessmentController@searchCourse');
Route::get($character.'/report/CA/view/{id}','Dean\ReportAssessmentController@viewCADetail');
Route::get($character.'/report/assessment/download/{id}','Dean\ReportAssessmentController@DownloadAssessmentReport');
Route::get($character.'/report/assessment/download/zipFiles/{id}/{download}','Dean\ReportAssessmentController@ZipFilesDownloadAssessmentReport');

Route::get($character.'/report/final_assessment/','Dean\ReportController@FinalAssessmentReport');
Route::post($character.'/report/FA/searchCourse/','Dean\ReportFinalAssessmentController@searchCourse');
Route::get($character.'/report/FA/view/{id}','Dean\ReportFinalAssessmentController@viewFADetail');
Route::get($character.'/report/final_assessment/download/{id}','Dean\ReportFinalAssessmentController@DownloadFinalAssessmentReport');
Route::get($character.'/report/final_assessment/download/zipFiles/{id}/{download}','Dean\ReportFinalAssessmentController@ZipFilesDownloadFinalAssessmentReport');

Route::get($character.'/report/E_Portfolio/course/List/', [
'as' => 'E_Portfolio_List', 'uses' => 'Dean\ReportController@E_Portfolio_List']);
Route::get($character.'/E_Portfolio/list/{id}', [
'as' => 'viewListE_Portfolio', 'uses' => 'Dean\ReportController@viewListE_Portfolio']);
Route::post($character.'/E_Portfolio/searchCourse/', 'Dean\E_PortfolioController@searchCourse');
Route::get($character.'/E_Portfolio/download/zipFiles/{course_id}/{checked}','Dean\E_PortfolioController@downloadZipFiles');
?>