<?php
$character = "/students";
Route::get($character.'/home', 'HomeController@index')->name('home');

Route::get($character.'/images/home_image/{user_id}', [
	     'as'         => 'student.home_image',
	     'uses'       => 'HomeController@studentDetails',
	     'middleware' => 'auth',
]);

Route::get($character.'/profile/', 'Student\ProfileController@profile')->name('student.Profile');

Route::get($character.'/images/profile/{image_name}', [
	'as'         => 'profile_image',
	'uses'       => 'Student\ProfileController@profileImage',
	'middleware' => 'auth',
]);

Route::get($character.'/profile/CV/{id}','Student\ProfileController@ProfileDownloadCV')->name('student.downloadCV');
Route::post($character.'/staffUploadImage', 'Student\ProfileController@uploadImages')->name('student.dropzone.uploadStaffImage');
Route::post($character.'/staffDestoryImage', 'Student\ProfileController@destroyImage')->name('student.dropzone.destoryStaffImage');
Route::post($character.'/profile/store', 'Student\ProfileController@store')->name('student.staff.submit');

//My Course
Route::get($character.'/course_list','Student\CourseController@index');
Route::post($character.'/searchTeachCourse', 'Student\CourseController@searchTeachCourse');
Route::get($character.'/course/action/{id}','Student\CourseController@courseAction');

//Lecturer Note
Route::get($character.'/lectureNote/{id}','Student\LectureNoteController@LectureNote');
Route::post($character.'/lectureNote/searchFiles', 'Student\LectureNoteController@searchLN');
Route::get($character.'/lectureNote/folder/{ln_id}','Student\LectureNoteController@LNFolderView');
Route::get($character.'/images/lectureNote/{ln_id}/{note}', [
	'as'         => 'student.lectureNote_image',
	'uses'       => 'Student\LectureNoteController@LectureNoteImage',
	'middleware' => 'auth',
]);
Route::get($character.'/lectureNote/download/{id}','Student\LectureNoteController@downloadLN');
Route::get($character.'/lectureNote/download/zipFiles/{course_id}/{download}','Student\LectureNoteController@zipFileDownload');

//TP
Route::get($character.'/teachingPlan/{id}','Student\TeachingPlanController@TeachingPlan');
Route::get($character.'/teachingPlan/report/{id}', 'Student\TeachingPlanController@TPDownload');


//Past Year CA Question
Route::get($character.'/PastYear/assessment/{id}','Student\PastYearController@PastYearAssessment');
Route::get($character.'/PastYear/assessment/{id}/assessment_name/{course_id}','Student\PastYearController@PastYearAssessmentName');
Route::get($character.'/PastYear/assessment/{id}/list/{ass_id}/','Student\PastYearController@PastYearAssessmentList');
Route::get($character.'/PastYear/assessment/download/zipFiles/{course_id}/{download}','Student\PastYearController@zipFileDownload');
Route::get($character.'/PastYear/assessment/name/download/zipFiles/{course_id}/{download}','Student\PastYearController@zipFileDownloadName');
Route::get($character.'/PastYear/assessment/list/download/zipFiles/{ass_id}/{download}','Student\PastYearController@zipFileDownloadList');
Route::post($character.'/PastYear/assessment/searchAssessment/', 'Student\PastYearController@searchAssessment')->name('PY.searchAssessment');
Route::post($character.'/PastYear/assessment/name/searchAssessmentName/', 'Student\PastYearController@searchAssessmentName')->name('PY.searchAssessmentName');
Route::post($character.'/PastYear/assessment/list/searchAssessmentlist/', 'Student\PastYearController@searchAssessmentlist')->name('PY.searchAssessmentlist');
Route::get($character.'/PastYear/assessment/download/{ass_li_id}', 'Student\PastYearController@downloadFiles');
Route::get($character.'/PastYear/assessment/view/whole_paper/{ass_id}', 'Student\PastYearController@view_wholePaper');
Route::get($character.'/PastYear/images/assessment/{image_name}', [
     'as'         => 'M_assessment_image',
     'uses'       => 'Student\PastYearController@assessmentImage',
     'middleware' => 'auth',
]);


//Past year Final question
Route::get($character.'/PastYear/FinalAssessment/{id}','Student\PastYearFinalController@PastYearAssessment');
Route::get($character.'/PastYear/FinalAssessment/{id}/assessment_name/{course_id}','Student\PastYearFinalController@PastYearAssessmentName');
Route::get($character.'/PastYear/FinalAssessment/{id}/list/{fx_id}/','Student\PastYearFinalController@PastYearAssessmentList');
Route::get($character.'/PastYear/FinalAssessment/download/zipFiles/{course_id}/{download}','Student\PastYearFinalController@zipFileDownload');
Route::get($character.'/PastYear/FinalAssessment/name/download/zipFiles/{course_id}/{download}','Student\PastYearFinalController@zipFileDownloadName');
Route::get($character.'/PastYear/FinalAssessment/list/download/zipFiles/{fx_id}/{download}','Student\PastYearFinalController@zipFileDownloadList');
Route::post($character.'/PastYear/FinalAssessment/searchAssessment/', 'Student\PastYearFinalController@searchAssessment')->name('PY.final.searchAssessment');
Route::post($character.'/PastYear/FinalAssessment/name/searchAssessmentName/', 'Student\PastYearFinalController@searchAssessmentName')->name('PY.final.searchAssessmentName');
Route::post($character.'/PastYear/FinalAssessment/list/searchAssessmentlist/', 'Student\PastYearFinalController@searchAssessmentlist')->name('PY.final.searchAssessmentlist');
Route::get($character.'/PastYear/FinalAssessment/download/{ass_fx_id}', 'Student\PastYearFinalController@downloadFiles');
Route::get($character.'/PastYear/images/final_assessment/{image_name}', [
	'as'         => 'assessment_final_image',
	'uses'       => 'Student\PastYearFinalController@FinalAssessmentImage',
	'middleware' => 'auth',
]);
Route::get($character.'/PastYear/final_assessment/view/whole_paper/{fx_id}', 'Student\PastYearFinalController@view_wholePaper');

//Past Year Lecturer Note
Route::get($character.'/PastYearNote/{id}','Student\PastYearNoteController@PastYearNote');
Route::get($character.'/PastYearNote/{id}/{view}/{view_id}','Student\PastYearNoteController@PastYearNoteViewIn');
Route::post($character.'/PastYear/lectureNote/searchFiles', 'Student\PastYearNoteController@searchLecturerNote');
Route::post($character.'/PastYear/lectureNote/searchPreviousFiles', 'Student\PastYearNoteController@searchLecturerNotePrevious');
Route::get($character.'/PastYearNote/download/zipFiles/{course_id}/{download}','Student\PastYearNoteController@zipFileDownload');
Route::get($character.'/PastYear/images/lectureNote/{ln_id}/{image_name}', [
     'as'         => 'lectureNote_image',
     'uses'       => 'Student\PastYearNoteController@LectureNoteImage',
     'middleware' => 'auth',
]);
Route::get($character.'/PastYear/lectureNote/download/{id}','Student\PastYearNoteController@downloadLN');

//Past Year TP
Route::get($character.'/PastYearTP/{id}','Student\PastYearTPController@PastYearTP');
Route::get($character.'/PastYearTP/{id}/course/{view_id}','Student\PastYearTPController@PastYearTPDownload');
Route::get($character.'/PastYearTP/download/zipFiles/{course_id}/{checked}','Student\PastYearTPController@downloadZipFiles');
Route::post($character.'/PastYearTP/searchFiles', 'Student\PastYearTPController@searchPastYearTP');
?>