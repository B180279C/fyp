<?php
$character = "/hod";
Route::get($character.'/home', 'HomeController@hodHome')->name('hod.home');

Route::get($character.'/images/home_image/{user_id}', [
	     'as'         => 'home_image',
	     'uses'       => 'HomeController@hodDetails',
	     'middleware' => 'auth',
]);

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
Route::post($character.'/assessment/searchKey/', 'Dean\AssessmentController@searchKey');
Route::post($character.'/assessment/searchAssessmentList/', 'Dean\AssessmentController@searchAssessmentList');
Route::get($character.'/assessment/AllZipFiles/{id}/{download}','Dean\AssessmentController@AllZipFileDownload');
Route::get($character.'/assessment/download/zipFiles/{ass_id}/{download}','Dean\AssessmentController@zipFileDownload');
Route::get($character.'/assessment/Action/Submit/{id}', 'Dean\AssessmentController@AssessmentSubmitAction');
Route::post($character.'/assessment/Action/HOD/', 'Dean\AssessmentController@SubmitSelf_D_Form');
Route::get($character.'/Assessment/report/{actionCA_id}','Dean\AssessmentController@ModerationFormReport');


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
Route::post($character.'/AssessmentResult/searchAssessmentForm/', 'Dean\AssessmentResultController@searchAssessmentForm');
Route::post($character.'/AssessmentResult/searchStudentList/', 'Dean\AssessmentResultController@searchStudentList');
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
Route::post($character.'/FinalExamination/searchAssessmentList/', 'Dean\FinalExaminationController@searchAssessmentList');
Route::post($character.'/FinalExamination/searchKey/', 'Dean\FinalExaminationController@searchKey');
Route::get($character.'/final_assessment/view/whole_paper/{fx_id}', 'Dean\FinalExaminationController@view_wholePaper');
Route::get($character.'/FinalExamination/download/{ass_fx_id}', 'Dean\FinalExaminationController@downloadFiles');
Route::get($character.'/FinalExamination/AllZipFiles/{id}/{download}','Dean\FinalExaminationController@AllZipFileDownload');
Route::get($character.'/FinalExamination/download/zipFiles/{fx_id}/{download}','Dean\FinalExaminationController@zipFileDownload');
Route::get($character.'/FinalExamination/Action/Submit/{id}','Dean\FinalExaminationController@FASubmitAction');
Route::post($character.'/FinalExamination/Action/HOD/', 'Dean\FinalExaminationController@SubmitSelf_D_Form');
Route::get($character.'/FinalExamination/report/{actionFA_id}','Dean\FinalExaminationController@ModerationFormReport');

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
Route::post($character.'/FinalResult/searchStudentList/', 'Dean\FinalExaminationResultController@searchStudentList');
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
Route::post($character.'/Attendance/store/', 'Dean\AttendanceController@storeAttendance');
Route::post($character.'/Attendance/edit/', 'Dean\AttendanceController@editAttendance');

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
Route::post($character.'/Moderator/teachingPlan/verify/','Dean\Moderator\M_TeachingPlanController@M_TP_VerifyAction');
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
Route::post($character.'/Moderator/assessment/searchKey/', 'Dean\Moderator\M_AssessmentController@searchKey');
Route::post($character.'/Moderator/assessment/searchAssessmentList/', 'Dean\Moderator\M_AssessmentController@searchAssessmentList');
Route::get($character.'/Moderator/assessment/download/{ass_li_id}', 'Dean\Moderator\M_AssessmentController@downloadFiles');

//Assessment Result
Route::get($character.'/Moderator/AssessmentResult/{id}/question/{question}', [
    'as' => 'hod.M_viewAssessmentStudentResult', 'uses' => 'Dean\Moderator\M_AssessmentResultController@viewAssessmentStudentResult']);
Route::get($character.'/Moderator/AssessmentResult/studentResult/{ass_id}/', [
    'as' => 'hod.M_viewstudentlist', 'uses' => 'Dean\Moderator\M_AssessmentResultController@viewstudentlist']);
Route::get($character.'/Moderator/AssessmentResult/view/student/{ar_stu_id}/', [
    'as' => 'hod.M_viewStudentResult', 'uses' => 'Dean\Moderator\M_AssessmentResultController@viewStudentResult']);
Route::post($character.'/Moderator/AssessmentResult/searchAssessmentForm/', 'Dean\Moderator\M_AssessmentResultController@searchAssessmentForm');
Route::post($character.'/Moderator/AssessmentResult/searchStudentList/', 'Dean\Moderator\M_AssessmentResultController@searchStudentList');
Route::get($character.'/Moderator/images/AssessmentResult/{image_name}', [
	'as'         => 'hod.M_assessmentResult_image',
	'uses'       => 'Dean\Moderator\M_AssessmentResultController@assessmentResult_image',
	'middleware' => 'auth',
]);
Route::get($character.'/Moderator/AssessmentResult/view/whole_paper/{ar_stu_id}', 'Dean\Moderator\M_AssessmentResultController@view_wholePaper');
Route::get($character.'/Moderator/AssessmentResult/result/{ar_stu_id}','Dean\Moderator\M_AssessmentResultController@downloadDocument');
//Moderator Assessment
Route::get($character.'/Moderator/Assessment/{id}','Dean\Moderator\M_AssessmentController@ModeratorAssessment');
Route::post($character.'/Moderator/Assessment/Moderation/','Dean\Moderator\M_AssessmentController@M_Ass_Moderate_Action');
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
Route::post($character.'/Moderator/FinalExamination/searchAssessmentList/', 'Dean\Moderator\M_FinalExamController@searchAssessmentList');
Route::post($character.'/Moderator/FinalExamination/searchKey/', 'Dean\Moderator\M_FinalExamController@searchKey');
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
Route::post($character.'/Moderator/FinalResult/searchStudentList/', 'Dean\Moderator\M_FinalExamResultController@searchStudentList');

//Moderator Final Assessment
Route::get($character.'/Moderator/FinalExamination/{id}','Dean\Moderator\M_FinalExamController@ModeratorFinalExam');
Route::post($character.'/Moderator/FinalExamination/Moderation/','Dean\Moderator\M_FinalExamController@M_FX_Moderate_Action');
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
Route::post($character.'/Reviewer/teachingPlan/approve/','Dean\Dean\D_TeachingPlanController@D_TP_VerifyAction');
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
Route::post($character.'/Reviewer/assessment/searchKey/', 'Dean\Dean\D_AssessmentController@searchKey');
Route::post($character.'/Reviewer/assessment/searchAssessmentList/', 'Dean\Dean\D_AssessmentController@searchAssessmentList');
Route::get($character.'/Reviewer/assessment/download/{ass_li_id}', 'Dean\Dean\D_AssessmentController@downloadFiles');

//Assessment Result
Route::get($character.'/Reviewer/AssessmentResult/{id}/question/{question}', [
'as' => 'viewAssessmentStudentResult', 'uses' => 'Dean\Dean\D_AssessmentResultController@viewAssessmentStudentResult'
]);
Route::get($character.'/Reviewer/AssessmentResult/studentResult/{ass_id}/', [
'as' => 'viewstudentlist', 'uses' => 'Dean\Dean\D_AssessmentResultController@viewstudentlist']);
Route::get($character.'/Reviewer/AssessmentResult/view/student/{ar_stu_id}/', [
'as' => 'viewStudentResult', 'uses' => 'Dean\Dean\D_AssessmentResultController@viewStudentResult']);
Route::post($character.'/Reviewer/AssessmentResult/searchAssessmentForm/', 'Dean\Dean\D_AssessmentResultController@searchAssessmentForm');
Route::post($character.'/Reviewer/AssessmentResult/searchStudentList/', 'Dean\Dean\D_AssessmentResultController@searchStudentList');
Route::get($character.'/Reviewer/images/AssessmentResult/{image_name}', [
	'as'         => 'assessmentResult_image',
	'uses'       => 'Dean\Dean\D_AssessmentResultController@assessmentResult_image',
	'middleware' => 'auth',
]);
Route::get($character.'/Reviewer/AssessmentResult/view/whole_paper/{ar_stu_id}', 'Dean\Dean\D_AssessmentResultController@view_wholePaper');
Route::get($character.'/Reviewer/AssessmentResult/result/{ar_stu_id}','Dean\Dean\D_AssessmentResultController@downloadDocument');

Route::get($character.'/Reviewer/Assessment/{id}','Dean\Dean\D_AssessmentController@DeanAssessment');
Route::post($character.'/Reviewer/Assessment/approve/','Dean\Dean\D_AssessmentController@D_Ass_Verify_Action');
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
Route::post($character.'/Reviewer/FinalExamination/searchAssessmentList/', 'Dean\Dean\D_FinalExamController@searchAssessmentList');
Route::post($character.'/Reviewer/FinalExamination/searchKey/', 'Dean\Dean\D_FinalExamController@searchKey');
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
Route::post($character.'/Reviewer/FinalResult/searchStudentList/', 'Dean\Dean\D_FinalExamResultController@searchStudentList');
	
Route::get($character.'/Reviewer/FinalExamination/{id}','Dean\Dean\D_FinalExamController@DeanFinalExam');	
Route::post($character.'/Reviewer/FinalExamination/verify/','Dean\Dean\D_FinalExamController@D_FX_Verify_Action');
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
?>