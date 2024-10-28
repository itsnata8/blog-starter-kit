<?php

use App\Libraries\CIAuth;
use App\Models\Setting;
use App\Models\SubCategory;
use App\Models\User;
use App\Models\SocialMedia;
use App\Models\Post;
use Carbon\Carbon;

if (!function_exists('get_user')) {
    function get_user()
    {
        if (CIAuth::check()) {
            $user = new User();
            return $user->asObject()->where('id', CIAuth::id())->first();
        } else {
            return null;
        }
    }
}

if (!function_exists('get_settings')) {
    function get_settings()
    {
        $settings = new Setting();
        $settings_data = $settings->asObject()->first();

        if (!$settings_data) {
            $data = array(
                'blog_title' => 'My Blog',
                'blog_email' => 'myemail@example.com',
                'blog_phone' => null,
                'blog_meta_keywords' => null,
                'blog_meta_description' => null,
                'blog_logo' => null,
                'blog_favicon' => null,
            );
            $settings->save($data);
            $new_settings_data = $settings->asObject()->first();
            return $new_settings_data;
        } else {
            return $settings_data;
        }
    }
}
if (!function_exists('get_social_media')) {
    function get_social_media()
    {
        $result = null;
        $social_media = new SocialMedia();
        $social_media_data = $social_media->asObject()->first();

        if (!$social_media_data) {
            $data = array(
                'facebook_url' => null,
                'twitter_url' => null,
                'instagram_url' => null,
                'youtube_url' => null,
                'github_url' => null,
                'linkedin_url' => null,
            );
            $social_media->save($data);
            $new_social_media_data = $social_media->asObject()->first();
            $result = $new_social_media_data;
        } else {
            return $social_media_data;
        }
        return $result;
    }
}
if (!function_exists('current_route_name')) {
    function current_route_name()
    {
        $router = \CodeIgniter\Config\Services::router();
        $route_name = $router->getMatchedRouteOptions()['as'];
        return $route_name;
    }
}

// FRONTEND HELPER
if (!function_exists('get_parent_categories')) {
    function get_parent_categories()
    {
        $category = new \App\Models\Category();
        return $category->asObject()->orderBy('ordering', 'asc')->findAll();
    }
}
if (!function_exists('get_subcategories_by_parent_category_id')) {
    function get_subcategories_by_parent_category_id($id)
    {
        $subcategory = new \App\Models\SubCategory();
        return $subcategory->asObject()
            ->orderBy('ordering', 'asc')
            ->where('parent_cat', $id)
            ->findAll();
    }
}
if (!function_exists('get_dependent_subcategories')) {
    function get_dependent_subcategories()
    {
        $subcategory = new \App\Models\SubCategory();
        return $subcategory->asObject()
            ->orderBy('ordering', 'asc')
            ->where('parent_cat =', 0)
            ->findAll();
    }
}
// Date format eg: JAN 14, 2024
if (!function_exists('date_formatter')) {
    function date_formatter($date)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->isoFormat('ll');
    }
}
// Calculate reading duration
if (!function_exists('get_reading_time')) {
    function get_reading_time($content)
    {
        $word_count = str_word_count(strip_tags($content));
        $word_per_minute = 200;
        $m = ceil($word_count / $word_per_minute);
        return $m <= 1 ? $m . ' Min read' : $m . ' Mins read';
    }
}
if (!function_exists('limit_words')) {
    function limit_words($content = null, $limit = 20)
    {
        return word_limiter($content, $limit);
    }
}
// Get home main latest post
if (!function_exists('get_home_main_latest_post')) {
    function get_home_main_latest_post()
    {
        $post = new Post();
        return $post->asObject()
            ->where('visibility', 1)
            ->orderBy('created_at', 'desc')
            ->first();
    }
}

if (!function_exists('get_6_home_latest_post')) {
    function get_6_home_latest_post()
    {
        $post = new Post();
        return $post->asObject()
            ->where('visibility', 1)
            ->limit(6, 1)
            ->orderBy('created_at', 'desc')
            ->get()
            ->getResult();
    }
}


function word_limiter($content = null, $limit = 20)
{
    if (stripos($content, " ")) {
        $ex_str = explode(" ", $content);
        if (count($ex_str) > $limit) {
            return implode(" ", array_slice($ex_str, 0, $limit)) . '...';
        } else {
            return $content;
        }
    } else {
        return $content;
    }
}
// sidebar categories
if (!function_exists('get_sidebar_categories')) {
    function get_sidebar_categories()
    {
        $subcat = new SubCategory();
        return $subcat->asObject()
            ->orderBy('name', 'asc')
            ->findAll();
    }
}

// count posts by category id
if (!function_exists('posts_by_category_id')) {
    function posts_by_category_id($id)
    {
        $post = new Post();
        $posts = $post->where('visibility', 1)
            ->where('category_id', $id)
            ->findAll();
        return count($posts);
    }
}
