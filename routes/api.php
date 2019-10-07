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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('rooms', 'Api\ApiController@getrooms');
Route::get('areas', 'Api\ApiController@getareas');
Route::get('getRoomInstance', 'Api\ApiController@getRoomInstance');
Route::post('changeStatusOfRoom', 'Api\ApiController@changeStatus');
Route::post('getRoomStatus', 'Api\ApiController@getRoomStatus');
Route::post('getPrevStatus', 'Api\ApiController@getPrevStatus');
Route::post('implementPrevStat', 'Api\ApiController@implementPrevStat');
Route::get('loadbelobadge', 'Api\ApiController@LoadBeloBadge');
Route::get('loadforbelopercent', 'Api\ApiController@LoadForBeloPercent');
Route::get('loadroomgrp', 'Api\ApiController@LoadRoomGRP');
Route::get('loadbalancerbadge', 'Api\ApiController@LoadBalancerBadge');

// Route::get('changeOfCertainRoom', 'Api\ApiController@changeOfCertainRoom');

Route::get('getRoomStatusAfterSave', 'Api\ApiController@getRoomStatusAfterSave');
Route::get('getcurrentroomstatus', 'Api\ApiController@checkifonnego');
Route::get('checkifoccupied', 'Api\ApiController@CheckIfOccupied');


// Route::post('login', 'Api\ApiController@login');
Route::post('login', 'Api\ApiController@loginWeb');
Route::post('getRoomTypeEtc', 'Api\ApiController@getRoomTypeEtc');
Route::post('checkin', 'Api\ApiController@checkin');
Route::post('checkIfOnNego', 'Api\ApiController@checkifonnego');
Route::get('getcurrentroomstatus', 'Api\ApiController@GetCurrentRoomStatus');

Route::post('main/notifications', 'TransactionsController@getMainNotifications');
Route::post('remarks/save', 'TransactionsController@saveAnyRemarks');
Route::post('inspection/resume', 'TransactionsController@resumeInspection');
Route::post('inspection/start', 'TransactionsController@startInspection');
Route::post('inspection/end', 'TransactionsController@saveInspection');
Route::post('inspection/logout', 'TransactionsController@logout');
Route::post('inspection/changeRoomStatus', 'TransactionsController@changeRoomStatus');
Route::post('inspection/endStatus', 'TransactionsController@endStatus');
Route::post('inspection/cancel', 'TransactionsController@cancel');
Route::post('inspection/checklist', 'TransactionsController@checklist');
Route::post('inspection/findChecklist', 'TransactionsController@findChecklist');
Route::post('inspection/areaChecklist', 'TransactionsController@areaChecklist');
Route::post('work/cancel', 'TransactionsController@cancelWork');


Route::post('roomcheck/checklist', 'TransactionsController@roomCheckChecklist');
Route::post('inspection/broadcast', 'TransactionsController@sendBroadcastInspection');
Route::post('inspection/acknowledge', 'TransactionsController@acknowledgeBroadcast');
Route::post('inspection/markDone', 'TransactionsController@markAsDoneBroadcastDetails');
Route::post('inspection/notificationsList', 'TransactionsController@fetchBroadcastNotifications');
Route::post('inspection/notificationsDetails', 'TransactionsController@fetchBroadcastNotificationDetails');
Route::post('inspection/areas', 'TransactionsController@fetchAreas');
Route::post('inspection/lastRemarksOfRoom', 'TransactionsController@lastRemarksOfRoom');
Route::post('inspection/checkIfRoomSTL', 'TransactionsController@checkIfRoomSTL');
Route::post('users/online', 'TransactionsController@fetchOnlineUsers');
Route::post('users/activity', 'TransactionsController@activityLogs');


// Reservation API
Route::get('reservation/addbook', 'Api\ReservationController@getAreaAndDetails');
Route::post('reservation/addreservation', 'Api\ReservationController@AddReservation');
Route::get('reservation/loadreservation', 'Api\ReservationController@LoadReservation');
Route::get('reservation/loadreservationtype', 'Api\ReservationController@LoadReservationType');
Route::get('reservation/loadreservationcategory', 'Api\ReservationController@LoadReservationCategory');
Route::post('reservation/updatereservationinformation', 'Api\ReservationController@UpdateReservationInformation');
Route::post('reservation/reslogin', 'Api\ReservationController@ResLogin');
Route::get('reservation/loadreservationrates', 'Api\ReservationController@LoadReservationRate');

// sir d dito ka
Route::post('clearMe', 'Api\MobileLogsCheckin@clearAll');
Route::post('mobile/tagboard/roomInfo', 'Api\MobileLogsCheckin@roomInformation');
Route::post('mobile/tagboard/onNego', 'Api\MobileLogsCheckin@OnNego');
Route::post('loginMobile', 'Api\MobileController@MobileLogin');

// Route::post('mobile/tagboard/checkOnNego', 'Api\MobileLogsCheckin@checkOnNego');

Route::post('mobile/tagboard/checkin', 'Api\MobileLogsCheckin@checkin');

// Route::post('mobile/tagboard/logs', 'Api\MobileLogsCheckin@LogsFromMobile');

Route::post('mobile/tagboard/cancel', 'Api\MobileLogsCheckin@cancel');
Route::post('mobile/tagboard/changeStatus', 'Api\MobileLogsCheckin@changeStatus');
Route::post('mobile/tagboard/addBuddies', 'Api\MobileLogsCheckin@addBuddies');

Route::post('mobile/tagboard/getAllRoomStatus', 'Api\MobileLogsCheckin@getAllRoomStatus');

Route::post('mobile/allowedStatus', 'Api\MobileController@allowedStatus');
Route::post('mobile/constant/checkroom', 'Api\MobileLogsCheckin@checkStatusesAfterLogin');

Route::post('users/buddies', 'Api\MobileLogsCheckin@getBuddies');
Route::post('users/online', 'TransactionsController@fetchOnlineUsers');
Route::post('mobile/getAllRooms', 'Api\ApiController@listOfRoomsMobile');
Route::post('mobile/getAllAreas', 'Api\ApiController@areaMobile');
Route::post('mobile/getAllRoomStatus', 'Api\MobileController@roomStatusForMobile');
Route::post('mobile/getAllGoingStatus', 'Api\MobileController@onGoingStatuses');
Route::post('mobile/tagboard/getRoomCurrentStatus', 'Api\MobileLogsCheckin@checkRoomStatusForUpdate');
//getAllResumeStatus
Route::post('mobile/tagboard/resumeStatuses', 'Api\MobileLogsCheckin@getAllResumeStatus');

Route::post('mobile/tagboard/getAllOngoingResumeStatus', 'Api\MobileLogsCheckin@getAllOngoingResumeStatus');
Route::post('mobile/tagboard/cancelRoomCheck', 'Api\MobileLogsCheckin@cancelRoomCheck');
// Route::post('mobile/cancelWaitingGuest', 'Api\MobileLogsCheckin@cancelOnWaitingGuest');

//RMS Logs API
Route::post('tagboard/logs', 'RMSLogsController@SaveRMSLogs');
Route::post('tagboard/updatelogs', 'RMSLogsController@UpdateRMSLogs');
Route::post('tagboard/getRoomDetails', 'RMSLogsController@getRoomDetails');
Route::post('tagboard/remarks', 'RMSLogsController@roomRemarks');
Route::post('tagboard/inventory', 'RMSLogsController@roomInventory');
Route::post('vclogs/logs/current', 'RMSLogsController@currentLogs');
Route::post('vclogs/logs/today', 'RMSLogsController@todayLogs');
Route::post('vcreserve/getlocale', 'RMSLogsController@getVCLocale');

//Timer API
Route::get('tagboard/timer', 'TimerController@GetTime');

// room history
Route::post('room/details', 'TransactionsController@startInspectFromNfc');
Route::get('room/history', 'Api\ApiController@getStatuses');
Route::post('room/history/post', 'RMSLogsController@getHistoryStatus');

Route::get('room/roomInfoAll', 'Api\ApiController@roomInfoAll');

Route::post('testURL', 'Api\ApiController@testRooms');

// turn away
Route::post('turnaway/getAllLocale', 'Api\TurnAwayController@getAllLocale');
Route::post('turnaway/getAllGuest', 'Api\TurnAwayController@getAllGuest');
Route::post('turnaway/getAllReason', 'Api\TurnAwayController@getAllReason');
Route::post('turnaway/insertRecord', 'Api\TurnAwayController@insertTurnAway');
Route::get('turnaway/getAllRecords', 'Api\TurnAwayController@getAllRecords');

//Event Logs API
Route::get('eventlogs/eventlogin', 'EventLogsController@EventLogin');
Route::get('eventlogs/loadreasons', 'EventLogsController@LoadReasons');
Route::post('eventlogs/saveeventinformation', 'EventLogsController@SaveEventInformation');
Route::get('eventlogs/loadevents', 'EventLogsController@LoadEvents');
Route::post('eventlogs/saveeventreason', 'EventLogsController@SaveEventReason');

Route::post('rms/reports', 'RerpotsControllers@generateReports');
Route::post('rms/getRemarks', 'RerpotsControllers@getRemarks');
Route::post('rms/getRemarksByDate', 'RerpotsControllers@getRemarksByDate');
Route::get('getPrevRemarks', 'RerpotsControllers@lastRemarksOfRoomWeb');
Route::post('roomCountPerUser', 'RerpotsControllers@getInspectionCout');

//Locale Info
Route::post('listLocale', 'Api\LocaleController@init');

//Operation API
Route::post('operation/checkin', 'OperationController@CheckIn');
Route::get('operation/getcheckininformation', 'OperationController@GetCheckInInformation');
Route::post('operation/onnegochange', 'OperationController@OnNegoChange');
Route::post('operation/cancelonnego', 'OperationController@CancelOnNego');
Route::post('operation/changestatus', 'OperationController@ChangeStatus');
Route::post('operation/cancelongoings', 'OperationController@CancelOnGoings');
Route::get('operation/checkuserrole', 'OperationController@CheckUserRole');
Route::get('operation/changestatusvalidation', 'OperationController@ChangeStatusValidation');
Route::get('operation/validateongoinguser', 'OperationController@ValidateOngoingUser');
Route::get('operation/getvehicleid', 'OperationController@GetVehicleId');
Route::get('operation/validateispest', 'OperationController@ValidateIsPest');

//Historical Inspection API for web
Route::get('historical/loadhistoricalinspection', 'HistoricalInspectionController@LoadHistoricalInspection');
Route::post('historical/login', 'HistoricalInspectionController@Login');
Route::get('historical/loadhistoricalremarks', 'HistoricalInspectionController@LoadHistoricalRemarks');
Route::get('historical/loadhistoricalinformation', 'HistoricalInspectionController@LoadHistoricalInformation');
Route::post('historical/saverepairinformation', 'HistoricalInspectionController@SaveRepairInformation');
Route::post('historical/approveinformation', 'HistoricalInspectionController@ApproveInformation');
Route::post('historical/disapproveinformation', 'HistoricalInspectionController@DisapproveInformation');


//Historical Inspection API for mobile
Route::post('historical/historicalInspectionMobile', 'HistoricalInspectionController@historicalMobile');
Route::post('historical/historicalInspectionMobileApprove', 'HistoricalInspectionController@historicalMobileValidation');
Route::post('historical/historicalInspectionMobileMM', 'HistoricalInspectionController@historicalInspectionMobileMM');
Route::post('historical/historicalInspectionSearch', 'HistoricalInspectionController@historicalSearchViaRoomId');
Route::post('historical/historicalInspectionPerRoomAndArea', 'HistoricalInspectionController@historicalMobilePerRoomAndArea');

Route::post('historical/historicalInspectionPerRoom', 'HistoricalInspectionController@perRoom');
Route::post('historical/historicalInspectionByDM', 'HistoricalInspectionController@validateByDM');

Route::post('historical/historicalInspectionForMM', 'HistoricalInspectionController@infoForMM');


// Route::post('device/checkifexist', 'Api\MobileController@updateTblDevice');
Route::post('device/updateOrInsertDevice', 'Api\MobileController@addOrEditDevice');
Route::post('device/updatedevice', 'Api\MobileController@logoutZero');

//Notification blocklist
Route::post('notification/blocklist/add', 'NotificationsController@addRoleBlocklist');
Route::post('notification/blocklist/remove', 'NotificationsController@removeRoleFromBlocklist');
Route::post('notification/group/add', 'NotificationsController@addGroup');
Route::post('notification/group/delete', 'NotificationsController@deleteGroup');
Route::post('notification/group/member/add', 'NotificationsController@addMember');
Route::post('notification/group/member/delete', 'NotificationsController@deleteMembers');

//Push Notification API
Route::post('device/pushnotification', 'Api\MobileController@preNotif');
Route::post('device/notifoccupiedrate', 'Api\MobileController@NotifOccupiedRate');
Route::post('device/notifstlrooms', 'Api\MobileController@NotifSTLRooms');
Route::post('device/notifdirtyrooms', 'Api\MobileController@NotifDirtyRooms');
Route::post('device/notifautopilotandroid', 'Api\MobileController@AutopilotNotificationAndroid');
Route::post('device/notifautopilotios', 'Api\MobileController@AutopilotNotificationIOS');
Route::post('device/notifautopilotshiftandroidios', 'Api\MobileController@NotifAutopilotShiftAndroidIOS');
// Route::post('device/notifautopilotshiftios', 'Api\MobileController@NotifAutopilotShiftIOS');

Route::post('image/upload', 'TransactionsController@uploadImage');
Route::post('room/hasWork', 'TransactionsController@testHasWork');

Route::post('test/inspection', 'Api\MobileController@test');
Route::post('test/inspection', 'Api\MobileController@test');
Route::get('test', 'Api\ApiController@testtime');

//Mobile Operation API
Route::post('mobileoperation/saveoccupiedauditinspection', 'MobileOperationController@SaveOccupiedAuditInspection');
Route::post('mobileoperation/validateoccupiedroom', 'MobileOperationController@ValidateOccupiedRoom');
Route::post('mobileoperation/checkmobileversion', 'MobileOperationController@CheckMobileVersion');

//RMS Backup API
Route::post('backup/replicatermsdata', 'RMSBackupController@ReplicateRMSData');

//Room Mileage API
// Route::post('room/roommileage', 'RoomMileageController@RoomMileage');

// Test date
Route::post('testdate', 'TransactionsController@test');