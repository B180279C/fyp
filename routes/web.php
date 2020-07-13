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
	Route::get('/student_list','StudentController@index')->name('admin.student_list.index');
	Route::get('/student/{id}','StudentController@edit')->name('admin.student_list.edit');
	Route::post('/student/{id}','StudentController@update')->name('student_list.update.submit');

	Route::get('staff/create','StaffController@create')->name('staff.create');
	Route::post('staff/create', 'StaffController@store')->name('staff.submit');
	Route::get('/staff_list','StaffController@index')->name('admin.staff_list.index');
	Route::get('/staff/{id}','StaffController@edit')->name('admin.staff_list.edit');
	Route::post('/staff/{id}','StaffController@update')->name('staff_list.update.submit');
	Route::post('/staffFaculty', 'StaffController@staffFaculty');


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


	Route::get('subjectsMPU/create/{level}','MPUController@create')->name('MPU.create');
	Route::post('subjectsMPU/create/{level}', 'MPUController@store')->name('MPU.submit');
	Route::get('subjectsMPU/view/{level}','MPUController@view')->name('MPU.view');
	Route::get('mpu_list','MPUController@index')->name('admin.mpu_list.index');
	Route::post('/generalStudiesEditModal', 'MPUController@generalStudiesEditModal');
	Route::post('/generalStudiesUpdateModal', 'MPUController@generalStudiesUpdateModal');
	Route::post('/generalStudiesTypeUpdateModal', 'MPUController@generalStudiesTypeUpdateModal');


	Route::get('faculty/create','FacultyController@create')->name('faculty.create');
	Route::post('faculty/create', 'FacultyController@store')->name('faculty.submit');
	Route::get('/faculty_list','FacultyController@index')->name('admin.faculty_list.index');
	Route::get('/faculty/{id}','FacultyController@edit')->name('admin.faculty_list.edit');
	Route::post('/faculty/{id}','FacultyController@update')->name('faculty_list.update.submit');

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
	Route::get('dean/home', 'HomeController@deanHome')->name('dean.home');
	Route::get('course_list','CourseController@index')->name('dean.course_list.index');
	Route::get('course/create','CourseController@create')->name('course.create');
	Route::post('course/create', 'CourseController@store')->name('course.submit');
	Route::post('/courseSubject', 'CourseController@courseSubject');
});











