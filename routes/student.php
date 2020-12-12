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
?>