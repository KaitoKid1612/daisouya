<?php

namespace App\Http\Controllers\DeliveryOffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Cashier;

class DashboardController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        // logger($login_user);

        // stripe_id作成
        // $login_user = $login_user->createAsStripeCustomer();

        // stripe_id 存在しなければ作成
        // $create_stripe = $login_user->createOrGetStripeCustomer();
        // logger($create_stripe);
        // $stripe_id = $login_user->stripe_id;
        // 取得
        // $user = Cashier::findBillable($stripe_id);
        // logger($user);
        // $balance = $user->balance();
        // logger($balance);

        // ユーザのカードから引き落とし
        // $user->applyBalance(-500, 'Premium customer top-up.');

        // ユーザーのカードに入金
        // $user->applyBalance(300, 'Bad usage penalty.');

        // $transactions = $user->balanceTransactions();

        // 顧客に確認してもらうため入金と引き落としのログを提供する
        // 全トランザクションの取得
        // foreach ($transactions as $transaction) {
        //     // トランザクションの金額
        //     $amount = $transaction->amount();
        //     logger( 'amount' . $amount);


        //     // 利用できるなら、関連するインボイスの取得
        //     $invoice = $transaction->invoice();
        //     logger( 'invoice' . $invoice);
        // }

        // logger($user->createSetupIntent());
        // logger($balance);

        # 支払い方法
        // $paymentMethods =  $user->paymentMethods();
        // $defaultPaymentMethod = $user->defaultPaymentMethod();
        // $user->addPaymentMethod($paymentMethods);
        // $user->deletePaymentMethods();



        // 購入処理
        // $paymentMethodID = ''
        // try {
        //     $stripeCharge = $login_user->charge(100, 'pm_1MIbh7DxrLwi5sltdqT83HlK');
        //     $msg = '支払いに成功しました。';
        //     logger($stripeCharge);
        // } catch (\Throwable $e) {
        //     $msg_error = mb_substr($e->__toString(), 0, 200);
        //     $msg = "支払いに失敗しました。{$msg_error}";
        // }


        // $msg = '';
        // // 購入処理
        // try {
        //     $stripeCharge = $login_user->charge(100, $payment_method_id);
        //     $stripe_refund = $login_user->refund($stripeCharge->id);

        //     logger($stripeCharge->id);
        //     $msg = '支払いに成功しました。';
        //     logger($stripeCharge);
        //     logger($stripe_refund);
        // } catch (\Throwable $e) {
        //     $msg_error = mb_substr($e->__toString(), 0, 200);
        //     $msg = "支払いに失敗しました。{$msg_error}";
        // }
        // logger($msg);
        // exit;

        return view('delivery_office.dashboard.index');
    }
}
