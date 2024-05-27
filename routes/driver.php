<?php

use App\Http\Controllers\Driver\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Driver\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Driver\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Driver\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Driver\Auth\NewPasswordController;
use App\Http\Controllers\Driver\Auth\PasswordResetLinkController;
use App\Http\Controllers\Driver\Auth\RegisteredUserController;
use App\Http\Controllers\Driver\Auth\VerifyEmailController;
use App\Http\Controllers\Driver\DashboardController;

use App\Http\Controllers\Driver\DriverController;
use App\Http\Controllers\Driver\DriverTaskReviewController;
use App\Http\Controllers\Driver\DeliveryOfficeTaskReviewController;
use App\Http\Controllers\Driver\DriverRegisterDeliveryOfficeController;
use App\Http\Controllers\Driver\DriverRegisterDeliveryOfficeMemoController;
use App\Http\Controllers\Driver\DriverTaskController;
use App\Http\Controllers\Driver\DriverScheduleController;
use App\Http\Controllers\Driver\RegisterRequestController;

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'basicauth'], function () {
    Route::get('/', [AuthenticatedSessionController::class, 'create'])
        ->middleware('guest')
        ->name('login');

    Route::middleware(['auth:drivers', 'driver.access.filter'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        // ドライバー
        Route::get('/user/show/{driver_id?}', [DriverController::class, 'show'])->name('user.show');
        Route::get('/user/edit', [DriverController::class, 'edit'])->name('user.edit');
        Route::post('/user/update', [DriverController::class, 'update'])->name('user.update');

        // ドライバー スケジュール
        Route::get('/driver-schedule', [DriverScheduleController::class, 'index'])->name('driver_schedule.index');
        Route::get('/driver-schedule/create', [DriverScheduleController::class, 'create'])->name('driver_schedule.create');
        Route::post('/driver-schedule/store', [DriverScheduleController::class, 'store'])->name('driver_schedule.store');
        Route::post('/driver-schedule/destroy/{schedule_id}', [DriverScheduleController::class, 'destroy'])->name('driver_schedule.destroy');

        // 稼働
        Route::get('/driver-task', [DriverTaskController::class, 'index'])->name('driver_task.index');
        Route::get('/driver-task/show/{task_id}', [DriverTaskController::class, 'show'])->name('driver_task.show');
        Route::post('/driver-task/update/{task_id}', [DriverTaskController::class, 'update'])->name('driver_task.update');

        // ドライバーレビュー
        Route::get('/driver-task-review', [DriverTaskReviewController::class, 'index'])->name('driver_task_review.index');
        Route::get('/driver-task-review/show/{review_id?}', [DriverTaskReviewController::class, 'show'])->name('driver_task_review.show');

        // 依頼者レビュー
        Route::get('/delivery-office-task-review/create', [DeliveryOfficeTaskReviewController::class, 'create'])->name('delivery_office_task_review.create');
        Route::post('/delivery-office-task-review/store', [DeliveryOfficeTaskReviewController::class, 'store'])->name('delivery_office_task_review.store');
        Route::get('/delivery-office-task-review/show/{review_id?}', [DeliveryOfficeTaskReviewController::class, 'show'])->name('delivery_office_task_review.show');


        // ドライバー 登録営業所
        Route::post('/driver-register-delivery-office/upsert', [DriverRegisterDeliveryOfficeController::class, 'upsert'])->name('driver_register_delivery_office.upsert');

        // ドライバー 登録営業所メモ
        Route::get('user/driver-register-delivery-office-memo', [DriverRegisterDeliveryOfficeMemoController::class, 'index'])->name('driver_register_delivery_office_memo.index');
        Route::get('user/driver-register-delivery-office-memo/create', [DriverRegisterDeliveryOfficeMemoController::class, 'create'])->name('driver_register_delivery_office_memo.create');
        Route::post('user/driver-register-delivery-office-memo/store', [DriverRegisterDeliveryOfficeMemoController::class, 'store'])->name('driver_register_delivery_office_memo.store');
        Route::get('user/driver-register-delivery-office-memo/edit/{register_office_memo_id}', [DriverRegisterDeliveryOfficeMemoController::class, 'edit'])->name('driver_register_delivery_office_memo.edit');
        Route::post('user/driver-register-delivery-office-memo/update/{register_office_memo_id}', [DriverRegisterDeliveryOfficeMemoController::class, 'update'])->name('driver_register_delivery_office_memo.update');
        Route::post('user/driver-register-delivery-office-memo/destroy/{register_office_memo_id}', [DriverRegisterDeliveryOfficeMemoController::class, 'destroy'])->name('driver_register_delivery_office_memo.destroy');
    });

    // 登録申請
    Route::get('/register-request/create', [RegisterRequestController::class, 'create'])->name('register_request.create');
    Route::post('/register-request/store', [RegisterRequestController::class, 'store'])->name('register_request.store');
    Route::get('/register-request/store', [RegisterRequestController::class, 'store'])->name('register_request.store_get');
    Route::get('/register-request/edit', [RegisterRequestController::class, 'edit'])->name('register_request.edit');
    Route::post('/register-request/update', [RegisterRequestController::class, 'update'])->name('register_request.update');


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
        ->middleware('auth:drivers')
        ->name('logout');
});
