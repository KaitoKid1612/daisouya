<?php

namespace App\Rules\Common;

use Illuminate\Contracts\Validation\Rule;

/**
 * 文字列に半角カタカナが含まれていないことを検証するルール。
 */
class NotHalfWidthKanaRule implements Rule
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
        // The regular expression matches any half-width katakana character
        return !preg_match('/[\x{FF61}-\x{FF9F}]/u', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attribute には半角カタカナを含めることはできません';
    }
}
