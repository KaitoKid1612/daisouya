<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeliveryOffice\Sanctum\AuthTokenController as DODeliveryOfficeAuthTokenController;
use App\Http\Controllers\Driver\Sanctum\AuthTokenController as DDriverAuthTokenController;

/* 依頼者 */
use App\Http\Controllers\DeliveryOffice\DeliveryOfficeController as DODeliveryOfficeController;
use App\Http\Controllers\DeliveryOffice\DeliveryPickupAddrController as DODeliveryPickupAddrController;
use App\Http\Controllers\DeliveryOffice\DriverController as DODriverController;
use App\Http\Controllers\DeliveryOffice\DriverScheduleController as DODriverScheduleController;
use App\Http\Controllers\DeliveryOffice\DriverTaskReviewController as DODriverTaskReviewController;
use App\Http\Controllers\DeliveryOffice\DeliveryOfficeTaskReviewController as DODeliveryOfficeTaskReviewController;
use App\Http\Controllers\DeliveryOffice\DriverTaskController as DODriverTaskController;
use App\Http\Controllers\DeliveryOffice\DriverTaskTemplateController as DODriverTaskTemplateController;
use App\Http\Controllers\DeliveryOffice\RegisterRequestController as DORegisterRequestController;
use App\Http\Controllers\DeliveryOffice\PaymentConfigController as DOPaymentConfigController;
use App\Http\Controllers\DeliveryOffice\PdfReceiptController as DOPdfReceiptController;
use App\Http\Controllers\DeliveryOffice\Auth\PasswordResetLinkController as DOPasswordResetLinkController;
use App\Http\Controllers\DeliveryOffice\Auth\NewPasswordController as DONewPasswordController;
use App\Http\Controllers\DeliveryOffice\DriverTaskPermissionController as DODriverTaskPermissionController;
use App\Http\Controllers\DeliveryOffice\DriverTaskUIController as DODriverTaskUIController;
use App\Http\Controllers\DeliveryOffice\DriverTaskPlanAllowDriverController as DODriverTaskPlanAllowDriverController;

/* ドライバー */
use App\Http\Controllers\Driver\DriverController as DDriverController;
use App\Http\Controllers\Driver\DriverTaskReviewController as DDriverTaskReviewController;
use App\Http\Controllers\Driver\DeliveryOfficeTaskReviewController as DDeliveryOfficeTaskReviewController;
use App\Http\Controllers\Driver\DriverRegisterDeliveryOfficeController as DDriverRegisterDeliveryOfficeController;
use App\Http\Controllers\Driver\DriverRegisterDeliveryOfficeMemoController as DDriverRegisterDeliveryOfficeMemoController;
use App\Http\Controllers\Driver\DriverTaskController as DDriverTaskController;
use App\Http\Controllers\Driver\DriverScheduleController as DDriverScheduleController;
use App\Http\Controllers\Driver\RegisterRequestController as DRegisterRequestController;
use App\Http\Controllers\Driver\DeliveryOfficeController as DDeliveryOfficeController;
use App\Http\Controllers\Driver\DeliveryCompanyController as DDeliveryCompanyController;
use App\Http\Controllers\Driver\Auth\PasswordResetLinkController as DPasswordResetLinkController;
use App\Http\Controllers\Driver\Auth\NewPasswordController as DNewPasswordController;
use App\Http\Controllers\Driver\DriverTaskPermissionController as DDriverTaskPermissionController;
use App\Http\Controllers\Driver\DriverTaskUIController as DDriverTaskUIController;
use App\Http\Controllers\Driver\DriverWaitingAllowPathController as DDriverWaitingAllowPathController;
use App\Http\Controllers\Guest\DriverPlanController;

/* ゲストやその他 */
use App\Http\Controllers\Guest\WebTermsServiceController;
use App\Http\Controllers\Guest\WebPrivacyPolicyController;
use App\Http\Controllers\Guest\WebCommerceLawController;
use App\Http\Controllers\Guest\WebContactController;
use App\Http\Controllers\Guest\RegionController;
use App\Http\Controllers\Guest\DeliveryCompanyController;
use App\Http\Controllers\Guest\PrefectureController;
use App\Http\Controllers\Guest\GenderController;
use App\Http\Controllers\Guest\ReviewScoreController;
use App\Http\Controllers\Guest\DriverWaitingAllowPathController;
use App\Http\Controllers\Guest\WebMatchPatternController;
use App\Http\Controllers\Guest\DriverTaskPlanController;

// ステータス系
use App\Http\Controllers\Guest\DriverTaskPaymentStatusController;
use App\Http\Controllers\Guest\DriverTaskRefundStatusController;
use App\Http\Controllers\Guest\DriverTaskStatusController;
use App\Http\Controllers\Guest\DeliveryOfficeTaskReviewPublicStatusController;
use App\Http\Controllers\Guest\DriverTaskReviewPublicStatusController;
use App\Http\Controllers\Guest\RegisterRequestStatusController;
// タイプ系
use App\Http\Controllers\Guest\UserTypeController;
use App\Http\Controllers\Guest\DeliveryOfficeTypesController;
use App\Http\Controllers\Guest\DeliveryOfficeChargeUserTypeController;
use App\Http\Controllers\Guest\WebContactTypeController;

// FCMデバイストークン Push通知
use App\Http\Controllers\DeliveryOffice\FcmDeviceTokenController as DOFcmDeviceTokenController;
use App\Http\Controllers\Driver\FcmDeviceTokenController as DFcmDeviceTokenController;
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


// use App\Models\DeliveryOffice;
// use App\Models\Driver;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Validation\ValidationException;

// use Illuminate\Http\JsonResponse;
// use Illuminate\Support\Facades\Validator;

/**
 * 依頼者のAPI 
 * @todo バリデートをJSON対応
 * 
 */
Route::group(['middleware' => 'basicauth'], function () {
    // 認証必要なし
    Route::prefix('delivery-office')->name('api.delivery_office.')->group(function () {
        // APIトークン作成 ログイン
        Route::post('/token/store', [DODeliveryOfficeAuthTokenController::class, 'store']);

        // 登録申請
        Route::post('/register-request/store', [DORegisterRequestController::class, 'store'])->name('register_request.store');
        Route::post('/register-request/update', [DORegisterRequestController::class, 'update'])->name('register_request.update');

        // パスワード忘れ メール
        Route::post('/forgot-password', [DOPasswordResetLinkController::class, 'store'])
            ->middleware('guest')
            ->name('password.email');

        // パスワード再設定
        Route::post('/reset-password', [DONewPasswordController::class, 'store'])
            ->middleware('guest')
            ->name('password.update');
    });
    // 認証必要
    Route::middleware(['auth:sanctum', 'abilities:delivery_office'])->prefix('delivery-office')->name('api.delivery_office.')->group(function () {
        // APIトークン削除 ログアウト
        Route::post('/token/destroy', [DODeliveryOfficeAuthTokenController::class, 'destroy']);

        // 動作テスト用
        Route::get('/test', function () {
            return '動作テスト';
        });
        Route::post('/test', function () {
            return '動作テスト';
        });

        // FCMデバイストークン
        Route::post('/fcm-device-token/upsert', [DOFcmDeviceTokenController::class, 'upsert'])->name('fcm_device_token.upsert');
        Route::get('/fcm-device-token/show/{fcm_token}', [DOFcmDeviceTokenController::class, 'show'])->name('fcm_device_token.show');
        Route::post('/fcm-device-token/destroy/{fcm_token}', [DOFcmDeviceTokenController::class, 'destroy'])->name('fcm_device_token.destroy');


        // ユーザー 依頼者アカウント
        Route::get('/user', [DODeliveryOfficeController::class, 'index'])->name('user.index');
        Route::post('/user/update', [DODeliveryOfficeController::class, 'update'])->name('user.update');

        // 決済設定
        Route::get('/user/payment-config', [DOPaymentConfigController::class, 'index'])->name('payment_config.index');
        Route::post('/user/payment-config/store', [DOPaymentConfigController::class, 'store'])->name('payment_config.store');
        Route::get('/user/payment-config/show/{payment_id}', [DOPaymentConfigController::class, 'show'])->name('payment_config.show');
        Route::post('/user/payment-config/destroy/{payment_id}', [DOPaymentConfigController::class, 'destroy'])->name('payment_config.destroy');

        Route::get('/user/payment-config/customer/show', [DOPaymentConfigController::class, 'showCustomer'])->name('payment_config.show_customer');
        Route::post('/user/payment-config/payment-method/store', [DOPaymentConfigController::class, 'storePaymentMethod'])->name('payment_config.store_payment_method');

        // ドライバー
        Route::get('/driver', [DODriverController::class, 'index'])->name('driver.index');
        Route::get('/driver/show/{driver_id}', [DODriverController::class, 'show'])->name('driver.show');

        // ドライバースケジュール
        Route::get('/driver/driver-schedule', [DODriverScheduleController::class, 'index'])->name('driver_schedule.index');

        // ドライバーのレビュー
        Route::get('/driver-task-review', [DODriverTaskReviewController::class, 'index'])->name('driver_task_review.index');
        Route::post('/driver-task-review/store', [DODriverTaskReviewController::class, 'store'])->name('driver_task_review.store');
        Route::get('/driver-task-review/show/{review_id?}', [DODriverTaskReviewController::class, 'show'])->name('driver_task_review.show');

        // 依頼者のレビュー
        Route::get('/delivery-office-task-review', [DODeliveryOfficeTaskReviewController::class, 'index'])->name('delivery_office_task_review.index');
        Route::get('/delivery-office-task-review/show/{review_id?}', [DODeliveryOfficeTaskReviewController::class, 'show'])->name('delivery_office_task_review.show');

        // 稼働依頼
        Route::get('/driver-task-list', [DODriverTaskController::class, 'index'])->name('driver_task.index');
        Route::post('/driver-task/store', [DODriverTaskController::class, 'store'])->name('driver_task.store');
        Route::get('/driver-task/show/{task_id}', [DODriverTaskController::class, 'show'])->name('driver_task.show');
        Route::post('/driver-task/update/{task_id}', [DODriverTaskController::class, 'update'])->name('driver_task.update');
        Route::get('/driver-task/calc-basic-price', [DODriverTaskController::class, 'calcPrice'])->name('driver_task.calc_price'); // 料金取得

        Route::get('/driver-task-template', [DODriverTaskTemplateController::class, 'index'])->name('driver_task_template.index');
        Route::post('/driver-task-template/delete/{id?}', [DODriverTaskTemplateController::class, 'delete'])->name('driver_task_template.delete');

        // 稼働依頼プランとドライバープランの対応関係
        Route::get('/driver-task-plan-allow-driver/check', [DODriverTaskPlanAllowDriverController::class, 'check'])->name('driver_task_plan_allow_driver.check');

        // 稼働依頼関係のPermission
        Route::get('/driver-task-permission/show/{task_id}', [DODriverTaskPermissionController::class, 'show'])->name('driver_task_permission.show');

        // 稼働依頼関係のUI
        Route::get('/driver-task-ui/show/{task_id}', [DODriverTaskUIController::class, 'show'])->name('driver_task_ui.show');

        // 集荷先住所
        Route::get('/delivery-pickup-addr', [DODeliveryPickupAddrController::class, 'index'])->name('delivery_pickup_addr.index');
        Route::post('/delivery-pickup-addr/store', [DODeliveryPickupAddrController::class, 'store'])->name('delivery_pickup_addr.store');
        Route::get('/delivery-pickup-addr/show/{pickup_id}', [DODeliveryPickupAddrController::class, 'show'])->name('delivery_pickup_addr.show');
        Route::post('/delivery-pickup-addr/update/{pickup_id}', [DODeliveryPickupAddrController::class, 'update'])->name('delivery_pickup_addr.update');
        Route::post('/delivery-pickup-addr/destroy/{pickup_id}', [DODeliveryPickupAddrController::class, 'destroy'])->name('delivery_pickup_addr.destroy');

        // 領収書PDF
        Route::post('/pdf-receipt/store', [DOPdfReceiptController::class, 'store'])->name('pdf_receipt.store');
    });

    /**
     * ドライバーのAPI 
     */
    // 認証必要なし
    Route::prefix('driver')->name("api.driver.")->group(function () {
        // APIトークン作成 ログイン
        Route::post('/token/store', [DDriverAuthTokenController::class, 'store']);

        // 登録申請
        Route::post('/register-request/store', [DRegisterRequestController::class, 'store'])->name('register_request.store');
        Route::post('/register-request/update', [DRegisterRequestController::class, 'update'])->name('register_request.update');

        // パスワード忘れ メール
        Route::post('/forgot-password', [DPasswordResetLinkController::class, 'store'])
            ->middleware('guest')
            ->name('password.email');

        // パスワード再設定
        Route::post('/reset-password', [DNewPasswordController::class, 'store'])
            ->middleware('guest')
            ->name('password.update');
    });
    // 認証必要
    Route::middleware(['auth:sanctum', 'abilities:driver', 'driver.access.filter'])->prefix('driver')->name("api.driver.")->group(function () {
        // APIトークン削除 ログアウト
        Route::post('/token/destroy', [DDriverAuthTokenController::class, 'destroy']);

        // 動作テスト用
        Route::get('/test', function () {
            return '動作テスト';
        });
        Route::post('/test', function () {
            return '動作テスト';
        });

        // FCMデバイストークン
        Route::post('/fcm-device-token/upsert', [DFcmDeviceTokenController::class, 'upsert'])->name('fcm_device_token.upsert');
        Route::get('/fcm-device-token/show/{fcm_token}', [DFcmDeviceTokenController::class, 'show'])->name('fcm_device_token.show');
        Route::post('/fcm-device-token/destroy/{fcm_token}', [DFcmDeviceTokenController::class, 'destroy'])->name('fcm_device_token.destroy');

        // ドライバーアカウント
        Route::get('/user/show/{driver_id?}', [DDriverController::class, 'show'])->name('user.show');
        Route::post('/user/update', [DDriverController::class, 'update'])->name('user.update');

        // 登録審査中ドライバーのアクセス権限パス
        Route::get('/user/allow-path/driver-waiting', [DDriverWaitingAllowPathController::class, 'index'])->name('driver_waiting_allow_path.index');

        // 配送会社
        Route::get('/delivery-company', [DDeliveryCompanyController::class, 'index'])->name('delivery_company.index'); // apiのみ
        Route::get('/delivery-company/show/{company_id}', [DDeliveryCompanyController::class, 'show'])->name('delivery_company.show'); // apiのみ

        // ドライバー登録営業所
        Route::get('/driver-register-delivery-office', [DDriverRegisterDeliveryOfficeController::class, 'index'])->name('driver_register_delivery_office.index'); // apiのみ
        Route::post('/driver-register-delivery-office/upsert', [DDriverRegisterDeliveryOfficeController::class, 'upsert'])->name('driver_register_delivery_office.upsert');

        // ドライバー登録営業所メモ
        Route::get('user/driver-register-delivery-office-memo', [DDriverRegisterDeliveryOfficeMemoController::class, 'index'])->name('driver_register_delivery_office_memo.index');
        Route::post('user/driver-register-delivery-office-memo/store', [DDriverRegisterDeliveryOfficeMemoController::class, 'store'])->name('driver_register_delivery_office_memo.store');
        Route::get('user/driver-register-delivery-office-memo/show/{register_office_memo_id}', [DDriverRegisterDeliveryOfficeMemoController::class, 'show'])->name('driver_register_delivery_office_memo.show'); // apiのみ
        Route::post('user/driver-register-delivery-office-memo/update/{register_office_memo_id}', [DDriverRegisterDeliveryOfficeMemoController::class, 'update'])->name('driver_register_delivery_office_memo.update');
        Route::post('user/driver-register-delivery-office-memo/destroy/{register_office_memo_id}', [DDriverRegisterDeliveryOfficeMemoController::class, 'destroy'])->name('driver_register_delivery_office_memo.destroy');

        // ドライバースケジュール
        Route::get('/driver-schedule', [DDriverScheduleController::class, 'index'])->name('driver_schedule.index');
        Route::post('/driver-schedule/store', [DDriverScheduleController::class, 'store'])->name('driver_schedule.store');
        Route::post('/driver-schedule/destroy/{schedule_id}', [DDriverScheduleController::class, 'destroy'])->name('driver_schedule.destroy');

        // 稼働依頼
        Route::get('/driver-task', [DDriverTaskController::class, 'index'])->name('driver_task.index');
        Route::get('/driver-task/show/{task_id}', [DDriverTaskController::class, 'show'])->name('driver_task.show');
        Route::post('/driver-task/update/{task_id}', [DDriverTaskController::class, 'update'])->name('driver_task.update');

        // 稼働依頼関係のPermission
        Route::get('/driver-task-permission/show/{task_id}', [DDriverTaskPermissionController::class, 'show'])->name('driver_task_permission.show');

        // 稼働依頼関係のUI
        Route::get('/driver-task-ui/show/{task_id}', [DDriverTaskUIController::class, 'show'])->name('driver_task_ui.show');

        // ドライバーのレビュー
        Route::get('/driver-task-review', [DDriverTaskReviewController::class, 'index'])->name('driver_task_review.index');
        Route::get('/driver-task-review/show/{review_id?}', [DDriverTaskReviewController::class, 'show'])->name('driver_task_review.show');

        // 依頼者のレビュー
        Route::get('/delivery-office-task-review', [DDeliveryOfficeTaskReviewController::class, 'index'])->name('delivery_office_task_review.index'); // apiのみ
        Route::post('/delivery-office-task-review/store', [DDeliveryOfficeTaskReviewController::class, 'store'])->name('delivery_office_task_review.store');
        Route::get('/delivery-office-task-review/show/{review_id?}', [DDeliveryOfficeTaskReviewController::class, 'show'])->name('delivery_office_task_review.show');

        // 依頼者
        Route::get('/delivery-office', [DDeliveryOfficeController::class, 'index'])->name('delivery_office.index');
        Route::get('/delivery-office/show/{delivery_office_id}', [DDeliveryOfficeController::class, 'show'])->name('delivery_office.show');
    });


    /** ゲストやその他のAPI */
    Route::name('api.guest.')->prefix('guest')->group(function () {
        // 利用規約 typeパラメータで分岐
        Route::get('/terms-service', [WebTermsServiceController::class, 'index'])->name('web_terms_service.index');

        // 特定商取引法に基づく表記 typeパラメータで分岐
        Route::get('/commerce-law', [WebCommerceLawController::class, 'index'])->name('web_commerce_law.index');

        // プライバシーポリシー typeパラメータで分岐
        Route::get('/privacy-policy', [WebPrivacyPolicyController::class, 'index'])->name('web_privacy_policy.index');

        // 配送会社
        Route::get('/delivery-company', [DeliveryCompanyController::class, 'index'])->name('delivery_company.index');
        Route::get('/delivery-company/show/{company_id}', [DeliveryCompanyController::class, 'show'])->name('delivery_company.show');

        // 地方
        Route::get('/region', [RegionController::class, 'index'])->name('region.index');
        Route::get('/region/show/{region_id}', [RegionController::class, 'show'])->name('region.show');

        // 都道府県
        Route::get('/prefecture', [PrefectureController::class, 'index'])->name('prefecture.index');
        Route::get('/prefecture/show/{prefecture_id}', [PrefectureController::class, 'show'])->name('prefecture.show');

        // 性別
        Route::get('/gender', [GenderController::class, 'index'])->name('gender.index');

        /* ステータス系 */
        // 稼働依頼ステータス
        Route::get('/driver-task-status', [DriverTaskStatusController::class, 'index'])->name('driver_task_status.index');
        Route::get('/driver-task-status/show/{status_id}', [DriverTaskStatusController::class, 'show'])->name('driver_task_status.show');

        // 稼働依頼支払いステータス
        Route::get('/driver-task-payment-status', [DriverTaskPaymentStatusController::class, 'index'])->name('driver_task_payment_status.index');
        Route::get('/driver-task-payment-status/show/{status_id}', [DriverTaskPaymentStatusController::class, 'show'])->name('driver_task_payment_status.show');

        // 稼働依頼返金ステータス
        Route::get('/driver-task-refund-status', [DriverTaskRefundStatusController::class, 'index'])->name('driver_task_refund_status.index');
        Route::get('/driver-task-refund-status/show/{status_id}', [DriverTaskRefundStatusController::class, 'show'])->name('driver_task_refund_status.show');

        // 依頼者へのレビュー公開ステータス
        Route::get('/delivery-office-task-review-public-status', [DeliveryOfficeTaskReviewPublicStatusController::class, 'index'])->name('delivery_office_task_review_public_status.index');
        Route::get('/delivery-office-task-review-public-status/show/{status_id}', [DeliveryOfficeTaskReviewPublicStatusController::class, 'show'])->name('delivery_office_task_review_public_status.show');

        // ドライバーへのレビュー公開ステータス
        Route::get('/driver-task-review-public-status', [DriverTaskReviewPublicStatusController::class, 'index'])->name('driver_task_review_public_status.index');
        Route::get('/driver-task-review-public-status/show/{status_id}', [DriverTaskReviewPublicStatusController::class, 'show'])->name('driver_task_review_public_status.show');

        // 登録申請ステータス
        Route::get('/register-request-status', [RegisterRequestStatusController::class, 'index'])->name('register_request_status.index');
        Route::get('/register-request-status/show/{status_id}', [RegisterRequestStatusController::class, 'show'])->name('register_request_status.show');

        /* タイプ系 */
        // ユーザータイプ
        Route::get('/user-type', [UserTypeController::class, 'index'])->name('user_type.index');
        Route::get('/user-type/show/{type_id}', [UserTypeController::class, 'show'])->name('user_type.show');

        // 依頼者タイプ
        Route::get('/delivery-office-type', [DeliveryOfficeTypesController::class, 'index'])->name('delivery_office_type.index');
        Route::get('/delivery-office-type/show/{type_id}', [DeliveryOfficeTypesController::class, 'show'])->name('delivery_office_type.show');

        // 請求に関するユーザの種類
        Route::get('/delivery-office-charge-user-type', [DeliveryOfficeChargeUserTypeController::class, 'index'])->name('delivery_office_charge_user_type.index');
        Route::get('/delivery-office-charge-user-type/show/{type_id}', [DeliveryOfficeChargeUserTypeController::class, 'show'])->name('delivery_office_charge_user_type.show');

        // お問い合わせタイプ
        Route::get('/web-contact-type', [WebContactTypeController::class, 'index'])->name('contact_type.index');
        Route::get('/web-contact-type/show/{type_id}', [WebContactTypeController::class, 'show'])->name('contact_type.show');

        // 認証
        Route::middleware(['auth:sanctum', 'ability:delivery_office,driver'])->group(function () {
            // お問い合わせ
            Route::post('/web-contact/store', [WebContactController::class, 'store'])->name('web_contact.store');
        });

        // 平均評価 form用
        Route::get('/review-score', [ReviewScoreController::class, 'index'])->name('review_score.index');

        // 登録審査中ドライバーのアクセス許可のパス
        Route::get('/allow-path/driver-waiting', [DriverWaitingAllowPathController::class, 'index'])->name('driver_waiting_allow_path.index');

        // 文字列のマッチパターンの方法
        Route::get('/web-match-pattern', [WebMatchPatternController::class, 'index'])->name('web_match_pattern_list.index');

        // 稼働依頼プラン
        Route::get('/driver-task-plan', [DriverTaskPlanController::class, 'index'])->name('driver_task_plan.index');

        // 稼働依頼プラン
        Route::get('/driver-plan', [DriverPlanController::class, 'index'])->name('driver_plan.index');
    });
});


// Route::post('/sanctum/token', function (Request $request) {

//     $user = DeliveryOffice::where('email', $request->email)->first();

//     $validator = Validator::make($request->all(), [
//         'email' => 'required|email',
//         'password' => 'required',
//         'device_name' => 'required',
//     ]);


//     try {
//         if ($validator->fails()) {
//             throw new ValidationException($validator, new JsonResponse($validator->errors(), 422));
//         }
//     } catch (\Illuminate\Validation\ValidationException $exception) {
//         // return response()->json($exception->errors(), JSON_UNESCAPED_UNICODE);
//         return response()->json($exception->errors(), 401, [], JSON_UNESCAPED_UNICODE);
//     }

//     try {
//         if (Hash::check($request->password, $user->password)) {
//             return response()->json($user->createToken($request->device_name, ['server:update'])->plainTextToken);
//         } else {
//             throw ValidationException::withMessages([
//                 'email' => ['ログインできません'],
//             ]);
//         }
//     } catch (\Illuminate\Validation\ValidationException $exception) {
//         return response()->json([
//             'error' => $exception->errors(),
//         ], 401, [], JSON_UNESCAPED_UNICODE);
//     }
// });


// ドライバーのトークン作成
// Route::post('/driver/sanctum/token', function (Request $request) {


//     $user = Driver::where('email', $request->email)->first();

//     $validator = Validator::make($request->all(), [
//         'email' => 'required|email',
//         'password' => 'required',
//         'device_name' => 'required',
//     ]);


//     try {
//         if ($validator->fails()) {
//             throw new ValidationException($validator, new JsonResponse($validator->errors(), 422));
//         }
//     } catch (\Illuminate\Validation\ValidationException $exception) {
//         // return response()->json($exception->errors(), JSON_UNESCAPED_UNICODE);
//         return response()->json($exception->errors(), 401, [], JSON_UNESCAPED_UNICODE);
//     }

//     try {
//         if (Hash::check($request->password, $user->password)) {
//             return response()->json($user->createToken($request->device_name, ['server:update'])->plainTextToken);
//         } else {
//             throw ValidationException::withMessages([
//                 'email' => ['ログインできません'],
//             ]);
//         }
//     } catch (\Illuminate\Validation\ValidationException $exception) {
//         return response()->json([
//             'error' => $exception->errors(),
//         ], 401, [], JSON_UNESCAPED_UNICODE);
//     }
// });