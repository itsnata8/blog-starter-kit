<?php

namespace App\Models;

use CodeIgniter\Model;

class Setting extends Model
{
    protected $table            = 'settings';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'blog_title',
        'blog_email',
        'blog_phone',
        'blog_meta_keywords',
        'blog_meta_description',
        'blog_logo',
        'blog_favicon',
    ];
}
