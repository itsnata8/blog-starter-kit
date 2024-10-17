<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSocialMediaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true
            ],
            'facebook_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'twitter_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'instagram_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'youtube_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'github_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'linkedin_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'created_at timestamp default current_timestamp',
            'update_at timestamp default current_timestamp on update current_timestamp',
        ]);
        $this->forge->addKey('id');
        $this->forge->createTable('social_media');
    }

    public function down()
    {
        $this->forge->dropTable('social_media');
    }
}
