<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true]);

Route::get('student/register','StudentController@create')->name('student.create');
Route::post('student/register', 'StudentController@store')->name('student.register.submit');

Route::middleware('is_student')->group(function(){
		Route::get('/home', 'HomeController@index')->name('home');
});

Route::middleware('is_admin')->group(function(){
	Route::get('admin/home', 'HomeController@adminHome')->name('admin.home');
	Route::get('student/create','StudentController@AdminCreateStudent')->name('admin.student.create');
	Route::post('student/create', 'StudentController@store')->name('admin.student.submit');
	Route::post('/studentUploadImage', 'StudentController@uploadImages')->name('dropzone.uploadStudentImage');
	Route::post('/studentDestoryImage', 'StudentController@destroyImage')->name('dropzone.destoryStudentImage');
	Route::post('/studentRemoveImage', 'StudentController@removeImage');
	Route::get('/student_list','StudentController@index')->name('admin.student_list.index');
	
	Route::get('images/student/{image_name}', [
	     'as'         => 'student_image.show',
	     'uses'       => 'StudentController@show',
	     'middleware' => 'auth',
	]);

	Route::get('/student/{id}','StudentController@edit')->name('admin.student_list.edit');
	Route::post('/student/{id}','StudentController@update')->name('student_list.update.submit');

	Route::get('staff/create','StaffController@create')->name('staff.create');
	Route::post('staff/create', 'StaffController@store')->name('staff.submit');

	Route::get('/staff_list','StaffController@index')->name('admin.staff_list.index');
	Route::get('images/staff/{image_name}', [
	     'as'         => 'staff_image.show',
	     'uses'       => 'StaffController@show',
	     'middleware' => 'auth',
	]);
	Route::get('/staff/CV/{id}','StaffController@downloadCV')->name('admin.downloadCV');

	Route::get('/staff/{id}','StaffController@edit')->name('admin.staff_list.edit');
	Route::post('/staff/{id}','StaffController@update')->name('staff_list.update.submit');
	Route::post('/staffFaculty', 'StaffController@staffFaculty');
	Route::post('/checkStaffID', 'StaffController@checkStaffID');
	Route::post('/removeImage', 'StaffController@removeImage');
	Route::post('/removeCV', 'StaffController@removeCV');
	Route::post('/staffUploadImage', 'StaffController@uploadImages')->name('dropzone.uploadStaffImage');
	Route::post('/staffDestoryImage', 'StaffController@destroyImage')->name('dropzone.destoryStaffImage');
	Route::post('/staffUploadCV', 'StaffController@uploadCV')->name('dropzone.uploadStaffCV');
	Route::post('/staffDestoryCV', 'StaffController@destroyCV')->name('dropzone.destoryStaffCV');

	Route::get('department/create','DepartmentController@create')->name('department.create');
	Route::post('department/create', 'DepartmentController@store')->name('department.submit');
	Route::get('/department_list','DepartmentController@index')->name('admin.department_list.index');
	Route::get('/department/{id}','DepartmentController@edit')->name('admin.department_list.edit');
	Route::post('/department/{id}','DepartmentController@update')->name('department_list.update.submit');

	Route::get('programme/create','ProgrammeController@create')->name('programme.create');
	Route::post('programme/create', 'ProgrammeController@store')->name('programme.submit');
	Route::get('/programme_list','ProgrammeController@index')->name('admin.programme_list.index');
	Route::get('/programme/{id}','ProgrammeController@edit')->name('admin.programme_list.edit');
	Route::post('/programme/{id}','ProgrammeController@update')->name('programme_list.update.submit');

	Route::get('subject/create/{id}','SubjectController@create')->name('subject.create');
	Route::post('subject/create/{id}', 'SubjectController@store')->name('subject.submit');
	Route::get('/subject_list','SubjectController@index')->name('admin.subject_list.index');
	Route::post('/subjectEditModal', 'SubjectController@subjectEditModal');
	Route::post('/subjectUpdateModal', 'SubjectController@subjectUpdateModal');
	Route::post('/subjectTypeUpdateModal', 'SubjectController@subjectTypeUpdateModal');
	Route::post('/syllabusPostUpload', 'SubjectController@postUpload')->name('dropzone.syllabusPostUpload');
    Route::post('/syllabusDestory', 'SubjectController@syllabusDestory')->name('dropzone.syllabusDestory');
    Route::get('/syllabus/download/{id}','SubjectController@downloadSyllabus')->name('subject.downloadSyllabus');

	Route::get('subjectsMPU/create/{level}','MPUController@create')->name('MPU.create');
	Route::post('subjectsMPU/create/{level}', 'MPUController@store')->name('MPU.submit');
	Route::get('subjectsMPU/view/{level}','MPUController@view')->name('MPU.view');
	Route::get('mpu_list','MPUController@index')->name('admin.mpu_list.index');
	Route::post('/generalStudiesEditModal', 'MPUController@generalStudiesEditModal');
	Route::post('/generalStudiesUpdateModal', 'MPUController@generalStudiesUpdateModal');
	Route::post('/generalStudiesTypeUpdateModal', 'MPUController@generalStudiesTypeUpdateModal');
	Route::get('/MPUsyllabus/download/{id}','MPUController@downloadSyllabus')->name('MPUsubject.downloadSyllabus');


	Route::get('faculty/create','FacultyController@create')->name('faculty.create');
	Route::post('faculty/create', 'FacultyController@store')->name('faculty.submit');
	Route::get('/faculty_list','FacultyController@index')->name('admin.faculty_list.index');
	Route::get('/faculty/{id}','FacultyController@edit')->name('admin.faculty_list.edit');
	Route::post('/faculty/{id}','FacultyController@update')->name('faculty_list.update.submit');

	Route::get('semester/create','SemesterController@create')->name('semester.create');
	Route::post('semester/create', 'SemesterController@store')->name('semester.submit');
	Route::get('/semester_list','SemesterController@index')->name('admin.semester_list.index');
	Route::get('/semester/{id}','SemesterController@edit')->name('admin.semester_list.edit');
	Route::post('/semester/{id}','SemesterController@update')->name('semester_list.update.submit');

});

// Route::middleware('is_staff')->group(function(){
// 	Route::get('staff/home', 'HomeController@staffHome')->name('staff.home');
// });
Route::middleware('is_teacher')->group(function(){
	Route::get('teacher/home', 'HomeController@teacherHome')->name('teacher.home');
});
Route::middleware('is_hod')->group(function(){
	Route::get('hod/home', 'HomeController@hodHome')->name('hod.home');
});
Route::middleware('is_dean')->group(function(){
	include('dean.php');
});











