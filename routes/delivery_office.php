<?php

use App\Http\Controllers\DeliveryOffice\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DeliveryOffice\Auth\ConfirmablePasswordController;
use App\Http\Controllers\DeliveryOffice\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\DeliveryOffice\Auth\EmailVerificationPromptController;
use App\Http\Controllers\DeliveryOffice\Auth\NewPasswordController;
use App\Http\Controllers\DeliveryOffice\Auth\PasswordResetLinkController;
use App\Http\Controllers\DeliveryOffice\Auth\RegisteredUserController;
use App\Http\Controllers\DeliveryOffice\Auth\VerifyEmailController;
use App\Http\Controllers\DeliveryOffice\DashboardController;
use App\Http\Controllers\DeliveryOffice\DeliveryOfficeController;
use App\Http\Controllers\DeliveryOffice\DeliveryPickupAddrController;
use App\Http\Controllers\DeliveryOffice\DriverController;
use App\Http\Controllers\DeliveryOffice\DriverTaskReviewController;
use App\Http\Controllers\DeliveryOffice\DriverTaskPlanAllowDriverController;
use App\Http\Controllers\DeliveryOffice\DeliveryOfficeTaskReviewController;
use App\Http\Controllers\DeliveryOffice\DriverTaskController;
use App\Http\Controllers\DeliveryOffice\DriverTaskTemplateController;
use App\Http\Controllers\DeliveryOffice\RegisterRequestController;
use App\Http\Controllers\DeliveryOffice\PaymentConfigController;
use App\Http\Controllers\DeliveryOffice\PdfReceiptController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'basicauth'], function () {
    Route::get('/', [AuthenticatedSessionController::class, 'create'])
        ->middleware('guest')
        ->name('login');

    Route::middleware('auth:delivery_offices')->group(function () {

        // ダッシュボード
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        // 依頼者アカウント
        Route::get('/user', [DeliveryOfficeController::class, 'index'])->name('user.index');
        Route::get('/user/edit', [DeliveryOfficeController::class, 'edit'])->name('user.edit');
        Route::post('/user/update', [DeliveryOfficeController::class, 'update'])->name('user.update');

        // 依頼者アカウント 決済設定
        Route::get('/user/payment-config', [PaymentConfigController::class, 'index'])->name('payment_config.index');
        Route::get('/user/payment-config/create', [PaymentConfigController::class, 'create'])->name('payment_config.create');
        Route::post('/user/payment-config/store', [PaymentConfigController::class, 'store'])->name('payment_config.store');
        Route::get('/user/payment-config/show/{payment_id}', [PaymentConfigController::class, 'show'])->name('payment_config.show');

        Route::post('/user/payment-config/destroy/{payment_id}', [PaymentConfigController::class, 'destroy'])->name('payment_config.destroy');


        // ドライバー
        Route::get('/driver', [DriverController::class, 'index'])->name('driver.index');
        // ドライバー web-api
        Route::get('api/driver', [DriverController::class, 'index'])->name('api.driver.index');
        Route::get('/driver/show/{driver_id}', [DriverController::class, 'show'])->name('driver.show');

        // ドライバーレビュー
        Route::get('/driver-task-review', [DriverTaskReviewController::class, 'index'])->name('driver_task_review.index');
        Route::get('/driver-task-review/create', [DriverTaskReviewController::class, 'create'])->name('driver_task_review.create');
        Route::post('/driver-task-review/store', [DriverTaskReviewController::class, 'store'])->name('driver_task_review.store');
        Route::get('/driver-task-review/show/{review_id?}', [DriverTaskReviewController::class, 'show'])->name('driver_task_review.show');

        // 依頼者レビュー
        Route::get('/delivery-office-task-review', [DeliveryOfficeTaskReviewController::class, 'index'])->name('delivery_office_task_review.index');

        // 稼働
        Route::get('/driver-task-list', [DriverTaskController::class, 'index'])->name('driver_task.index');
        Route::get('/driver-task/create', [DriverTaskController::class, 'create'])->name('driver_task.create');
        Route::post('/driver-task/store/{action?}/{id?}', [DriverTaskController::class, 'store'])->name('driver_task.store');
        Route::get('/driver-task/show/{task_id}', [DriverTaskController::class, 'show'])->name('driver_task.show');
        Route::get('/driver-task/edit/{task_id}', [DriverTaskController::class, 'edit'])->name('driver_task.edit');
        Route::post('/driver-task/update/{task_id}', [DriverTaskController::class, 'update'])->name('driver_task.update');
        // API
        Route::get('/driver-task/calc-basic-price', [DriverTaskController::class, 'calcPrice'])->name('driver_task.calc_price'); // 料金取得

        // Driver template
        Route::get('/driver-task-template', [DriverTaskTemplateController::class, 'index'])->name('driver_task_template.index');
        Route::post('/driver-task-template/delete/{id?}', [DriverTaskTemplateController::class, 'delete'])->name('driver_task_template.delete');

        // 稼働依頼プランとドライバープランの対応関係
        // API
        Route::get('api/driver-task-plan-allow-driver/check', [DriverTaskPlanAllowDriverController::class, 'check'])->name('api.driver_task_plan_allow_driver.check');


        // 配送営業所 集荷先住所
        Route::get('/delivery-pickup-addr', [DeliveryPickupAddrController::class, 'index'])->name('delivery_pickup_addr.index');
        Route::get('/delivery-pickup-addr/create', [DeliveryPickupAddrController::class, 'create'])->name('delivery_pickup_addr.create');
        Route::post('/delivery-pickup-addr/store', [DeliveryPickupAddrController::class, 'store'])->name('delivery_pickup_addr.store');
        // Route::get('/delivery-pickup-addr/show/{pickup_id}', [DeliveryPickupAddrController::class, 'show'])->name('delivery_pickup_addr.show');
        Route::get('/delivery-pickup-addr/edit/{pickup_id}', [DeliveryPickupAddrController::class, 'edit'])->name('delivery_pickup_addr.edit');
        Route::post('/delivery-pickup-addr/update/{pickup_id}', [DeliveryPickupAddrController::class, 'update'])->name('delivery_pickup_addr.update');
        Route::post('/delivery-pickup-addr/destroy/{pickup_id}', [DeliveryPickupAddrController::class, 'destroy'])->name('delivery_pickup_addr.destroy');

        // 領収書PDF
        Route::post('/pdf-receipt/store', [PdfReceiptController::class, 'store'])->name('pdf_receipt.store');
    });

    // 登録申請
    Route::get('/register-request/create', [RegisterRequestController::class, 'create'])->name('register_request.create');
    Route::post('/register-request/store', [RegisterRequestController::class, 'store'])->name('register_request.store');
    Route::get('/register-request/store-get', [RegisterRequestController::class, 'store'])->name('register_request.store_get');
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
        ->middleware('auth:delivery_offices')
        ->name('logout');
});