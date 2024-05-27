<?php

namespace App\Rules\Common;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Driver;
use App\Models\DriverTaskPlanAllowDriver;
use App\Libs\DriverTask\DriverTaskPlanAllowDriverSupport;

/**
 * 稼働依頼プランとドライバーのプランが対応しているか判定
 */
class DriverTaskPlanAllowDriverRule implements Rule
{

    private $driver_task_plan_id;
    private $driver_id;

    /**
     * Create a new rule instance.
     * @param int $driver_task_plan_id 稼働依頼プランID。
     * @param int $driver_id ドライバーID。
     * @return void
     */
    public function __construct(?int $driver_task_plan_id, ?int $driver_id = null)
    {
        $this->driver_task_plan_id = $driver_task_plan_id;
        $this->driver_id = $driver_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $driver_task_plan_id = $this->driver_task_plan_id;
        $driver_id = $this->driver_id;


        $driver_task_plan_allow_driver_support = new DriverTaskPlanAllowDriverSupport();

        $result = $driver_task_plan_allow_driver_support->checkAllow($driver_task_plan_id, $driver_id);

        return $result;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '稼働依頼プランとドライバーのプランが不整合です';
    }
}
