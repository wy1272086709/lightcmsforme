<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class VerCheck implements Rule
{
    public function __construct()
    {

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
        return version_compare($value, '0.0');
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '不是合法的版本字符串!';
    }

}