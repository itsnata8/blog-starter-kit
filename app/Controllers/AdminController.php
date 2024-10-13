<?php

namespace App\Controllers;

use App\Libraries\CIAuth;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AdminController extends BaseController
{
    protected $helpers = ['url', 'form', 'CIMail', 'CIFunctions'];
    public function index()
    {
        $data = [
            'pageTitle' => 'Dashboard',

        ];
        return view('backend/pages/home', $data);
    }
    public function logoutHandler()
    {
        CIAuth::forget();
        return redirect()->route('admin.login.form')->with('fail', 'You are logged out!');
    }
    public function profile()
    {
        $data = array(
            'pageTitle' => 'Profile',
        );
        return view('backend/pages/profile', $data);
    }
}
