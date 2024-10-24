<?php

namespace App\Models;

use CodeIgniter\Model;

class Post extends Model
{
    protected $table            = 'posts';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['author_id', 'category_id', 'title', 'slug', 'content', 'featured_image', 'tags', 'meta_keywords', 'meta_description', 'visibility'];
}
