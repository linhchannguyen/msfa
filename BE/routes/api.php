<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\SampleUpload;
use Illuminate\Support\Facades\File;

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
Route::group(['middleware' => 'action.log'], function () {
    Route::group(['middleware' => 'convert.value'], function () {
        // WHEN NOT LOGIN, define text "lowercase" , example business type A. -> a. Z. -> z.
        // START GROUP BUSINESS Z
        Route::name('z.')->group(function () {
            Route::post('/login', 'App\Http\Controllers\Api\AuthController@Login');
            Route::get('/get-system-parameter', 'App\Http\Controllers\Api\SystemParameterController@getSystemParameter');

            Route::prefix('develop')->namespace('App\Http\Controllers\Api')->group(function () {
                Route::post('/login', 'DevelopController@login');
            });
        });
        // END GROUP BUSINESS Z

        // WHEN HAS LOGIN , text "uppercase" , example A. B. C.
        Route::group(['middleware' => 'jwt.verify'], function () {
            // START GROUP BUSINESS A
            Route::name('A.')->group(function () {
                Route::prefix('stock')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/get-screen-data', 'StockController@getScreenData');
                    Route::post('/', 'StockController@searchStock');
                    Route::get('/content', 'StockController@allContent');
                    Route::get('/facility-category', 'StockController@facilityCategoty');
                    Route::post('/edit', 'StockController@editStock');
                    Route::post('/create', 'StockController@addStock');
                    Route::delete('/', 'StockController@deleteStock');
                });
                Route::prefix('/daily-report')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/', 'DailyReportController@getListData');
                    Route::get('/mode', 'DailyReportController@getModeReport');
                    Route::get('/detail', 'DailyReportController@getDataDetail');
                    Route::put('/vacation', 'DailyReportController@registerVacationReport');
                    Route::delete('/vacation', 'DailyReportController@deleteVacationReport');
                    Route::put('/temporarily-save', 'DailyReportController@saveReport');
                    Route::post('/submit', 'DailyReportController@submitReport');
                    Route::delete('/submit', 'DailyReportController@cancelReport');
                    Route::post('/approval', 'DailyReportController@approvalReport');
                    Route::delete('/approval', 'DailyReportController@cancelApprovalReport');
                });

                Route::prefix('calendar')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/', 'CalendarController@getListData');
                    Route::get('/schedule', 'CalendarController@getListSchedule');
                });
                Route::prefix('schedule')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/get-screen-data-schedule', 'ScheduleController@getScreenDataSchedule');
                    Route::post('/search-visit', 'ScheduleController@searchVisit');
                    Route::get('/get-schedule', 'ScheduleController@getScheduleListByUserLogin');
                    Route::post('/copy-schedule', 'ScheduleController@copySchedule');
                    Route::post('/add-facility-select', 'ScheduleController@addFacilitySelect');
                    Route::post('/add-facility-person-select', 'ScheduleController@addStock');
                    Route::get('/search-stock', 'ScheduleController@getStockList');
                    Route::get('/select-facility-person-group', 'ScheduleController@selectFacilityPersonGroup');
                    Route::get('/get-facility-group', 'ScheduleController@getFacilityGroup');
                    Route::get('/get-person-group', 'ScheduleController@getPersonGroup');
                    Route::put('/update-schedule', 'ScheduleController@updateSchedule');
                    Route::post('/add-other-than-interview', 'ScheduleController@addOtherThanInterview');
                    Route::post('/add-visit', 'ScheduleController@addVisit');
                });
                Route::prefix('/unapproved-list')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/', 'UnapprovedController@getData');
                });
                Route::prefix('/watch-user-color')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::post('/change-color', 'WatchUserColorController@changeColorUser');
                    Route::get('/get-screen-data-calendar', 'WatchUserColorController@getScreenDataCalendar');
                    Route::get('/get-list-by-user-and-type', 'WatchUserColorController@getListScheduleByUserAndType');
                    Route::get('/get-list-by-user-login', 'WatchUserColorController@getWatchUserList');
                    Route::delete('/delete/{id}', 'WatchUserColorController@deleteWatchUser');
                });

                Route::prefix('/interview-details')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/get-screen-data', 'InterviewDetailsController@getScreenData');
                    Route::get('/get-active-date', 'InterviewDetailsController@getActiveDate');
                    Route::get('/get-interview-content', 'InterviewDetailsController@getInterviewContent');
                    Route::get('/get-date-time-setting', 'InterviewDetailsController@getDateTimeSetting');
                    Route::delete('/delete-schedule', 'InterviewDetailsController@deleteSchedule');
                    Route::delete('/delete-interviewer', 'InterviewDetailsController@deleteInterviewer');
                    Route::delete('/delete-all-interviewer', 'InterviewDetailsController@deleteAllInterview');
                    Route::post('/save-date-time-setting', 'InterviewDetailsController@saveDateTimeSetting');

                    //InternalSchedule
                    Route::get('/internal-schedule', 'InterviewDetailsController@getInternalSchedule');
                    Route::post('/save-internal-schedule', 'InterviewDetailsController@saveInternalSchedule');
                    Route::delete('/delete-internal-schedule', 'InterviewDetailsController@deleteInternalSchedule');

                    //PrivateSchedule
                    Route::get('/private-schedule', 'InterviewDetailsController@getPrivateSchedule');
                    Route::post('/save-private-schedule', 'InterviewDetailsController@savePrivateSchedule');
                    Route::delete('/delete-private-schedule', 'InterviewDetailsController@deletePrivateSchedule');
                    Route::post('/add-interview-destination', 'InterviewDetailsController@addInterviewDestination');

                    //Add Stock
                    Route::post('/check-person-cd', 'InterviewDetailsController@checkPersonCd');
                    Route::post('/add-stock', 'InterviewDetailsController@addStock');

                    Route::post('/create-plan-schedule', 'InterviewDetailsController@createPlanSchedule');
                });
                Route::prefix('/interview-detailed-input')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/get-screen-data', 'InterviewDetailedInputController@getScreenData');
                    Route::get('/', 'InterviewDetailedInputController@getInterviewDetailedInput');
                    Route::get('/check-exist', 'InterviewDetailedInputController@checkInterviewDetailedInput');
                    Route::get('/get-detail-area', 'InterviewDetailedInputController@getDetailArea');
                    Route::post('/save-plan', 'InterviewDetailedInputController@savePlan');
                    Route::post('/save', 'InterviewDetailedInputController@save');
                });
            });
            // END GROUP BUSINESS A

            // START GROUP BUSINESS B
            Route::name('B.')->group(function () {
                Route::prefix('/briefing-search')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/', 'BriefingSearchController@getIndexBriefing');
                    Route::get('/list-data', 'BriefingSearchController@getListDataBriefing');
                    Route::get('/index-detail', 'BriefingSearchController@getDataIndexDetailBriefing');
                    Route::get('/detail', 'BriefingSearchController@getDataDetailBriefing');
                    Route::post('/save-detail', 'BriefingSearchController@saveDetailBriefing');
                    Route::post('/submit', 'BriefingSearchController@submitBriefing');
                    Route::post('/approval', 'BriefingSearchController@approvalBriefing');
                    Route::post('/remand', 'BriefingSearchController@remandBriefing');
                    Route::delete('/delete-detail', 'BriefingSearchController@deleteDetailBriefing');
                    Route::put('/cancel-submit', 'BriefingSearchController@cancelSubmitBriefing');
                    Route::put('/pending', 'BriefingSearchController@pendingBriefing');
                });
            });
            // END GROUP BUSINESS B

            // START GROUP BUSINESS C
            Route::name('C.')->group(function () {
                Route::prefix('/attendant-management')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/get-screen-data', 'AttendantManagementController@getScreenDataAttendant');
                    Route::post('/search', 'AttendantManagementController@getData');
                    Route::post('/add-convention-attendee', 'AttendantManagementController@addConventionAttendee');
                });

                Route::prefix('/convention-search')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/', 'ConventionSearchController@index');
                    Route::get('/list', 'ConventionSearchController@getData');
                    Route::get('/index', 'ConventionSearchController@getIndex');
                    Route::get('/detail', 'ConventionSearchController@getConventionDetail');
                    Route::get('/detail/copy', 'ConventionSearchController@copyConventionDetail');
                    Route::get('/detail/selection-series', 'ConventionSearchController@seriesConventionDetail');
                    Route::post('/detail/save-plan', 'ConventionSearchController@savePlanConventionDetail');
                    Route::post('/detail/submit-plan', 'ConventionSearchController@submitPlanConventionDetail');
                    Route::post('/detail/save-result', 'ConventionSearchController@saveResultConventionDetail');
                    Route::post('/detail/submit-result', 'ConventionSearchController@submitResultConventionDetail');
                    Route::post('/detail/cancel-submit', 'ConventionSearchController@cancelSubmitConvention');
                    Route::post('/detail/remand', 'ConventionSearchController@remandConvention');
                    Route::post('/detail/approval', 'ConventionSearchController@approvalConvention');
                    Route::delete('/detail/delete', 'ConventionSearchController@deleteConvention');
                    Route::delete('/detail/pending', 'ConventionSearchController@pendingConvention');
                });

                Route::prefix('/lecture-series-selection')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/', 'LectureSeriesSelectionController@getLectureSeriesSelection');
                    Route::post('/register', 'LectureSeriesSelectionController@register');
                });
                Route::prefix('export')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::any('/list-attendee', 'ExportController@listAttendee');
                });

                Route::prefix('attendant-collective-registration')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::post('/import-csv', 'AttendantCollectiveRegistrationController@importCsv');
                });
                Route::prefix('/attendant-bulk-registration-error-content-output')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::post('/export-csv', 'AttendantBulkRegistrationErrorContentOutputController@exportCsv');
                });
            });
            // END GROUP BUSINESS C

            // START GROUP BUSINESS D
            Route::name('D.')->group(function () {
                Route::prefix('custom-material-management')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/get-screen-data', 'CustomMaterialsManagementController@getScreenDataCustomMaterial');
                    Route::post('/search', 'CustomMaterialsManagementController@searchCustomMaterial');
                });
                Route::prefix('/material-evaluation-input')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/', 'MaterialEvaluationInputController@getMaterialEvaluationInput');
                    Route::post('/register-item', 'MaterialEvaluationInputController@registerItem');
                });

                Route::prefix('/document-search')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/', 'DocumentSearchController@index');
                    Route::get('/list', 'DocumentSearchController@getData');
                    Route::get('/detail', 'DocumentSearchController@getDocumentDetail');
                    Route::get('/detail/profile', 'DocumentSearchController@getDocumentProfile');
                    Route::get('/detail/review-comment', 'DocumentSearchController@getDocumentReviewComment');
                    Route::get('/detail/custom', 'DocumentSearchController@getDocumentCustom');
                    Route::get('/registration', 'DocumentSearchController@indexDocumentRegistration');
                    Route::get('/registration/detail', 'DocumentSearchController@getDocumentRegistrationDetail');
                    Route::post('/registration/save', 'DocumentSearchController@saveDocumentRegistrationDetail');
                    Route::delete('/registration/delete', 'DocumentSearchController@deleteDocumentRegistrationDetail');
                });
                // D01-S07 slide show
                Route::get('document/slide-show/{id}', 'App\Http\Controllers\Api\DocumentCustomController@slideShow')->name('api.document.slide-show');
                Route::prefix('/document-custom')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/list-document-original', 'DocumentCustomController@listOriginalDocument');
                    Route::get('/content-original-document/{id}', 'DocumentCustomController@contentOriginalDocument');
                    Route::any('/create', 'DocumentCustomController@createCustomDocument')->name('api.document-custom.create');
                    Route::any('/update/{id}', 'DocumentCustomController@updateCustomDocument')->name('api.document-custom.update');
                    Route::post('/delete/{id}', 'DocumentCustomController@deleteCustomDocument')->name('api.document-custom.delete');
                    Route::post('/change-status-disue-flag/{id}', 'DocumentCustomController@changeStatusDisueFlag')->name('api.document-custom.change-status-dissue-flag');
                });
            });
            // END GROUP BUSINESS D

            // START GROUP BUSINESS E
            Route::name('E.')->group(function () {
                Route::prefix('/qa-management')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/get-screen-data', 'QAManagementController@getScreenData');
                    Route::post('/create-qa', 'QAManagementController@createtQA');
                    Route::post('/search', 'QAManagementController@searchQA');
                });

                Route::prefix('/posting-prohibited-user-management')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/get-posting-prohibited', 'PostedUserManagementController@allPostingProhibited');
                    Route::delete('/cancel-posting-prohibited', 'PostedUserManagementController@cancelPostingProhibited');
                    Route::get('/get-unsuitable-report', 'PostedUserManagementController@getUnsuitableReport');
                });
                Route::prefix('manage-qa')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('question/{id}', 'QuestionAndAnswerController@infoQA')->name('api.manage-qa.info-question');
                    Route::get('answer/list/{id}', 'QuestionAndAnswerController@listAnswerOfQuestion')->name('api.manage-qa.list-answer');

                    Route::post('answer/register', 'QuestionAndAnswerController@registerAnswer')->name('api.manage-qa.register-answer');
                    Route::post('answer/register-best-answer/{id}', 'QuestionAndAnswerController@registerBestAnswer')->name('api.manage-qa.register-best-answer');

                    Route::post('question/unsuitable-report/register/{id}', 'QuestionAndAnswerController@registerUnsuitableReportQuestion')->name('api.manage-qa.unsuitable-report-question');
                    Route::post('answer/unsuitable-report/register/{id}', 'QuestionAndAnswerController@registerUnsuitableReportAnswer')->name('api.manage-qa.unsuitable-report-answer');

                    Route::post('question/accept-answer/{id}', 'QuestionAndAnswerController@openAcceptAnswer')->name('api.manage-qa.accept-answer');
                    Route::post('question/stop-accept-answer/{id}', 'QuestionAndAnswerController@closeAcceptAnswer')->name('api.manage-qa.stop-accept-answer');

                    Route::post('register-posting-prohibited/{id}', 'QuestionAndAnswerController@registerPostingProhibited')->name('api.manage-qa.register-posting-prohibited');

                    Route::post('question/cancel-inform/{id}', 'QuestionAndAnswerController@cancelInformQuestion')->name('api.manage-qa.cancel-inform-question');
                    Route::post('answer/cancel-inform/{id}', 'QuestionAndAnswerController@cancelInformAnswer')->name('api.manage-qa.cancel-inform-answer');

                    Route::post('question/cancel-inform-all/{id}', 'QuestionAndAnswerController@cancelInformAllQuestion')->name('api.manage-qa.cancel-all-inform-question');
                    Route::post('answer/cancel-inform-all/{id}', 'QuestionAndAnswerController@cancelInformAllAnswer')->name('api.manage-qa.cancel-all-inform-answer');

                    Route::post('question/delete/{id}', 'QuestionAndAnswerController@deleteQuestion')->name('api.manage-qa.delete-question');
                    Route::post('answer/delete/{id}', 'QuestionAndAnswerController@deleteAnswer')->name('api.manage-qa.delete-answer');

                    Route::get('question/list-unsuitable-report/{id}', 'QuestionAndAnswerController@listUnsuitableReportQuestion')->name('api.manage-qa.list-unsuitable-report-question');
                    Route::get('answer/list-unsuitable-report/{id}', 'QuestionAndAnswerController@listUnsuitableReportAnswer')->name('api.manage-qa.list-unsuitable-report-answer');
                });
            });
            // END GROUP BUSINESS E

            // START GROUP BUSINESS F
            Route::name('F.')->group(function () {
                Route::prefix('/knowledge-management')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/get-screen-data', 'KnowledgeManagementController@getScreenData');
                    Route::post('/search', 'KnowledgeManagementController@searchKnowledge');
                    Route::post('/search-pre', 'KnowledgeManagementController@searchPre');
                    Route::get('/knowledge-detail/{id}', 'KnowledgeManagementController@getKnowledgeDetail');
                    Route::post('/register-knowledge-nice', 'KnowledgeManagementController@registerKnowledgeNice');
                    Route::post('/register-comment', 'KnowledgeManagementController@registerComment');
                    Route::put('/delete-knowledge-nice', 'KnowledgeManagementController@deleteKnowledgeNice');
                    Route::post('/register-knowledge-request', 'KnowledgeManagementController@registerKnowledgeRequest');
                });
                Route::prefix('/timeline-search')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/', 'TimelineSearchController@getIndexTimeline');
                    Route::get('/list-data', 'TimelineSearchController@getListDataTimeline');
                });

                Route::prefix('/knowledge-input')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/get-screen-data', 'KnowledgeInputController@getScreenData');
                    Route::get('/', 'KnowledgeInputController@getKnowledgeInput');
                    Route::post('/update-and-create', 'KnowledgeInputController@updateAndCreate');
                    Route::post('/none-public', 'KnowledgeInputController@nonePublic');
                    Route::post('/public', 'KnowledgeInputController@publicKnowledge');
                    Route::post('/remand', 'KnowledgeInputController@remand');
                    Route::post('/approve', 'KnowledgeInputController@approve');
                    Route::post('/submit', 'KnowledgeInputController@submit');
                    Route::post('/rejection', 'KnowledgeInputController@rejection');
                    Route::delete('/delete-knowledge', 'KnowledgeInputController@deleteKnowledge');
                    Route::delete('/delete-timeline', 'KnowledgeInputController@deleteTimeline');
                });
            });
            // END GROUP BUSINESS F

            // START GROUP BUSINESS G
            Route::name('G.')->group(function () {
                Route::prefix('/target-facility-management')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/get-screen-data', 'TargetFacilityManagementController@getScreenData');
                    Route::get('/', 'TargetFacilityManagementController@targetFacilityManagement');
                });

                Route::prefix('/in-facility-target-person-management')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/get-screen-data', 'InFacilityTargetPersonManagementController@getScreenData');
                    Route::post('/', 'InFacilityTargetPersonManagementController@getInFacilityTargetPersonManagement');
                    Route::post('/save-in-facility-target-person', 'InFacilityTargetPersonManagementController@saveInFacilityTargetPerson');
                    Route::post('/export', 'InFacilityTargetPersonManagementController@exportInFacility');
                });

                Route::prefix('/target-facility-person-setting')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/get-screen-data', 'TargetFacilityPersonSettingController@getScreenData');
                    Route::post('/', 'TargetFacilityPersonSettingController@getTargetFacilityPersonSetting');
                    Route::post('/save-target-facility-person-setting', 'TargetFacilityPersonSettingController@saveTargetFacilityPersonSetting');
                    Route::post('/export', 'TargetFacilityPersonSettingController@exportTargetFacilityPersonSetting');
                });

                Route::prefix('/target-selection-period-management')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/', 'TargetSelectionPeriodManagementController@getTargetSelectionPeriodManagement');
                    Route::post('/save', 'TargetSelectionPeriodManagementController@saveTargetSelectionPeriodManagement');
                });
            });
            // END GROUP BUSINESS G

            // START GROUP BUSINESS H
            Route::name('H.')->group(function () {
                Route::prefix('/person-detail-notes')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/get-screen-data', 'PersonDetailNoteController@getScreenDataNotes');
                    Route::post('/search/{id}', 'PersonDetailNoteController@searchPersonDetailNotes');
                    Route::post('/create', 'PersonDetailNoteController@createPersonDetailNotes');
                    Route::delete('/delete', 'PersonDetailNoteController@deletePersonDetailNotes');
                    Route::put('/update', 'PersonDetailNoteController@updatePersonDetailNotes');
                    Route::put('/person-consideration-confirm/{id}', 'PersonDetailNoteController@personConsiderationConfirm');
                });

                Route::prefix('/person-detail-network')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/get-screen-data/{id}', 'PersonDetailNetworkController@getScreenDataNetwork');
                    Route::post('/search', 'PersonDetailNetworkController@searchPersonDetailNetwork');
                    Route::get('/workplace-history/{id}', 'PersonDetailNetworkController@getWorkplaceHistoryList');
                });

                Route::prefix('/person-detail-time-line')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/get-screen-data', 'PersonDetailTimelineController@getScreenDataTimeline');
                    Route::post('/search', 'PersonDetailTimelineController@searchTimeline');
                });
                Route::prefix('/facility-search')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/', 'FacilitySearchController@getData');
                    Route::get('/information', 'FacilitySearchController@getFacilityInformation');
                    Route::get('/basic-information', 'FacilitySearchController@getFacilityDetail');
                    Route::put('/basic-information/consultation-time', 'FacilitySearchController@saveConsultationTimeFacility');
                    Route::get('/working-individual/index', 'FacilitySearchController@getIndexWorkingIndividualFacility');
                    Route::get('/working-individual', 'FacilitySearchController@getWorkingIndividualFacility');
                    Route::put('/working-individual/segment-type', 'FacilitySearchController@saveSegmentTypeFacility');
                });

                Route::prefix('/person-search')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/', 'PersonSearchController@getData');
                    Route::get('/information', 'PersonSearchController@getPersonInformation');
                    Route::get('/basic-information', 'PersonSearchController@getPersonDetail');
                    Route::get('/basic-information/department', 'PersonSearchController@getDepartmentPersonDetail');
                    Route::put('/basic-information/medical-office', 'PersonSearchController@saveMedicalOffice');
                    Route::get('/working-individual', 'PersonSearchController@getWorkingIndividual');
                });

                Route::prefix('/personal-details-working-information')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/get-screen-data', 'PersonalDetailsWorkingInformationController@getScreenData');
                    Route::post('/', 'PersonalDetailsWorkingInformationController@getWorkingInformation');
                });

                Route::prefix('/facility-details-time-line')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/get-screen-data', 'FacilityDetailsTimeLineController@getScreenData');
                    Route::get('/', 'FacilityDetailsTimeLineController@getFacilityDetailsTimeLine');
                });
                Route::prefix('/facility-details-notes')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/get-screen-data', 'FacilityDetailsNotesController@getScreenData');
                    Route::get('/', 'FacilityDetailsNotesController@getFacilityDetailsNotes');
                    Route::post('/save-consideration-confirm', 'FacilityDetailsNotesController@saveConsiderationConfirm');
                    Route::delete('/delete-consideration', 'FacilityDetailsNotesController@deleteConsideration');
                    Route::post('/save-new-mode', 'FacilityDetailsNotesController@saveNewMode');
                    Route::post('/update-mode', 'FacilityDetailsNotesController@updateMode');
                });
            });
            // END GROUP BUSINESS H

            // START GROUP BUSINESS I
            Route::name('I.')->group(function () {
                Route::prefix('/user-management')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/user-list', 'UserManagementController@userList');
                    Route::post('/create-user', 'UserManagementController@createUser');
                    Route::post('/update-user', 'UserManagementController@updateUser');
                    Route::delete('/delete-user', 'UserManagementController@deleteUser');
                    Route::get('/user-organization', 'UserManagementController@getListUserOrganization');
                    Route::delete('/delete-user-organization', 'UserManagementController@deleteUserOrganization');
                    Route::post('/update-user-organization', 'UserManagementController@updateUserOrganization');
                    Route::get('/get-screen-data', 'UserManagementController@getScreenData');
                    Route::get('/get-approval-user', 'UserManagementController@getApprovalUser');
                    Route::post('/update-approval-user', 'UserManagementController@updateApprovalUser');
                    Route::delete('/delete-approval-user', 'UserManagementController@deleteApprovalUser');
                });

                Route::prefix('permission-setting')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/get-screen-data', 'PermissionSettingController@getScreenData');
                    Route::get('/', 'PermissionSettingController@getPermissionList');
                    Route::delete('/delete-permission', 'PermissionSettingController@deletePermission');
                    Route::post('/upsert-permission', 'PermissionSettingController@upsertPermission');
                });

                Route::prefix('password-reissue')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/', 'PasswordReissueController@getPasswordReissue');
                    Route::post('/send-mail-password-reissue', 'PasswordReissueController@sendMailPasswordReissue');
                });
                Route::prefix('recommended-period-confirmation')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/', 'RecommendedPeriodConfirmationController@getUserList');
                    Route::get('/orgnization', 'RecommendedPeriodConfirmationController@getUserListOrgnization');
                    Route::get('/role', 'RecommendedPeriodConfirmationController@getUserListRole');
                    Route::get('/approval', 'RecommendedPeriodConfirmationController@getUserListApproval');
                });
            });
            // END GROUP BUSINESS I

            // START GROUP BUSINESS Z
            Route::name('Z.')->group(function () {
                Route::get('/logout', 'App\Http\Controllers\Api\AuthController@logout');
                Route::post('/change-password', 'App\Http\Controllers\Api\AuthController@changePassword');
                Route::post('/proxy-user-selection', 'App\Http\Controllers\Api\AuthController@proxyUserSelection')->middleware('role.has:R90');

                Route::prefix('account')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/info', 'AccountSettingController@getAccountInfo');
                    Route::put('/info', 'AccountSettingController@putAccountInfo');
                    Route::get('/point', 'AccountSettingController@getAccountPoint');
                    Route::get('/point_target_type', 'AccountSettingController@getPointTargetType');
                    Route::post('/avatar', 'AccountSettingController@uploadAvatar');
                });

                Route::prefix('widget')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/', 'HomeController@getScreenData');
                    Route::get('/message', 'HomeController@getMessageList');
                    Route::post('/message/{id}', 'HomeController@readMessage');
                    Route::get('/inform', 'HomeController@getInformList');
                    Route::put('/informed', 'HomeController@informed');
                    Route::get('/activity-plan', 'HomeController@sameDayActivityPlan');
                    Route::get('/external-url', 'HomeController@externalURL');
                    Route::get('/non-submission', 'HomeController@nonSubmission');
                    Route::get('/actual-digestion-ranking', 'HomeController@actualDigestionRanking');
                    Route::get('/before-sales-results', 'HomeController@sameAsBeforeSalesResults');
                    Route::get('/actual-digestion-process', 'HomeController@actualDigestionProcess');
                });

                Route::prefix('message')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/', 'MessageController@allMessage');
                    Route::get('/{id}', 'MessageController@getMessageInfo');
                    Route::post('/', 'MessageController@createMessage');
                    Route::delete('/{id}', 'MessageController@deleteMessage');
                    Route::put('/{id}', 'MessageController@updateMessage');
                });
                Route::prefix('inform')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::post('/', 'InformController@searchInform');
                    Route::put('/archived', 'InformController@archived');
                    Route::put('/read-inform/{id}', 'InformController@readInform');
                    Route::get('/installed', 'InformController@installed');
                    Route::post('/register', 'InformController@registerInformSetting');
                });

                Route::prefix('inform-category')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/', 'InformCategoryController@getInformCategoryList');
                });
                Route::prefix('list-organization')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/', 'OrganizationController@getListData');
                    Route::get('/user', 'OrganizationController@getListUser');
                });

                Route::prefix('list-organization-management')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/', 'OrganizationManagementController@getListData');
                    Route::get('/user', 'OrganizationManagementController@getListUser');
                });

                Route::prefix('list-item')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/', 'ItemController@getListItem');
                    Route::get('/filter', 'ItemController@getDataFilter');
                    Route::post('/change-select', 'ItemController@changeSelectProduct');
                });

                Route::prefix('list-region')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/', 'RegionController@getListData');
                    Route::get('/prefecture', 'RegionController@getDataPrefecture');
                    Route::get('/prefecture/municipality', 'RegionController@getDataMunicipality');
                });

                Route::prefix('list-material')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/', 'MaterialController@getData');
                    Route::get('/filter', 'MaterialController@getDataFilter');
                    Route::get('/filter-org', 'MaterialController@getOrgDocuemnt');
                });
                Route::prefix('/list-condition-area')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/', 'ConditionAreaController@getListItem');
                    Route::get('/facility', 'ConditionAreaController@getListFacility');
                });

                Route::prefix('list-facility-personal')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/', 'FacilityPersonalController@getData');
                    Route::get('/facility', 'FacilityPersonalController@getListFacility');
                    Route::get('/medical-staff', 'FacilityPersonalController@getMedicalStaff');
                });
                Route::prefix('/list-facility-group')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/', 'FacilityGroupController@getData');
                    Route::delete('/delete', 'FacilityGroupController@deleteFacilityGroup');
                    Route::put('/update', 'FacilityGroupController@updateFacilityGroup');
                    Route::put('/change-select', 'FacilityGroupController@changeSelectFacilityGroup');
                });

                Route::prefix('/list-person-group')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/', 'PersonGroupController@getData');
                    Route::delete('/delete', 'PersonGroupController@deletePersonGroup');
                    Route::put('/update', 'PersonGroupController@updatePersonGroup');
                    Route::put('/change-select', 'PersonGroupController@changeSelectPersonGroup');
                });
                Route::prefix('/interview-content-setting')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/', 'InterviewContentController@getData');
                });
            });
            // END GROUP BUSINESS Z

            // START GROUP BUSINESS APPLY ALL SOURCE
            Route::name('OTHER.')->group(function () {
                Route::prefix('policy')->namespace('App\Http\Controllers\Api')->group(function () {
                    Route::get('/role', 'RolePolicyController@getRoleList');
                    Route::get('/permission', 'RolePolicyController@getPermissionList');
                });
                Route::get('files/{type}/{id}', 'App\Http\Controllers\Api\ManagementFileController@getFile');
                // load file
                Route::get('file/{filename}', function ($filename) {
                    $string = substr($filename, 0, -10) ?? "";
                    $string = substr($string, 10);
                    $string = strrev($string);
                    $string = json_decode(base64_decode($string));
                    $path = storage_path('app/public/' . $string->path);
                    if (!File::exists($path)) {
                        abort(404);
                    }
                    return response()->download($path);
                });
            });
            // END GROUP BUSINESS APPLY ALL SOURCE
        });
    });
});
