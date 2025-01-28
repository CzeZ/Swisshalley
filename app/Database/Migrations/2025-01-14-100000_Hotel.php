<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Hotel extends Migration
{
	public function up()
    {
        $this->forge->addField([
            'row_id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
			],
			'hotel_id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
            ],
            'hotel_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'price' => [
                'type'       => 'INT',
            ],
            'country_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
            ],
            'city_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
            ],
			'star' => [
                'type'           => 'INT',
                'constraint'     => 1,
                'unsigned'       => true,
            ],
            'image' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('row_id', true);
        $this->forge->createTable('hotel');
    }

    public function down()
    {
        $this->forge->dropTable('hotel');
    }
}

?>