<?php
	
	//Detail
	Route::get('dean/home', 'HomeController@deanHome')->name('dean.home');
	Route::get('images/home_image/{user_id}', [
	     'as'         => 'home_image',
	     'uses'       => 'HomeController@deanDetails',
	     'middleware' => 'auth',
	]);
	Route::get('images/profile/{image_name}', [
	     'as'         => 'profile_image',
	     'uses'       => 'StaffController@profileImage',
	     'middleware' => 'auth',
	]);

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

	//Course PortFolio
	Route::get('/CoursePortFolio', 'Dean\C_PortFolioController@index')->name('dean.C_potrfolio.index');
	

	// CourseList
	Route::post('/uploadCourses', 'Dean\CourseController@importExcel')->name('dropzone.uploadCourses');
	Route::post('/course/excel/create', 'Dean\CourseController@storeCourses')->name('course.excel.submit');
	Route::get('course_list','Dean\CourseController@index')->name('dean.course_list.index');
	Route::get('course/create','Dean\CourseController@create')->name('course.create');
	Route::post('course/create', 'Dean\CourseController@store')->name('course.submit');
	Route::post('/courseSubject', 'Dean\CourseController@courseSubject');
	Route::get('/course/{id}','Dean\CourseController@edit')->name('course.edit');
	Route::post('/course/{id}','Dean\CourseController@update')->name('course.update.submit');
	Route::get('/course/remove/{id}', 'Dean\CourseController@removeActiveCourse');
	Route::post('/searchTeachCourse', 'Dean\CourseController@searchTeachCourse');
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
	Route::get('images/lectureNote/{image_name}', [
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


	//Past year CA Result
	Route::get('/PastYear/sampleResult/{id}/previous/{course_id}/{search}','Dean\PastYearController@PastYearResultAssessmentList')->name('dean.PastYearResultAssessmentList');
	Route::get('/PastYear/sampleResult/{id}/name/{ass_id}/{search}','Dean\PastYearController@PastYearStudentList')->name('dean.PastYearStudentList');
	Route::get('/PastYear/sampleResult/{id}/result/{ar_stu_id}','Dean\PastYearController@PastYearResultList')->name('dean.PastYearResultList');
	Route::get('/PastYear/assessment/sampleResult/download/zipFiles/{course_id}/{download}','Dean\PastYearController@zipFileDownloadResult');
	Route::get('/PastYear/sampleResult/list/download/zipFiles/{course_id}/{download}','Dean\PastYearController@zipFileDownloadResultList');
	Route::get('/PastYear/sampleResult/download/zipFiles/{ass_id}/{download}','Dean\PastYearController@zipFileDownloadStudent');
	Route::get('/PastYear/Student/{student_id}/download/zipFiles/{ass_id}/{download}', [
    'as' => 'zipFileDownloadDocument', 'uses' => 'Dean\PastYearController@zipFileDownloadDocument']);
    Route::get('/PastYear/sampleResult/download/{ar_stu_id}', 'Dean\PastYearController@downloadFilesResult');
	Route::post('/PastYear/assessment/sampleResult/searchSampleResult/', 'Dean\PastYearController@searchAssessmentSampleResult');
	Route::post('/PastYear/result/searchAssessmentResult/', 'Dean\PastYearController@searchAssessmentResult');
	Route::post('/PastYear/assessment/sampleResult/searchStudentList/', 'Dean\PastYearController@searchStudentList');

	//Past Year FInal Result
	Route::get('/PastYear/FinalSampleResult/{id}/previous/{course_id}/{search}','Dean\PastYearFinalController@PastYearStudentList')->name('dean.PastYearStudentList');
	Route::get('/PastYear/FinalSampleResult/{id}/result/{fxr_id}','Dean\PastYearFinalController@PastYearResultList')->name('dean.PastYearResultList');

	Route::get('/PastYear/FinalSampleResult/download/zipFiles/{course_id}/{download}','Dean\PastYearFinalController@zipFileDownloadResult');
	Route::get('/PastYear/FinalSampleResult/Student/{student_id}/download/zipFiles/{course_id}/{download}', [
    'as' => 'zipFileDownloadDocument', 'uses' => 'Dean\PastYearFinalController@zipFileDownloadDocument']);
	Route::get('/PastYear/FinalSampleResult/student/download/zipFiles/{course_id}/{download}','Dean\PastYearFinalController@zipFileDownloadStudent');
	Route::get('/PastYear/FinalSampleResult/download/{fxr_id}', 'Dean\PastYearFinalController@downloadFilesResult');
	Route::post('/PastYear/FinalSampleResult/searchAssessmentResult/', 'Dean\PastYearFinalController@searchAssessmentResult');
	Route::post('/PastYear/FinalSampleResult/searchStudentList/', 'Dean\PastYearFinalController@searchStudentList');


	//Past Year Lecturer Note
	Route::get('/PastYearNote/{id}','Dean\PastYearNoteController@PastYearNote')->name('dean.pastYearNote');
	Route::get('/PastYearNote/{id}/{view}/{view_id}','Dean\PastYearNoteController@PastYearNoteViewIn')->name('dean.PastYearNoteViewIn');
	Route::post('/PastYear/lectureNote/searchFiles', 'Dean\PastYearNoteController@searchLecturerNote');
	Route::post('/PastYear/lectureNote/searchPreviousFiles', 'Dean\PastYearNoteController@searchLecturerNotePrevious');
	Route::get('/PastYearNote/download/zipFiles/{course_id}/{download}','Dean\PastYearNoteController@zipFileDownload');

	Route::get('PastYearTP/{id}','Dean\PastYearTPController@PastYearTP')->name('dean.pastYearTP');
	Route::get('PastYearTP/{id}/course/{view_id}','Dean\PastYearTPController@PastYearTPDownload')->name('dean.PastYearTPDownload');

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
	//Moderator Teaching Plan
	Route::get('/Moderator/teachingPlan/{id}','Dean\Moderator\M_TeachingPlanController@ModeratorTeachingPlan');
	Route::post('/Moderator/teachingPlan/verify/','Dean\Moderator\M_TeachingPlanController@M_TP_VerifyAction');
	//Moderator Assessment
	Route::get('/Moderator/Assessment/{id}','Dean\Moderator\M_AssessmentController@ModeratorAssessment');
	Route::post('/Moderator/Assessment/Moderation/','Dean\Moderator\M_AssessmentController@M_Ass_Moderate_Action');
	Route::get('/Moderator/Assessment/report/{actionCA_id}','Dean\Moderator\M_AssessmentController@ModerationFormReport');
	//Moderator Final Assessment
	Route::get('/Moderator/FinalExamination/{id}','Dean\Moderator\M_FinalExamController@ModeratorFinalExam');
	Route::post('/Moderator/FinalExamination/Moderation/','Dean\Moderator\M_FinalExamController@M_FX_Moderate_Action');
	Route::get('/Moderator/FinalExamination/report/{actionFA_id}','Dean\Moderator\M_FinalExamController@ModerationFormReport');

	//Dean
	Route::get('Dean','Dean\Dean\D_CourseController@index');
	Route::post('/searchCourse', 'Dean\Dean\D_CourseController@searchCourse');
	Route::get('/Dean/course/{id}','Dean\Dean\D_CourseController@DeanAction');
	//Dean Student list
	Route::get('/Dean/assign/student/{id}','Dean\Dean\D_StudentListController@DeanStudent');
	Route::post('/searchDeanStudent', 'Dean\Dean\D_StudentListController@searchDeanStudent');
	//Dean Lecture Note
	Route::get('/Dean/lectureNote/{id}','Dean\Dean\D_LectureNoteController@DeanLectureNote');
	Route::post('/Dean/lectureNote/searchFiles', 'Dean\Dean\D_LectureNoteController@searchDeanLN');
	Route::get('/Dean/lectureNote/folder/{ln_id}','Dean\Dean\D_LectureNoteController@DeanLNFolderView');
	//Dean Teaching Plan
	Route::get('/Dean/teachingPlan/{id}','Dean\Dean\D_TeachingPlanController@DeanTeachingPlan');
	Route::post('/Dean/teachingPlan/approve/','Dean\Dean\D_TeachingPlanController@D_TP_VerifyAction');
	//Dean Assessment
	Route::get('/Dean/Assessment/{id}','Dean\Dean\D_AssessmentController@DeanAssessment');
	Route::post('/Dean/Assessment/approve/','Dean\Dean\D_AssessmentController@D_Ass_Verify_Action');
	Route::get('/Dean/Assessment/report/{actionCA_id}','Dean\Dean\D_AssessmentController@ModerationFormReport');
	//Dean Final Assessment
	Route::get('/Dean/FinalExamination/{id}','Dean\Dean\D_FinalExamController@DeanFinalExam');
	Route::post('/Dean/FinalExamination/verify/','Dean\Dean\D_FinalExamController@D_FX_Verify_Action');
	Route::post('/Dean/FinalExamination/approve/','Dean\Dean\D_FinalExamController@D_FX_Approve_Action');
	Route::get('/Dean/FinalExamination/report/{actionFA_id}','Dean\Dean\D_FinalExamController@ModerationFormReport');
?>