<?php

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

Route::get('/', 'WelcomeController@index')->name('welcome');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('parents/home','userRoles\ParentController@index')->name('parent.home');
Route::get('sstTeacher/home','userRoles\TeacherController@index')->name('teacher.home');
Route::get('sspPrinciples/home','userRoles\PrincipleController@index')->name('principle.home');
Route::get('ssaAdmins/home','userRoles\AdminController@index')->name('admin.home');

/* ========== Parent Routes ============= */
Route::get('parents/children','userRoles\ParentController@children')->name('parents.children');
Route::get('parents/editson','userRoles\ParentController@editChild')->name('parent.editSon.view');
Route::post('parents/editson','userRoles\ParentController@editSon')->name('parent.editSon');
Route::get('parents/removechild','userRoles\ParentController@removeChild')->name('parent.removeChild');
Route::get('parents/addChild','userRoles\ParentController@addView')->name('parents.add.view');
Route::post('parents/add-child','userRoles\ParentController@addChild')->name('parents.add');
Route::get('parents/teachers','userRoles\ParentController@selectTeachers')->name('parents.selectTeachers');
Route::post('parents/teachers','userRoles\ParentController@teachers')->name('parents.teachers');
Route::get('parents/principles','userRoles\ParentController@principles')->name('parents.principles');
Route::get('parents/settings','userRoles\ParentController@showSettings')->name('parents.showSettings');
Route::post('parents/settings','userRoles\ParentController@settings')->name('parents.settings');
Route::get('parents/inbox','userRoles\ParentController@myMsg')->name('parent.get.messages');


/* ========== Teacher Routes ============= */
Route::get('sstTeacher/students','userRoles\TeacherController@students')->name('teacher.students');
Route::get('sstTeacher/classes','userRoles\TeacherController@classes')->name('teacher.classes');
Route::post('sstTeacher/displaystd','userRoles\TeacherController@displaystd')->name('teacher.displaystd');
Route::get('sstTeacher/parents','userRoles\TeacherController@parents')->name('teacher.parents');
Route::post('sstTeacher/displaypts','userRoles\TeacherController@displaypts')->name('teacher.displaypts');
Route::get('sstTeacher/colleagues','userRoles\TeacherController@colleagues')->name('teacher.colleagues');
Route::post('sstTeacher/displayStf','userRoles\TeacherController@displayStf')->name('teacher.displayStf');
Route::get('sstTeacher/sendMsg','userRoles\TeacherController@teacherContactView')->name('teacher.contact.parents');
Route::get('sstTeacher/addParent','userRoles\TeacherController@addView')->name('teacher.add.parent');
Route::post('sstTeacher/add-Parent','userRoles\TeacherController@addpnt')->name('teacher.addParent');
Route::get('sstTeacher/inbox','userRoles\TeacherController@myMsg')->name('teacher.get.messages');


/* =========== Admins Routes ========= */
Route::get('ssaAdmins/teachers','userRoles\AdminController@teachers')->name('admins.teachers');
Route::get('ssaAdmins/parents','userRoles\AdminController@parents')->name('admins.parents');
Route::get('ssaAdmins/addteacher','userRoles\AdminController@addTeacherView')->name('admins.addteacher.view');
Route::get('ssaAdmins/editteacher','userRoles\AdminController@editTeacherView')->name('admins.edit.teacher.view');
Route::post('ssaAdmins/editteacher/{tid}','userRoles\AdminController@editTeacher')->name('admins.edit.teacher');
Route::get('ssaAdmins/unassignclass','userRoles\AdminController@unassignclass')->name('class.unassign');
Route::post('ssaAdmins/addteacher','userRoles\AdminController@addTeacher')->name('admin.addteacher');
Route::get('ssaAdmins/viewparent','userRoles\AdminController@viewParent')->name('admin.parent.view');
Route::get('ssaAdmins/contactus','userRoles\AdminController@contactus')->name('admins.contactus');
Route::get('ssaAdmins/schoolurls','userRoles\AdminController@schoolUrlsView')->name('admins.schoolurls.view');
Route::post('ssaAdmins/schoolurls','userRoles\AdminController@editSchoolUrls')->name('admins.schoolurls');
Route::get('admins/resetnewyear','userRoles\AdminController@resetNewYear')->name('admins.reset');
Route::get('admins/blockparent','userRoles\AdminController@blockParent')->name('block.parent');


/* =========== Principles Routes ========= */
Route::get('sspPrinciples/teachers','userRoles\PrincipleController@teachers')->name('principles.teachers');
Route::get('sspPrinciples/contact','userRoles\PrincipleController@sendView')->name('principles.send.view');
Route::post('sspPrinciples/contact','MessagesController@principleSend')->name('principles.send');
Route::get('sspPrinciples/contactus','userRoles\PrincipleController@contactus')->name('principles.contactus');

/* =========== Chat Routes ============ */
Route::post('sstTeacher/sendMsg','MessagesController@sendMsg')->name('teacher.send.toparent');
Route::post('sstTeacher/sendMsgs','MessagesController@tContactPts')->name('teacher.toparents');
Route::get('chat/{from}','MessagesController@privateChat')->name('private.chat');
Route::post('chat/reply','MessagesController@sendBack')->name('send.back');


/* ============ POSTS ROUTES ========== */
Route::get('posts/delete','PostController@delete')->name('delete.post');
Route::post('posts/createPost','PostController@createPost')->name('create.post');
Route::post('posts/editPost','PostController@editPost')->name('edit.post');
Route::post('posts/likePost','PostController@likePost')->name('like.post');
Route::post('posts/commentPost','PostController@commentPost')->name('comment.post');



/* ====================== Supervisor ======================== */
Route::get('supervisor/home','userRoles\SupervisorController@index')->name('supervisor.home');

/*for admin*/
Route::get('supervisor/admins','userRoles\SupervisorController@admins')->name('supervisor.admins');

/*for principle*/
Route::get('supervisor/principles','userRoles\SupervisorController@principles')->name('supervisor.principles');

/*for teachers*/
Route::get('supervisor/teachers','userRoles\SupervisorController@teachers')->name('supervisor.teachers');

/*for parents*/
Route::get('supervisor/parents','userRoles\SupervisorController@parents')->name('supervisor.parents');

/*for posts*/
Route::get('supervisor/posts','userRoles\SupervisorController@posts')->name('supervisor.posts');

/*for messages*/
Route::get('supervisor/messages','userRoles\SupervisorController@messages')->name('supervisor.messages');

/*for users*/
Route::get('supervisor/deleteuser','userRoles\SupervisorController@deleteUser')->name('supervisor.deleteuser');

Route::get('supervisor/editview','userRoles\SupervisorController@editUserView')->name('supervisor.editview');

Route::post('supervisor/edituser','userRoles\SupervisorController@editUser')->name('supervisor.edituser');


Route::get('supervisor/addview','userRoles\SupervisorController@addUserView')->name('supervisor.addview');

Route::post('supervisor/adduser','userRoles\SupervisorController@addUser')->name('supervisor.adduser');

Route::get('testpush','SchoolController@testpush');