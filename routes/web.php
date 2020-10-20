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

	Route::get('/FacultyPortFolio', 'F_PortFolioController@index')->name('dean.F_potrfolio.index');
	Route::post('/searchFiles', 'F_PortFolioController@searchFiles');	
	Route::get('/FacultyPortFolio/LecturerCV/', 'F_PortFolioController@lecturerCV')->name('dean.F_potrfolio.lecturerCV');
	Route::get('/FacultyPortFolio/Syllabus/', 'F_PortFolioController@Syllabus')->name('dean.F_potrfolio.syllabus');
	Route::post('/searchLecturerCV', 'F_PortFolioController@searchLecturerCV');
	Route::post('/searchSyllabus', 'F_PortFolioController@searchSyllabus');
	Route::get('/dean/staff/CV/{id}','F_PortFolioController@downloadCV')->name('dean.downloadCV');
	Route::get('/dean/syllabusDownload/{id}','F_PortFolioController@downloadSyllabus')->name('dean.downloadSyllabus');

	Route::post('/openNewFolder', 'F_PortFolioController@openNewFolder');
	Route::post('/folderNameEdit', 'F_PortFolioController@folderNameEdit');
	Route::post('/updateFolderName', 'F_PortFolioController@updateFolderName');
	Route::get('/FacultyPortFolio/remove/{id}', 'F_PortFolioController@removeActiveFile');
	Route::get('/faculty_portfolio/folder/{folder_id}', 'F_PortFolioController@folder_view')->name('dean.F_potrfolio.folder_view');
	Route::post('/portfolio_uploadFile', 'F_PortFolioController@uploadFiles')->name('dropzone.uploadFiles');
	Route::post('/destoryFiles', 'F_PortFolioController@destroyFiles')->name('dropzone.destoryFiles');
	Route::post('/storeFiles', 'F_PortFolioController@storeFiles');
	Route::get('/faculty/portfolio/{id}','F_PortFolioController@downloadFP')->name('dean.downloadFP');

	Route::get('/CoursePortFolio', 'C_PortFolioController@index')->name('dean.C_potrfolio.index');
	Route::post('/searchCourse', 'C_PortFolioController@searchCourse');

	Route::post('/uploadCourses', 'CourseController@importExcel')->name('dropzone.uploadCourses');
	Route::post('/course/excel/create', 'CourseController@storeCourses')->name('course.excel.submit');
	
	Route::get('course_list','CourseController@index')->name('dean.course_list.index');
	Route::get('course/create','CourseController@create')->name('course.create');
	Route::post('course/create', 'CourseController@store')->name('course.submit');
	Route::post('/courseSubject', 'CourseController@courseSubject');
	Route::get('/course/{id}','CourseController@edit')->name('course.edit');
	Route::post('/course/{id}','CourseController@update')->name('course.update.submit');
	Route::get('/course/remove/{id}', 'CourseController@removeActiveCourse');
	Route::post('/searchTeachCourse', 'CourseController@searchTeachCourse');
	Route::get('course/action/{id}','CourseController@courseAction');

	Route::get('/assign/student/{id}','AssignStudentController@viewAssignStudent');
	Route::post('/searchAssignStudent', 'AssignStudentController@searchAssignStudent');
	Route::post('/showStudent','AssignStudentController@showStudent');
	Route::post('/storeStudent', 'AssignStudentController@storeStudent');
	Route::post('/uploadAssignStudent', 'AssignStudentController@importExcelStudent')->name('dropzone.uploadAssignStudent');
	Route::post('/assignStudent/excel/create', 'AssignStudentController@storeAssignStudent')->name('assignStudent.excel.submit');
	Route::get('/assignStudent/remove/{id}','AssignStudentController@removeActiveStudent');

	Route::get('/lectureNote/{id}','LectureNoteController@viewLectureNote');
	Route::post('/lectureNote/searchFiles', 'LectureNoteController@searchFiles');
	Route::get('/lectureNote/folder/{folder_id}', 'LectureNoteController@folder_view')->name('dean.note.folder_view');
	Route::post('/lectureNote/openNewFolder', 'LectureNoteController@openNewFolder');
	Route::post('/lectureNote/folderNameEdit', 'LectureNoteController@folderNameEdit');
	Route::post('/lectureNote/updateFolderName', 'LectureNoteController@updateFolderName');
	Route::get('/lectureNote/remove/{id}', 'LectureNoteController@removeActive');
	Route::post('/note_uploadFiles', 'LectureNoteController@uploadFiles')->name('note.dropzone.uploadFiles');
	Route::post('/note_destoryFiles', 'LectureNoteController@destroyFiles')->name('note.dropzone.destoryFiles');
	Route::post('/note_storeFiles', 'LectureNoteController@storeFiles');
	Route::get('/lectureNote/download/zipFiles/{id}','LectureNoteController@zipFileDownload');
	Route::get('/lectureNote/download/{id}','LectureNoteController@downloadLN')->name('dean.downloadLN');

	Route::get('/teachingPlan/{id}','TeachingPlanController@viewTeachingPlan')->name('tp.view');
	Route::get('/teachingPlan/create/weekly/{id}','TeachingPlanController@createTeachingPlan')->name('tp.create');
	Route::post('/teachingPlan/create/weekly/{id}', 'TeachingPlanController@storeTP')->name('tp.submit');
	Route::post('/removeTopic', 'TeachingPlanController@removeTopic');
	Route::post('/teachingPlan/searchPlan', 'TeachingPlanController@searchPlan');

	Route::post('/teachingPlan/getSyllabusData', 'TeachingPlanController@getSyllabusData');
	Route::get('/teachingPlan/create/assessment/{id}','TeachingPlanController@createTPAss')->name('tpAss.create');
	Route::post('/teachingPlan/create/assessment/{id}', 'TeachingPlanController@storeTPAss')->name('tpAss.submit');
	
	
	Route::get('/assessment/{id}','AssessmentController@viewAssessment')->name('dean.viewAssessment');
	Route::post('/assessment/getSyllabusData', 'AssessmentController@getSyllabusData');
	Route::get('/assessment/create/{id}/question/{question}', [
    'as' => 'createQuestion', 'uses' => 'AssessmentController@create_question']);
    Route::get('/assessment/folder/{folder_id}', 'AssessmentController@folder_view')->name('dean.ass.folder_view');
    Route::post('/assessment/openNewFolder', 'AssessmentController@openNewFolder');
	Route::post('/assessment/folderNameEdit', 'AssessmentController@folderNameEdit');
	Route::post('/assessment/updateFolderName', 'AssessmentController@updateFolderName');
	Route::get('/assessment/remove/{id}', 'AssessmentController@removeActive');
	Route::post('/ass_uploadFiles', 'AssessmentController@uploadFiles')->name('assessment.dropzone.uploadFiles');
	Route::post('/ass_destoryFiles', 'AssessmentController@destroyFiles')->name('assessment.dropzone.destoryFiles');
	Route::post('/ass_storeFiles', 'AssessmentController@storeFiles');
	Route::get('images/assessment/{image_name}', [
	     'as'         => 'assessment_image',
	     'uses'       => 'AssessmentController@assessmentImage',
	     'middleware' => 'auth',
	]);
	Route::get('/assessment/view/whole_paper/{ass_id}', 'AssessmentController@view_wholePaper');
	Route::get('/assessment/download/{ass_id}', 'AssessmentController@downloadFiles');
	Route::get('/assessment/folder/{id}/previous/{course_id}/question/{question}/{list}', [
    'as' => 'viewPreviousQuestion', 'uses' => 'AssessmentController@viewPreviousQuestion']);
    Route::get('/assessment/folder/{id}/previous/{folder_id}/{list}', [
    'as' => 'dean.ass.previous_folder_view', 'uses' => 'AssessmentController@previous_folder_view']);
    Route::post('/assessment/searchKey/', 'AssessmentController@searchKey');
    Route::get('/assessment/{id}/previous/{course_id}/list', [
    'as' => 'viewPreviousAssessment', 'uses' => 'AssessmentController@viewPreviousAssessment']);
    Route::post('/assessment/list/searchListKey/', 'AssessmentController@searchListKey');


    Route::get('/AssessmentResult/{id}/', [
    'as' => 'viewAssessmentResult', 'uses' => 'AssessmentResultController@viewAssessmentResult']);
});











