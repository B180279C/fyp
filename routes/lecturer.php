<?php
$character = "/lecturer";
Route::get('lecturer/home', 'HomeController@teacherHome')->name('teacher.home');

Route::get($character.'/images/home_image/{user_id}', [
	     'as'         => 'lecturer.home_image',
	     'uses'       => 'HomeController@lecturerDetails',
	     'middleware' => 'auth',
]);

Route::post($character.'/notification/getNum', 'Dean\NotificationController@getNum');

Route::get($character.'/profile/', 'Dean\ProfileController@profile')->name('lecturer.Profile');

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

Route::get($character.'/profile/CV/{id}','Dean\ProfileController@ProfileDownloadCV')->name('lecturer.downloadCV');
Route::post($character.'/staffUploadImage', 'Dean\ProfileController@uploadImages')->name('lecturer.dropzone.uploadStaffImage');
Route::post($character.'/staffDestoryImage', 'Dean\ProfileController@destroyImage')->name('lecturer.dropzone.destoryStaffImage');
Route::post($character.'/staffUploadCV', 'Dean\ProfileController@uploadCV')->name('lecturer.dropzone.uploadStaffCV');
Route::post($character.'/staffDestoryCV', 'Dean\ProfileController@destroyCV')->name('lecturer.dropzone.destoryStaffCV');
Route::post($character.'/staffUploadSign', 'Dean\ProfileController@uploadSign')->name('lecturer.dropzone.uploadStaffSign');
Route::post($character.'/staffDestorySign', 'Dean\ProfileController@destroySign')->name('lecturer.dropzone.destoryStaffSign');
Route::post($character.'/profile/store', 'Dean\ProfileController@store')->name('lecturer.staff.submit');


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
	'as'         => 'lecturer.lectureNote_image',
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
    'as' => 'lecturer.createQuestion', 'uses' => 'Dean\AssessmentController@create_question']);
Route::get($character.'/assessment/create/{id}/list/{coursework}/{question}', [
    'as' => 'createAssList', 'uses' => 'Dean\AssessmentController@create_assessment_list']);
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
	     'as'         => 'lecturer.assessment_image',
	     'uses'       => 'Dean\AssessmentController@assessmentImage',
	     'middleware' => 'auth',
	]);
Route::get($character.'/assessment/view/whole_paper/{ass_id}', 'Dean\AssessmentController@view_wholePaper');
Route::get($character.'/assessment/download/{ass_li_id}', 'Dean\AssessmentController@downloadFiles');
Route::post($character.'/assessment/searchKey/', 'Dean\AssessmentController@searchKey')->name('lecturer.searchKey');
Route::post($character.'/assessment/searchAssessmentList/', 'Dean\AssessmentController@searchAssessmentList')->name('lecturer.searchAssessmentList');
Route::get($character.'/assessment/AllZipFiles/{id}/{download}','Dean\AssessmentController@AllZipFileDownload');
Route::get($character.'/assessment/download/zipFiles/{ass_id}/{download}','Dean\AssessmentController@zipFileDownload');
Route::get($character.'/assessment/Action/Submit/{id}', 'Dean\AssessmentController@AssessmentSubmitAction');
Route::post($character.'/assessment/Action/HOD/', 'Dean\AssessmentController@SubmitSelf_D_Form')->name('lecturer.CA.submit_for_verify');
Route::get($character.'/Assessment/report/{actionCA_id}','Dean\AssessmentController@ModerationFormReport');
Route::get($character.'/assessment/create/previous/{id}/{question}','Dean\AssessmentController@createPreviousAss');
Route::post($character.'/assessment/question/SampleStored/Active','Dean\AssessmentController@getSampleStoredActive')->name('lecturer.question.getSampleStoredActive');
Route::post($character.'/assessment/get/SampleStored/','Dean\AssessmentController@getSampleStored')->name('lecturer.getSampleStored');
Route::post($character.'/assessment/get/SampleStoredEdit/','Dean\AssessmentController@getSampleStoredEdit')->name('lecturer.getSampleStoredEdit');


    // Continuous Assessment Student Result
Route::get($character.'/AssessmentResult/{id}/question/{question}', [
    'as' => 'lecturer.viewAssessmentStudentResult', 'uses' => 'Dean\AssessmentResultController@viewAssessmentStudentResult']);
Route::post($character.'/ass_rs_uploadFiles', 'Dean\AssessmentResultController@uploadFiles');
Route::post($character.'/ass_rs_destoryFiles', 'Dean\AssessmentResultController@destroyFiles');
Route::post($character.'/ass_rs_storeFiles', 'Dean\AssessmentResultController@storeFiles');
Route::get($character.'/AssessmentResult/studentResult/{ass_id}/', [
    'as' => 'lecturer.viewstudentlist', 'uses' => 'Dean\AssessmentResultController@viewstudentlist']);
Route::get($character.'/AssessmentResult/view/student/{ar_stu_id}/', [
    'as' => 'lecturer.viewStudentResult', 'uses' => 'Dean\AssessmentResultController@viewStudentResult']);
Route::get($character.'/AssessmentResult/result/{ar_stu_id}','Dean\AssessmentResultController@downloadDocument');
Route::post($character.'/AssessmentResult/searchAssessmentForm/', 'Dean\AssessmentResultController@searchAssessmentForm')->name('lecturer.searchAssessmentForm');
Route::post($character.'/AssessmentResult/searchStudentList/', 'Dean\AssessmentResultController@searchStudentList')->name('lecturer.searchStudentList');
Route::get($character.'/images/AssessmentResult/{image_name}', [
	'as'         => 'lecturer.assessmentResult_image',
	'uses'       => 'Dean\AssessmentResultController@assessmentResult_image',
	'middleware' => 'auth',
]);
Route::get($character.'/AssessmentResult/view/whole_paper/{ar_stu_id}', 'Dean\AssessmentResultController@view_wholePaper');
Route::get($character.'/AssessmentResult/remove/{id}', 'Dean\AssessmentResultController@removeActive');
Route::get($character.'/AssessmentResultStudent/remove/{ar_stu_id}', 'Dean\AssessmentResultController@removeStudentActive');
Route::get($character.'/AssessmentResult/AllZipFiles/{id}/{download}','Dean\AssessmentResultController@AllZipFileDownload');
Route::get($character.'/AssessmentResult/download/zipFiles/{ass_id}/{download}','Dean\AssessmentResultController@zipFileDownload');
Route::get($character.'/AssessmentResult/Student/{student_id}/download/zipFiles/{ass_id}/{download}', [
    'as' => 'lecturer.zipFileDownloadStudent', 'uses' => 'Dean\AssessmentResultController@zipFileDownloadStudent']);

// FinalExamination
Route::get($character.'/FinalExamination/{id}/', [
    'as' => 'lecturer.viewFinalExamination', 'uses' => 'Dean\FinalExaminationController@viewFinalExamination']);
Route::post($character.'/FinalExamination/getSyllabusData', 'Dean\FinalExaminationController@getSyllabusData');
Route::get($character.'/FinalExamination/list/{coursework}/{id}/', [
    'as' => 'create_final_list', 'uses' => 'Dean\FinalExaminationController@create_final_list']);
Route::get($character.'/FinalExamination/question/{coursework}/{id}/', [
    'as' => 'lecturer.createQuestion', 'uses' => 'Dean\FinalExaminationController@create_question']);
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
	     'as'         => 'lecturer.assessment_final_image',
	     'uses'       => 'Dean\FinalExaminationController@FinalAssessmentImage',
	     'middleware' => 'auth',
	]);
Route::post($character.'/FinalExamination/searchAssessmentList/', 'Dean\FinalExaminationController@searchAssessmentList')->name('lecturer.final.searchAssessmentList');
Route::post($character.'/FinalExamination/searchKey/', 'Dean\FinalExaminationController@searchKey')->name('lecturer.final.searchKey');
Route::get($character.'/final_assessment/view/whole_paper/{fx_id}', 'Dean\FinalExaminationController@view_wholePaper');
Route::get($character.'/FinalExamination/download/{ass_fx_id}', 'Dean\FinalExaminationController@downloadFiles');
Route::get($character.'/FinalExamination/AllZipFiles/{id}/{download}','Dean\FinalExaminationController@AllZipFileDownload');
Route::get($character.'/FinalExamination/download/zipFiles/{fx_id}/{download}','Dean\FinalExaminationController@zipFileDownload');
Route::get($character.'/FinalExamination/Action/Submit/{id}','Dean\FinalExaminationController@FASubmitAction');
Route::post($character.'/FinalExamination/Action/HOD/', 'Dean\FinalExaminationController@SubmitSelf_D_Form')->name('lecturer.FA.submit_for_verify');
Route::get($character.'/FinalExamination/report/{actionFA_id}','Dean\FinalExaminationController@ModerationFormReport');
Route::get($character.'/FinalExamination/create/previous/{id}/','Dean\FinalExaminationController@createPreviousAss');

// Final Examination Result
Route::get($character.'/FinalResult/{id}', [
    'as' => 'lecturer.viewFinalResult', 'uses' => 'Dean\FinalExaminationResultController@viewFinalResult']);
Route::post($character.'/final_rs_uploadFiles', 'Dean\FinalExaminationResultController@uploadFiles');
Route::post($character.'/final_rs_destoryFiles', 'Dean\FinalExaminationResultController@destroyFiles');
Route::post($character.'/final_rs_storeFiles', 'Dean\FinalExaminationResultController@storeFiles');
Route::get($character.'/FinalResult/view/student/{fxr_id}/', [
    'as' => 'lecturer.viewFinalStudentResult', 'uses' => 'Dean\FinalExaminationResultController@viewFinalStudentResult']);
Route::get($character.'/FinalResult/result/{fxr_id}','Dean\FinalExaminationResultController@downloadDocument');
Route::get($character.'/images/FinalResult/{image_name}', [
	'as'         => 'lecturer.FinalResult_image',
	'uses'       => 'Dean\FinalExaminationResultController@FinalResult_image',
	'middleware' => 'auth',
]);
Route::get($character.'/FinalResult/view/whole_paper/{fxr_id}', 'Dean\FinalExaminationResultController@view_wholePaper');
Route::get($character.'/FinalResult/remove/{fxr_id}', 'Dean\FinalExaminationResultController@removeStudentActive');
Route::post($character.'/FinalResult/searchStudentList/', 'Dean\FinalExaminationResultController@searchStudentList')->name('lecturer.final.searchStudentList');
Route::get($character.'/FinalResult/download/zipFiles/{course_id}/{download}','Dean\FinalExaminationResultController@zipFileDownload');
Route::get($character.'/FinalResult/Student/{student_id}/download/zipFiles/{course_id}/{download}', [
    'as' => 'lecturer.zipFileDownloadFinalResult', 'uses' => 'Dean\FinalExaminationResultController@zipFileDownloadStudent']);

//E_Portfolio
Route::get($character.'/E_Portfolio/{id}', [
    'as' => 'lecturer.viewE_Portfolio', 'uses' => 'Dean\E_PortfolioController@viewE_Portfolio']);
Route::get($character.'/E_Portfolio/report/{id}', [
    'as' => 'lecturer.Download_E_Portfolio', 'uses' => 'Dean\E_PortfolioController@Download_E_Portfolio']);
Route::get($character.'/E_Portfolio/course/List/', [
    'as' => 'lecturer.E_Portfolio_List', 'uses' => 'Dean\E_PortfolioController@E_Portfolio_List']);
Route::post($character.'/E_Portfolio/searchCourse/', 'Dean\E_PortfolioController@searchCourse');
Route::get($character.'/E_Portfolio/download/zipFiles/{course_id}/{checked}','Dean\E_PortfolioController@downloadZipFiles');
Route::get($character.'/E_Portfolio/list/{id}', [
    'as' => 'lecturer.viewListE_Portfolio', 'uses' => 'Dean\E_PortfolioController@viewListE_Portfolio']);

//Timetable
Route::get($character.'/Timetable/{id}', [
    'as' => 'lecturer.viewTimetable', 'uses' => 'Dean\TimetableController@viewTimetable']);

//Attendance
Route::get($character.'/Attendance/{id}', 'Dean\AttendanceController@viewAttendance');
Route::get($character.'/Attendance/{id}/student_list/{date}', 'Dean\AttendanceController@viewStudentList');
Route::post($character.'/Attendance/store/', 'Dean\AttendanceController@storeAttendance')->name('lecturer.storeAttendance');
Route::post($character.'/Attendance/edit/', 'Dean\AttendanceController@editAttendance')->name('lecturer.editAttendance');
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
Route::post($character.'/PastYear/assessment/searchAssessment/', 'Dean\PastYearController@searchAssessment')->name('lecturer.PY.searchAssessment');
Route::post($character.'/PastYear/assessment/name/searchAssessmentName/', 'Dean\PastYearController@searchAssessmentName')->name('lecturer.PY.searchAssessmentName');
Route::post($character.'/PastYear/assessment/list/searchAssessmentlist/', 'Dean\PastYearController@searchAssessmentlist')->name('lecturer.PY.searchAssessmentlist');
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
Route::post($character.'/PastYear/FinalAssessment/searchAssessment/', 'Dean\PastYearFinalController@searchAssessment')->name('lecturer.PY.final.searchAssessment');
Route::post($character.'/PastYear/FinalAssessment/name/searchAssessmentName/', 'Dean\PastYearFinalController@searchAssessmentName')->name('lecturer.PY.final.searchAssessmentName');
Route::post($character.'/PastYear/FinalAssessment/list/searchAssessmentlist/', 'Dean\PastYearFinalController@searchAssessmentlist')->name('lecturer.PY.final.searchAssessmentlist');
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
Route::post($character.'/PastYear/assessment/sampleResult/searchSampleResult/', 'Dean\PastYearController@searchAssessmentSampleResult')->name('lecturer.PY.searchSampleResult');
Route::post($character.'/PastYear/result/searchAssessmentResult/', 'Dean\PastYearController@searchAssessmentResult')->name('lecturer.PY.searchAssessmentResult');
Route::post($character.'/PastYear/assessment/sampleResult/searchStudentList/', 'Dean\PastYearController@searchStudentList')->name('lecturer.PY.searchStudentList');
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
Route::post($character.'/PastYear/FinalSampleResult/searchAssessmentResult/', 'Dean\PastYearFinalController@searchAssessmentResult')->name('lecturer.PY.final.searchAssessmentResult');
Route::post($character.'/PastYear/FinalSampleResult/searchStudentList/', 'Dean\PastYearFinalController@searchStudentList')->name('lecturer.PY.final.searchStudentList');
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
Route::get($character.'/Moderator/images/lectureNote/{ln_id}/{note}', [
	'as'         => 'lecturer.lectureNote_image',
	'uses'       => 'Dean\Moderator\M_LectureNoteController@LectureNoteImage',
	'middleware' => 'auth',
]);
Route::get($character.'/Moderator/lectureNote/download/{id}','Dean\Moderator\M_LectureNoteController@downloadLN');

//Moderator Teaching Plan
Route::get($character.'/Moderator/teachingPlan/{id}','Dean\Moderator\M_TeachingPlanController@ModeratorTeachingPlan');
Route::post($character.'/Moderator/teachingPlan/verify/','Dean\Moderator\M_TeachingPlanController@M_TP_VerifyAction')->name('lecturer.tp_verify_form');
Route::get($character.'/Moderator/teachingPlan/report/{id}', 'Dean\Moderator\M_TeachingPlanController@TPDownload');
//Moderator Assessment
Route::get($character.'/Moderator/viewAssessment/{id}','Dean\Moderator\M_AssessmentController@viewAssessment');
Route::post($character.'/Moderator/assessment/getSyllabusData', 'Dean\Moderator\M_AssessmentController@getSyllabusData');
Route::get($character.'/Moderator/assessment/create/{id}/question/{coursework}/{question}', [
    'as' => 'lecturer.M_V_Question', 'uses' => 'Dean\Moderator\M_AssessmentController@create_question']);
Route::get($character.'/Moderator/assessment/create/{id}/list/{coursework}/{question}', [
    'as' => 'create_assessment_list', 'uses' => 'Dean\Moderator\M_AssessmentController@create_assessment_list']);
Route::get($character.'/Moderator/assessment/view_list/{ass_id}', 'Dean\Moderator\M_AssessmentController@assessment_list_view');
Route::get($character.'/Moderator/assessment/view/whole_paper/{ass_id}', 'Dean\Moderator\M_AssessmentController@view_wholePaper');
Route::get($character.'/Moderator/images/assessment/{image_name}', [
	     'as'         => 'lecturer.M_assessment_image',
	     'uses'       => 'Dean\Moderator\M_AssessmentController@assessmentImage',
	     'middleware' => 'auth',
	]);
Route::post($character.'/Moderator/assessment/searchKey/', 'Dean\Moderator\M_AssessmentController@searchKey')->name('lecturer.moderator.searchKey');
Route::post($character.'/Moderator/assessment/searchAssessmentList/', 'Dean\Moderator\M_AssessmentController@searchAssessmentList')->name('lecturer.moderator.searchAssessmentList');
Route::get($character.'/Moderator/assessment/download/{ass_li_id}', 'Dean\Moderator\M_AssessmentController@downloadFiles');

//Assessment Result
Route::get($character.'/Moderator/AssessmentResult/{id}/question/{question}', [
    'as' => 'lecturer.M_viewAssessmentStudentResult', 'uses' => 'Dean\Moderator\M_AssessmentResultController@viewAssessmentStudentResult']);
Route::get($character.'/Moderator/AssessmentResult/studentResult/{ass_id}/', [
    'as' => 'lecturer.M_viewstudentlist', 'uses' => 'Dean\Moderator\M_AssessmentResultController@viewstudentlist']);
Route::get($character.'/Moderator/AssessmentResult/view/student/{ar_stu_id}/', [
    'as' => 'lecturer.M_viewStudentResult', 'uses' => 'Dean\Moderator\M_AssessmentResultController@viewStudentResult']);
Route::post($character.'/Moderator/AssessmentResult/searchAssessmentForm/', 'Dean\Moderator\M_AssessmentResultController@searchAssessmentForm')->name('lecturer.moderator.searchAssessmentForm');
Route::post($character.'/Moderator/AssessmentResult/searchStudentList/', 'Dean\Moderator\M_AssessmentResultController@searchStudentList')->name('lecturer.moderator.searchStudentList');
Route::get($character.'/Moderator/images/AssessmentResult/{image_name}', [
	'as'         => 'lecturer.M_assessmentResult_image',
	'uses'       => 'Dean\Moderator\M_AssessmentResultController@assessmentResult_image',
	'middleware' => 'auth',
]);
Route::get($character.'/Moderator/AssessmentResult/view/whole_paper/{ar_stu_id}', 'Dean\Moderator\M_AssessmentResultController@view_wholePaper');
Route::get($character.'/Moderator/AssessmentResult/result/{ar_stu_id}','Dean\Moderator\M_AssessmentResultController@downloadDocument');
//Moderator Assessment
Route::get($character.'/Moderator/Assessment/{id}','Dean\Moderator\M_AssessmentController@ModeratorAssessment');
Route::post($character.'/Moderator/Assessment/Moderation/','Dean\Moderator\M_AssessmentController@M_Ass_Moderate_Action')->name('lecturer.create.CAModerationForm');
Route::get($character.'/Moderator/Assessment/report/{actionCA_id}','Dean\Moderator\M_AssessmentController@ModerationFormReport');

//Final Assessment
Route::get($character.'/Moderator/FinalExam/{id}/', [
    'as' => 'lecturer.M_FinalExamination', 'uses' => 'Dean\Moderator\M_FinalExamController@viewFinalExamination']);
Route::post($character.'/Moderator/FinalExamination/getSyllabusData', 'Dean\Moderator\M_FinalExamController@getSyllabusData');
Route::get($character.'/Moderator/FinalExamination/list/{coursework}/{id}/', [
    'as' => 'create_final_list', 'uses' => 'Dean\Moderator\M_FinalExamController@create_final_list']);
Route::get($character.'/Moderator/FinalExamination/question/{coursework}/{id}/', [
    'as' => 'lecturer.M_FX_Question', 'uses' => 'Dean\Moderator\M_FinalExamController@create_question']);
Route::get($character.'/Moderator/images/final_assessment/{image_name}', [
	     'as'         => 'lecturer.M_assessment_final_image',
	     'uses'       => 'Dean\Moderator\M_FinalExamController@FinalAssessmentImage',
	     'middleware' => 'auth',
]);
Route::get($character.'/Moderator/FinalExamination/view_list/{fx_id}', 'Dean\Moderator\M_FinalExamController@final_assessment_list_view');
Route::post($character.'/Moderator/FinalExamination/searchAssessmentList/', 'Dean\Moderator\M_FinalExamController@searchAssessmentList')->name('lecturer.moderator.Final_searchAssessmentList');
Route::post($character.'/Moderator/FinalExamination/searchKey/', 'Dean\Moderator\M_FinalExamController@searchKey')->name('lecturer.m.Final.searchKey');
Route::get($character.'/Moderator/final_assessment/view/whole_paper/{fx_id}', 'Dean\Moderator\M_FinalExamController@view_wholePaper');
Route::get($character.'/Moderator/FinalExamination/download/{ass_fx_id}', 'Dean\Moderator\M_FinalExamController@downloadFiles');

//Final Assessment Result
Route::get($character.'/Moderator/FinalResult/{id}', [
    'as' => 'lecturer.M_viewFinalResult', 'uses' => 'Dean\Moderator\M_FinalExamResultController@viewFinalResult']);
Route::get($character.'/Moderator/FinalResult/view/student/{fxr_id}/', [
    'as' => 'lecturer.M_viewFinalStudentResult', 'uses' => 'Dean\Moderator\M_FinalExamResultController@viewFinalStudentResult']);
Route::get($character.'/Moderator/FinalResult/result/{fxr_id}','Dean\Moderator\M_FinalExamResultController@downloadDocument');
Route::get($character.'/Moderator/images/FinalResult/{image_name}', [
	     'as'         => 'lecturer.M_FinalResult_image',
	     'uses'       => 'Dean\Moderator\M_FinalExamResultController@FinalResult_image',
	     'middleware' => 'auth',
]);
Route::get($character.'/Moderator/FinalResult/view/whole_paper/{fxr_id}', 'Dean\Moderator\M_FinalExamResultController@view_wholePaper');
Route::post($character.'/Moderator/FinalResult/searchStudentList/', 'Dean\Moderator\M_FinalExamResultController@searchStudentList')->name('lecturer.m.final.searchStudentList');

//Moderator Final Assessment
Route::get($character.'/Moderator/FinalExamination/{id}','Dean\Moderator\M_FinalExamController@ModeratorFinalExam');
Route::post($character.'/Moderator/FinalExamination/Moderation/','Dean\Moderator\M_FinalExamController@M_FX_Moderate_Action')->name('lecturer.create.FA_ModerationForm');
Route::get($character.'/Moderator/FinalExamination/report/{actionFA_id}','Dean\Moderator\M_FinalExamController@ModerationFormReport');
//Moderator E_PortFolio
Route::get($character.'/Moderator/E_Portfolio/{id}','Dean\Moderator\E_PortfolioController@viewE_Portfolio');
Route::get($character.'/Moderator/E_Portfolio/report/{id}','Dean\Moderator\E_PortfolioController@Download_E_Portfolio');

//Moderator Timetable
Route::get($character.'/Moderator/timetable/{id}','Dean\Moderator\M_TimetableController@viewTimetable');

//Moderator Attendance
Route::get($character.'/Moderator/Attendance/{id}','Dean\Moderator\M_AttendanceController@viewAttendance');
Route::get($character.'/Moderator/Attendance/{id}/student_list/{date}', 'Dean\Moderator\M_AttendanceController@viewStudentList');

//Moderator Past Year CA Question
Route::get($character.'/Moderator/PastYear/assessment/{id}','Dean\Moderator\M_PastYearController@PastYearAssessment');
Route::get($character.'/Moderator/PastYear/assessment/{id}/assessment_name/{course_id}','Dean\Moderator\M_PastYearController@PastYearAssessmentName');
Route::get($character.'/Moderator/PastYear/assessment/{id}/list/{ass_id}/','Dean\Moderator\M_PastYearController@PastYearAssessmentList');
Route::post($character.'/Moderator/PastYear/assessment/searchAssessment/', 'Dean\Moderator\M_PastYearController@searchAssessment')->name('lecturer.m.PY.searchAssessment');
Route::post($character.'/Moderator/PastYear/assessment/name/searchAssessmentName/', 'Dean\Moderator\M_PastYearController@searchAssessmentName')->name('lecturer.m.PY.searchAssessmentName');
Route::post($character.'/Moderator/PastYear/assessment/list/searchAssessmentlist/', 'Dean\Moderator\M_PastYearController@searchAssessmentlist')->name('lecturer.m.PY.searchAssessmentlist');
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
Route::post($character.'/Moderator/PastYear/FinalAssessment/searchAssessment/', 'Dean\Moderator\M_PastYearFinalController@searchAssessment')->name('lecturer.m.PY.final.searchAssessment');
Route::post($character.'/Moderator/PastYear/FinalAssessment/name/searchAssessmentName/', 'Dean\Moderator\M_PastYearFinalController@searchAssessmentName')->name('lecturer.m.PY.final.searchAssessmentName');
Route::post($character.'/Moderator/PastYear/FinalAssessment/list/searchAssessmentlist/', 'Dean\Moderator\M_PastYearFinalController@searchAssessmentlist')->name('lecturer.m.PY.final.searchAssessmentlist');
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
Route::post($character.'/Moderator/PastYear/assessment/sampleResult/searchSampleResult/', 'Dean\Moderator\M_PastYearController@searchAssessmentSampleResult')->name('lecturer.m.PY.searchSampleResult');
Route::post($character.'/Moderator/PastYear/result/searchAssessmentResult/', 'Dean\Moderator\M_PastYearController@searchAssessmentResult')->name('lecturer.m.PY.searchAssessmentResult');
Route::post($character.'/Moderator/PastYear/assessment/sampleResult/searchStudentList/', 'Dean\Moderator\M_PastYearController@searchStudentList')->name('lecturer.m.PY.searchStudentList');
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
Route::post($character.'/Moderator/PastYear/FinalSampleResult/searchAssessmentResult/', 'Dean\Moderator\M_PastYearFinalController@searchAssessmentResult')->name('lecturer.m.PY.final.searchAssessmentResult');
Route::post($character.'/Moderator/PastYear/FinalSampleResult/searchStudentList/', 'Dean\Moderator\M_PastYearFinalController@searchStudentList')->name('lecturer.m.PY.final.searchStudentList');
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
?>