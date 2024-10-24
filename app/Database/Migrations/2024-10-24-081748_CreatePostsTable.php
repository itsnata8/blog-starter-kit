<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePostsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true
            ],
            'author_id' => [
                'type' => 'INT',
                'constraint' => 11
            ],
            'category_id' => [
                'type' => 'INT',
                'constraint' => 11
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'content' => [
                'type' => 'TEXT',
            ],
            'featured_image' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'tags' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'meta_keywords' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'meta_description' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'visibility' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1
            ],
            'created_at timestamp default current_timestamp',
            'updated_at timestamp default current_timestamp on update current_timestamp',
        ]);
        $this->forge->addKey('id');
        $this->forge->createTable('posts');
    }

    public function down()
    {
        $this->forge->dropTable('posts');
    }
}
