<?php

namespace App\Models;

use CodeIgniter\Model;

class SubCategory extends Model
{
    protected $table            = 'subcategories';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['name', 'slug', 'parent_cat', 'description', 'ordering'];
}
