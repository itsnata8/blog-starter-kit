<?php

namespace App\Controllers;

use App\Models\User;
use App\Libraries\Hash;
use App\Libraries\CIAuth;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PasswordResetToken;
use Carbon\Carbon;


class AuthController extends BaseController
{
    protected $helpers = ['url', 'form', 'CIMail'];
    public function loginForm()
    {
        $data = [
            'pageTitle' => 'Login',
            'validation' => null
        ];
        return view('backend/pages/auth/login', $data);
    }
    public function loginHandler()
    {
        $fieldType = filter_var($this->request->getVar('login_id'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if ($fieldType == 'email') {
            $isValid = $this->validate([
                'login_id' => [
                    'rules' => 'required|valid_email|is_not_unique[users.email]',
                    'errors' => [
                        'required' => 'Email is required',
                        'valid_email' => 'Please, check the email field. It does not appears to be valid.',
                        'is_not_unique' => 'Email is not exists in our system.'
                    ]
                ],
                'password' => [
                    'rules' => 'required|min_length[5]|max_length[45]',
                    'errors' => [
                        'required' => 'Password is required',
                        'min_length' => 'Password must have atleast 5 characters in length.',
                        'max_length' => 'Password must not have characters more than 45 in length.'
                    ]
                ]
            ]);
        } else {
            $isValid = $this->validate([
                'login_id' => [
                    'rules' => 'required|is_not_unique[users.username]',
                    'errors' => [
                        'required' => 'Username is required',
                        'is_not_unique' => 'Username is not exists in our system.'
                    ]
                ],
                'password' => [
                    'rules' => 'required|min_length[5]|max_length[45]',
                    'errors' => [
                        'required' => 'Password is required',
                        'min_length' => 'Password must have atleast 5 characters in length.',
                        'max_length' => 'Password must not have characters more than 45 in length.'
                    ]
                ]
            ]);
        }
        if (!$isValid) {
            return view('backend/pages/auth/login', [
                'pageTitle' => 'Login',
                'validation' => $this->validator
            ]);
        } else {
            $user = new User();
            $userInfo = $user->where($fieldType, $this->request->getVar('login_id'))->first();
            $check_password = Hash::check($this->request->getVar('password'), $userInfo['password']);

            if (!$check_password) {
                return redirect()->route('admin.login.form')->with('fail', 'Wrong password')->withInput();
            } else {
                CIAuth::setCIAuth($userInfo);
                return redirect()->route('admin.home');
            }
        }
    }
    public function forgotForm()
    {
        $data = array(
            'pageTitle' => 'Forgot Password',
            'validation' => null
        );
        return view('backend/pages/auth/forgot', $data);
    }
    public function sendPasswordResetLink()
    {
        $isValid = $this->validate([
            'email' => [
                'rules' => 'required|valid_email|is_not_unique[users.email]',
                'errors' => [
                    'required' => 'Email is required',
                    'valid_email' => 'Please, check the email field. It does not appears to be valid.',
                    'is_not_unique' => 'Email is not exists in our system.'
                ]
            ]
        ]);
        if (!$isValid) {
            return view('backend/pages/auth/forgot', [
                'pageTitle' => 'Forgot Password',
                'validation' => $this->validator
            ]);
        } else {
            // get credential
            $user = new User();
            $user_info = $user->asObject()->where('email', $this->request->getVar('email'))->first();

            // generate token
            $token = bin2hex(openssl_random_pseudo_bytes(65));

            // get reset password token
            $password_reset_token = new PasswordResetToken();
            $isOldTokenExists = $password_reset_token->asObject()->where('email', $user_info->email)->first();

            if ($isOldTokenExists) {
                $password_reset_token->where('email', $user_info->email)
                    ->set(['token' => $token, 'created_at' => Carbon::now()])
                    ->update();
            } else {
                $password_reset_token->insert([
                    'email' => $user_info->email,
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]);
            }

            // create action link
            $actionLink = base_url(route_to('admin.reset-password', $token));

            $mail_data = array(
                'actionLink' => $actionLink,
                'user' => $user_info
            );

            $view = \Config\Services::renderer();
            $mail_body = $view->setVar('mail_data', $mail_data)->render('email-templates/forgot-email-template');

            $mailConfig = array(
                'mail_from_email' => env('MAIL_FROM_ADDRESS'),
                'mail_from_name' => env('MAIL_FROM_NAME'),
                'mail_recipient_email' => $user_info->email,
                'mail_recipient_name' => $user_info->name,
                'mail_subject' => 'Reset Password',
                'mail_body' => $mail_body
            );

            // send email
            if (sendEmail($mailConfig)) {
                return redirect()->route('admin.forgot.form')->with('success', 'We have e-mailed your password reset link.');
            } else {
                return redirect()->route('admin.forgot.form')->with('fail', 'Something went wrong. Please try again later.');
            }
        }
    }
    public function resetPassword($token)
    {
        $passwordResetPassword = new PasswordResetToken();
        $check_token = $passwordResetPassword->asObject()->where('token', $token)->first();

        if (!$check_token) {
            return redirect()->route('admin.forgot.form')->with('fail', 'Invalid token. Please try again later.');
        } else {
            // check if token not expired (not older than 15 minutes)
            $diffMins = Carbon::createFromFormat('Y-m-d H:i:s', $check_token->created_at)->diffInMinutes(Carbon::now());

            if ($diffMins > 150000) {
                return view('backend/pages/auth/reset', [
                    'pageTitle' => 'Reset Password',
                    'validation' => null,
                    'token' => $token
                ]);
            }
        }
    }
    public function resetPasswordHandler($token)
    {
        $isValid = $this->validate([
            'new_password' => [
                'rules' => 'required|min_length[5]|max_length[20]|is_password_strong[]',
                'errors' => [
                    'required' => 'Enter new password',
                    'min_length' => 'New password must be at least 5 characters',
                    'max_length' => 'New password must not be more than 20 characters',
                    'is_password_strong' => 'New password must contains atleast 1 uppercase, 1 lowercase, 1 number and 1 special character.'
                ]
            ],
            'confirm_new_password' => [
                'rules' => 'required|matches[new_password]',
                'matches' => 'Password does not match.'
            ]
        ]);

        if ($isValid) {
            return view('backend/pages/auth/reset', [
                'pageTitle' => 'Reset Password',
                'validation' => null,
                'token' => $token,
            ]);
        } else {
            echo 'Form validated';
        }
    }
}
