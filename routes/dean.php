<?php
	
	//Detail
	Route::get('/home', 'HomeController@deanHome')->name('dean.home');

	Route::get('/dean/images/home_image/{user_id}', [
	     'as'         => 'home_image',
	     'uses'       => 'HomeController@deanDetails',
	     'middleware' => 'auth',
	]);


	Route::get('profile/', 'Dean\ProfileController@profile')->name('Profile');
	Route::get('images/profile/{image_name}', [
	     'as'         => 'profile_image',
	     'uses'       => 'Dean\ProfileController@profileImage',
	     'middleware' => 'auth',
	]);

	Route::get('sign/profile/{image_name}', [
	     'as'         => 'profile_sign',
	     'uses'       => 'Dean\ProfileController@profileSign',
	     'middleware' => 'auth',
	]);

	Route::get('/profile/CV/{id}','Dean\ProfileController@ProfileDownloadCV')->name('downloadCV');
	Route::post('/staffUploadImage', 'Dean\ProfileController@uploadImages')->name('dropzone.uploadStaffImage');
	Route::post('/staffDestoryImage', 'Dean\ProfileController@destroyImage')->name('dropzone.destoryStaffImage');
	Route::post('/staffUploadCV', 'Dean\ProfileController@uploadCV')->name('dropzone.uploadStaffCV');
	Route::post('/staffDestoryCV', 'Dean\ProfileController@destroyCV')->name('dropzone.destoryStaffCV');
	Route::post('/staffUploadSign', 'Dean\ProfileController@uploadSign')->name('dropzone.uploadStaffSign');
	Route::post('/staffDestorySign', 'Dean\ProfileController@destroySign')->name('dropzone.destoryStaffSign');
	Route::post('/profile/store', 'Dean\ProfileController@store')->name('staff.submit');

	// Faculty PortFolio
	Route::get('/FacultyPortFolio', 'Dean\F_PortFolioController@index')->name('dean.F_potrfolio.index');
	Route::post('/searchFiles', 'Dean\F_PortFolioController@searchFiles');	
	Route::get('/FacultyPortFolio/LecturerCV/', 'Dean\F_PortFolioController@lecturerCV')->name('dean.F_potrfolio.lecturerCV');
	Route::get('/FacultyPortFolio/Syllabus/', 'Dean\F_PortFolioController@Syllabus')->name('dean.F_potrfolio.syllabus');
	Route::post('/searchLecturerCV', 'Dean\F_PortFolioController@searchLecturerCV');
	Route::post('/searchSyllabus', 'Dean\F_PortFolioController@searchSyllabus');
	Route::get('/dean/staff/CV/{id}','Dean\F_PortFolioController@downloadCV')->name('dean.downloadCV');
	Route::get('/dean/syllabusDownload/{id}','Dean\F_PortFolioController@downloadSyllabus')->name('dean.downloadSyllabus');
	Route::post('/openNewFolder', 'Dean\F_PortFolioController@openNewFolder');
	Route::post('/folderNameEdit', 'Dean\F_PortFolioController@folderNameEdit');
	Route::post('/updateFolderName', 'Dean\F_PortFolioController@updateFolderName');
	Route::get('/FacultyPortFolio/remove/{id}', 'Dean\F_PortFolioController@removeActiveFile');
	Route::get('/faculty_portfolio/folder/{folder_id}', 'Dean\F_PortFolioController@folder_view')->name('dean.F_potrfolio.folder_view');
	Route::post('/portfolio_uploadFile', 'Dean\F_PortFolioController@uploadFiles')->name('dropzone.uploadFiles');
	Route::post('/destoryFiles', 'Dean\F_PortFolioController@destroyFiles')->name('dropzone.destoryFiles');
	Route::post('/storeFiles', 'Dean\F_PortFolioController@storeFiles');
	Route::get('/faculty/portfolio/{id}','Dean\F_PortFolioController@downloadFP')->name('dean.downloadFP');

	//Course List
	Route::get('/CourseList', 'Dean\C_PortFolioController@index')->name('dean.C_potrfolio.index');
	Route::post('/searchCPCourse', 'Dean\C_PortFolioController@searchCourse');
	Route::get('/CourseList/create','Dean\CourseController@create')->name('course.create');
	Route::get('/CourseList/{id}','Dean\CourseController@edit')->name('course.edit');
	Route::post('CourseList/create', 'Dean\CourseController@store')->name('course.submit');
	Route::post('/courseSubject', 'Dean\CourseController@courseSubject');
	Route::post('/changeModerator', 'Dean\CourseController@changeModerator');
	Route::post('/CourseList/{id}','Dean\CourseController@update')->name('course.update.submit');
	Route::get('/CourseList/remove/{id}', 'Dean\CourseController@removeActiveCourse');
	Route::get('/CourseList/action/{id}','Dean\C_PortFolioController@CourseListAction');
	Route::get('/timetable/remove/{id}', 'Dean\CourseController@removeActiveTimetable');
	

	//All Course Action
	//Student list
	Route::get('/CourseList/assign/student/{id}','Dean\Course\C_StudentListController@StudentList');
	// Route::post('/searchCourseListStudent', 'Dean\Course\C_StudentListController@searchStudent');
	//Lecture Note
	Route::get('/CourseList/lectureNote/{id}','Dean\Course\C_LectureNoteController@LectureNote');
	Route::post('/CourseList/lectureNote/searchFiles', 'Dean\Course\C_LectureNoteController@searchLN');
	Route::get('/CourseList/lectureNote/folder/{ln_id}','Dean\Course\C_LectureNoteController@LNFolderView');
	//Teaching Plan
	Route::get('/CourseList/teachingPlan/{id}','Dean\Course\C_TeachingPlanController@TeachingPlan');
	// Route::post('/CourseList/teachingPlan/approve/','Dean\Dean\D_TeachingPlanController@D_TP_VerifyAction');

	//Assessment
	Route::get('/CourseList/assessment/{id}','Dean\Course\C_AssessmentController@Assessment');
	Route::post('/CourseList/assessment/getSyllabusData', 'Dean\Course\C_AssessmentController@getSyllabusData');
	Route::get('/CourseList/assessment/create/{id}/question/{coursework}/{question}', [
    'as' => 'createQuestion', 'uses' => 'Dean\Course\C_AssessmentController@create_question']);
	Route::get('/CourseList/assessment/view_list/{ass_id}', 'Dean\Course\C_AssessmentController@assessment_list_view')->name('dean.ass.assessment_list_view');
	Route::get('/CourseList/assessment/view/whole_paper/{ass_id}', 'Dean\Course\C_AssessmentController@view_wholePaper');
	Route::get('/CourseList/images/assessment/{image_name}', [
	     'as'         => 'assessment_image',
	     'uses'       => 'Dean\Course\C_AssessmentController@assessmentImage',
	     'middleware' => 'auth',
	]);
	Route::post('/CourseList/assessment/searchKey/', 'Dean\Course\C_AssessmentController@searchKey');
    Route::post('/CourseList/assessment/searchAssessmentList/', 'Dean\Course\C_AssessmentController@searchAssessmentList');
    Route::get('/CourseList/assessment/download/{ass_li_id}', 'Dean\Course\C_AssessmentController@downloadFiles');

    //Assessment Result
    Route::get('/CourseList/AssessmentResult/{id}/question/{question}', [
    'as' => 'viewAssessmentStudentResult', 'uses' => 'Dean\Course\C_AssessmentResultController@viewAssessmentStudentResult']);
	Route::get('/CourseList/AssessmentResult/studentResult/{ass_id}/', [
    'as' => 'viewstudentlist', 'uses' => 'Dean\Course\C_AssessmentResultController@viewstudentlist']);
    Route::get('/CourseList/AssessmentResult/view/student/{ar_stu_id}/', [
    'as' => 'viewStudentResult', 'uses' => 'Dean\Course\C_AssessmentResultController@viewStudentResult']);
    Route::post('/CourseList/AssessmentResult/searchAssessmentForm/', 'Dean\Course\C_AssessmentResultController@searchAssessmentForm');
    Route::post('/CourseList/AssessmentResult/searchStudentList/', 'Dean\Course\C_AssessmentResultController@searchStudentList');
    Route::get('/CourseList/images/AssessmentResult/{image_name}', [
	     'as'         => 'assessmentResult_image',
	     'uses'       => 'Dean\Course\C_AssessmentResultController@assessmentResult_image',
	     'middleware' => 'auth',
	]);
	Route::get('/CourseList/AssessmentResult/view/whole_paper/{ar_stu_id}', 'Dean\Course\C_AssessmentResultController@view_wholePaper');
	Route::get('/CourseList/AssessmentResult/result/{ar_stu_id}','Dean\Course\C_AssessmentResultController@downloadDocument')->name('dean.downloadStudentResult');
	


	//Final Assessment
	Route::get('/CourseList/FinalExamination/{id}/', [
    'as' => 'FinalExamination', 'uses' => 'Dean\Course\C_FinalExamController@viewFinalExamination']);
    Route::post('/CourseList/FinalExamination/getSyllabusData', 'Dean\Course\C_FinalExamController@getSyllabusData');
    Route::get('/CourseList/FinalExamination/question/{coursework}/{id}/', [
    'as' => 'createQuestion', 'uses' => 'Dean\Course\C_FinalExamController@create_question']);
    
	Route::get('/CourseList/FinalExamination/view_list/{fx_id}', 'Dean\Course\C_FinalExamController@final_assessment_list_view')->name('dean.final.final_assessment_list_view');
	Route::get('/CourseList/images/final_assessment/{image_name}', [
	     'as'         => 'assessment_final_image',
	     'uses'       => 'Dean\Course\C_FinalExamController@FinalAssessmentImage',
	     'middleware' => 'auth',
	]);
	Route::post('/CourseList/FinalExamination/searchAssessmentList/', 'Dean\Course\C_FinalExamController@searchAssessmentList');
	Route::post('/CourseList/FinalExamination/searchKey/', 'Dean\Course\C_FinalExamController@searchKey');
	Route::get('/CourseList/final_assessment/view/whole_paper/{fx_id}', 'Dean\Course\C_FinalExamController@view_wholePaper');
	Route::get('/CourseList/FinalExamination/download/{ass_fx_id}', 'Dean\Course\C_FinalExamController@downloadFiles');
	
	//Final Assessment
	Route::get('/CourseList/FinalResult/{id}', [
    'as' => 'viewFinalResult', 'uses' => 'Dean\Course\C_FinalExamResultController@viewFinalResult']);
	Route::get('/CourseList/FinalResult/view/student/{fxr_id}/', [
    'as' => 'viewFinalStudentResult', 'uses' => 'Dean\Course\C_FinalExamResultController@viewFinalStudentResult']);
    Route::get('/CourseList/FinalResult/result/{fxr_id}','Dean\Course\C_FinalExamResultController@downloadDocument')->name('dean.downloadStudentResult');
    Route::get('/CourseList/images/FinalResult/{image_name}', [
	     'as'         => 'FinalResult_image',
	     'uses'       => 'Dean\Course\C_FinalExamResultController@FinalResult_image',
	     'middleware' => 'auth',
	]);
	Route::get('/CourseList/FinalResult/view/whole_paper/{fxr_id}', 'Dean\Course\C_FinalExamResultController@view_wholePaper');
	Route::post('/CourseList/FinalResult/searchStudentList/', 'Dean\Course\C_FinalExamResultController@searchStudentList');

	//Course List E-PortFolio
	Route::get('/CourseList/E_Portfolio/{id}', [
    'as' => 'viewE_Portfolio', 'uses' => 'Dean\Course\E_PortfolioController@viewE_Portfolio']);

	//Course List Timetable
    Route::get('/CourseList/timetable/{id}', [
    'as' => 'viewTimetable', 'uses' => 'Dean\Course\C_TimetableController@viewTimetable']);

    //Course List Attendance
    Route::get('/CourseList/Attendance/{id}', [
    'as' => 'M_Attendance', 'uses' => 'Dean\Course\C_AttendanceController@viewAttendance']);
    Route::get('/CourseList/Attendance/{id}/student_list/{date}', 'Dean\Course\C_AttendanceController@viewStudentList');

	//My Course
	Route::post('uploadCourses', 'Dean\CourseController@importExcel')->name('dropzone.uploadCourses');
	Route::post('course/excel/create', 'Dean\CourseController@storeCourses')->name('course.excel.submit');
	Route::get('course_list','Dean\CourseController@index')->name('dean.course_list.index');
	Route::post('searchTeachCourse', 'Dean\CourseController@searchTeachCourse');
	Route::get('course/action/{id}','Dean\CourseController@courseAction');

	//Assign Student
	Route::get('/assign/student/{id}','Dean\AssignStudentController@viewAssignStudent');
	Route::post('/searchAssignStudent', 'Dean\AssignStudentController@searchAssignStudent');
	Route::post('/showStudent','Dean\AssignStudentController@showStudent');
	Route::post('/storeStudent', 'Dean\AssignStudentController@storeStudent');
	Route::post('/uploadAssignStudent', 'Dean\AssignStudentController@importExcelStudent')->name('dropzone.uploadAssignStudent');
	Route::post('/assignStudent/excel/create', 'Dean\AssignStudentController@storeAssignStudent')->name('assignStudent.excel.submit');
	Route::get('/assignStudent/remove/{id}','Dean\AssignStudentController@removeActiveStudent');


	//Note
	Route::get('/lectureNote/{id}','Dean\LectureNoteController@viewLectureNote');
	Route::post('/lectureNote/searchFiles', 'Dean\LectureNoteController@searchFiles');
	Route::get('/lectureNote/folder/{folder_id}', 'Dean\LectureNoteController@folder_view')->name('dean.note.folder_view');
	Route::post('/lectureNote/openNewFolder', 'Dean\LectureNoteController@openNewFolder');
	Route::post('/lectureNote/folderNameEdit', 'Dean\LectureNoteController@folderNameEdit');
	Route::post('/lectureNote/SelectPreviousSemester', 'Dean\LectureNoteController@SelectPreviousSemester');
	Route::post('/lectureNote/SelectFolderSemester', 'Dean\LectureNoteController@SelectFolderSemester');
	Route::post('/lectureNote/SelectFolderPlace', 'Dean\LectureNoteController@SelectFolderPlace');
	Route::post('/lectureNote/SelectFolder', 'Dean\LectureNoteController@SelectFolder');
	Route::post('/lectureNote/GetUsedSemester', 'Dean\LectureNoteController@GetUsedSemester');
	Route::post('/lectureNote/updateFolderName', 'Dean\LectureNoteController@updateFolderName');
	Route::get('/lectureNote/remove/{id}', 'Dean\LectureNoteController@removeActive');
	Route::get('images/lectureNote/{ln_id}/{image_name}', [
	     'as'         => 'lectureNote_image',
	     'uses'       => 'Dean\LectureNoteController@LectureNoteImage',
	     'middleware' => 'auth',
	]);
	Route::post('/note_uploadFiles', 'Dean\LectureNoteController@uploadFiles')->name('note.dropzone.uploadFiles');
	Route::post('/note_destoryFiles', 'Dean\LectureNoteController@destroyFiles')->name('note.dropzone.destoryFiles');
	Route::post('/note_storeFiles', 'Dean\LectureNoteController@storeFiles');
	Route::post('/note_storePreviousFiles', 'Dean\LectureNoteController@storePreviousFiles');
	Route::get('/lectureNote/download/{id}','Dean\LectureNoteController@downloadLN')->name('dean.downloadLN');
	Route::get('/lectureNote/download/zipFiles/{course_id}/{download}','Dean\LectureNoteController@zipFileDownload');

	//TP
	Route::get('/teachingPlan/{id}','Dean\TeachingPlanController@viewTeachingPlan')->name('tp.view');
	Route::get('/teachingPlan/create/weekly/{id}','Dean\TeachingPlanController@createTeachingPlan')->name('tp.create');
	Route::post('/teachingPlan/create/weekly/{id}', 'Dean\TeachingPlanController@storeTP')->name('tp.submit');
	Route::get('/teachingPlan/create/previous/weekly/{id}','Dean\TeachingPlanController@createPreviousTP')->name('Previoustp.create');
	Route::post('/removeTopic', 'Dean\TeachingPlanController@removeTopic');
	Route::post('/teachingPlan/searchPlan', 'Dean\TeachingPlanController@searchPlan');

	Route::post('/teachingPlan/getSyllabusData', 'Dean\TeachingPlanController@getSyllabusData');
	Route::get('/teachingPlan/create/assessment/{id}','Dean\TeachingPlanController@createTPAss')->name('tpAss.create');
	Route::get('/teachingPlan/create/new/assessment/{id}','Dean\TeachingPlanController@createNewTPAss')->name('NewtpAss.create');
	Route::get('/teachingPlan/create/previous/assessment/{id}','Dean\TeachingPlanController@createPreviousTPAss')->name('PrevioustpAss.create');
	Route::post('/teachingPlan/create/assessment/{id}', 'Dean\TeachingPlanController@storeTPAss')->name('tpAss.submit');
	Route::get('/teachingPlan/create/CQI/{id}','Dean\TeachingPlanController@createTPCQI')->name('tpCQI.create');
	Route::post('/teachingPlan/store/CQI/', 'Dean\TeachingPlanController@storeTPCQI')->name('tpCQI.submit');
	Route::post('/teachingPlan/CQI/Edit/', 'Dean\TeachingPlanController@CQIEdit');
	Route::post('/teachingPlan/CQIUpdate/', 'Dean\TeachingPlanController@CQIUpdate');
	Route::get('/teachingPlan/CQIRemove/{id}', 'Dean\TeachingPlanController@removeActive');
	Route::get('/teachingPlan/report/{id}', 'Dean\TeachingPlanController@TPDownload');
	Route::get('/teachingPlan/Action/Submit/{id}', 'Dean\TeachingPlanController@TPSubmitAction');
	
	//Assessment
	Route::get('/assessment/{id}','Dean\AssessmentController@viewAssessment')->name('dean.viewAssessment');
	Route::post('/assessment/getSyllabusData', 'Dean\AssessmentController@getSyllabusData');
	Route::get('/assessment/create/{id}/question/{coursework}/{question}', [
    'as' => 'createQuestion', 'uses' => 'Dean\AssessmentController@create_question']);
    Route::post('/assessment/openNewAssessment', 'Dean\AssessmentController@openNewAssessment');
    Route::post('/assessment/AssessmentNameEdit', 'Dean\AssessmentController@AssessmentNameEdit');
	Route::post('/assessment/updateAssessmentName', 'Dean\AssessmentController@updateAssessmentName');
	Route::get('/assessment/view_list/{ass_id}', 'Dean\AssessmentController@assessment_list_view')->name('dean.ass.assessment_list_view');
	Route::get('/assessment/remove/{id}', 'Dean\AssessmentController@removeActive');
	Route::get('/assessment/remove/list/{id}', 'Dean\AssessmentController@removeActiveList');
	Route::post('/ass_uploadFiles', 'Dean\AssessmentController@uploadFiles')->name('assessment.dropzone.uploadFiles');
	Route::post('/ass_destoryFiles', 'Dean\AssessmentController@destroyFiles')->name('assessment.dropzone.destoryFiles');
	Route::post('/ass_storeFiles', 'Dean\AssessmentController@storeFiles');
	Route::get('images/assessment/{image_name}', [
	     'as'         => 'assessment_image',
	     'uses'       => 'Dean\AssessmentController@assessmentImage',
	     'middleware' => 'auth',
	]);
	Route::get('/assessment/view/whole_paper/{ass_id}', 'Dean\AssessmentController@view_wholePaper');
	Route::get('/assessment/download/{ass_li_id}', 'Dean\AssessmentController@downloadFiles');
	Route::post('/assessment/searchKey/', 'Dean\AssessmentController@searchKey');
    Route::post('/assessment/searchAssessmentList/', 'Dean\AssessmentController@searchAssessmentList');
    Route::get('/assessment/AllZipFiles/{id}/{download}','Dean\AssessmentController@AllZipFileDownload');
    Route::get('/assessment/download/zipFiles/{ass_id}/{download}','Dean\AssessmentController@zipFileDownload');
    Route::get('/assessment/Action/Submit/{id}', 'Dean\AssessmentController@AssessmentSubmitAction');
	Route::post('/assessment/Action/HOD/', 'Dean\AssessmentController@SubmitSelf_D_Form');
	Route::get('/Assessment/report/{actionCA_id}','Dean\AssessmentController@ModerationFormReport');

    // Continuous Assessment Student Result
    Route::get('/AssessmentResult/{id}/question/{question}', [
    'as' => 'viewAssessmentStudentResult', 'uses' => 'Dean\AssessmentResultController@viewAssessmentStudentResult']);
	Route::post('/ass_rs_uploadFiles', 'Dean\AssessmentResultController@uploadFiles')->name('assessmentResult.dropzone.uploadFiles');
	Route::post('/ass_rs_destoryFiles', 'Dean\AssessmentResultController@destroyFiles')->name('assessmentResult.dropzone.destoryFiles');
	Route::post('/ass_rs_storeFiles', 'Dean\AssessmentResultController@storeFiles');
	Route::get('/AssessmentResult/studentResult/{ass_id}/', [
    'as' => 'viewstudentlist', 'uses' => 'Dean\AssessmentResultController@viewstudentlist']);
    Route::get('/AssessmentResult/view/student/{ar_stu_id}/', [
    'as' => 'viewStudentResult', 'uses' => 'Dean\AssessmentResultController@viewStudentResult']);

    Route::get('/AssessmentResult/result/{ar_stu_id}','Dean\AssessmentResultController@downloadDocument')->name('dean.downloadStudentResult');
    Route::post('/AssessmentResult/searchAssessmentForm/', 'Dean\AssessmentResultController@searchAssessmentForm');
    Route::post('/AssessmentResult/searchStudentList/', 'Dean\AssessmentResultController@searchStudentList');
    Route::get('images/AssessmentResult/{image_name}', [
	     'as'         => 'assessmentResult_image',
	     'uses'       => 'Dean\AssessmentResultController@assessmentResult_image',
	     'middleware' => 'auth',
	]);
	Route::get('/AssessmentResult/view/whole_paper/{ar_stu_id}', 'Dean\AssessmentResultController@view_wholePaper');
	Route::get('/AssessmentResult/remove/{id}', 'Dean\AssessmentResultController@removeActive');
	Route::get('/AssessmentResultStudent/remove/{ar_stu_id}', 'Dean\AssessmentResultController@removeStudentActive');
	Route::get('/AssessmentResult/AllZipFiles/{id}/{download}','Dean\AssessmentResultController@AllZipFileDownload');
	Route::get('/AssessmentResult/download/zipFiles/{ass_id}/{download}','Dean\AssessmentResultController@zipFileDownload');
	Route::get('/AssessmentResult/Student/{student_id}/download/zipFiles/{ass_id}/{download}', [
    'as' => 'zipFileDownloadStudent', 'uses' => 'Dean\AssessmentResultController@zipFileDownloadStudent']);
    

	// FinalExamination
	Route::get('/FinalExamination/{id}/', [
    'as' => 'viewFinalExamination', 'uses' => 'Dean\FinalExaminationController@viewFinalExamination']);
    Route::post('/FinalExamination/getSyllabusData', 'Dean\FinalExaminationController@getSyllabusData');
    Route::get('/FinalExamination/question/{coursework}/{id}/', [
    'as' => 'createQuestion', 'uses' => 'Dean\FinalExaminationController@create_question']);
    Route::post('/FinalExamination/openNewAssessment', 'Dean\FinalExaminationController@openNewAssessment');
    Route::post('/FinalExamination/AssessmentNameEdit', 'Dean\FinalExaminationController@AssessmentNameEdit');
	Route::post('/FinalExamination/updateAssessmentName', 'Dean\FinalExaminationController@updateAssessmentName')->name('final_updateAssessmentName');
	Route::get('/FinalExamination/view_list/{fx_id}', 'Dean\FinalExaminationController@final_assessment_list_view')->name('dean.final.final_assessment_list_view');
	Route::get('/FinalExamination/remove/{id}', 'Dean\FinalExaminationController@removeActive');
	Route::get('/FinalExamination/remove/list/{id}', 'Dean\FinalExaminationController@removeActiveList');
	Route::post('/FinalExamination/uploadFiles', 'Dean\FinalExaminationController@uploadFiles')->name('assessment_final.dropzone.uploadFiles');
	Route::post('/FinalExamination/destoryFiles', 'Dean\FinalExaminationController@destroyFiles')->name('assessment.dropzone.destoryFiles');
	Route::post('/FinalExamination/storeFiles', 'Dean\FinalExaminationController@storeFiles');
	Route::get('/images/final_assessment/{image_name}', [
	     'as'         => 'assessment_final_image',
	     'uses'       => 'Dean\FinalExaminationController@FinalAssessmentImage',
	     'middleware' => 'auth',
	]);
	Route::post('/FinalExamination/searchAssessmentList/', 'Dean\FinalExaminationController@searchAssessmentList');
	Route::post('/FinalExamination/searchKey/', 'Dean\FinalExaminationController@searchKey');
	Route::get('/final_assessment/view/whole_paper/{fx_id}', 'Dean\FinalExaminationController@view_wholePaper');
	Route::get('/FinalExamination/download/{ass_fx_id}', 'Dean\FinalExaminationController@downloadFiles');
	Route::get('/FinalExamination/AllZipFiles/{id}/{download}','Dean\FinalExaminationController@AllZipFileDownload');
	Route::get('/FinalExamination/download/zipFiles/{fx_id}/{download}','Dean\FinalExaminationController@zipFileDownload');
	Route::get('/FinalExamination/Action/Submit/{id}','Dean\FinalExaminationController@FASubmitAction');
	Route::post('/FinalExamination/Action/HOD/', 'Dean\FinalExaminationController@SubmitSelf_D_Form');
	Route::get('/FinalExamination/report/{actionFA_id}','Dean\FinalExaminationController@ModerationFormReport');

	// Final Examination Result
	Route::get('/FinalResult/{id}', [
    'as' => 'viewFinalResult', 'uses' => 'Dean\FinalExaminationResultController@viewFinalResult']);
    Route::post('/final_rs_uploadFiles', 'Dean\FinalExaminationResultController@uploadFiles')->name('FinalResult.dropzone.uploadFiles');
	Route::post('/final_rs_destoryFiles', 'Dean\FinalExaminationResultController@destroyFiles')->name('FinalResult.dropzone.destoryFiles');
	Route::post('/final_rs_storeFiles', 'Dean\FinalExaminationResultController@storeFiles');
	Route::get('/FinalResult/view/student/{fxr_id}/', [
    'as' => 'viewFinalStudentResult', 'uses' => 'Dean\FinalExaminationResultController@viewFinalStudentResult']);
    Route::get('/FinalResult/result/{fxr_id}','Dean\FinalExaminationResultController@downloadDocument')->name('dean.downloadStudentResult');
    Route::get('images/FinalResult/{image_name}', [
	     'as'         => 'FinalResult_image',
	     'uses'       => 'Dean\FinalExaminationResultController@FinalResult_image',
	     'middleware' => 'auth',
	]);
	Route::get('/FinalResult/view/whole_paper/{fxr_id}', 'Dean\FinalExaminationResultController@view_wholePaper');
	Route::get('/FinalResult/remove/{fxr_id}', 'Dean\FinalExaminationResultController@removeStudentActive');
	Route::post('/FinalResult/searchStudentList/', 'Dean\FinalExaminationResultController@searchStudentList');
	Route::get('/FinalResult/download/zipFiles/{course_id}/{download}','Dean\FinalExaminationResultController@zipFileDownload');
	Route::get('/FinalResult/Student/{student_id}/download/zipFiles/{course_id}/{download}', [
    'as' => 'zipFileDownloadFinalResult', 'uses' => 'Dean\FinalExaminationResultController@zipFileDownloadStudent']);

    //E_Portfolio
	Route::get('/E_Portfolio/{id}', [
    'as' => 'viewE_Portfolio', 'uses' => 'Dean\E_PortfolioController@viewE_Portfolio']);
    Route::get('/E_Portfolio/report/{id}', [
    'as' => 'Download_E_Portfolio', 'uses' => 'Dean\E_PortfolioController@Download_E_Portfolio']);
    Route::get('/E_Portfolio/course/List/', [
    'as' => 'E_Portfolio_List', 'uses' => 'Dean\E_PortfolioController@E_Portfolio_List']);
    Route::post('/E_Portfolio/searchCourse/', 'Dean\E_PortfolioController@searchCourse');
    Route::get('/E_Portfolio/download/zipFiles/{course_id}/{checked}','Dean\E_PortfolioController@downloadZipFiles');
    Route::get('/E_Portfolio/list/{id}', [
    'as' => 'viewListE_Portfolio', 'uses' => 'Dean\E_PortfolioController@viewListE_Portfolio']);

    //Timetable
    Route::get('/Timetable/{id}', [
    'as' => 'viewTimetable', 'uses' => 'Dean\TimetableController@viewTimetable']);

    //Attendance
    Route::get('/Attendance/{id}', 'Dean\AttendanceController@viewAttendance');
    Route::get('/Attendance/{id}/student_list/{date}', 'Dean\AttendanceController@viewStudentList');
    Route::post('/Attendance/store/', 'Dean\AttendanceController@storeAttendance');
    Route::post('/Attendance/edit/', 'Dean\AttendanceController@editAttendance');
    Route::post('/Attendance/openQR_Code/', 'Dean\AttendanceController@openQR_Code');
    Route::get('/Attendance/QR_code/{attendance_id}/{code}', 'Dean\AttendanceController@QR_Code');
    

    //Past Year CA Question
    Route::get('/PastYear/assessment/{id}','Dean\PastYearController@PastYearAssessment')->name('dean.pastYear');
    Route::get('/PastYear/assessment/{id}/assessment_name/{course_id}','Dean\PastYearController@PastYearAssessmentName')->name('dean.pastYearASSName');
    Route::get('/PastYear/assessment/{id}/list/{ass_id}/','Dean\PastYearController@PastYearAssessmentList')->name('dean.pastYearASSList');
    Route::get('/PastYear/assessment/download/zipFiles/{course_id}/{download}','Dean\PastYearController@zipFileDownload');
    Route::get('/PastYear/assessment/name/download/zipFiles/{course_id}/{download}','Dean\PastYearController@zipFileDownloadName');
    Route::get('/PastYear/assessment/list/download/zipFiles/{ass_id}/{download}','Dean\PastYearController@zipFileDownloadList');
	Route::post('/PastYear/assessment/searchAssessment/', 'Dean\PastYearController@searchAssessment');
	Route::post('/PastYear/assessment/name/searchAssessmentName/', 'Dean\PastYearController@searchAssessmentName');
	Route::post('/PastYear/assessment/list/searchAssessmentlist/', 'Dean\PastYearController@searchAssessmentlist');
	Route::get('/PastYear/assessment/download/{ass_li_id}', 'Dean\PastYearController@downloadFiles');
	Route::get('/PastYear/assessment/view/whole_paper/{ass_id}', 'Dean\PastYearController@view_wholePaper');
	Route::get('/PastYear/images/assessment/{image_name}', [
	     'as'         => 'M_assessment_image',
	     'uses'       => 'Dean\PastYearController@assessmentImage',
	     'middleware' => 'auth',
	]);


	//Past year Final question
	Route::get('/PastYear/FinalAssessment/{id}','Dean\PastYearFinalController@PastYearAssessment')->name('dean.pastYearFinal');
	Route::get('/PastYear/FinalAssessment/{id}/assessment_name/{course_id}','Dean\PastYearFinalController@PastYearAssessmentName')->name('dean.pastYearASSName');
	Route::get('/PastYear/FinalAssessment/{id}/list/{fx_id}/','Dean\PastYearFinalController@PastYearAssessmentList')->name('dean.pastYearASSList');
	Route::get('/PastYear/FinalAssessment/download/zipFiles/{course_id}/{download}','Dean\PastYearFinalController@zipFileDownload');
	Route::get('/PastYear/FinalAssessment/name/download/zipFiles/{course_id}/{download}','Dean\PastYearFinalController@zipFileDownloadName');
	Route::get('/PastYear/FinalAssessment/list/download/zipFiles/{fx_id}/{download}','Dean\PastYearFinalController@zipFileDownloadList');
	Route::post('/PastYear/FinalAssessment/searchAssessment/', 'Dean\PastYearFinalController@searchAssessment');
	Route::post('/PastYear/FinalAssessment/name/searchAssessmentName/', 'Dean\PastYearFinalController@searchAssessmentName');
	Route::post('/PastYear/FinalAssessment/list/searchAssessmentlist/', 'Dean\PastYearFinalController@searchAssessmentlist');
	Route::get('/PastYear/FinalAssessment/download/{ass_fx_id}', 'Dean\PastYearFinalController@downloadFiles');
	Route::get('/PastYear/images/final_assessment/{image_name}', [
	     'as'         => 'assessment_final_image',
	     'uses'       => 'Dean\PastYearFinalController@FinalAssessmentImage',
	     'middleware' => 'auth',
	]);
	Route::get('/PastYear/final_assessment/view/whole_paper/{fx_id}', 'Dean\PastYearFinalController@view_wholePaper');


	//Past year CA Result
	Route::get('/PastYear/sampleResult/{id}/previous/{course_id}/{search}','Dean\PastYearController@PastYearResultAssessmentList')->name('dean.PastYearResultAssessmentList');
	Route::get('/PastYear/sampleResult/{id}/name/{ass_id}/{search}','Dean\PastYearController@PastYearStudentList')->name('dean.PastYearStudentList');
	Route::get('/PastYear/sampleResult/{id}/result/{ar_stu_id}','Dean\PastYearController@PastYearResultList')->name('dean.PastYearResultList');
	Route::get('/PastYear/assessment/sampleResult/download/zipFiles/{course_id}/{download}','Dean\PastYearController@zipFileDownloadResult');
	Route::get('/PastYear/sampleResult/list/download/zipFiles/{course_id}/{download}','Dean\PastYearController@zipFileDownloadResultList');
	Route::get('/PastYear/sampleResult/download/zipFiles/{ass_id}/{download}','Dean\PastYearController@zipFileDownloadStudent');
	Route::get('/PastYear/Student/{student_id}/download/zipFiles/{ass_id}/{download}', [
    'as' => 'zipFileDownloadDocument', 'uses' => 'Dean\PastYearController@zipFileDownloadDocument']);
	Route::post('/PastYear/assessment/sampleResult/searchSampleResult/', 'Dean\PastYearController@searchAssessmentSampleResult');
	Route::post('/PastYear/result/searchAssessmentResult/', 'Dean\PastYearController@searchAssessmentResult');
	Route::post('/PastYear/assessment/sampleResult/searchStudentList/', 'Dean\PastYearController@searchStudentList');
	Route::get('/PastYear/images/AssessmentResult/{image_name}', [
	     'as'         => 'M_assessmentResult_image',
	     'uses'       => 'Dean\PastYearController@assessmentResult_image',
	     'middleware' => 'auth',
	]);
	Route::get('/PastYear/AssessmentResult/view/whole_paper/{ar_stu_id}', 'Dean\PastYearController@view_wholePaperResult');
	Route::get('/PastYear/AssessmentResult/result/{ar_stu_id}','Dean\PastYearController@downloadDocument');


	//Past Year FInal Result
	Route::get('/PastYear/FinalSampleResult/{id}/previous/{course_id}/{search}','Dean\PastYearFinalController@PastYearStudentList')->name('dean.PastYearStudentList');
	Route::get('/PastYear/FinalSampleResult/{id}/result/{fxr_id}','Dean\PastYearFinalController@PastYearResultList')->name('dean.PastYearResultList');
	Route::get('/PastYear/FinalSampleResult/download/zipFiles/{course_id}/{download}','Dean\PastYearFinalController@zipFileDownloadResult');
	Route::get('/PastYear/FinalSampleResult/Student/{student_id}/download/zipFiles/{course_id}/{download}', [
    'as' => 'zipFileDownloadDocument', 'uses' => 'Dean\PastYearFinalController@zipFileDownloadDocument']);
	Route::get('/PastYear/FinalSampleResult/student/download/zipFiles/{course_id}/{download}','Dean\PastYearFinalController@zipFileDownloadStudent');
	Route::post('/PastYear/FinalSampleResult/searchAssessmentResult/', 'Dean\PastYearFinalController@searchAssessmentResult');
	Route::post('/PastYear/FinalSampleResult/searchStudentList/', 'Dean\PastYearFinalController@searchStudentList');
	Route::get('/PastYear/images/FinalResult/{image_name}', [
	     'as'         => 'FinalResult_image',
	     'uses'       => 'Dean\PastYearFinalController@FinalResult_image',
	     'middleware' => 'auth',
	]);
	Route::get('/PastYear/FinalResult/view/whole_paper/{fxr_id}', 'Dean\PastYearFinalController@view_wholePaperResult');
	Route::get('/PastYear/FinalSampleResult/download/{fxr_id}', 'Dean\PastYearFinalController@downloadFilesResult');


	//Past Year Lecturer Note
	Route::get('/PastYearNote/{id}','Dean\PastYearNoteController@PastYearNote')->name('dean.pastYearNote');
	Route::get('/PastYearNote/{id}/{view}/{view_id}','Dean\PastYearNoteController@PastYearNoteViewIn')->name('dean.PastYearNoteViewIn');
	Route::post('/PastYear/lectureNote/searchFiles', 'Dean\PastYearNoteController@searchLecturerNote');
	Route::post('/PastYear/lectureNote/searchPreviousFiles', 'Dean\PastYearNoteController@searchLecturerNotePrevious');
	Route::get('/PastYearNote/download/zipFiles/{course_id}/{download}','Dean\PastYearNoteController@zipFileDownload');
	Route::get('/PastYear/images/lectureNote/{ln_id}/{image_name}', [
	     'as'         => 'lectureNote_image',
	     'uses'       => 'Dean\PastYearNoteController@LectureNoteImage',
	     'middleware' => 'auth',
	]);
	Route::get('/PastYear/lectureNote/download/{id}','Dean\PastYearNoteController@downloadLN');

	//Past Year TP
	Route::get('/PastYearTP/{id}','Dean\PastYearTPController@PastYearTP')->name('dean.pastYearTP');
	Route::get('/PastYearTP/{id}/course/{view_id}','Dean\PastYearTPController@PastYearTPDownload')->name('dean.PastYearTPDownload');
	Route::get('/PastYearTP/download/zipFiles/{course_id}/{checked}','Dean\PastYearTPController@downloadZipFiles');
	Route::post('/PastYearTP/searchFiles', 'Dean\PastYearTPController@searchPastYearTP');

	//Moderator
	Route::get('Moderator','Dean\Moderator\M_CourseController@index');
	Route::post('/searchModeratorCourse', 'Dean\Moderator\M_CourseController@searchModeratorCourse');
	Route::get('/Moderator/course/{id}','Dean\Moderator\M_CourseController@ModeratorAction');
	//Moderator Student list
	Route::get('/Moderator/assign/student/{id}','Dean\Moderator\M_StudentListController@ModeratorStudent');
	Route::post('/searchModeratorStudent', 'Dean\Moderator\M_StudentListController@searchModeratorStudent');
	//Moderator Lecture Note
	Route::get('/Moderator/lectureNote/{id}','Dean\Moderator\M_LectureNoteController@ModeratorLectureNote');
	Route::post('/Moderator/lectureNote/searchFiles', 'Dean\Moderator\M_LectureNoteController@searchModeratorLN');
	Route::get('/Moderator/lectureNote/folder/{ln_id}','Dean\Moderator\M_LectureNoteController@ModeratorLNFolderView');
	Route::get('/Moderator/images/lectureNote/{ln_id}/{image_name}', [
	'as'         => 'lectureNote_image',
	'uses'       => 'Dean\Moderator\M_LectureNoteController@LectureNoteImage',
	'middleware' => 'auth',
	]);
	Route::get('/Moderator/lectureNote/download/{id}','Dean\Moderator\M_LectureNoteController@downloadLN');
	//Moderator Teaching Plan
	Route::get('/Moderator/teachingPlan/{id}','Dean\Moderator\M_TeachingPlanController@ModeratorTeachingPlan');
	Route::post('/Moderator/teachingPlan/verify/','Dean\Moderator\M_TeachingPlanController@M_TP_VerifyAction');
	Route::get('/Moderator/teachingPlan/report/{id}', 'Dean\Moderator\M_TeachingPlanController@TPDownload');
	//Moderator Assessment
	Route::get('/Moderator/viewAssessment/{id}','Dean\Moderator\M_AssessmentController@viewAssessment');
	Route::post('/Moderator/assessment/getSyllabusData', 'Dean\Moderator\M_AssessmentController@getSyllabusData');
	Route::get('/Moderator/assessment/create/{id}/question/{coursework}/{question}', [
    'as' => 'createQuestion', 'uses' => 'Dean\Moderator\M_AssessmentController@create_question']);
	Route::get('/Moderator/assessment/view_list/{ass_id}', 'Dean\Moderator\M_AssessmentController@assessment_list_view')->name('dean.ass.assessment_list_view');
	Route::get('/Moderator/assessment/view/whole_paper/{ass_id}', 'Dean\Moderator\M_AssessmentController@view_wholePaper');
	Route::get('/Moderator/images/assessment/{image_name}', [
	     'as'         => 'assessment_image',
	     'uses'       => 'Dean\Moderator\M_AssessmentController@assessmentImage',
	     'middleware' => 'auth',
	]);
	Route::post('/Moderator/assessment/searchKey/', 'Dean\Moderator\M_AssessmentController@searchKey');
    Route::post('/Moderator/assessment/searchAssessmentList/', 'Dean\Moderator\M_AssessmentController@searchAssessmentList');
    Route::get('/Moderator/assessment/download/{ass_li_id}', 'Dean\Moderator\M_AssessmentController@downloadFiles');

    //Assessment Result
    Route::get('/Moderator/AssessmentResult/{id}/question/{question}', [
    'as' => 'viewAssessmentStudentResult', 'uses' => 'Dean\Moderator\M_AssessmentResultController@viewAssessmentStudentResult']);
	Route::get('/Moderator/AssessmentResult/studentResult/{ass_id}/', [
    'as' => 'viewstudentlist', 'uses' => 'Dean\Moderator\M_AssessmentResultController@viewstudentlist']);
    Route::get('/Moderator/AssessmentResult/view/student/{ar_stu_id}/', [
    'as' => 'viewStudentResult', 'uses' => 'Dean\Moderator\M_AssessmentResultController@viewStudentResult']);
    Route::post('/Moderator/AssessmentResult/searchAssessmentForm/', 'Dean\Moderator\M_AssessmentResultController@searchAssessmentForm');
    Route::post('/Moderator/AssessmentResult/searchStudentList/', 'Dean\Moderator\M_AssessmentResultController@searchStudentList');
    Route::get('/Moderator/images/AssessmentResult/{image_name}', [
	     'as'         => 'assessmentResult_image',
	     'uses'       => 'Dean\Moderator\M_AssessmentResultController@assessmentResult_image',
	     'middleware' => 'auth',
	]);
	Route::get('/Moderator/AssessmentResult/view/whole_paper/{ar_stu_id}', 'Dean\Moderator\M_AssessmentResultController@view_wholePaper');
	Route::get('/Moderator/AssessmentResult/result/{ar_stu_id}','Dean\Moderator\M_AssessmentResultController@downloadDocument')->name('dean.downloadStudentResult');
	//Moderator Assessment
	Route::get('/Moderator/Assessment/{id}','Dean\Moderator\M_AssessmentController@ModeratorAssessment');
	Route::post('/Moderator/Assessment/Moderation/','Dean\Moderator\M_AssessmentController@M_Ass_Moderate_Action');
	Route::get('/Moderator/Assessment/report/{actionCA_id}','Dean\Moderator\M_AssessmentController@ModerationFormReport');

	//Final Assessment
	Route::get('/Moderator/FinalExam/{id}/', [
    'as' => 'FinalExamination', 'uses' => 'Dean\Moderator\M_FinalExamController@viewFinalExamination']);
    Route::post('/Moderator/FinalExamination/getSyllabusData', 'Dean\Moderator\M_FinalExamController@getSyllabusData');
    Route::get('/Moderator/FinalExamination/question/{coursework}/{id}/', [
    'as' => 'createQuestion', 'uses' => 'Dean\Moderator\M_FinalExamController@create_question']);
    
	Route::get('/Moderator/FinalExamination/view_list/{fx_id}', 'Dean\Moderator\M_FinalExamController@final_assessment_list_view')->name('dean.final.final_assessment_list_view');
	Route::get('/Moderator/images/final_assessment/{image_name}', [
	     'as'         => 'assessment_final_image',
	     'uses'       => 'Dean\Moderator\M_FinalExamController@FinalAssessmentImage',
	     'middleware' => 'auth',
	]);
	Route::post('/Moderator/FinalExamination/searchAssessmentList/', 'Dean\Moderator\M_FinalExamController@searchAssessmentList');
	Route::post('/Moderator/FinalExamination/searchKey/', 'Dean\Moderator\M_FinalExamController@searchKey');
	Route::get('/Moderator/final_assessment/view/whole_paper/{fx_id}', 'Dean\Moderator\M_FinalExamController@view_wholePaper');
	Route::get('/Moderator/FinalExamination/download/{ass_fx_id}', 'Dean\Moderator\M_FinalExamController@downloadFiles');
	//Final Assessment Result
	Route::get('/Moderator/FinalResult/{id}', [
    'as' => 'viewFinalResult', 'uses' => 'Dean\Moderator\M_FinalExamResultController@viewFinalResult']);
	Route::get('/Moderator/FinalResult/view/student/{fxr_id}/', [
    'as' => 'viewFinalStudentResult', 'uses' => 'Dean\Moderator\M_FinalExamResultController@viewFinalStudentResult']);
    Route::get('/Moderator/FinalResult/result/{fxr_id}','Dean\Moderator\M_FinalExamResultController@downloadDocument')->name('dean.downloadStudentResult');
    Route::get('/Moderator/images/FinalResult/{image_name}', [
	     'as'         => 'FinalResult_image',
	     'uses'       => 'Dean\Moderator\M_FinalExamResultController@FinalResult_image',
	     'middleware' => 'auth',
	]);
	Route::get('/Moderator/FinalResult/view/whole_paper/{fxr_id}', 'Dean\Moderator\M_FinalExamResultController@view_wholePaper');
	Route::post('/Moderator/FinalResult/searchStudentList/', 'Dean\Moderator\M_FinalExamResultController@searchStudentList');

	//Moderator Final Assessment
	Route::get('/Moderator/FinalExamination/{id}','Dean\Moderator\M_FinalExamController@ModeratorFinalExam');
	Route::post('/Moderator/FinalExamination/Moderation/','Dean\Moderator\M_FinalExamController@M_FX_Moderate_Action');
	Route::get('/Moderator/FinalExamination/report/{actionFA_id}','Dean\Moderator\M_FinalExamController@ModerationFormReport');
	//Moderator E_PortFolio
	Route::get('/Moderator/E_Portfolio/{id}', [
    'as' => 'viewE_Portfolio', 'uses' => 'Dean\Moderator\E_PortfolioController@viewE_Portfolio']);
    Route::get('/Moderator/E_Portfolio/report/{id}', [
    'as' => 'Download_E_Portfolio', 'uses' => 'Dean\Moderator\E_PortfolioController@Download_E_Portfolio']);

    //Moderator Timetable
    Route::get('/Moderator/timetable/{id}', [
    'as' => 'M_timetable', 'uses' => 'Dean\Moderator\M_TimetableController@viewTimetable']);

    //Moderator Attendance
    Route::get('/Moderator/Attendance/{id}', [
    'as' => 'M_Attendance', 'uses' => 'Dean\Moderator\M_AttendanceController@viewAttendance']);
    Route::get('/Moderator/Attendance/{id}/student_list/{date}', 'Dean\Moderator\M_AttendanceController@viewStudentList');

   	//Moderator Past Year CA Question
    Route::get('/Moderator/PastYear/assessment/{id}','Dean\Moderator\M_PastYearController@PastYearAssessment');
    Route::get('/Moderator/PastYear/assessment/{id}/assessment_name/{course_id}','Dean\Moderator\M_PastYearController@PastYearAssessmentName');
    Route::get('/Moderator/PastYear/assessment/{id}/list/{ass_id}/','Dean\Moderator\M_PastYearController@PastYearAssessmentList');
	Route::post('/Moderator/PastYear/assessment/searchAssessment/', 'Dean\Moderator\M_PastYearController@searchAssessment');
	Route::post('/Moderator/PastYear/assessment/name/searchAssessmentName/', 'Dean\Moderator\M_PastYearController@searchAssessmentName');
	Route::post('/Moderator/PastYear/assessment/list/searchAssessmentlist/', 'Dean\Moderator\M_PastYearController@searchAssessmentlist');
	Route::get('/Moderator/PastYear/assessment/view/whole_paper/{ass_id}', 'Dean\Moderator\M_PastYearController@view_wholePaper');
	Route::get('/Moderator/PastYear/images/assessment/{image_name}', [
	     'as'         => 'M_assessment_image',
	     'uses'       => 'Dean\Moderator\M_PastYearController@assessmentImage',
	     'middleware' => 'auth',
	]);
	Route::get('/Moderator/PastYear/assessment/download/{ass_li_id}', 
		'Dean\Moderator\M_PastYearController@downloadFiles');

	//Moderator Past year Final question
	Route::get('/Moderator/PastYear/FinalAssessment/{id}','Dean\Moderator\M_PastYearFinalController@PastYearAssessment');
	Route::get('/Moderator/PastYear/FinalAssessment/{id}/assessment_name/{course_id}','Dean\Moderator\M_PastYearFinalController@PastYearAssessmentName');
	Route::get('/Moderator/PastYear/FinalAssessment/{id}/list/{fx_id}/','Dean\Moderator\M_PastYearFinalController@PastYearAssessmentList');
	Route::post('/Moderator/PastYear/FinalAssessment/searchAssessment/', 'Dean\Moderator\M_PastYearFinalController@searchAssessment');
	Route::post('/Moderator/PastYear/FinalAssessment/name/searchAssessmentName/', 'Dean\Moderator\M_PastYearFinalController@searchAssessmentName');
	Route::post('/Moderator/PastYear/FinalAssessment/list/searchAssessmentlist/', 'Dean\Moderator\M_PastYearFinalController@searchAssessmentlist');
	Route::get('/Moderator/PastYear/FinalAssessment/download/{ass_fx_id}', 'Dean\Moderator\M_PastYearFinalController@downloadFiles');
	Route::get('/Moderator/PastYear/images/final_assessment/{image_name}', [
	     'as'         => 'M_assessment_final_image',
	     'uses'       => 'Dean\Moderator\M_PastYearFinalController@FinalAssessmentImage',
	     'middleware' => 'auth',
	]);
	Route::get('/Moderator/PastYear/final_assessment/view/whole_paper/{fx_id}', 'Dean\Moderator\M_PastYearFinalController@view_wholePaper');


	//Moderator Past year CA Result
	Route::get('/Moderator/PastYear/sampleResult/{id}/previous/{course_id}/{search}','Dean\Moderator\M_PastYearController@PastYearResultAssessmentList');
	Route::get('/Moderator/PastYear/sampleResult/{id}/name/{ass_id}/{search}','Dean\Moderator\M_PastYearController@PastYearStudentList');
	Route::get('/Moderator/PastYear/sampleResult/{id}/result/{ar_stu_id}','Dean\Moderator\M_PastYearController@PastYearResultList');
	Route::post('/Moderator/PastYear/assessment/sampleResult/searchSampleResult/', 'Dean\Moderator\M_PastYearController@searchAssessmentSampleResult');
	Route::post('/Moderator/PastYear/result/searchAssessmentResult/', 'Dean\Moderator\M_PastYearController@searchAssessmentResult');
	Route::post('/Moderator/PastYear/assessment/sampleResult/searchStudentList/', 'Dean\Moderator\M_PastYearController@searchStudentList');
	Route::get('/Moderator/PastYear/images/AssessmentResult/{image_name}', [
	     'as'         => 'M_assessmentResult_image',
	     'uses'       => 'Dean\Moderator\M_PastYearController@assessmentResult_image',
	     'middleware' => 'auth',
	]);
	Route::get('/Moderator/PastYear/AssessmentResult/view/whole_paper/{ar_stu_id}', 'Dean\Moderator\M_PastYearController@view_wholePaperResult');
	Route::get('/Moderator/PastYear/AssessmentResult/result/{ar_stu_id}','Dean\Moderator\M_PastYearController@downloadDocument');

	//Moderator Past Year FInal Result
	Route::get('/Moderator/PastYear/FinalSampleResult/{id}/previous/{course_id}/{search}','Dean\Moderator\M_PastYearFinalController@PastYearStudentList');
	Route::get('/Moderator/PastYear/FinalSampleResult/{id}/result/{fxr_id}','Dean\Moderator\M_PastYearFinalController@PastYearResultList');
	Route::post('/Moderator/PastYear/FinalSampleResult/searchAssessmentResult/', 'Dean\Moderator\M_PastYearFinalController@searchAssessmentResult');
	Route::post('/Moderator/PastYear/FinalSampleResult/searchStudentList/', 'Dean\Moderator\M_PastYearFinalController@searchStudentList');
	Route::get('/Moderator/PastYear/images/FinalResult/{image_name}', [
	     'as'         => 'M_FinalResult_image',
	     'uses'       => 'Dean\Moderator\M_PastYearFinalController@FinalResult_image',
	     'middleware' => 'auth',
	]);
	Route::get('/Moderator/PastYear/FinalResult/view/whole_paper/{fxr_id}', 'Dean\Moderator\M_PastYearFinalController@view_wholePaperResult');
	Route::get('/Moderator/PastYear/FinalSampleResult/download/{fxr_id}', 'Dean\Moderator\M_PastYearFinalController@downloadFilesResult');


	//Moderator Past Year Lecturer Note
	Route::get('/Moderator/PastYearNote/{id}','Dean\Moderator\M_PastYearNoteController@PastYearNote');
	Route::get('/Moderator/PastYearNote/{id}/{view}/{view_id}','Dean\Moderator\M_PastYearNoteController@PastYearNoteViewIn');
	Route::post('/Moderator/PastYear/lectureNote/searchFiles', 'Dean\Moderator\M_PastYearNoteController@searchLecturerNote');
	Route::post('/Moderator/PastYear/lectureNote/searchPreviousFiles', 'Dean\Moderator\M_PastYearNoteController@searchLecturerNotePrevious');
	Route::get('/Moderator/PastYear/images/lectureNote/{ln_id}/{image_name}', [
	'as'         => 'M_lectureNote_image',
	'uses'       => 'Dean\Moderator\M_PastYearNoteController@LectureNoteImage',
	'middleware' => 'auth',
	]);
	Route::get('/Moderator/PastYear/lectureNote/download/{id}','Dean\Moderator\M_PastYearNoteController@downloadLN');


	//Moderator Past Year TP
	Route::get('/Moderator/PastYearTP/{id}','Dean\Moderator\M_PastYearTPController@PastYearTP');
	Route::get('/Moderator/PastYearTP/{id}/course/{view_id}','Dean\Moderator\M_PastYearTPController@PastYearTPDownload');
	Route::post('/Moderator/PastYearTP/searchFiles', 'Dean\Moderator\M_PastYearTPController@searchPastYearTP');

	//Reviewer
	Route::get('Reviewer','Dean\Dean\D_CourseController@index');
	Route::post('/searchCourse', 'Dean\Dean\D_CourseController@searchCourse');
	Route::get('/Reviewer/course/{id}','Dean\Dean\D_CourseController@DeanAction');
	//Dean Student list
	Route::get('/Reviewer/assign/student/{id}','Dean\Dean\D_StudentListController@DeanStudent');
	Route::post('/searchDeanStudent', 'Dean\Dean\D_StudentListController@searchDeanStudent');
	//Dean Lecture Note
	Route::get('/Reviewer/lectureNote/{id}','Dean\Dean\D_LectureNoteController@DeanLectureNote');
	Route::post('/Reviewer/lectureNote/searchFiles', 'Dean\Dean\D_LectureNoteController@searchDeanLN');
	Route::get('/Reviewer/lectureNote/folder/{ln_id}','Dean\Dean\D_LectureNoteController@DeanLNFolderView');
	Route::get('/Reviewer/images/lectureNote/{ln_id}/{image_name}', [
		'as'         => 'lectureNote_image',
		'uses'       => 'Dean\Dean\D_LectureNoteController@LectureNoteImage',
		'middleware' => 'auth',
	]);
	Route::get('/Reviewer/lectureNote/download/{id}','Dean\Dean\D_LectureNoteController@downloadLN');
	//Dean Teaching Plan
	Route::get('/Reviewer/teachingPlan/{id}','Dean\Dean\D_TeachingPlanController@DeanTeachingPlan');
	Route::post('/Reviewer/teachingPlan/approve/','Dean\Dean\D_TeachingPlanController@D_TP_VerifyAction');
	Route::get('/Reviewer/teachingPlan/report/{id}', 'Dean\Dean\D_TeachingPlanController@TPDownload');
	//Dean Assessment
	Route::get('/Reviewer/viewAssessment/{id}','Dean\Dean\D_AssessmentController@viewAssessment');
	Route::post('/Reviewer/assessment/getSyllabusData', 'Dean\Dean\D_AssessmentController@getSyllabusData');
	Route::get('/Reviewer/assessment/create/{id}/question/{coursework}/{question}', [
    'as' => 'createQuestion', 'uses' => 'Dean\Dean\D_AssessmentController@create_question']);
	Route::get('/Reviewer/assessment/view_list/{ass_id}', 'Dean\Dean\D_AssessmentController@assessment_list_view')->name('dean.ass.assessment_list_view');
	Route::get('/Reviewer/assessment/view/whole_paper/{ass_id}', 'Dean\Dean\D_AssessmentController@view_wholePaper');
	Route::get('/Reviewer/images/assessment/{image_name}', [
	     'as'         => 'assessment_image',
	     'uses'       => 'Dean\Dean\D_AssessmentController@assessmentImage',
	     'middleware' => 'auth',
	]);
	Route::post('/Reviewer/assessment/searchKey/', 'Dean\Dean\D_AssessmentController@searchKey');
    Route::post('/Reviewer/assessment/searchAssessmentList/', 'Dean\Dean\D_AssessmentController@searchAssessmentList');
    Route::get('/Reviewer/assessment/download/{ass_li_id}', 'Dean\Dean\D_AssessmentController@downloadFiles');

    //Assessment Result
    Route::get('/Reviewer/AssessmentResult/{id}/question/{question}', [
    'as' => 'viewAssessmentStudentResult', 'uses' => 'Dean\Dean\D_AssessmentResultController@viewAssessmentStudentResult']);
	Route::get('/Reviewer/AssessmentResult/studentResult/{ass_id}/', [
    'as' => 'viewstudentlist', 'uses' => 'Dean\Dean\D_AssessmentResultController@viewstudentlist']);
    Route::get('/Reviewer/AssessmentResult/view/student/{ar_stu_id}/', [
    'as' => 'viewStudentResult', 'uses' => 'Dean\Dean\D_AssessmentResultController@viewStudentResult']);
    Route::post('/Reviewer/AssessmentResult/searchAssessmentForm/', 'Dean\Dean\D_AssessmentResultController@searchAssessmentForm');
    Route::post('/Reviewer/AssessmentResult/searchStudentList/', 'Dean\Dean\D_AssessmentResultController@searchStudentList');
    Route::get('/Reviewer/images/AssessmentResult/{image_name}', [
	     'as'         => 'assessmentResult_image',
	     'uses'       => 'Dean\Dean\D_AssessmentResultController@assessmentResult_image',
	     'middleware' => 'auth',
	]);
	Route::get('/Reviewer/AssessmentResult/view/whole_paper/{ar_stu_id}', 'Dean\Dean\D_AssessmentResultController@view_wholePaper');
	Route::get('/Reviewer/AssessmentResult/result/{ar_stu_id}','Dean\Dean\D_AssessmentResultController@downloadDocument')->name('dean.downloadStudentResult');

	Route::get('/Reviewer/Assessment/{id}','Dean\Dean\D_AssessmentController@DeanAssessment');
	Route::post('/Reviewer/Assessment/approve/','Dean\Dean\D_AssessmentController@D_Ass_Verify_Action');
	Route::get('/Reviewer/Assessment/report/{actionCA_id}','Dean\Dean\D_AssessmentController@ModerationFormReport');

	//Dean Final Assessment
	Route::get('/Reviewer/FinalExam/{id}/', [
    'as' => 'FinalExamination', 'uses' => 'Dean\Dean\D_FinalExamController@viewFinalExamination']);
    Route::post('/Reviewer/FinalExamination/getSyllabusData', 'Dean\Dean\D_FinalExamController@getSyllabusData');
    Route::get('/Reviewer/FinalExamination/question/{coursework}/{id}/', [
    'as' => 'createQuestion', 'uses' => 'Dean\Dean\D_FinalExamController@create_question']);
    
	Route::get('/Reviewer/FinalExamination/view_list/{fx_id}', 'Dean\Dean\D_FinalExamController@final_assessment_list_view')->name('dean.final.final_assessment_list_view');
	Route::get('/Reviewer/images/final_assessment/{image_name}', [
	     'as'         => 'assessment_final_image',
	     'uses'       => 'Dean\Dean\D_FinalExamController@FinalAssessmentImage',
	     'middleware' => 'auth',
	]);
	Route::post('/Reviewer/FinalExamination/searchAssessmentList/', 'Dean\Dean\D_FinalExamController@searchAssessmentList');
	Route::post('/Reviewer/FinalExamination/searchKey/', 'Dean\Dean\D_FinalExamController@searchKey');
	Route::get('/Reviewer/final_assessment/view/whole_paper/{fx_id}', 'Dean\Dean\D_FinalExamController@view_wholePaper');
	Route::get('/Reviewer/FinalExamination/download/{ass_fx_id}', 'Dean\Dean\D_FinalExamController@downloadFiles');
	//Final Assessment Result
	Route::get('/Reviewer/FinalResult/{id}', [
    'as' => 'viewFinalResult', 'uses' => 'Dean\Dean\D_FinalExamResultController@viewFinalResult']);
	Route::get('/Reviewer/FinalResult/view/student/{fxr_id}/', [
    'as' => 'viewFinalStudentResult', 'uses' => 'Dean\Dean\D_FinalExamResultController@viewFinalStudentResult']);
    Route::get('/Reviewer/FinalResult/result/{fxr_id}','Dean\Dean\D_FinalExamResultController@downloadDocument')->name('dean.downloadStudentResult');
    Route::get('/Reviewer/images/FinalResult/{image_name}', [
	     'as'         => 'FinalResult_image',
	     'uses'       => 'Dean\Dean\D_FinalExamResultController@FinalResult_image',
	     'middleware' => 'auth',
	]);
	Route::get('/Reviewer/FinalResult/view/whole_paper/{fxr_id}', 'Dean\Dean\D_FinalExamResultController@view_wholePaper');
	Route::post('/Reviewer/FinalResult/searchStudentList/', 'Dean\Dean\D_FinalExamResultController@searchStudentList');
	
	Route::get('/Reviewer/FinalExamination/{id}','Dean\Dean\D_FinalExamController@DeanFinalExam');	
	Route::post('/Reviewer/FinalExamination/approve/','Dean\Dean\D_FinalExamController@D_FX_Approve_Action');
	Route::get('/Reviewer/FinalExamination/report/{actionFA_id}','Dean\Dean\D_FinalExamController@ModerationFormReport');

	//Dean E_PortFolio
	Route::get('/Reviewer/E_Portfolio/{id}', [
    'as' => 'viewE_Portfolio', 'uses' => 'Dean\Dean\E_PortfolioController@viewE_Portfolio']);
    Route::get('/Reviewer/E_Portfolio/report/{id}', [
    'as' => 'Download_E_Portfolio', 'uses' => 'Dean\Dean\E_PortfolioController@Download_E_Portfolio']);

    //Dean Timetable
    Route::get('/Reviewer/timetable/{id}', [
    'as' => 'view_Timetable', 'uses' => 'Dean\Dean\D_TimetableController@viewTimetable']);

    //Dean Attendance
    Route::get('/Reviewer/Attendance/{id}', [
    'as' => 'M_Attendance', 'uses' => 'Dean\Dean\D_AttendanceController@viewAttendance']);
    Route::get('/Reviewer/Attendance/{id}/student_list/{date}', 'Dean\Dean\D_AttendanceController@viewStudentList');
?>