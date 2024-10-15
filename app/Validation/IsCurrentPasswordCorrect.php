<?php

namespace App\Validation;

use App\Libraries\CIAuth;
use App\Libraries\Hash;
use App\Models\User;

class IsCurrentPasswordCorrect
{
    public function check_current_password($password)
    {

        $password = trim($password);
        $user_id = CIAuth::id();
        $user = new User();
        $user_info = $user->where('id', $user_id)->first();

        if (!Hash::check($password, $user_info->password)) {
            return false;
        }
        return true;
    }
}
