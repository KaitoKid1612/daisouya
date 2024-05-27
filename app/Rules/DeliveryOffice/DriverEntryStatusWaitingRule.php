<?php

namespace App\Rules\DeliveryOffice;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Driver;

/**
 * 審査中のドライバーならバリデート
 */
class DriverEntryStatusWaitingRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $driver = Driver::select()->where([
            ['id', $value],
            ['driver_entry_status_id', '=', 2],
        ])->first();

        if ($driver) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '審査中のドライバーです';
    }
}
