<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CurriculumCategoryController;
use App\Http\Controllers\Result\ResultsController;
use App\Http\Controllers\Setup\ClassesController;
use App\Http\Controllers\Setup\LevelsController;
use App\Http\Controllers\Setup\SectionsController;
use App\Http\Controllers\Setup\SessionsController;
use App\Http\Controllers\Setup\SubjectsController;
use App\Http\Controllers\Setup\TermsController;
use App\Http\Controllers\Users\GuardiansController;
use App\Http\Controllers\Users\StudentsController;
use App\Http\Controllers\Users\UsersController;
use App\Http\Controllers\Users\StaffController;

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

Route::get('fetch-curriculum-setup', [CurriculumCategoryController::class, 'fetchCurriculumCategory']);

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register'])->middleware('permission:create-users');

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::post('logout', [AuthController::class, 'logout']);

        Route::get('user', [AuthController::class, 'user']); //->middleware('permission:read-users');
    });
});

//////////////////////////////// APP APIS //////////////////////////////////////////////
Route::group(['middleware' => 'auth:sanctum'], function () {
    // Protected routes for authenticated users
    Route::get('fetch-necessary-params', [Controller::class, 'fetchNecessayParams']);
    Route::get('user-notifications', [UsersController::class, 'userNotifications']);

    Route::group(['prefix' => 'curriculum'], function () {
        Route::post('level-group/save', [CurriculumCategoryController::class, 'storeCurriculumLevelGroup']);
        Route::post('level/save', [CurriculumCategoryController::class, 'storeCurriculumLevel']);

        Route::get('level-group/all', [CurriculumCategoryController::class, 'allCurriculumLevelGroups']);
        Route::get('level/all', [CurriculumCategoryController::class, 'allCurriculumLevels']);
        Route::put('level-group/update/{curriculum_level_group}', [CurriculumCategoryController::class, 'updateCurriculumLevelGroup']);
    });
    Route::group(['prefix' => 'result'], function () {
        Route::get('set-selection-options', [ResultsController::class, 'setSelectionOptions']);
        Route::get('student-selection-options', [ResultsController::class, 'studentSelectionOptions']);
        Route::get('get-subject-students', [ResultsController::class, 'getSubjectStudent']);
        Route::post('record-result', [ResultsController::class, 'recordResult']);
        Route::post('result-action', [ResultsController::class, 'resultAction']);
        Route::post('upload-bulk-result', [ResultsController::class, 'uploadBulkResult']);
        Route::get('get-recorded-result', [ResultsController::class, 'getRecordedResultForApproval']);
        Route::get('class-broadsheet', [ResultsController::class, 'classBroadSheet']);
        Route::get('get-student-result-details', [ResultsController::class, 'getStudentResultDetails']);
        Route::get('give-student-remark', [ResultsController::class, 'giveStudentRemark']);

        // Route::post('level-group/save', [CurriculumCategoryController::class, 'storeCurriculumLevelGroup']);
        // Route::post('level/save', [CurriculumCategoryController::class, 'storeCurriculumLevel']);

        // Route::get('level-group/all', [CurriculumCategoryController::class, 'allCurriculumLevelGroups']);
        // Route::get('level/all', [CurriculumCategoryController::class, 'allCurriculumLevels']);
        // Route::put('level-group/update/{curriculum_level_group}', [CurriculumCategoryController::class, 'updateCurriculumLevelGroup']);
    });

    Route::group(['prefix' => 'school-setup'], function () {
        Route::get('set/color-code', [Controller::class, 'setColorCode']);
        Route::get('fetch-session-and-term', [Controller::class, 'fetchSessionAndTerm']);

        Route::get('levels', [LevelsController::class, 'index']);

        Route::get('fetch-level-class', [LevelsController::class, 'fetchLevelAndClass']);
        Route::get('fetch-specific-curriculum-level-groups', [LevelsController::class, 'fetchSpecificCurriculumLevels']);
        Route::post('level/save', [LevelsController::class, 'store']);
        Route::put('level/update/{level}', [LevelsController::class, 'update']);
        Route::delete('level/destroy/{level}', [LevelsController::class, 'destroy']);

        Route::resource('classes', ClassesController::class);
        Route::post('class/assign-teacher', [ClassesController::class, 'assignClassTeacher']);

        Route::resource('sections', SectionsController::class);

        Route::resource('subjects', SubjectsController::class);
        Route::get('fetch-teacher-subject', [SubjectsController::class, 'fetchTeacherSubject']);
        Route::put('assign-subject/{subject_teacher}', [SubjectsController::class, 'assignSubject']);

        Route::post('session/activate', [SessionsController::class, 'activate']);
        Route::post('term/activate', [TermsController::class, 'activate']);
    });
    Route::group(['prefix' => 'user-setup'], function () {
        Route::get('all-students-table', [StudentsController::class, 'allStudentsTable']);
        Route::get('students/create', [StudentsController::class, 'create']);
        Route::post('students/store', [StudentsController::class, 'store']);
        Route::get('students/show/{student}', [StudentsController::class, 'show']);
        Route::post('students/upload/bulk', [StudentsController::class, 'uploadBulkStudents']);

        Route::get('admin-reset/password', [UsersController::class, 'adminResetUserPassword']);

        // Route::resource('staff', StaffController::class);
        Route::get('staff', [StaffController::class, 'index']);
        Route::get('staff/create', [StaffController::class, 'create']);
        Route::post('staff/store', [StaffController::class, 'store']);
        Route::get('staff/show/{staff}', [StaffController::class, 'show']);

        Route::get('guardians', [GuardiansController::class, 'index']);
        Route::get('guardian/show/{guardian}', [GuardiansController::class, 'show']);
    });
});
