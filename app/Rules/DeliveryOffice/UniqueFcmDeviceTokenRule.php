<?php

namespace App\Rules\DeliveryOffice;

use Illuminate\Contracts\Validation\Rule;
use App\Models\FcmDeviceTokenDeliveryOffice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/**
 * デバイス名とFCMトークンは複合ユニーク。一致する組み合わせが存在したらバリデート
 */
class UniqueFcmDeviceTokenRule implements Rule
{

    private $device_name;
    private $fcm_token;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($device_name, $fcm_token)
    {
        $this->device_name = $device_name;
        $this->fcm_token = $fcm_token;
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
        $device_name = $this->device_name;
        $fcm_token = $this->fcm_token;

        $result = FcmDeviceTokenDeliveryOffice::select()->where([
            ['device_name', $device_name],
            ['fcm_token', $fcm_token]
        ])->first();

        // logger($result);

        if ($result) {
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
        return 'device_nameとfcm_tokenの組み合わせはすでに存在しています';
    }
}
