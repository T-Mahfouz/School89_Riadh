<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/


Route::group(['middleware' => 'auth:api'],function(){
	
	/* ========== Posts ============= */
	Route::post('posts/createpost','PostController@createPost');
	//Route::post('posts/addimage','PostController@createPost');
	Route::post('posts/editPost','PostController@editPost');
	Route::get('posts/delete','PostController@delete');
	Route::post('posts/commentPost','PostController@commentPost');
	Route::post('posts/likePost','PostController@likePost');
	Route::get('posts/postInfo','PostController@postInfo');

	/* ========== Parents =========== */
	Route::get('parents/children','userRoles\ParentController@children');
	Route::get('parents/removechild','userRoles\ParentController@removeChild');
	Route::post('parents/add-child','userRoles\ParentController@ApiAddChild');
	Route::post('parents/editson','userRoles\ParentController@editSon');
	Route::post('parents/teachers','userRoles\ParentController@teachers');
	Route::get('parents/principles','userRoles\ParentController@principles');	
	Route::get('parents/inbox','userRoles\ParentController@myMsg');
        Route::post('parents/contact','MessagesController@sendMsg');
        //Route::post('parents/settings','userRoles\ParentController@settings');

	/* ============ Teachers =========== */
	Route::post('teachers/students','userRoles\TeacherController@displaystd');
	Route::get('teachers/classes','userRoles\TeacherController@classes');
	Route::post('teachers/parents','userRoles\TeacherController@displaypts');
	Route::post('teachers/colleagues','userRoles\TeacherController@displayStf');
	Route::post('teachers/contact','MessagesController@sendMsg');
	Route::post('teachers/toparents','MessagesController@tContactPts');
	Route::get('teachers/inbox','userRoles\TeacherController@myMsg');
	Route::post('teachers/addParent','userRoles\TeacherController@addpnt');
	Route::post('teachers/contactspecificparents','MessagesController@contactSpecificParents');
	Route::get('teachers/blockparent','userRoles\TeacherController@blockParent');
	//Route::post('teachers/settings','userRoles\TeacherController@settings');

	/* =========== Principles Routes ========= */
	Route::get('principles/teachers','userRoles\PrincipleController@teachers');
	Route::post('principles/contact','MessagesController@principleSend');
	Route::get('principles/contactus','userRoles\PrincipleController@contactus');
	Route::get('principles/onlineteachers','userRoles\PrincipleController@onlineTeachers');
	Route::get('principles/parents','userRoles\PrincipleController@parents');
	Route::get('principles/blockparent','userRoles\PrincipleController@blockParent');
	Route::get('principles/blockparent','userRoles\PrincipleController@blockParent');

	Route::get('principles/unblockparent','userRoles\PrincipleController@unBlockParent');
	Route::post('principles/addteacher','userRoles\PrincipleController@addTeacher');
	Route::post('principles/contactanyuser','MessagesController@contactAnyUser');
	Route::post('principles/assign','userRoles\PrincipleController@editTeacher');
	Route::get('principles/unassignclass','userRoles\PrincipleController@unassignclass');
	
	Route::post('principles/editaboutus','userRoles\PrincipleController@editAboutUs');
	Route::post('principles/addaboutus','userRoles\PrincipleController@addAboutUs');

	/* =========== Admins Routes ========= */
	Route::get('admins/teachers','userRoles\AdminController@teachers');
	Route::get('admins/parents','userRoles\AdminController@parents');
	Route::post('admins/assign','userRoles\AdminController@editTeacher');
	Route::get('admins/unassignclass','userRoles\AdminController@unassignclass');
	Route::post('admins/addteacher','userRoles\AdminController@addTeacher');
	Route::get('admins/viewparent','userRoles\AdminController@viewParent');
	Route::get('admins/contactus','userRoles\AdminController@contactus');
	Route::post('admins/editschoolurls','userRoles\AdminController@editSchoolUrls');
	Route::get('admins/blockedparents','userRoles\AdminController@blockedParents');
    Route::get('admins/resetnewyear','userRoles\AdminController@resetNewYear');
    Route::get('admins/blockparent','userRoles\AdminController@blockParent');
    Route::get('admins/unblockparent','userRoles\AdminController@unBlockParent');
    Route::post('admins/addnewlevel','userRoles\AdminController@addnewlevel');
    Route::post('admins/addnewclass','userRoles\AdminController@addnewclass');
    Route::post('admins/editclass','userRoles\AdminController@editclass');
    Route::post('admins/addnewsubject','userRoles\AdminController@addNewSubject');
    Route::post('admins/contactanyuser','MessagesController@contactAnyUser');

    Route::post('admins/editaboutus','userRoles\AdminController@editAboutUs');
	Route::post('admins/addaboutus','userRoles\AdminController@addAboutUs');

	/* ============ Public Functions ============ */
	Route::get('/timeline', 'HomeController@index');
	Route::get('/user', 'UserController@profileDetails');
	Route::get('/userposts', 'UserController@userPosts');
	Route::get('followers','UserController@followers');
	Route::get('whoami','UserController@whoAmI');
	
	Route::get('/lastcamemessage','MessagesController@lastCommingMsg');
	Route::get('notifications','MessagesController@notifications');
	Route::get('openmessage','MessagesController@markAsRead');
	Route::get('lastmessages','MessagesController@lastMessages');
	Route::post('user/settings','UserController@settings');
	Route::get('principle','UserController@principle');

	Route::get('resendvc','VerificationController@resendVC');
    Route::post('activateaccount','VerificationController@activateAccount');
    
	Route::get('logout','UserController@logout');
	

	/* ============ Private Chat ================ */
	Route::get('privatechat','MessagesController@privateChat');
	
	
	
	/* ============ Subjects =====================*/
    	
	

});


Route::post('/register','Auth\RegisterController@apiRegister');


/* ============ School affairs ========== */
Route::get('classes', 'SchoolController@classes');
Route::get('levels', 'SchoolController@levels');
Route::post('classesoflevel', 'SchoolController@classesOfLevel');
Route::get('subjects', 'SchoolController@subjects');
Route::get('schoolurls','SchoolController@schoolUrls');

Route::post('forgetpassword','SchoolController@forgetPassword');
Route::post('testvc','SchoolController@TestVC');
Route::post('changepassword','SchoolController@changePassword');
Route::post('sendcontactus','SchoolController@sendContactUs');


Route::get('aboutus','SchoolController@aboutUs');