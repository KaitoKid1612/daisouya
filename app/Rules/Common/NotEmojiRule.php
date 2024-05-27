<?php

namespace App\Rules\Common;

use Illuminate\Contracts\Validation\Rule;

/**
 * 文字列に絵文字が含まれているかどうかを確認するための検証ルール。
 */
class NotEmojiRule implements Rule
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
        // The regular expression matches any emoji
        return !preg_match('/[\x{1F000}-\x{1F9FF}]/u', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attribute には絵文字を含めることはできません';
    }
}
