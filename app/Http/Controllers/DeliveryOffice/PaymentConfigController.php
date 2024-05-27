<?php

namespace App\Http\Controllers\DeliveryOffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Cashier;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentMethod;
use App\Models\DeliveryOffice;
use App\Http\Requests\DeliveryOffice\PaymentConfigStorePaymentMethodRequest;

/**
 * 決済設定
 */
class PaymentConfigController extends Controller
{
    /**
     * 登録済み支払い方法一覧
     * +API機能
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        // API時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $stripe_id = $login_user->stripe_id ?? '';

        // 支払い方法一覧
        try {
            $payment_method_list = $login_user->paymentMethods(); // 支払い方法一覧
        } catch (\Throwable $e) {
            $payment_method_list = [];
            log::error($e);
        }
        // logger($payment_method_list->toArray());

        // デフォルトの支払い方法
        // logger($login_user->defaultPaymentMethod());

        // デフォルトの支払い方法があるか
        // logger($login_user->hasDefaultPaymentMethod());

        $msg = '';


        $api_status = true;
        if ($payment_method_list) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $payment_method_list
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return view('delivery_office.payment_config.index', [
                'payment_method_list' => $payment_method_list,
            ]);
        }
    }

    /**
     * 支払い方法登録画面
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        // クレジットカードの登録に必要なシークレットをStripeから取得する
        $intent = $login_user->createSetupIntent();
        // logger($intent);
        // exit;
        return view('delivery_office.payment_config.create', [
            'intent' => $intent
        ]);
    }

    /**
     * 支払い方法登録処理
     * +API機能
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報
        // logger($request);

        // API時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $api_status = true;
        $msg = "";

        try {
            // Stripe顧客を作成する
            $stripe_customer = $login_user->createOrGetStripeCustomer();
            // トークンを受け取り、Stripeに検証した上で、usersテーブルに、支払い情報を登録する。
            $result = $login_user->updateDefaultPaymentMethod($request->payment_method);
            if ($result) {
                $msg = "クレジットカードを登録しました。";
                $api_status = true;
            } else {
                $msg = "クレジットカードを登録できませんでした!";
                $api_status = false;
            }
        } catch (\Throwable $e) {
            $api_status = false;
            Log::error($e->__toString());
        }


        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'message' => $msg
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return redirect()->route('delivery_office.payment_config.index')->with([
                'msg' => $msg
            ]);
        }


        // return response()->redirectTo('/users/payment_method');
    }

    /**
     * 取得
     * +API機能
     *
     * @param  int  $payment_id
     * @return \Illuminate\Http\Response
     */
    public function show($payment_id, Request $request)
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        // API時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $api_status = true;

        $payment_item = $login_user->findPaymentMethod($payment_id);
        // logger($payment_item);

        if ($payment_item) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $payment_item
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return view('delivery_office.payment_config.show', [
                'payment_item' => $payment_item,
            ]);
        }
    }

    /**
     * 支払い方法削除。
     * +API機能
     * 
     * @param  int  $payment_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($payment_id, Request $request)
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        // API時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $api_status = true;
        $msg = "";

        try {
            $result = $login_user->deletePaymentMethod($payment_id);
            $msg = '削除しました。';

            if ($result) {
                $api_status = true;
            } else {
                $api_status = false;
            }
        } catch (\Throwable $e) {
            $api_status = false;
            $msg = '削除できませんでした!';
            Log::error($e->__toString());
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                "message" => $msg
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return redirect()->route('delivery_office.payment_config.index')->with([
                'msg' => $msg
            ]);
        }
    }

    /**
     * stripe customerID 取得
     * +API機能
     */
    public function showCustomer()
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        // API時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $api_status = true;

        try {
            // Stripe顧客を作成する
            $stripe_customer = $login_user->createOrGetStripeCustomer();
        } catch (\Throwable $e) {
            $api_status = false;
            Log::error(__METHOD__ . ':' . __LINE__ . ' ' . $e);
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                "data" => $stripe_customer
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            // viewなし
        }
    }

    /**
     * stripe 支払い方法 作成
     * +API機能
     */
    public function storePaymentMethod(PaymentConfigStorePaymentMethodRequest $request)
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        // API時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }
        $email = $login_user->email;
        $stripe_name = $login_user->stripeName();
        $customer_id = $login_user->stripe_id;

        $number = $request->number;
        $exp_month = $request->exp_month;
        $exp_year = $request->exp_year;
        $cvc = $request->cvc;

        $api_status = true;
        $msg = "";
        try {
            Stripe::setApiKey(
                config('stripe.stripe_secret')
            );

            // stripe customer 作成
            if (!$customer_id) {
                $customer = Customer::create([
                    'email' => $email,
                    'name' => $stripe_name
                ]);

                $customer_id = $customer->id;
                // database にstripe_idを保存
                DeliveryOffice::where('id', $login_id)
                    ->update(['stripe_id' => $customer_id]);
            }

            // 支払い方法 登録
            $payment_method = PaymentMethod::create([
                'type' => 'card',
                'card' => [
                    'number' => $number,
                    'exp_month' => $exp_month,
                    'exp_year' => $exp_year,
                    'cvc' => $cvc,
                ],
            ]);

            // 登録した支払い方法を顧客と接続
            $response =  $payment_method->attach([
                'customer' => $customer_id,
            ]);

            if ($response) {
                $msg = "クレジットカードを登録しました。";
            } else {
                $msg = "クレジットカードを登録できませんでした!";
            }
        } catch (\Throwable $e) {
            $api_status = false;
            $msg = "クレジットカードを登録できませんでした!";
            Log::error(__METHOD__ . ':' . __LINE__ . ' ' . $e);
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                "msg" => $msg,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            // viewなし
        }
    }
}
