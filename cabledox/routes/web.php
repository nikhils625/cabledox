<?php 
  
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CableMasterController;
use App\Http\Controllers\FinalCheckSheetQuestionnaireController;
use App\Http\Controllers\TestParameterController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobCableController;
use App\Http\Controllers\JobDrawingController;
use App\Http\Controllers\JobTermnationDetailController;

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

	if(\Auth::user()) {
		return redirect()->back();
	} else {
		return view('auth.login');
	}
    /*return view('welcome');*/
});

Auth::routes();

Route::middleware(['web', 'auth'])->group(function () {
    /*dashboard*/
	Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    /*clients*/
  	Route::resource('clients', ClientController::class, ['names' => [
	    	'index' => 'clients.index',
	    	'create' => 'clients.create',
	    	'store' => 'clients.store',
	    	'show' => 'clients.show',
	    	'edit' => 'clients.edit',
	    	'update' => 'clients.update',
    	],  'except' => ['destroy','changeStatus']
  	]);

  	Route::post('clients/destroy/{id?}', [ClientController::class, 'destroy'])->name('clients.destroy');

  	Route::get('client/changeStatus', [ClientController::class, 'changeStatus'])->name('client.changeStatus');

	/*users*/
	Route::resource('users', UserController::class, ['names' => [
		    'index'  => 'users.index',
		    'create' => 'users.create',
		    'store'  => 'users.store',
		    'show'   => 'users.show',
		    'edit'   => 'users.edit',
		    'update' => 'users.update',
		], 'except'  => [ 'destroy']
	]);

	Route::post('users/destroy/{id?}', [UserController::class, 'destroy'])->name('users.destroy');

	Route::group(['prefix' => 'user'],function () {
		Route::get('/change-password-form/', [UserController::class, 'changePasswordForm'])->name('users.change-password-form');

		Route::post('/change-password/', [UserController::class, 'changePassword'])->name('users.change-password');

		Route::get('/my-profile/', [UserController::class, 'myProfile'])->name('users.my-profile');

		Route::post('/update-profile/{id?}', [UserController::class, 'updateProfile'])->name('users.update-profile');

		Route::get('/change-status', [UserController::class, 'changeStatus'])->name('users.change-status');

		/* for creating and assinging permissions*/
        Route::get('create-permission', [UserController::class, 'createPermission']);
        Route::get('give-permission', [UserController::class, 'givePermission']);
        Route::get('revoke-permission', [UserController::class, 'revokePermission']);
	});
    
    /* Final Check  Sheet */
    Route::resource('final-check-sheet', FinalCheckSheetQuestionnaireController::class, ['names' => [
		    'index'  => 'final-check-sheet.index',
		    'create' => 'final-check-sheet.create',
		    'store'  => 'final-check-sheet.store',
		    'show'   => 'final-check-sheet.show',
		    'edit'   => 'final-check-sheet.edit',
		    'update' => 'final-check-sheet.update',
		], 'except'  => [ 'destroy']
	]);

	Route::post('/destroy/{id?}', [FinalCheckSheetQuestionnaireController::class, 'destroy'])->name('final-check-sheet.destroy');

	/* Test Parameter Sheet */
    Route::resource('test-parameters', TestParameterController::class, 
    	['names' => [
		    'index'  => 'test-parameters.index',
		    'create' => 'test-parameters.create',
		    'store'  => 'test-parameters.store',
		    'show'   => 'test-parameters.show',
		    'edit'   => 'test-parameters.edit',
		    'update' => 'test-parameters.update',
		], 'except'  => [ 'destroy']
	]);

	Route::post('test-parameters/destroy/{id?}', [TestParameterController::class,
	 'destroy'])->name('test-parameters.destroy');

	/*Cable Master*/
	Route::resource('cable-masters', CableMasterController::class, ['names' => [
		    'index'  => 'cable-masters.index',
		    'create' => 'cable-masters.create',
		    'store'  => 'cable-masters.store',
		    'show'   => 'cable-masters.show',
		    'edit'   => 'cable-masters.edit',
		    'update' => 'cable-masters.update',
		], 'except'  => [ 'destroy']
	]);

	Route::post('cable-masters/destroy/{id?}', [CableMasterController::class, 'destroy'])->name('cable-masters.destroy');

	Route::group(['prefix' => 'cable-master'],function () {
		Route::get('/change-status', [CableMasterController::class, 'changeStatus'])->name('cable-masters.change-status');
	});

	/* Jobs */
    Route::resource('jobs', JobController::class, 
    	['names' => [
		    'index'  => 'jobs.index',
		    'create' => 'jobs.create',
		    'store'  => 'jobs.store',
		    'show'   => 'jobs.show',
		    'edit'   => 'jobs.edit',
		    'update' => 'jobs.update',
		], 'except'  => [ 'destroy']
	]);

	Route::post('jobs/destroy/{id?}', [JobController::class,
	 'destroy'])->name('jobs.destroy');

	Route::post('jobs/insertJobDrawing/{job_id?}', [JobController::class,
	 'insertJobDrawing'])->name('jobs.insertJobDrawing');

	Route::post('jobs/removeJobDrawing', [JobController::class,
	 'removeJobDrawing'])->name('jobs.removeJobDrawing');

	Route::any('drawing/download-drawing-image/{encodedFilePath}/{fileName?}', [JobController::class, 'downloadDrawingImage'])->name('drawing.download-drawing-image');
	
	Route::post('jobs/get-job-cable-locations', [JobController::class, 'getJobCableLocations'])->name('job.get-job-cable-locations');

	Route::any('termination-details/{job_id?}/add', [JobController::class, 'addTerminationdetails'])->name('termination-details.add');

	Route::any('termination-details/get-details', [JobController::class, 'getTerminationDetails'])->name('job.get-termination-details');

	Route::post('termination-details/save/{job_id?}', [JobController::class, 'saveTerminationdetails'])->name('job.termination-details-save');

	Route::any('job/final-check-sheet/{job_id?}/add', [JobController::class, 'finalCheckSheet'])->name('job.add-final-check-sheet');

	Route::any('job/view-final-check-sheet/{job_id?}', [JobController::class, 'viewFinalCheckSheet'])->name('job.view-final-check-sheet');

	Route::any('job/get-cable-details', [JobController::class, 'getCableDetails'])->name('job.get-cable-details');

	Route::any('job/get-check-sheet-details', [JobController::class, 'getCheckSheetDetails'])->name('job.get-check-sheet-details');

	Route::post('job/final-check-sheet/save/{job_id?}', [JobController::class, 'saveFinalCheckSheet'])->name('job.save-final-check-sheet');

	Route::post('job/get-job-locations/', [JobController::class, 'getJobLocation'])->name('job.get-job-locations');	

	Route::any('job/download-job-asset/{encodedFilePath}/{fileName?}', [JobController::class, 'downloadJobAsset'])->name('job.download-job-asset');

	Route::get('area-of-work/list/{jobId?}', [JobController::class, 'listAreaOfWork'])->name('area-of-work.list');

	Route::get('change-status-area-of-work', [JobController::class, 'changeStatusAreaOfWork'])->name('change-status-area-of-work');

	Route::any('test-results/{job_id}/add', [JobController::class, 'addTestResults'])->name('test-results.add');

	Route::any('test-results/getdetail', [JobController::class, 'getTestResults'])->name('jobs.get-test-results');

	Route::any('test-results/save/{job_id?}', [JobController::class, 'saveTestResults'])->name('test-results.save');

	Route::any('report-issue/save/{job_id}', [JobController::class, 'saveReportIssue'])->name('report-issue.save');

	Route::get('report-issue/list/{job_id}', [JobController::class, 'getReportIssue'])->name('report-issue.list');

	Route::get('report-issue/get_comment', [JobController::class, 'getCommentReportedIssue'])->name('report-issue.get_comment');

	Route::any('report-issue/save_comment', [JobController::class, 'saveCommentReportedIssue'])->name('report-issue.save_comment');

	Route::get('report-issue/changeReportStatus', [JobController::class, 'changeReportStatus'])->name('report-issue.changeReportStatus');

	Route::any('job/get-job-checklist-details/', [JobController::class, 'getJobChecklistDetails'])->name('job.get-job-checklist-details');

	Route::any('job/checklist/{jobId?}/save', [JobController::class, 'saveJobChecklist'])->name('job.save-job-checklist');

	Route::any('job/close/{jobId?}', [JobController::class, 'closeJob'])->name('job.close');

	/*Job Cables*/
	Route::resource('job-cables', JobCableController::class, ['names' => [
		    'index'  => 'job-cables.index',
		    'create' => 'job-cables.create',
		    'store'  => 'job-cables.store',
		    'show'   => 'job-cables.show',
		    'edit'   => 'job-cables.edit',
		    'update' => 'job-cables.update',
		], 'except'  => ['index', 'destroy']
	]);

	Route::get('job-cables/index/{jobId?}', [JobCableController::class, 'index'])->name('job-cables.index');

	/*Route::post('job-cables/destroy/{id?}', [JobCableController::class, 'destroy'])->name('cable-masters.destroy');*/

	Route::group(['prefix' => 'job-cable'],function () {
		Route::post('/get-cable-id', [JobCableController::class, 'getAutoGeneratedCableId'])->name('job-cables.get-cable-id');

		Route::post('/destroy/{id?}', [JobCableController::class, 'destroy'])->name('job-cables.destroy');

		Route::post('/get-cable-type-details', [JobCableController::class, 'getCableTypeDetails'])->name('job-cables.get-cable-type-details');
	});
});
// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
