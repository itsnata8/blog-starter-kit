<?php

namespace App\Controllers;

use App\Models\Post;
use App\Models\SubCategory;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class BlogController extends BaseController
{
    protected $helpers = ['url', 'form', 'CIMail', 'CIFunctions'];
    public function index()
    {
        $data = [
            'pageTitle' => get_settings()->blog_title,
        ];
        return view('frontend/pages/home', $data);
    }
    public function categoryPost($category_slug)
    {
        $subcat = new SubCategory();
        $subcategory = $subcat->asObject()->where('slug', $category_slug)->first();
        $post = new Post();

        $data = [];
        $data['pageTitle'] = 'Category:' . $subcategory->name;
        $data['category'] = $subcategory;
        $data['page'] = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $data['perPage'] = 6;
        $data['total'] = count($post->where('visibility', 1)->where('category_id', $subcategory->id)->findAll());
        $data['posts'] = $post->asObject()->where('visibility', 1)->where('category_id', $subcategory->id)->paginate($data['perPage']);
        $data['pager'] = $post->where('visibility', 1)->where('category_id', $subcategory->id)->pager;
        return view('frontend/pages/category_posts', $data);
    }
    public function tagPosts($tag)
    {
        $post = new Post();
        $data = [];
        $data['pageTitle'] = 'Tag:' . $tag;
        $data['tag'] = $tag;
        $data['page'] = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $data['perPage'] = 6;
        $data['total'] = count($post->where('visibility', 1)->like('tags', '%' . $tag . '%')->findAll());
        $data['posts'] = $post->asObject()
            ->where('visibility', 1)
            ->like('tags', '%' . $tag . '%')
            ->orderBy('created_at', 'desc')
            ->paginate($data['perPage']);
        $data['pager'] = $post->where('visibility', 1)->like('tags', '%' . $tag . '%')->pager;
        return view('frontend/pages/tag_posts', $data);
    }
    public function searchPosts()
    {
        $request = \Config\Services::request();
        $searchData = $request->getGet();
        $search = isset($searchData) && isset($searchData['s']) ? $searchData['s'] : null;
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $perPage = 6;

        // get data object
        $post = new Post();
        // get data count object
        $post2 = new Post();

        if ($search == '') {
            $paginated_data = $post->asObject()->where('visibility', 1)->paginate($perPage);
            $total = $post->where('visibility', 1)->countAllResults();
            $pager = $post->pager;
        } else {
            $keywords = explode(" ", trim($search));
            $post = $this->getSearchdata($post, $keywords);
            $post2 = $this->getSearchData($post2, $keywords);

            $paginated_data = $post->asObject()->where('visibility', 1)->paginate($perPage);
            $total = $post2->where('visibility', 1)->countAllResults();
            $pager = $post->pager;

            $data = [
                'pageTitle' => 'Search for: ' . $search,
                'posts' => $paginated_data,
                'pager' => $pager,
                'page' => $page,
                'perPage' => $perPage,
                'search' => $search,
                'total' => $total
            ];
            return view('frontend/pages/search_posts', $data);
        }
    }
    public function getSearchData($object, $keywords)
    {
        $object->select('*');
        $object->groupStart();
        foreach ($keywords as $keyword) {
            $object->orLike('title', $keyword)
                ->orLike('tags', $keyword);
        }
        return $object->groupEnd();
    }
    public function readPost($slug)
    {
        $post = new Post();
        try {
            $post = $post->asObject()->where('slug', $slug)->first();
            $data = [
                'pageTitle' => $post->title,
                'post' => $post
            ];
            return view('frontend/pages/single_post', $data);
        } catch (\Exception $e) {
            throw $e;
        }
    }
    public function contactUs()
    {
        $data = [
            'pageTitle' => 'Contact Us',
            'validation' => null
        ];
        return view('frontend/pages/contact_us', $data);
    }
    public function contactUsSend()
    {
        $request = \Config\Services::request();

        // validate the form
        $isValid = $this->validate([
            'name' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Please enter your name.'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'Please enter your email.',
                    'valid_email' => 'Please enter a valid email.'
                ]
            ],
            'subject' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Please enter your subject.'
                ]
            ],
            'message' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Please enter your message.',
                ]
            ],
        ]);

        if (!$isValid) {
            $data = [
                'pageTitle' => 'Contact Us',
                'validation' => $this->validator
            ];
            return view('frontend/pages/contact_us', $data);
        } else {
            $mail_body = 'Message from: <b>' . $request->getVar('name') . '</b></br>';
            $mail_body .= '-------------------------------------</br>';
            $mail_body .= $request->getVar('message') . '</br>';
            $mailConfig = array(
                'mail_from_email' => env('EMAIL_FROM_ADDRESS'),
                'mail_from_name' => $request->getVar('name'),
                'mail_recipient_email' => get_settings()->blog_email,
                'mail_recipient_name' => get_settings()->blog_title,
                'mail_subject' => $request->getVar('subject'),
                'mail_body' => $mail_body,
            );
            if (sendEmail($mailConfig)) {
                return redirect()->route('contact-us')->with('success', 'Your message has been sent successfully.');
            } else {
                return redirect()->route('contact-us')->with('fail', 'Something went wrong. Please try again later.');
            }
        }
    }
}
