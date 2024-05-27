<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Admin\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Admin\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Admin\Auth\NewPasswordController;
use App\Http\Controllers\Admin\Auth\PasswordResetLinkController;
use App\Http\Controllers\Admin\Auth\RegisteredUserController;
use App\Http\Controllers\Admin\Auth\VerifyEmailController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DeliveryCompanyController;
use App\Http\Controllers\Admin\DeliveryOfficeController;
use App\Http\Controllers\Admin\DeliveryPickupAddrController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\DriverTaskReviewController;
use App\Http\Controllers\Admin\DeliveryOfficeTaskReviewController;
use App\Http\Controllers\Admin\DriverTaskController;
use App\Http\Controllers\Admin\DriverTaskPlanController;
use App\Http\Controllers\Admin\DriverPlanController;
use App\Http\Controllers\Admin\DriverTaskPlanAllowDriverController;
use App\Http\Controllers\Admin\DriverScheduleController;
use App\Http\Controllers\Admin\DriverRegisterDeliveryOfficeController;
use App\Http\Controllers\Admin\DriverRegisterDeliveryOfficeMemoController;
use App\Http\Controllers\Admin\PdfInvoiceController;
use App\Http\Controllers\Admin\RegisterRequestDriverController;
use App\Http\Controllers\Admin\RegisterRequestDeliveryOfficeController;
use App\Http\Controllers\Admin\WebConfigBaseController;
use App\Http\Controllers\Admin\WebConfigSystemController;
use App\Http\Controllers\Admin\WebSystemInfoController;
use App\Http\Controllers\Admin\WebContactController;
use App\Http\Controllers\Admin\WebPaymentLogController;
use App\Http\Controllers\Admin\WebNoticeLogController;
use App\Http\Controllers\Admin\WebRedisController;
use App\Http\Controllers\Admin\WebFailedJobController;
use App\Http\Controllers\Admin\WebBusySeasonController;

/**
 * 管理者(admin)
 */
Route::get('/', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');


Route::middleware('auth:admins')->group(function () {

    // ダッシュボード
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // 管理者
    Route::get('/user', [AdminController::class, 'index'])->name('user.index');
    Route::get('/user/create', [AdminController::class, 'create'])->name('user.create');
    Route::post('/user/store', [AdminController::class, 'store'])->name('user.store');
    Route::get('/user/edit/{admin_id}', [AdminController::class, 'edit'])->name('user.edit');
    Route::post('/user/update/{admin_id}', [AdminController::class, 'update'])->name('user.update');
    Route::post('/user/destroy/{admin_id}', [AdminController::class, 'destroy'])->name('user.destroy');

    // 配送会社
    Route::get('/delivery-company', [DeliveryCompanyController::class, 'index'])->name('delivery_company.index');
    Route::get('/delivery-company/create', [DeliveryCompanyController::class, 'create'])->name('delivery_company.create');
    Route::post('/delivery-company/store', [DeliveryCompanyController::class, 'store'])->name('delivery_company.store');
    Route::get('/delivery-company/show/{company_id}', [DeliveryCompanyController::class, 'show'])->name('delivery_company.show');
    Route::get('/delivery-company/edit/{company_id}', [DeliveryCompanyController::class, 'edit'])->name('delivery_company.edit');
    Route::post('/delivery-company/update/{company_id}', [DeliveryCompanyController::class, 'update'])->name('delivery_company.update');
    Route::post('/delivery-company/destroy/{company_id}', [DeliveryCompanyController::class, 'destroy'])->name('delivery_company.destroy');

    // 配送営業所
    Route::get('/delivery-office', [DeliveryOfficeController::class, 'index'])->name('delivery_office.index');
    Route::get('/delivery-office/create', [DeliveryOfficeController::class, 'create'])->name('delivery_office.create');
    Route::post('/delivery-office/store', [DeliveryOfficeController::class, 'store'])->name('delivery_office.store');
    Route::get('/delivery-office/show/{office_id}', [DeliveryOfficeController::class, 'show'])->name('delivery_office.show');
    Route::get('/delivery-office/edit/{office_id}', [DeliveryOfficeController::class, 'edit'])->name('delivery_office.edit');
    Route::post('/delivery-office/update/{office_id}', [DeliveryOfficeController::class, 'update'])->name('delivery_office.update');
    Route::post('/delivery-office/destroy/{office_id}', [DeliveryOfficeController::class, 'destroy'])->name('delivery_office.destroy');
    Route::post('/delivery-office/restore-delete/{office_id}', [DeliveryOfficeController::class, 'restoreDelete'])->name('delivery_office.restore_delete');
    Route::get('/delivery-office/unsubscribe', [DeliveryOfficeController::class, 'unsubscribe'])->name('delivery_office.unsubscribe');


    // 配送営業所 集荷先住所
    Route::get('/delivery-pickup-addr', [DeliveryPickupAddrController::class, 'index'])->name('delivery_pickup_addr.index');
    Route::get('/delivery-pickup-addr/create', [DeliveryPickupAddrController::class, 'create'])->name('delivery_pickup_addr.create');
    Route::post('/delivery-pickup-addr/store', [DeliveryPickupAddrController::class, 'store'])->name('delivery_pickup_addr.store');
    Route::get('/delivery-pickup-addr/show/{pickup_id}', [DeliveryPickupAddrController::class, 'show'])->name('delivery_pickup_addr.show');
    Route::get('/delivery-pickup-addr/edit/{pickup_id}', [DeliveryPickupAddrController::class, 'edit'])->name('delivery_pickup_addr.edit');
    Route::post('/delivery-pickup-addr/update/{pickup_id}', [DeliveryPickupAddrController::class, 'update'])->name('delivery_pickup_addr.update');
    Route::post('/delivery-pickup-addr/destroy/{pickup_id}', [DeliveryPickupAddrController::class, 'destroy'])->name('delivery_pickup_addr.destroy');

    // ドライバー
    Route::get('/driver', [DriverController::class, 'index'])->name('driver.index');
    Route::get('/driver/create', [DriverController::class, 'create'])->name('driver.create');
    Route::post('/driver/store', [DriverController::class, 'store'])->name('driver.store');
    Route::get('/driver/show/{driver_id}', [DriverController::class, 'show'])->name('driver.show');
    Route::get('/driver/edit/{driver_id}', [DriverController::class, 'edit'])->name('driver.edit');
    Route::post('/driver/update/{driver_id}', [DriverController::class, 'update'])->name('driver.update');
    Route::post('/driver/destroy/{driver_id}', [DriverController::class, 'destroy'])->name('driver.destroy');
    Route::post('/driver/restore-delete/{driver_id}', [DriverController::class, 'restoreDelete'])->name('driver.restore_delete');
    Route::get('/driver/export', [DriverController::class, 'export_index'])->name('driver.export.index');
    Route::POST('/driver/export/read', [DriverController::class, 'export_read'])->name('driver.export.read');
    Route::get('/driver/unsubscribe', [DriverController::class, 'unsubscribe'])->name('driver.unsubscribe');

    // 依頼者レビュー
    Route::get('/driver-task-review/delivery-office', [DeliveryOfficeTaskReviewController::class, 'index'])->name('delivery_office_task_review.index');
    Route::get('/driver-task-review/delivery-office/create', [DeliveryOfficeTaskReviewController::class, 'create'])->name('delivery_office_task_review.create');
    Route::post('/driver-task-review/delivery-office/store', [DeliveryOfficeTaskReviewController::class, 'store'])->name('delivery_office_task_review.store');
    Route::get('/driver-task-review/delivery-office/show/{review_id}', [DeliveryOfficeTaskReviewController::class, 'show'])->name('delivery_office_task_review.show');
    Route::get('/driver-task-review/delivery-office/edit/{review_id}', [DeliveryOfficeTaskReviewController::class, 'edit'])->name('delivery_office_task_review.edit');
    Route::post('/driver-task-review/delivery-office/update/{review_id}', [DeliveryOfficeTaskReviewController::class, 'update'])->name('delivery_office_task_review.update');
    Route::post('/driver-task-review/delivery-office/destroy/{review_id}', [DeliveryOfficeTaskReviewController::class, 'destroy'])->name('delivery_office_task_review.destroy');


    // ドライバーレビュー
    Route::get('/driver-task-review/driver', [DriverTaskReviewController::class, 'index'])->name('driver_task_review.index');
    Route::get('/driver-task-review/driver/create', [DriverTaskReviewController::class, 'create'])->name('driver_task_review.create');
    Route::post('/driver-task-review/driver/store', [DriverTaskReviewController::class, 'store'])->name('driver_task_review.store');
    Route::get('/driver-task-review/driver/show/{review_id}', [DriverTaskReviewController::class, 'show'])->name('driver_task_review.show');
    Route::get('/driver-task-review/driver/edit/{review_id}', [DriverTaskReviewController::class, 'edit'])->name('driver_task_review.edit');
    Route::post('/driver-task-review/driver/update/{review_id}', [DriverTaskReviewController::class, 'update'])->name('driver_task_review.update');
    Route::post('/driver-task-review/driver/destroy/{review_id}', [DriverTaskReviewController::class, 'destroy'])->name('driver_task_review.destroy');

    // ドライバー スケジュール
    Route::get('/driver-schedule', [DriverScheduleController::class, 'index'])->name('driver_schedule.index');
    Route::get('/driver-schedule/create', [DriverScheduleController::class, 'create'])->name('driver_schedule.create');
    Route::post('/driver-schedule/store', [DriverScheduleController::class, 'store'])->name('driver_schedule.store');
    Route::get('/driver-schedule/edit/{schedule_id}', [DriverScheduleController::class, 'edit'])->name('driver_schedule.edit');
    Route::post('/driver-schedule/update/{schedule_id}', [DriverScheduleController::class, 'update'])->name('driver_schedule.update');
    Route::post('/driver-schedule/destroy/{schedule_id}', [DriverScheduleController::class, 'destroy'])->name('driver_schedule.destroy');

    // ドライバー 登録営業所
    Route::get('/driver-register-delivery-office', [DriverRegisterDeliveryOfficeController::class, 'index'])->name('driver_register_delivery_office.index');
    Route::get('/driver-register-delivery-office/edit', [DriverRegisterDeliveryOfficeController::class, 'edit'])->name('driver_register_delivery_office.edit');
    Route::post('/driver-register-delivery-office/upsert', [DriverRegisterDeliveryOfficeController::class, 'upsert'])->name('driver_register_delivery_office.upsert');
    Route::post('/driver-register-delivery-office/destroy/{register_office_id}', [DriverRegisterDeliveryOfficeController::class, 'destroy'])->name('driver_register_delivery_office.destroy');

    // ドライバー 登録営業所メモ
    Route::get('/driver-register-delivery-office-memo', [DriverRegisterDeliveryOfficeMemoController::class, 'index'])->name('driver_register_delivery_office_memo.index');
    Route::get('/driver-register-delivery-office-memo/create', [DriverRegisterDeliveryOfficeMemoController::class, 'create'])->name('driver_register_delivery_office_memo.create');
    Route::post('/driver-register-delivery-office-memo/store', [DriverRegisterDeliveryOfficeMemoController::class, 'store'])->name('driver_register_delivery_office_memo.store');
    Route::get('/driver-register-delivery-office-memo/edit/{register_office_memo_id}', [DriverRegisterDeliveryOfficeMemoController::class, 'edit'])->name('driver_register_delivery_office_memo.edit');
    Route::post('/driver-register-delivery-office-memo/update/{register_office_memo_id}', [DriverRegisterDeliveryOfficeMemoController::class, 'update'])->name('driver_register_delivery_office_memo.update');
    Route::post('/driver-register-delivery-office-memo/destroy/{register_office_memo_id}', [DriverRegisterDeliveryOfficeMemoController::class, 'destroy'])->name('driver_register_delivery_office_memo.destroy');

    // 稼働依頼
    Route::get('/driver-task', [DriverTaskController::class, 'index'])->name('driver_task.index');
    Route::get('/driver-task/create', [DriverTaskController::class, 'create'])->name('driver_task.create');
    Route::post('/driver-task/store', [DriverTaskController::class, 'store'])->name('driver_task.store');
    Route::get('/driver-task/show/{task_id}', [DriverTaskController::class, 'show'])->name('driver_task.show');
    Route::get('/driver-task/edit/{task_id}', [DriverTaskController::class, 'edit'])->name('driver_task.edit');
    Route::post('/driver-task/update/{task_id}', [DriverTaskController::class, 'update'])->name('driver_task.update');
    Route::post('/driver-task/destroy/{task_id}', [DriverTaskController::class, 'destroy'])->name('driver_task.destroy');
    Route::get('/driver-task/export', [DriverTaskController::class, 'export_index'])->name('driver_task.export.index');
    Route::POST('/driver-task/export/read', [DriverTaskController::class, 'export_read'])->name('driver_task.export.read');
    Route::POST('/driver-task/payment/refund/{task_id}', [DriverTaskController::class, 'paymentRefund'])->name('driver_task.payment.refund');

    // 請求書PDF
    Route::get('/pdf-invoice/create', [PdfInvoiceController::class, 'create'])->name('pdf_invoice.create');
    Route::post('/pdf-invoice/store', [PdfInvoiceController::class, 'store'])->name('pdf_invoice.store');


    // 営業所登録申請
    Route::get('/register-request/delivery_office', [RegisterRequestDeliveryOfficeController::class, 'index'])->name('register_request_delivery_office.index');
    Route::get('/register-request/delivery_office/show/{register_request_id}', [RegisterRequestDeliveryOfficeController::class, 'show'])->name('register_request_delivery_office.show');
    Route::get('/register-request/delivery_office/edit/{register_request_id}', [RegisterRequestDeliveryOfficeController::class, 'edit'])->name('register_request_delivery_office.edit');
    Route::post('/register-request/delivery_office/update/{register_request_id}', [RegisterRequestDeliveryOfficeController::class, 'update'])->name('register_request_delivery_office.update');
    Route::post('/register-request/delivery_office/destroy/{register_request_id}', [RegisterRequestDeliveryOfficeController::class, 'destroy'])->name('register_request_delivery_office.destroy');

    // ドライバー登録申請
    Route::get('/register-request/driver', [RegisterRequestDriverController::class, 'index'])->name('register_request_driver.index');
    Route::get('/register-request/driver/show/{register_request_id}', [RegisterRequestDriverController::class, 'show'])->name('register_request_driver.show');
    Route::get('/register-request/driver/edit/{register_request_id}', [RegisterRequestDriverController::class, 'edit'])->name('register_request_driver.edit');
    Route::post('/register-request/driver/update/{register_request_id}', [RegisterRequestDriverController::class, 'update'])->name('register_request_driver.update');
    Route::post('/register-request/driver/destroy/{register_request_id}', [RegisterRequestDriverController::class, 'destroy'])->name('register_request_driver.destroy');


    // お問い合わせ
    Route::get('/contact', [WebContactController::class, 'index'])->name('web_contact.index');
    Route::get('/contact/show/{contact_id}', [WebContactController::class, 'show'])->name('web_contact.show');
    Route::get('/contact/edit/{contact_id}', [WebContactController::class, 'edit'])->name('web_contact.edit');
    Route::post('/contact/update/{contact_id}', [WebContactController::class, 'update'])->name('web_contact.update');
    Route::post('/contact/destroy/{contact_id}', [WebContactController::class, 'destroy'])->name('web_contact.destroy');

    // サイト基本設定
    Route::get('/base-config', [WebConfigBaseController::class, 'index'])->name('web_config_base.index');
    Route::get('/base-config/edit', [WebConfigBaseController::class, 'edit'])->name('web_config_base.edit');
    Route::post('/base-config/update', [WebConfigBaseController::class, 'update'])->name('web_config_base.update');

    // Webスケジュール設定
    Route::get('/base-config/busy-season', [WebBusySeasonController::class, 'index'])->name('web_busy_season.index');
    Route::get('/base-config/busy-season/create', [WebBusySeasonController::class, 'create'])->name('web_busy_season.create');
    Route::post('/base-config/busy-season/store', [WebBusySeasonController::class, 'store'])->name('web_busy_season.store');
    Route::post('/base-config/busy-season/destroy{busy_season_id}', [WebBusySeasonController::class, 'destroy'])->name('web_busy_season.destroy');
    // Webスケジュール設定 web-api
    Route::get('/api/base-config/busy-season', [WebBusySeasonController::class, 'index'])->name('api.web_busy_season.index');

    // サイトシステム設定
    Route::get('/system-config', [WebConfigSystemController::class, 'index'])->name('web_config_system.index');
    Route::get('/system-config/edit', [WebConfigSystemController::class, 'edit'])->name('web_config_system.edit');
    Route::post('/system-config/update', [WebConfigSystemController::class, 'update'])->name('web_config_system.update');

    // 稼働依頼プラン
    Route::get('/system-config/driver-task-plan', [DriverTaskPlanController::class, 'index'])->name('driver_task_plan.index');
    Route::get('/system-config/driver-task-plan/edit/{driver_task_plan_id}', [DriverTaskPlanController::class, 'edit'])->name('driver_task_plan.edit');
    Route::post('/system-config/driver-task-plan/update/{driver_task_plan_id}', [DriverTaskPlanController::class, 'update'])->name('driver_task_plan.update');

    // ドライバープラン
    Route::get('/system-config/driver-plan', [DriverPlanController::class, 'index'])->name('driver_plan.index');

    // 稼働依頼プランとドライバープランの対応関係
    Route::get('/system-config/driver-task-plan-allow-driver', [DriverTaskPlanAllowDriverController::class, 'index'])->name('driver_task_plan_allow_driver.index');


    /* システム情報 */
    // PHPINFO
    Route::get('/system-info/phpinfo', [WebSystemInfoController::class, 'index'])->name('web_system_info.index');
    // 決済ログ
    Route::get('/system-info/log/payment', [WebPaymentLogController::class, 'index'])->name('web_payment_log.index');
    // 通知ログ
    Route::get('/system-info/log/notice', [WebNoticeLogController::class, 'index'])->name('web_notice_log.index');

    /* Redis */
    Route::get('/system-info/redis', [WebRedisController::class, 'index'])->name('web_redis.index');
    Route::get('/system-info/redis/show/{redis_id}', [WebRedisController::class, 'show'])->name('web_redis.show');

    /* ジョブの失敗(Redis) */
    Route::get('/system-info/failed-job', [WebFailedJobController::class, 'index'])->name('web_failed_job.index');
    Route::get('/system-info/failed-job/show/{failed_job_id}', [WebFailedJobController::class, 'show'])->name('web_failed_job.show');
});


/**
 * 認証関係
 */
Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware('guest')
    ->name('register');

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest');

Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');

Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')
    ->name('password.request');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.update');

Route::get('/verify-email', [EmailVerificationPromptController::class, '__invoke'])
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])
    ->middleware('auth')
    ->name('password.confirm');

Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store'])
    ->middleware('auth');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:admins')
    ->name('logout');
