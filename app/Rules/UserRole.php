<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserRole implements Rule
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
        //
        $passed = false;
        $currentRole = Auth::user()->role;
        // check what is requested against currentRole
        switch ($value) {
                #super role is requested
            case 'super':
                if ($currentRole == 'super') {
                    $passed = true;
                } else {
                    $passed = false;
                }
                break;
            case 'admin':
                if (in_array($currentRole, ['super', 'admin'])) {
                    $passed = true;
                } else {
                    $passed = false;
                }
                break;
            case 'moderator':
                if (in_array($currentRole, ['super', 'admin'])) {
                    $passed = true;
                } else {
                    $passed = false;
                }
                break;
            case 'auditor':
                if (in_array($currentRole, ['super', 'admin', 'moderator'])) {
                    $passed = true;
                } else {
                    $passed = false;
                }
                break;
            case 'user':
                if (in_array($currentRole, ['super', 'admin', 'moderator'])) {
                    $passed = true;
                } else {
                    $passed = false;
                }
                break;
            default:
                $passed = false;
                break;
        }
        return $passed;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Insufficient Role Permissions for This Role Change';
    }
}
