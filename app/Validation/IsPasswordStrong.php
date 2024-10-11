<?php

namespace App\Validation;

class IsPasswordStrong
{
    public function is_password_strong($password): bool
    {
        $password = trim($password);
        if (!preg_match('/^(?=.*[\W])(?=.*[a-z])(?=.*[0-9]).{5,20}$/', $password)) {
            return false;
        }
        return true;
    }
}
