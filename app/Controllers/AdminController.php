<?php

namespace App\Controllers;

use App\Models\User;
use App\Libraries\Hash;
use App\Models\Setting;
use App\Models\Category;
use App\Libraries\CIAuth;
use App\Models\SocialMedia;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use SSP;

class AdminController extends BaseController
{
    protected $helpers = ['url', 'form', 'CIMail', 'CIFunctions'];
    protected $db;

    public function __construct()
    {
        require_once APPPATH . 'ThirdParty/ssp.php';
        $this->db = db_connect();
    }
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
    public function updatePersonalDetails()
    {
        $request = \Config\Services::request();
        $validation = \Config\Services::validation();
        $user_id = CIAuth::id();

        if ($request->isAJAX()) {
            $this->validate([
                'name' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Full name is required'
                    ]
                ],
                'username' => [
                    'rules' => 'required|min_length[4]|is_unique[users.username,id,' . $user_id . ']',
                    'errors' => [
                        'required' => 'Username is required',
                        'min_length' => 'Username must have atleast 4 characters in length',
                        'is_unique' => 'Username is already taken!'
                    ]
                ]
            ]);

            if ($validation->run() == FALSE) {
                $errors = $validation->getErrors();
                return json_encode(['status' => 0, 'errors' => $errors]);
            } else {
                $user = new User();
                $update = $user->where('id', $user_id)->set([
                    'name' => $request->getVar('name'),
                    'username' => $request->getVar('username'),
                    'bio' => $request->getVar('bio'),
                ])->update();

                if ($update) {
                    $user_info = $user->find($user_id);
                    return json_encode(['status' => 1, 'user_info' => $user_info, 'msg' => 'Profile updated successfully!']);
                } else {
                    return json_encode(['status' => 0, 'msg' => 'Something went wrong!']);
                }
            }
        }
    }
    public function updatePictureProfile()
    {
        $request = \Config\Services::request();
        $user_id = CIAuth::id();
        $user = new User();
        $user_info = $user->asObject()->where('id', $user_id)->first();

        $path = 'images/users/';
        $file = $request->getFile('user_profile_file');
        $old_picture = $user_info->picture;
        $new_filename = 'UIMG_' . $user_id . $file->getRandomName();

        if ($file->move($path, $new_filename)) {
            if ($old_picture != null && file_exists($path . $old_picture)) {
                unlink($path . $old_picture);
            }
            $user->where('id', $user_info->id)
                ->set(
                    ['picture' => $new_filename]
                )->update();

            echo json_encode(['status' => 1, 'msg' => 'Profile picture updated successfully!', 'path' => $path . $new_filename]);
        } else {
            echo json_encode(['status' => 0, 'msg' => 'Something went wrong!']);
        }
    }
    public function changePassword()
    {
        $request = \Config\Services::request();

        if ($request->isAJAX()) {
            $validation = \Config\Services::validation();
            $user_id = CIAuth::id();
            $user = new User();
            $user_info = $user->asObject()->where('id', $user_id)->first();

            // validate the form 
            $this->validate([
                'current_password' => [
                    'rules' => 'required|min_length[5]|check_current_password[current_password]',
                    'errors' => [
                        'required' => 'Enter current password',
                        'min_length' => 'Current password must have atleast 5 characters in length',
                        'check_current_password' => 'Current password is incorrect'
                    ]
                ],
                'new_password' => [
                    'rules' => 'required|min_length[5]|max_length[20]|is_password_strong[new_password]',
                    'errors' => [
                        'required' => 'New password is required',
                        'min_length' => 'New password must have atleast 5 characters in length',
                        'max_length' => 'New password must have atmost 20 characters in length',
                        'is_password_strong' => 'Password must have atleast 1 uppercase, 1 lowercase, 1 number and 1 special character'
                    ]
                ],
                'confirm_new_password' => [
                    'rules' => 'required|matches[new_password]',
                    'errors' => [
                        'required' => 'Confirm new password is required',
                        'matches' => 'Confirm new password must match with new password'
                    ]
                ]
            ]);

            if ($validation->run() === FALSE) {
                $errors = $validation->getErrors();
                return $this->response->setJSON(['status' => 0, 'token' => csrf_hash(), 'errors' => $errors]);
            } else {
                // update user(admin) password in db
                $user->where('id', $user_info->id)
                    ->set(['password' => Hash::make($request->getVar('new_password'))])->update();

                // Send email notification to user(admin) email
                $mail_data = array(
                    'user' => $user_info,
                    'new_password' => $request->getVar('new_password')
                );

                $view = \Config\Services::renderer();
                $mail_body = $view->setVar('mail_data', $mail_data)->render('email-templates/password-changed-email-template');

                $mailConfig = array(
                    'mail_from_email' => env('EMAIL_FROM_ADDRESS'),
                    'mail_from_name' => env('EMAIL_FROM_NAME'),
                    'mail_recipient_email' => $user_info->email,
                    'mail_recipient_name' => $user_info->name,
                    'mail_subject' => 'Password Changed',
                    'mail_body' => $mail_body
                );
                sendEmail($mailConfig);
                return $this->response->setJSON(['status' => 1, 'token' => csrf_hash(), 'msg' => 'Password changed successfully!']);
            }
        }
    }
    public function settings()
    {
        $data = [
            'pageTitle' => 'Settings',
        ];

        return view('backend/pages/settings', $data);
    }

    public function updateGeneralSettings()
    {
        $request = \Config\Services::request();

        if ($request->isAJAX()) {
            $validation = \Config\Services::validation();

            $this->validate([
                'blog_title' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Blog title is required'
                    ]
                ],
                'blog_email' => [
                    'rules' => 'required|valid_email',
                    'errors' => [
                        'required' => 'Blog email is required',
                        'valid_email' => 'Invalid email address'
                    ]
                ]
            ]);
            if ($validation->run() === FALSE) {
                $errors = $validation->getErrors();
                return $this->response->setJSON(['status' => 0, 'token' => csrf_hash(), 'errors' => $errors]);
            } else {
                $settings = new Setting();
                $setting_id = $settings->asObject()->first()->id;
                $update = $settings->where('id', $setting_id)
                    ->set([
                        'blog_title' => $request->getVar('blog_title'),
                        'blog_email' => $request->getVar('blog_email'),
                        'blog_phone' => $request->getVar('blog_phone'),
                        'blog_meta_keywords' => $request->getVar('blog_meta_keywords'),
                        'blog_meta_description' => $request->getVar('blog_meta_description'),
                    ])->update();
                if ($update) {
                    return $this->response->setJSON(['status' => 1, 'token' => csrf_hash(), 'msg' => 'Settings updated successfully!']);
                } else {
                    return $this->response->setJSON(['status' => 0, 'token' => csrf_hash(), 'msg' => 'Something went wrong!']);
                }
            }
        }
    }
    public function updateBlogLogo()
    {
        $request = \Config\Services::request();

        if ($request->isAJAX()) {
            $settings = new Setting();
            $path = 'images/blog/';
            $file = $request->getFile('blog_logo');
            $setting_data = $settings->asObject()->first();
            $old_blog_logo = $setting_data->blog_logo;
            $new_filename = 'blog_logo_' . $file->getRandomName();
            if ($file->move($path, $new_filename)) {
                if ($old_blog_logo != null && file_exists($path . $old_blog_logo)) {
                    unlink($path . $old_blog_logo);
                }
                $update = $settings->where('id', $setting_data->id)->set(['blog_logo' => $new_filename])->update();
                if ($update) {
                    return $this->response->setJSON(['status' => 1, 'token' => csrf_hash(), 'msg' => 'Blog logo updated successfully!']);
                } else {
                    return $this->response->setJSON(['status' => 0, 'token' => csrf_hash(), 'msg' => 'Something went wrong!']);
                }
            } else {

                return $this->response->setJSON(['status' => 0, 'token' => csrf_hash(), 'msg' => 'Something went wrong!']);
            }
        }
    }
    public function updateBlogFavicon()
    {

        $request = \Config\Services::request();

        if ($request->isAjax()) {
            $settings = new Setting();
            $path = 'images/blog/';
            $file = $request->getFile('blog_favicon');
            $setting_data = $settings->asObject()->first();
            $old_blog_favicon = $setting_data->blog_favicon;
            $new_filename = 'favicon_' . $file->getRandomName();

            if ($file->move($path, $new_filename)) {
                if ($old_blog_favicon != null && file_exists($path . $old_blog_favicon)) {
                    unlink($path . $old_blog_favicon);
                }
                $update = $settings->where('id', $setting_data->id)->set(['blog_favicon' => $new_filename])->update();
                if ($update) {
                    return $this->response->setJSON(['status' => 1, 'token' => csrf_hash(), 'msg' => 'Blog favicon updated successfully!']);
                } else {
                    return $this->response->setJSON(['status' => 0, 'token' => csrf_hash(), 'msg' => 'Something went wrong!']);
                }
            } else {
                return $this->response->setJSON(['status' => 0, 'token' => csrf_hash(), 'msg' => 'Something went wrong!']);
            }
        }
    }
    public function updateSocialMedia()
    {
        $request = \Config\Services::request();

        if ($request->isAJAX()) {
            $validation = \Config\Services::validation();
            $this->validate([
                'facebook_url' => [
                    'rules' => 'permit_empty|valid_url_strict',
                    'errors' => [
                        'valid_url_strict' => 'Invalid facebook page URL'
                    ]
                ],
                'twitter_url' => [
                    'rules' => 'permit_empty|valid_url_strict',
                    'errors' => [
                        'valid_url_strict' => 'Invalid twitter URL'
                    ]
                ],
                'instagram_url' => [
                    'rules' => 'permit_empty|valid_url_strict',
                    'errors' => [
                        'valid_url_strict' => 'Invalid instagram URL'
                    ]
                ],
                'youtube_url' => [
                    'rules' => 'permit_empty|valid_url_strict',
                    'errors' => [
                        'valid_url_strict' => 'Invalid youtube channel URL'
                    ]
                ],
                'github_url' => [
                    'rules' => 'permit_empty|valid_url_strict',
                    'errors' => [
                        'valid_url_strict' => 'Invalid github URL'
                    ]
                ],
                'linkedin_url' => [
                    'rules' => 'permit_empty|valid_url_strict',
                    'errors' => [
                        'valid_url_strict' => 'Invalid linkedin URL'
                    ]
                ],
            ]);
            if ($validation->run() === FALSE) {
                $errors = $validation->getErrors();
                return $this->response->setJSON(['status' => 0, 'token' => csrf_hash(), 'errors' => $errors]);
            } else {
                $social_media = new SocialMedia();
                $social_media_id = $social_media->asObject()->first()->id;
                $update = $social_media->where('id', $social_media_id)->set([
                    'facebook_url' => $request->getVar('facebook_url'),
                    'twitter_url' => $request->getVar('twitter_url'),
                    'instagram_url' => $request->getVar('instagram_url'),
                    'youtube_url' => $request->getVar('youtube_url'),
                    'github_url' => $request->getVar('github_url'),
                    'linkedin_url' => $request->getVar('linkedin_url'),
                ])->update();

                if ($update) {
                    return $this->response->setJSON(['status' => 1, 'token' => csrf_hash(), 'msg' => 'Social media updated successfully!']);
                } else {
                    return $this->response->setJSON(['status' => 0, 'token' => csrf_hash(), 'msg' => 'Something went wrong!']);
                }
            }
        }
    }
    public function categories()
    {
        $data = [
            'pageTitle' => 'Categories',
        ];

        return view('backend/pages/categories', $data);
    }
    public function addCategory()
    {
        $request = \Config\Services::request();

        if ($request->isAJAX()) {
            $validation = \Config\Services::validation();

            $this->validate([
                'category_name' => [
                    'rules' => 'required|is_unique[categories.name]',
                    'errors' => [
                        'required' => 'Category name is required',
                        'is_unique' => 'Category name already exists'
                    ]
                ]
            ]);
            if ($validation->run() === FALSE) {
                $errors = $validation->getErrors();
                return $this->response->setJSON(['status' => 0, 'token' => csrf_hash(), 'errors' => $errors]);
            } else {
                $category = new Category();
                $save = $category->save(['name' => $request->getVar('category_name')]);
                if ($save) {
                    return $this->response->setJSON(['status' => 1, 'token' => csrf_hash(), 'msg' => 'Category added successfully!']);
                } else {
                    return $this->response->setJSON(['status' => 0, 'token' => csrf_hash(), 'msg' => 'Something went wrong!']);
                }
            }
        }
    }
    public function getCategories()
    {
        // DB Details
        $dbDetails = array(
            "host" => $this->db->hostname,
            "user" => $this->db->username,
            "pass" => $this->db->password,
            "db"   => $this->db->database
        );

        $table = "categories";
        $primaryKey = "id";
        $columns = array(
            array('db' => 'id', 'dt' => 0),
            array('db' => 'name', 'dt' => 1),
            array(
                "db" => 'id',
                "dt" => 2,
                "formatter" => function ($d, $row) {
                    return "(x) will be added later";
                }
            ),
            array(
                "db" => "id",
                "dt" => 3,
                "formatter" => function ($d, $row) {
                    return "<div class='btn-group'>
                    <button class='btn btn-sm btn-link p-0 mx-1 editCategoryBtn' data-id='" . $row['id'] . "'>Edit</button>
                    <button class='btn btn-sm btn-link p-0 mx-1 deleteCategoryBtn' data-id='" . $row['id'] . "'>Delete</button>
                    </div>";
                }
            ),
            array(
                "db" => "ordering",
                "dt" => 4,
            ),
        );

        return json_encode(
            SSP::simple($_GET, $dbDetails, $table, $primaryKey, $columns)
        );
    }
}
