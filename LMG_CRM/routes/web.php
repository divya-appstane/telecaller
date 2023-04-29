<?php

use App\Http\Controllers\AdminDashboard;
use App\Http\Controllers\AdminLeadsController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\TerritoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\indexController;
use App\Http\Controllers\TelecallerDashboard;
use App\Http\Controllers\TelecallerLeadsController;
use App\Http\Controllers\MarketingDashboard;
use App\Http\Controllers\MarketingLeadsController;
use App\Http\Controllers\CrmDashboard;
use App\Http\Controllers\CrmLeadsController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\FeedbackController;
use App\Models\LMGEmployee;
use App\Models\UserAdmin;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
////////////////////////////////////////////////
Route::get('/changePasswords', 
    function ()
    {
        // INSERT PASSWORD FILED VALUE TO EMPLOYEE TABLE
        $employees = LMGEmployee::get();
        foreach ($employees as $emp) {
            if(empty($emp->password))
            {            
                $emp->password = Hash::make($emp->emppass);
                $emp->update();
            }
        } 

        // INSERT PASSWORD FIELD VALUE TO ADMIN TABLE
        // $admins = UserAdmin::get();
        // foreach ($admins as $admin) {
        //     if(empty($admin->adpass)){
        //         $admin->adpass = $admin->password;
        //         $admin->password = Hash::make($admin->password);
        //         $admin->update();
        //     }
        // } 
    }
);
/////////////////////////////////////////////////


////////////////////////////////////////////////
//////// SYSTEM CLEAN UPS //////////////////////
Route::get('/cleanProjectEnvironment', 
    function()
    {
        \Artisan::call('optimize:clear');
        // \Artisan::call('make:controller TestController --resource');
        // \Artisan::call('composer dump:autoload');
        system('composer dump-autoload');
        dd("Done!");
    }
);


////////////////////////////////////////////////

Route::get('/getInfo', function(){
    phpinfo();
});

Route::group(['prefix' => '/'], 
    function() {
        // Middleware group
        Route::middleware('check_if_user_login')->group(
            function()
            {
                Route::get('', [indexController::class, 'viewLoginPage'])->name('user.viewLoginPage');
                Route::post('', [indexController::class, 'verifyLogin'])->name('user.checklogin');
            }
        );

        Route::middleware('guest:front')->group(
            function()
            {
                Route::get('/view-profile',[indexController::class, 'viewProfile'])->name('viewMyProfile');
                Route::get('/edit-profile',[indexController::class, 'editProfile'])->name('editMyProfile');
                Route::post('/edit-profile',[indexController::class, 'updateProfile'])->name('updateMyProfile');
                Route::get('/change-password', function(){
                    $user = Auth::guard('front')->user();
                    $title = $user->designation == 1 ? 'Admin' : 'User';
                    $title .= " | Change Password";
                    return view('user.changePassword', compact('title'));
                })->name('changeProfilePassword');
                Route::post('/change-password',[indexController::class, 'changePassword'])->name('updatePassword');
                Route::post('/verify-old-password', [indexController::class, 'verifyOldPassword'])->name('verifyOldPassword');
                
                Route::middleware('rolepermission:super-admin')->prefix('/admin')->name('admin.')->group(
                    function()
                    {
                        Route::get('/system-users', [indexController::class, 'getAllSystemUsers'])->name('system.users');
                        Route::post('/toggleAccountStatus', [indexController::class, 'toggleUserAccountStatus'])->name('system.users.toggleStatus');
                        Route::get('/viewUserProfile/{emp_id}', [indexController::class, 'show'])->name('system.viewUserProfile');
                        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
                        Route::prefix('/role-permission')->name("role-permission.")->group(
                            function()
                            {
                                Route::post('/updatePermission', [RolePermissionController::class, 'store'])->name('store.storePermission');
                                Route::get('/viewAllRole', [RolePermissionController::class, 'index'])->name('view.allRole');
                                Route::get('/viewAllPermission/{id}', [RolePermissionController::class, 'show'])->name('view.allPermission');
                            }
                        );
                        Route::prefix('/leads')->name("leads.")->group(
                            function()
                            {
                                Route::get('/viewAllLeads', [AdminLeadsController::class, 'index'])->name('view.allLeads');
                                Route::get('/viewSingleLead/{id}', [AdminLeadsController::class, 'show'])->name('view.single');
                                Route::post('/editSingleLead', [AdminLeadsController::class, 'update'])->name('update.single');
                                Route::get('/viewLeadfollowUpDetails/{id}', [AdminLeadsController::class, 'getFollowupList'])->name('view.followUpsDetails');
                                Route::get('/viewLeadSinglefollowUpDetails/{id}', [AdminLeadsController::class, 'getSingleFollowupData'])->name('view.singleFollowUpsDetails');
                                Route::get('/addNewLeadBulk', [AdminLeadsController::class, 'createBulkLead'])->name('add.bulkLeadForm');
                                Route::post('/addNewLeadBulk', [AdminLeadsController::class, 'storeBulkLead'])->name('store.bulkLead');
                                Route::get('/addNewLead', [AdminLeadsController::class, 'create'])->name('add.sigleLeadForm');
                                Route::post('/addNewLead', [AdminLeadsController::class, 'store'])->name('store.sigleLead');
                            }
                        );
                    }
                );
                Route::middleware(['rolepermission:business-development-associate|business-development-executive|business-development-manager'])->prefix('/telecaller')->name("telecaller.")->group(
                    function()
                    {
                       
                        Route::get('/dashboard', [TelecallerDashboard::class, 'index'])->name('dashboard')->middleware('prevent-back-history');
                        
                        Route::prefix('/leads')->name("leads.")->group(
                            function()
                            {
                                Route::middleware('prevent-back-history')->group(function(){
                                    Route::get('/viewAllLeads', [TelecallerLeadsController::class, 'index'])->name('view.allLeads');
                                    Route::get('/viewPendingLeads', [TelecallerLeadsController::class, 'getPendingLeads'])->name('pendingLeads');
                                    Route::get('/viewSingleLead/{id}', [TelecallerLeadsController::class, 'show'])->name('view.single');
                                    Route::get('/viewLeadfollowUpDetails/{id}', [TelecallerLeadsController::class, 'getFollowupList'])->name('view.followUpsDetails');
                                    Route::get('/viewLeadSinglefollowUpDetails/{id}', [TelecallerLeadsController::class, 'getSingleFollowupData'])->name('view.singleFollowUpsDetails');
                                    Route::get('/addNewLead', [TelecallerLeadsController::class, 'create'])->name('add.sigleLeadForm');
                                    Route::post('/addNewLead', [TelecallerLeadsController::class, 'store'])->name('store.sigleLead');
                                    Route::get('/addNewLeadBulk', [TelecallerLeadsController::class, 'createBulkLead'])->name('add.bulkLeadForm');
                                    Route::post('/addNewLeadBulk', [TelecallerLeadsController::class, 'storeBulkLead'])->name('store.bulkLead');
                                    Route::post('/editSingleLead', [TelecallerLeadsController::class, 'update'])->name('update.single');
                                });

                                Route::get('/leadCallEngagement/{id}', [TelecallerLeadsController::class, 'getLeadCalledPage'])->name('view.leadCalledEngagementView');
                                Route::get('/leadCallReEngagement/{id}', [TelecallerLeadsController::class, 'getLeadReCalledPage'])->name('view.leadCalledReEngagementView');
                                Route::post('/saveCallEngagementData', [TelecallerLeadsController::class, 'saveLeadCallEngagementData'])->name('save.leadCallEngagementData');

                                Route::post('/createsession',[TelecallerLeadsController::class, 'createSession'])->name('store.createSession');
                            }
                        );
                    }
                );
                Route::middleware('rolepermission:business-development-associate-on-field|business-development-executive-on-field|business-development-manager-on-field')->prefix('/marketing')->name("marketing.")->group(
                    function()
                    {
                        Route::get('/dashboard', [MarketingDashboard::class, 'index'])->name('dashboard');
                        Route::prefix('/leads')->name("leads.")->group(
                            function()
                            {
                                Route::get('/viewAllLeads', [MarketingLeadsController::class, 'index'])->name('view.allLeads');
                                Route::get('/viewPendingLeads', [MarketingLeadsController::class, 'getPendingLeads'])->name('pendingLeads');
                                Route::get('/viewSingleLead/{id}', [MarketingLeadsController::class, 'show'])->name('view.single');
                                Route::get('/viewLeadfollowUpDetails/{id}', [MarketingLeadsController::class, 'getFollowupList'])->name('view.followUpsDetails');
                                Route::get('/viewLeadSinglefollowUpDetails/{id}', [MarketingLeadsController::class, 'getSingleFollowupData'])->name('view.singleFollowUpsDetails');
                                Route::post('/editSingleLead', [MarketingLeadsController::class, 'update'])->name('update.single');
                                Route::get('/sales-meeting/{id}', [MarketingLeadsController::class, 'editMeeting'])->name('show.salesMeetingg');
                                Route::post('/sales-meeting', [MarketingLeadsController::class, 'updateMeeting'])->name('update.salesMeeting');
                                Route::post('/saveCallStatus', [MarketingLeadsController::class, 'saveCallStatus'])->name('saveCallStatus');
                                
                            }
                        );
                    }
                );
                Route::middleware('rolepermission:customer-relationship-executive|customer-relationship-manager')->prefix('/crm')->name("crm.")->group(
                    function()
                    {
                        Route::get('/dashboard', [CrmDashboard::class, 'index'])->name('dashboard')->middleware('prevent-back-history');
                        Route::prefix('/leads')->name("leads.")->group(
                            function()
                            {
                                Route::middleware('prevent-back-history')->group(function(){

                                    Route::get('/viewAllLeads', [CrmLeadsController::class, 'index'])->name('view.allLeads');
                                    Route::get('/viewPendingLeads', [CrmLeadsController::class, 'getPendingLeads'])->name('view.pendingLeads');
                                    Route::get('/pendingLeadsFeedback2', [CrmLeadsController::class, 'getPendingLeads'])->name('view.pendingLeadsFeedback2');
                                    Route::get('/pendingLeadsFeedback3', [CrmLeadsController::class, 'getPendingLeads'])->name('view.pendingLeadsFeedback3');
                                    Route::get('/viewPendingLeadsNotIn', [CrmLeadsController::class, 'getPendingLeads'])->name('view.pendingLeadsNotIn');
                                    Route::get('/viewPendingLeadsRegister', [CrmLeadsController::class, 'getPendingLeads'])->name('view.pendingLeadsRegister');
                                    Route::get('/viewLeadfollowUpDetails/{id}', [CrmLeadsController::class, 'getFollowupList'])->name('view.followUpsDetails');
                                    Route::get('/viewLeadSinglefollowUpDetails/{id}', [CrmLeadsController::class, 'getSingleFollowupData'])->name('view.singleFollowUpsDetails');
                                
                                });
                                
                                Route::get('/feedbackCallView/{id}', [CrmLeadsController::class, 'feedbackCall'])->name('view.feedbackCallView');
                                Route::get('/feedbackCallViewStepTwo/{id}', [CrmLeadsController::class, 'feedbackCallStepTwo'])->name('view.feedbackCallViewStepTwo');
                                Route::get('/feedbackCallViewStepThree/{id}', [CrmLeadsController::class, 'feedbackCallStepThree'])->name('view.feedbackCallViewStepThree');
                                Route::get('/feedbackCallViewNotIntersted/{id}', [CrmLeadsController::class, 'feedbackCallNotIn'])->name('view.feedbackCallViewNotIntersted');
                                Route::get('/feedbackCallViewRegister/{id}', [CrmLeadsController::class, 'feedbackCallRegister'])->name('view.feedbackCallViewRegister');
                                Route::post('/feedbackCallView', [CrmLeadsController::class, 'update'])->name('update.single');
                                Route::post('/feedbackAdd', [CrmLeadsController::class, 'store'])->name('store.feedback');
                                Route::post('/feedbackAddStepTwo', [CrmLeadsController::class, 'storetwo'])->name('storetwo.feedbacktwo');
                                Route::post('/feedbackAddStepThree', [CrmLeadsController::class, 'storethree'])->name('storethree.feedbackthree');
                                Route::post('/feedbackAddNotIn', [CrmLeadsController::class, 'storenotin'])->name('storenotin.feedbacknotin');
                                Route::post('/feedbackRegister', [CrmLeadsController::class, 'storeregister'])->name('storeregister.feedbackregister');

                                Route::post('/createsession',[CrmLeadsController::class, 'createSession'])->name('store.createSession');
                                
                            }
                        );
                    }
                );
                Route::prefix('/area')->name("area.")->group(
                    function() {
                        Route::middleware('rolepermission:,area-master-add')->get('/addNewArea', [AreaController::class, 'create'])->name('addArea');
                        Route::middleware('rolepermission:,area-master-add')->post('/addNewArea', [AreaController::class, 'store'])->name('storeArea');
                        Route::middleware('rolepermission:,area-master-view')->get('/viewArea', [AreaController::class, 'index'])->name('viewArea');
                        Route::middleware('rolepermission:,area-master-update')->post('/toggleAeraStatus', [AreaController::class, 'changeAreaStatus'])->name('changeStatus');
                        Route::middleware('rolepermission:,area-master-update')->get('/editArea/{id}', [AreaController::class, 'show'])->name('editArea');
                        Route::middleware('rolepermission:,area-master-update')->post('/updateArea', [AreaController::class, 'update'])->name('updateArea');
                        Route::middleware('rolepermission:,area-master-delete')->post('/deleteArea', [AreaController::class,'destroy'])->name('deleteArea');
                        Route::middleware('rolepermission:,area-master-view')->post('/areawiseBDF', [AreaController::class,'areawisebdf'])->name('areawiseBDF');
                    }
                );
                Route::prefix('/territory')->name("territory.")->group(
                    function() {
                        Route::middleware('rolepermission:,territory-master-add')->get('/addNewTerritory', [TerritoryController::class, 'create'])->name('addTerritory');
                        Route::middleware('rolepermission:,territory-master-add')->post('/addNewTerritory', [TerritoryController::class, 'store'])->name('storeTerritory');
                        Route::middleware('rolepermission:,territory-master-view')->get('/viewTerritory', [TerritoryController::class, 'index'])->name('viewTerritory');
                        Route::middleware('rolepermission:,territory-master-view')->get('/viewDetails/{agentcode?}', [TerritoryController::class, 'show'])->name('viewDetails');
                    }
                );

                Route::prefix('/feedback')->name("feedback.")->group(
                    function() {
                        Route::middleware('rolepermission:,feedback-master-add')->get('/addNewFeedback', [FeedbackController::class, 'create'])->name('addFeedback');
                        Route::middleware('rolepermission:,feedback-master-add')->post('/addNewFeedback', [FeedbackController::class, 'store'])->name('storeFeedback');
                        Route::middleware('rolepermission:,feedback-master-view')->get('/viewFeedback', [FeedbackController::class, 'index'])->name('viewFeedback');
                        Route::middleware('rolepermission:,feedback-master-update')->get('/editFeedback/{id}', [FeedbackController::class, 'show'])->name('editFeedback');
                        Route::middleware('rolepermission:,feedback-master-update')->post('/updateFeedback', [FeedbackController::class, 'update'])->name('updateFeedback');
                        Route::middleware('rolepermission:,feedback-master-delete')->post('/deleteFeedback', [FeedbackController::class,'destroy'])->name('deleteFeedback');
                        Route::middleware('rolepermission:,feedback-master-update')->post('/changeOrder', [FeedbackController::class,'changeOrder'])->name('changeOrder');
                    }
                );
                Route::get('/logout', [indexController::class, 'logout'])->name('logout');
            }
        );
    }
);

