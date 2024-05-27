<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\DeliveryOffice\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Guest\WebContactController;
use App\Http\Controllers\Guest\WebTermsServiceController;
use App\Http\Controllers\Guest\WebCommerceLawController;
use App\Http\Controllers\Guest\WebPrivacyPolicyController;
use App\Http\Controllers\Test\TestMailcontroller;
use App\Http\Controllers\Other\FileController;

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

Route::group(['middleware' => 'basicauth'], function () {
    // ファイル表示
    Route::get("/storage-file/{path?}", [FileController::class, 'show'])->name('storage_file.show')->where('path', '(.*)');

    Route::get('/', [AuthenticatedSessionController::class, 'create'])
        ->middleware('guest')
        ->name('login');

    Route::name('guest.')->group(function () {
        // お問い合わせ
        Route::get('/web-contact/create', [WebContactController::class, 'create'])->name('web_contact.create');
        // Route::get('/web-contact/store', [WebContactController::class, 'store'])->name('web_contact.store');
        Route::post('/web-contact/store', [WebContactController::class, 'store'])->name('web_contact.store');
        Route::get('/web-contact/done', [WebContactController::class, 'done'])->name('web_contact.done');

        // 利用規約 typeパラメータで分岐
        Route::get('/terms-service', [WebTermsServiceController::class, 'index'])->name('web_terms_service.index');

        // 特定商取引法に基づく表記 typeパラメータで分岐
        Route::get('/commerce-law', [WebCommerceLawController::class, 'index'])->name('web_commerce_law.index');

        // プライバシーポリシー typeパラメータで分岐
        Route::get('/privacy-policy', [WebPrivacyPolicyController::class, 'index'])->name('web_privacy_policy.index');
    });

    /** テスト */
    Route::name('test.')->group(function () {
        Route::get('test/mail', [TestMailController::class, 'index'])->name('mail.index');
    });
});


// Route::get('/', function () {
//     return view('delivery_office.login');
// });

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');


// Route::get('test/mail', function () {
//     return view('emails.test');
// });

// require __DIR__.'/auth.php';