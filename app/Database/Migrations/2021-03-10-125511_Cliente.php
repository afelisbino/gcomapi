<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Cliente extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'cli_id' => [
				'type' => 'INT',
				'null' => false,
				'auto_increment' => true
			],
			'cli_nome' => [
				'type' => 'varchar',
				'constraint' => 100,
				'null' => false
			]
		]);

		$this->forge->addPrimaryKey('cli_id');

		$this->forge->createTable('cliente', true, ['ENGINE' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('cliente', true, true);
	}
}
