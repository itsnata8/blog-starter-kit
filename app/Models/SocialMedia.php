<?php

namespace App\Models;

use CodeIgniter\Model;

class SocialMedia extends Model
{
    protected $table            = 'social_media';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'youtube_url',
        'github_url',
        'linkedin_url'
    ];
}
