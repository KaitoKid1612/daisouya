<?php

namespace App\Libs\DriverTask;

use App\Models\Driver;
use App\Models\DriverTaskPlanAllowDriver;

/**
 * 稼働依頼プランで対応可能なドライバープランの管理
 */
class DriverTaskPlanAllowDriverSupport
{
    /**
     * 指定した稼働依頼プランが、指定したドライバーで稼働できるか判定
     * 
     * @param int $driver_task_plan_id 稼働依頼プランID。
     * @param int $driver_id ドライバーID。
     */
    public function checkAllow(?int $driver_task_plan_id, ?int $driver_id)
    {
        $result = false;
        if ($driver_id) {
            $driver = Driver::select()->where('id', $driver_id)->first();
            $driver_plan_id = $driver->driver_plan_id ?? ''; // ドライバープラン

            if ($driver_plan_id) {
                // 許可リストに存在するかチェック
                $driver_task_plan_allow_Driver =  DriverTaskPlanAllowDriver::select()->where([
                    ['driver_task_plan_id', $driver_task_plan_id],
                    ['driver_plan_id', $driver_plan_id],
                ])->first();

                if ($driver_task_plan_allow_Driver) {
                    $result = true;
                } else {
                    $result = false;
                }
            } else {
                // プランを所持していないドライバーは依頼できない
                $result = false;
            }
        } else {
            $result = true;
        }
        return $result;
    }
}
