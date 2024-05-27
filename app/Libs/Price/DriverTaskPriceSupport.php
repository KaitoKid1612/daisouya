<?php

namespace App\Libs\Price;

use App\Models\DriverTaskPlan;
use App\Models\WebBusySeason;
use App\Models\WebConfigSystem;

/**
 * 稼働依頼の料金計算
 */
class DriverTaskPriceSupport
{

    /**
     * 料金を計算
     *
     * プラン、繁忙期かどうか、依頼する日によって、料金は変動する。
     */
    public function getPrice(int $driver_task_plan_id, int $freight_cost, $task_date, $system_price)
    {
        $total_including_tax = null; //税込合計金額
        $total_excluding_tax = null; // 税抜合計金額
        $tax = null; // 税金
//        $system_price = null; // システム利用料
        $busy_system_price = null; // システム利用料(繁忙期)
        $emergency_price = null; // 緊急依頼料金

        $config_system = WebConfigSystem::select()->where('id', 1)->first();
        $tax_rate = $config_system->default_tax_rate; // 税率

        // プランの取得
        $driver_task_plan = DriverTaskPlan::find($driver_task_plan_id);
        if ($driver_task_plan) {

            $busy_season = WebBusySeason::select()->where('busy_date', $task_date)->first();

            $emergency_price = $this->getEmergencyPrice($driver_task_plan_id, $task_date);
            if (in_array($driver_task_plan_id, [1])) {
                /* プレミアム と スタンダード */
                // システム利用料 + 緊急依頼料金 + 運賃

                /* 通常期。繁忙期でも通常期料金 */
                $system_price = $driver_task_plan->system_price;
                $total_excluding_tax = $system_price + $emergency_price + $freight_cost;

                $tax = ceil(($total_excluding_tax) * ($tax_rate / 100)); // 消費税

                $total_including_tax = $total_excluding_tax + $tax;
            } elseif (in_array($driver_task_plan_id, [2])) {
                /* プレミアム と スタンダード */
                // システム利用料 + 緊急依頼料金 + 運賃

                if ($busy_season) {
                    /* 繁忙期 */
                    $busy_system_price = $driver_task_plan->busy_system_price ?? 0;
                    $total_excluding_tax = $busy_system_price + $emergency_price + $freight_cost;
                } else {
                    /* 通常期 */
                    $total_excluding_tax = $system_price + $emergency_price + $freight_cost;
                }

                $tax = ceil(($total_excluding_tax) * ($tax_rate / 100)); // 消費税

                $total_including_tax = $total_excluding_tax + $tax;
            } elseif ($driver_task_plan_id == 3) {
                /* ライト */
                // 運賃のxx%(システム利用料) + 緊急依頼料金 + 運賃

                $system_price_percent = $driver_task_plan->system_price_percent ?? null; // システム利用料金(運賃の%)
                $busy_system_price_percent = $driver_task_plan->busy_system_price_percent ?? null; // システム料金(繁忙期,運賃の%)
                $busy_system_price_percent_over = $driver_task_plan->busy_system_price_percent_over ?? null; // システム料金(繁忙期,運賃の%,既定運賃以上の場合)

                if ($busy_season) {
                    /* 繁忙期 */

                    // 運賃によって、システム利用料は変わる
                    if ($freight_cost >= 20000) {
                        $busy_system_price = ceil($freight_cost * ($busy_system_price_percent_over / 100));
                    } else {
                        $busy_system_price = ceil($freight_cost * ($busy_system_price_percent / 100));
                    }
                    $total_excluding_tax = $busy_system_price + $emergency_price + $freight_cost;
                } else {
                    /* 通常期 */
                    $system_price = ceil($freight_cost * ($system_price_percent / 100));
                    $total_excluding_tax = $system_price + $emergency_price + $freight_cost;
                }

                $tax = ceil(($total_excluding_tax) * ($tax_rate / 100)); // 消費税

                $total_including_tax = $total_excluding_tax + $tax;
            }
        }

        $result = [
            "total_including_tax" => $total_including_tax, // 税込合計金額
            "total_excluding_tax" => $total_excluding_tax, // 税抜合計金額
            "tax" => $tax, // 税金
            "system_price" => $system_price, // システム利用料
            "busy_system_price" => $busy_system_price, // システム利用料(繁忙期)
            "freight_cost" => $freight_cost, // 運賃
            "emergency_price" => $emergency_price, // 緊急依頼料金
        ];

        // logger($result);
        return $result;
    }

    /**
     * 緊急依頼料金を取得
     *
     * 依頼申し込み日、依頼日の関係で緊急依頼になるかどうか決まる。
     * また稼働依頼プランによって料金も変わる
     *
     * @param int $driver_task_plan_id 稼働依頼プランID
     * @param string $task_date 稼働依頼日。形式は `yyyy-mm-dd` を期待する
     * @param string $request_date 稼働。形式は `yyyy-mm-dd HH:MM:SS` を期待する
     *
     * @return int 緊急料金の金額。この金額は整数で返されます。
     */
    public function getEmergencyPrice(
        int $driver_task_plan_id,
        $task_date,
        $request_date = ''
    ) {
        $config_system = WebConfigSystem::select()->where('id', 1)->first();
        $driver_task_plan = DriverTaskPlan::find($driver_task_plan_id); // 稼働依頼プラン
        $emergency_price = null; // 緊急依頼料金

        /* -- Rule --
        緊急依頼の範囲は管理画面で設定している。
        稼働日のfrom時間前 ~ 稼働日のto時間前 で決まる。
        申込日が緊急依頼の範囲なら、緊急依頼料金を加算。
        そうでなければ、緊急依頼料金を加算しない。 */

        $soon_price_time_limit_from = $config_system->soon_price_time_limit_from; // from 緊急依頼の時間の範囲
        $soon_price_time_limit_to = $config_system->soon_price_time_limit_to; // to 緊急依頼の時間の範囲

        $dt_task_date = new \DateTime("{$task_date} 00:00:00"); // 稼働日
        $dt_request_date = new \DateTime($request_date); // 申込日

        // 緊急依頼の範囲 DateTime from
        $dt_soon_task_date_from = clone $dt_task_date;
        if ($soon_price_time_limit_from >= 0) {
            $dt_soon_task_date_from->modify("+{$soon_price_time_limit_from} hours");
        } else {
            $dt_soon_task_date_from->modify("{$soon_price_time_limit_from} hours");
        }

        // 緊急依頼の範囲 DateTime to
        $dt_soon_task_date_to = clone $dt_task_date;
        if ($soon_price_time_limit_to >= 0) {
            $dt_soon_task_date_to->modify("+{$soon_price_time_limit_to} hours");
        } else {
            $dt_soon_task_date_to->modify("{$soon_price_time_limit_to} hours");
        }

        // 申し込み日が、緊急依頼の範囲であれば料金を適用
        if ($dt_request_date >= $dt_soon_task_date_from && $dt_request_date <= $dt_soon_task_date_to) {
            $emergency_price = $driver_task_plan->emergency_price;
        } else {
            $emergency_price = 0;
        }

        return $emergency_price;
    }

    /**
     * 指定した稼働日に指定したプランを利用した稼働は、繁忙期料金が適用対象か判定。
     *
     * @param int $driver_task_plan_id 稼働依頼プランID。
     * @param string|null $task_date 稼働日（'Y-m-d'形式の日付文字列）。
     *
     * @return bool 繁忙期料金が適用される場合はtrue、そうでない場合はfalse。
     */
    public function checkBusyTaskPrice(int $driver_task_plan_id, ?string $task_date)
    {
        $result = false;
        $busy_season = WebBusySeason::select()->where('busy_date', $task_date)->first();

        $driver_task_plan = DriverTaskPlan::select()->where('id', $driver_task_plan_id)->first();

        if ($busy_season) {
            // 指定したプランに繁忙期設定がされているか
            if ($driver_task_plan && $driver_task_plan->busy_system_price || ($driver_task_plan->busy_system_price_percent && $driver_task_plan->busy_system_price_percent_over)) {
                $result = true;
            } else {
                $result = false;
            }
        } else {
            $result = false;
        }
        return $result;
    }
}
