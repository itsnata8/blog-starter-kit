<?php

namespace App\Controllers;

use SSP;
use App\Models\Post;
use App\Models\User;
use App\Libraries\Hash;
use App\Models\Setting;
use App\Models\Category;
use App\Libraries\CIAuth;
use App\Models\SocialMedia;
use App\Models\SubCategory;
use App\Controllers\BaseController;
use SawaStacks\CodeIgniter\Slugify;

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
                    $subcategory = new SubCategory();
                    $subcategories = $subcategory->where(['parent_cat' => $row['id']])->findAll();
                    return count($subcategories);
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
    public function getCategory()
    {
        $request = \Config\Services::request();

        if ($request->isAJAX()) {
            $id = $request->getVar('category_id');
            $category = new Category();
            $category_data = $category->find($id);
            return $this->response->setJSON(['data' => $category_data]);
        }
    }
    public function updateCategory()
    {
        $request = \Config\Services::request();
        $id = $request->getVar('category_id');
        $validation = \Config\Services::validation();

        $this->validate([
            'category_name' => [
                'rules' => 'required|is_unique[categories.name,id,' . $id . ']',
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
            $update = $category->where('id', $id)
                ->set(['name' => $request->getVar('category_name')])
                ->update();

            if ($update) {
                return $this->response->setJSON(['status' => 1, 'token' => csrf_hash(), 'msg' => 'Category updated successfully!']);
            } else {
                return $this->response->setJSON(['status' => 0, 'token' => csrf_hash(), 'msg' => 'Something went wrong!']);
            }
        }
    }
    public function deleteCategory()
    {
        $request = \Config\Services::request();

        if ($request->isAJAX()) {
            $id = $request->getVar('category_id');
            $category = new Category();

            $delete = $category->delete($id);

            if ($delete) {
                return $this->response->setJSON(['status' => 1, 'msg' => 'Category deleted successfully!']);
            } else {
                return $this->response->setJSON(['status' => 0,  'msg' => 'Something went wrong!']);
            }
        }
    }
    public function reorderCategories()
    {
        $request = \Config\Services::request();

        if ($request->isAJAX()) {
            $positions = $request->getVar('positions');
            $category = new Category();

            foreach ($positions as $position) {
                $index = $position[0];
                $newPosition = $position[1];
                $category->where('id', $index)
                    ->set(['ordering' => $newPosition])
                    ->update();
            }
            return $this->response->setJSON(['status' => 1, 'msg' => 'Categories reordered successfully!']);
        }
    }
    public function getParentCategories()
    {
        $request = \Config\Services::request();

        if ($request->isAJAX()) {
            $id = $request->getVar('parent_category_id');
            $options = '<option value="0">Uncategorized</option>';
            $category = new Category();
            $parent_categories = $category->findAll();

            if (count($parent_categories)) {
                $added_options = '';
                foreach ($parent_categories as $parent_category) {
                    $isSelected = $parent_category['id'] == $id ? 'selected' : '';
                    $added_options .= '<option value="' . $parent_category['id'] . '" ' . $isSelected . '>' . $parent_category['name'] . '</option>';
                }
                $options = $options . $added_options;
                return $this->response->setJSON(['status' => 1, 'data' => $options]);
            } else {
                return $this->response->setJSON(['status' => 1, 'data' => $options]);
            }
        }
    }
    public function addSubcategory()
    {
        $request = \Config\Services::request();

        if ($request->isAJAX()) {
            $validation = \Config\Services::validation();

            $this->validate([
                'subcategory_name' => [
                    'rules' => 'required|is_unique[sub_categories.name]',
                    'errors' => [
                        'required' => 'Subcategory name is required',
                        'is_unique' => 'Subcategory name already exists'
                    ]
                ]
            ]);

            if ($validation->run() === FALSE) {
                $errors = $validation->getErrors();
                return $this->response->setJSON(['status' => 0, 'token' => csrf_hash(), 'errors' => $errors]);
            } else {
                $subcategory = new SubCategory();
                $subcategory_name = $request->getVar('subcategory_name');
                $subcategory_description = $request->getVar('description');
                $subcategory_parent = $request->getVar('parent_cat');
                $subcategory_slug = Slugify::model(SubCategory::class)->make($subcategory_name);

                $save = $subcategory->save([
                    'name' => $subcategory_name,
                    'parent_cat' => $subcategory_parent,
                    'slug' => $subcategory_slug,
                    'description' => $subcategory_description
                ]);
                if ($save) {
                    return $this->response->setJSON(['status' => 1, 'token' => csrf_hash(), 'msg' => 'Subcategory added successfully!']);
                } else {
                    return $this->response->setJSON(['status' => 0, 'token' => csrf_hash(), 'msg' => 'Something went wrong!']);
                }
            }
        }
    }
    public function getSubcategories()
    {
        $category = new Category();
        $subcategory = new SubCategory();

        // DB Details
        $dbDetails = array(
            "host" => $this->db->hostname,
            "user" => $this->db->username,
            "pass" => $this->db->password,
            "db"   => $this->db->database
        );

        $table =  "sub_categories";
        $primaryKey = "id";
        $columns = array(
            array(
                "db" => "id",
                "dt" => 0
            ),
            array(
                "db" => "name",
                "dt" => 1
            ),
            array(
                "db" => "id",
                "dt" => 2,
                "formatter" => function ($d, $row) use ($category, $subcategory) {
                    $parent_cat_id = $subcategory->asObject()->where("id", $row["id"])->first()->parent_cat;
                    $parent_cat_name = ' - ';
                    if ($parent_cat_id != 0) {
                        $parent_cat_name = $category->asObject()->where("id", $parent_cat_id)->first()->name;
                    }
                    return $parent_cat_name;
                }
            ),
            array(
                "db" => "id",
                "dt" => 3,
                "formatter" => function ($d, $row) {
                    return "(x) will be added later";
                }
            ),
            array(
                "db" => "id",
                "dt" => 4,
                "formatter" => function ($d, $row) {
                    return "<div class='btn-group'>
                    <button class='btn btn-sm btn-link p-0 mx-1 editSubCategoryBtn' data-id='" . $row['id'] . "'>Edit</button>
                    <button class='btn btn-sm btn-link p-0 mx-1 deleteSubCategoryBtn' data-id='" . $row['id'] . "'>Delete</button>
                    </div>";
                }
            ),
            array(
                "db" => "ordering",
                "dt" => 5
            )
        );

        return json_encode(SSP::simple($_GET, $dbDetails, $table, $primaryKey, $columns));
    }
    public function getSubcategory()
    {
        $request = \Config\Services::request();

        if ($request->isAJAX()) {
            $id = $request->getVar('subcategory_id');
            $subcategory = new SubCategory();
            $subcategory_data = $subcategory->find($id);
            return $this->response->setJSON(['data' => $subcategory_data]);
        }
    }
    public function updateSubCategory()
    {
        $request = \Config\Services::request();

        if ($request->isAJAX()) {
            $id = $request->getVar('subcategory_id');
            $validation = \Config\Services::validation();

            $this->validate([
                'subcategory_name' => [
                    'rules' => 'required|is_unique[sub_categories.name,id,' . $id . ']',
                    'errors' => [
                        'required' => 'Subcategory name is required',
                        'is_unique' => 'Subcategory name already exists'
                    ]
                ]
            ]);
            if ($validation->run() === FALSE) {
                $errors = $validation->getErrors();
                return $this->response->setJSON(['status' => 0, 'token' => csrf_hash(), 'errors' => $errors]);
            } else {
                $subcategory = new SubCategory();
                $data = array(
                    'name' => $request->getVar('subcategory_name'),
                    'parent_cat' => $request->getVar('parent_cat'),
                    'description' => $request->getVar('description')
                );
                $save = $subcategory->update($id, $data);

                if ($save) {
                    return $this->response->setJSON(['status' => 1, 'token' => csrf_hash(), 'msg' => 'Subcategory updated successfully!']);
                } else {
                    return $this->response->setJSON(['status' => 0, 'token' => csrf_hash(), 'msg' => 'Something went wrong!']);
                }
            }
        }
    }
    public function reorderSubCategories()
    {
        $request = \Config\Services::request();

        if ($request->isAJAX()) {
            $positions = $request->getVar('positions');
            $subcategory = new SubCategory();
            foreach ($positions as $position) {
                $index = $position[0];
                $newPosition = $position[1];
                $subcategory->where('id', $index)->set(['ordering' => $newPosition])->update();
            }
            return $this->response->setJSON(['status' => 1, 'msg' => 'Subcategories reordered successfully!']);
        }
    }
    public function deleteSubCategory()
    {
        $request = \Config\Services::request();

        if ($request->isAJAX()) {
            $id = $request->getVar('subcategory_id');
            $subcategory = new SubCategory();

            $delete = $subcategory->where('id', $id)->delete();

            if ($delete) {
                return $this->response->setJSON(['status' => 1, 'msg' => 'Subcategory deleted successfully!']);
            } else {
                return $this->response->setJSON(['status' => 0, 'msg' => 'Something went wrong!']);
            }
        }
    }
    public function addPost()
    {
        $subcategory = new SubCategory();
        $data = [
            'pageTitle' => 'Add new post',
            'categories' => $subcategory->asObject()->findAll()
        ];
        return view('backend/pages/new-post', $data);
    }
    public function createPost()
    {
        $request = \Config\Services::request();

        if ($request->isAJAX()) {
            $validation = \Config\Services::validation();

            $this->validate([
                'title' => [
                    'rules' => 'required|is_unique[posts.title]',
                    'errors' => [
                        'required' => 'Post title is required',
                        'is_unique' => 'Post title already exists'
                    ]
                ],
                'content' => [
                    'rules' => 'required|min_length[20]',
                    'errors' => [
                        'required' => 'Post content is required',
                        'min_length' => 'Post content must have atleast 20 characters in length'
                    ]
                ],
                'category' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Post category is required'
                    ]
                ],
                'featured_image' => [
                    'rules' => 'uploaded[featured_image]|is_image[featured_image]|max_size[featured_image,2048]',
                    'errors' => [
                        'uploaded' => 'Featured image is required',
                        'is_image' => 'Featured image must be an image',
                        'max_size' => 'Featured image must be less than 2MB'
                    ]
                ]
            ]);

            if ($validation->run() === FALSE) {
                $errors = $validation->getErrors();
                return $this->response->setJSON(['status' => 0, 'token' => csrf_hash(), 'errors' => $errors]);
            } else {
                $user_id = CIAuth::id();
                $path = 'images/posts/';

                $file = $request->getFile('featured_image');
                $filename = $file->getClientName();

                // make post featured images folder is not exists
                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }
                // upload featured image
                if ($file->move($path, $filename)) {
                    \Config\Services::image()
                        ->withFile($path . $filename)
                        ->fit(150, 150, 'center')
                        ->save($path . 'thumb_' . $filename);

                    \Config\Services::image()
                        ->withFile($path . $filename)
                        ->resize(450, 300, true, 'width')
                        ->save($path . 'resized_' . $filename);

                    $post = new Post();
                    $data = array(
                        'author_id' => $user_id,
                        'category_id' => $request->getVar('category'),
                        'title' => $request->getVar('title'),
                        'slug' => Slugify::model(Post::class)->make($request->getVar('title')),
                        'featured_image' => $filename,
                        'tags' => $request->getVar('tags'),
                        'meta_keywords' => $request->getVar('meta_keywords'),
                        'meta_description' => $request->getVar('meta_description'),
                        'visibility' => $request->getVar('visibility')
                    );
                    $save = $post->insert($data);
                    $last_id = $post->getInsertID();

                    if ($save) {
                        return $this->response->setJSON(['status' => 1, 'token' => csrf_hash(), 'msg' => 'Post created successfully!']);
                    } else {
                        return $this->response->setJSON(['status' => 0, 'token' => csrf_hash(), 'msg' => 'Something went wrong!']);
                    }
                } else {
                    return $this->response->setJSON(['status' => 0, 'token' => csrf_hash(), 'msg' => 'Error on uploading featured image.']);
                }
            }
        }
    }
    public function allPosts()
    {
        $data = [
            'pageTitle' => 'All posts'
        ];
        return view('backend/pages/all-posts', $data);
    }
    public function getPosts()
    {
        // DB Details
        $dbDetails = array(
            'host' => $this->db->hostname,
            'user' => $this->db->username,
            'pass' => $this->db->password,
            'db'  => $this->db->database
        );
        $table = "posts";
        $primaryKey = "id";
        $columns = array(
            array('db' => 'id', 'dt' => 0),
            array(
                'db' => 'id',
                'dt' => 1,
                'formatter' => function ($d, $row) {
                    $post = new Post();
                    $image = $post->asObject()->find($row['id'])->featured_image;
                    return '<img src="/images/posts/thumb_' . $image . '" class="img-thumbnail" style="max-width:70px"/>';
                }
            ),
            array(
                'db' => 'title',
                'dt' => 2
            ),
            array(
                'db' => 'id',
                'dt' => 3,
                'formatter' => function ($d, $row) {
                    $post = new Post();
                    $category_id = $post->asObject()->find($row['id'])->category_id;
                    $subcategory = new SubCategory();
                    $category_name = $subcategory->asObject()->find($category_id)->name;
                    return $category_name;
                }
            ),
            array(
                'db' => 'id',
                'dt' => 4,
                'formatter' => function ($d, $row) {
                    $post = new Post();
                    $visibility = $post->asObject()->find($row['id'])->visibility;
                    return $visibility == 1 ? 'Public' : 'Private';
                }
            ),
            array(
                'db' => 'id',
                'dt' => 5,
                'formatter' => function ($d, $row) {
                    return "<div class='btn-group'>
                    <a href='' class='btn btn-sm btn-link p-0 mx-1'>View</a>
                    <a href='" . route_to('admin.edit-post', $row['id']) . "' class='btn btn-sm btn-link p-0 mx-1'>Edit</a>
                    <button class='btn btn-sm btn-link p-0 mx-1 deletePostBtn' data-id='" . $row['id'] . "'>Delete</button>
                    </div>";
                }
            )
        );

        return json_encode(
            SSP::simple($_GET, $dbDetails, $table, $primaryKey, $columns)
        );
    }
    public function editPost($id)
    {
        $subcategory = new SubCategory();
        $post = new Post();
        $data = [
            'pageTitle' => 'Edit post',
            'categories' => $subcategory->asObject()->findAll(),
            'post' => $post->asObject()->find($id)
        ];
        return view('backend/pages/edit-post', $data);
    }
    public function updatePost()
    {
        $request = \Config\Services::request();

        if ($request->isAJAX()) {
            $validation = \Config\Services::validation();
            $post_id = $request->getVar('post_id');
            $user_id = CIAuth::id();
            $post = new Post();

            if (isset($_FILES['featured_image']['name']) && !empty($_FILES['featured_image']['name'])) {
                $this->validate([
                    'title' => [
                        'rules' => 'required|is_unique[posts.title,id,' . $post_id . ']',
                        'errors' => [
                            'required' => 'Post title is required',
                            'is_unique' => 'Post title already exists'
                        ]
                    ],

                    'content' => [
                        'rules' => 'required|min_length[20]',
                        'errors' => [
                            'required' => 'Post content is required',
                            'min_length' => 'Post content must have atleast 20 characters in length'
                        ]
                    ],
                    'featured_image' => [
                        'rules' => 'uploaded[featured_image]|is_image[featured_image]|max_size[featured_image,2048]',
                        'errors' => [
                            'uploaded' => 'Featured image is required',
                            'is_image' => 'Featured image must be an image',
                            'max_size' => 'Featured image must be less than 2mb'

                        ]
                    ]

                ]);
            } else {
                $this->validate([
                    'title' => [
                        'rules' => 'required|is_unique[posts.title,id,' . $post_id . ']',
                        'errors' => [
                            'required' => 'Post title is required',
                            'is_unique' => 'Post title already exists'
                        ]
                    ],
                    'content' => [
                        'rules' => 'required|min_length[20]',
                        'errors' => [
                            'required' => 'Post content is required',
                            'min_length' => 'Post content must have atleast 20 characters in length'
                        ]
                    ]
                ]);
            }
            if ($validation->run() === FALSE) {
                $errors = $validation->getErrors();
                return $this->response->setJSON(['status' => 0, 'token' => csrf_hash(), 'errors' => $errors]);
            } else {
                if (isset($_FILES['featured_image']['name']) && !empty($_FILES['featured_image']['name'])) {
                    $path = 'images/posts/';
                    $file = $request->getFile('featured_image');
                    $filename = $file->getClientName();
                    $old_post_featured_image = $post->asObject()->find($post_id)->featured_image;

                    // upload featured image
                    if ($file->move($path, $filename)) {
                        // create thumb image
                        \Config\Services::image()
                            ->withFile($path . $filename)
                            ->fit(150, 150, 'center')
                            ->save($path . 'thumb_' . $filename);

                        // create resized image
                        \Config\Services::image()
                            ->withFile($path . $filename)
                            ->resize(450, 300, true, 'width')
                            ->save($path . 'resized_' . $filename);

                        // delete old image
                        if ($old_post_featured_image != null && file_exists($path . $old_post_featured_image)) {
                            unlink($path . $old_post_featured_image);
                        }
                        if (file_exists($path . 'thumb_' . $old_post_featured_image)) {
                            unlink($path . 'thumb_' . $old_post_featured_image);
                        }
                        if (file_exists($path . 'resized_' . $old_post_featured_image)) {
                            unlink($path . 'resized_' . $old_post_featured_image);
                        }

                        // update post details in DB
                        $data = array(
                            'author_id' => $user_id,
                            'category_id' => $request->getVar('category'),
                            'title' => $request->getVar('title'),
                            'slug' => Slugify::model(Post::class)->make($request->getVar('title')),
                            'content' => $request->getVar('content'),
                            'featured_image' => $filename,
                            'tags' => $request->getVar('tags'),
                            'meta_keywords' => $request->getVar('meta_keywords'),
                            'meta_description' => $request->getVar('meta_description'),
                            'visibility' => $request->getVar('visibility'),
                        );
                        $update = $post->update($post_id, $data);
                        if ($update) {
                            return $this->response->setJSON(['status' => 1, 'token' => csrf_hash(), 'msg' => 'Post updated successfully']);
                        } else {
                            return $this->response->setJSON(['status' => 0, 'token' => csrf_hash(), 'msg' => 'Failed to update post']);
                        }
                    } else {
                        return $this->response->setJSON(['status' => 0, 'token' => csrf_hash(), 'msg' => 'Failed to upload featured image']);
                    }
                } else {
                    // update post details
                    $data = array(
                        'author_id' => $user_id,
                        'category_id' => $request->getVar('category'),
                        'title' => $request->getVar('title'),
                        'slug' => Slugify::model(Post::class)->make($request->getVar('title')),
                        'content' => $request->getVar('content'),
                        'tags' => $request->getVar('tags'),
                        'meta_keywords' => $request->getVar('meta_keywords'),
                        'meta_description' => $request->getVar('meta_description'),
                        'visibility' => $request->getVar('visibility'),
                    );
                    $update = $post->update($post_id, $data);
                    if ($update) {
                        return $this->response->setJSON(['status' => 1, 'token' => csrf_hash(), 'msg' => 'Post updated successfully']);
                    } else {
                        return $this->response->setJSON(['status' => 0, 'token' => csrf_hash(), 'msg' => 'Failed to update post']);
                    }
                }
            }
        }
    }
}
