<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCategoriesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'ordering' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 10000
            ],
            'created_at timestamp default current_timestamp',
            'created_at timestamp default current_timestamp on update current_timestamp',
        ]);
        $this->forge->addKey('id');
        $this->forge->createTable('categories');
    }

    public function down()
    {
        $this->forge->dropTable('categories');
    }
}
