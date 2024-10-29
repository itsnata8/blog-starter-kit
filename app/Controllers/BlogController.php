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
}
