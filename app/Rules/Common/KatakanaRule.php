<?php

namespace App\Rules\Common;

use Illuminate\Contracts\Validation\Rule;

/**
 * カタカナのみを受け付ける。
 */
class KatakanaRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Check if the value only contains Katakana characters
        return preg_match('/^[ァ-ヴヵヶ]+$/u', $value) === 1;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attribute はカタカナのみ有効です';
    }
}
