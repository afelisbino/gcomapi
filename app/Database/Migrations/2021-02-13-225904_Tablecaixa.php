<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Tablecaixa extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'cxa_id' => [
				'type' => 'INT',
				'null' => false,
				'auto_increment' => true
			],
			'cxa_data_abertura' => [
				'type' => 'datetime',
				'null' => false,
			],
			'cxa_data_fechamento' => [
				'type' => 'datetime',
				'null' => true
			],
			'cxa_status' => [
				'type' => 'enum',
				'constraint' => ['aberto', 'fechado'],
				'null' => false,
				'default' => 'aberto'
			]
		]);

		$this->forge->addPrimaryKey('cxa_id');

		$this->forge->createTable('caixa', true, ['engine' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('caixa', true, true);
	}
}
