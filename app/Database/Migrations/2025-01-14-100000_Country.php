<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Country extends Migration
{
	public function up()
    {
        $this->forge->addField([
			'country_id' => [
                'type'           => 'VARCHAR',
                'constraint' => '10'
            ],
            'country_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
        ]);
        $this->forge->addKey('country_id', true);
        $this->forge->createTable('country');
    }

    public function down()
    {
        $this->forge->dropTable('country');
    }
}

?>