<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Clienteuuid extends Migration
{
	public function up()
	{
		$this->forge->addColumn('cliente', [
			'cli_uuid' => [
				'type' => 'varchar',
				'constraint' => 255,
				'null' => true,
				'unique' => true
			]
		]);
	}

	public function down()
	{
		$this->forge->dropColumn('cliente', 'cli_uuid');
	}
}
