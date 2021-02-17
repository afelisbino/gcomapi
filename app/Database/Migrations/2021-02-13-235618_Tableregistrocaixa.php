<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Tableregistrocaixa extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'rsc_id' => [
				'type' => 'int',
				'null' => false,
				'auto_increment' => true
			],
			'rsc_data_saida' => [
				'type' => 'datetime',
				'null' => false
			],
			'rsc_vlr' => [
				'type' => 'decimal',
				'constraint' => '22,2',
				'null' => false,
				'default' => 0,00
			],
			'cxa_id' => [
				'type' => 'int',
				'null' => false
			]
		]);

		$this->forge->addPrimaryKey('rsc_id');
		$this->forge->addForeignKey('cxa_id', 'caixa', 'cxa_id', 'CASCADE', 'CASCADE');

		$this->forge->createTable('registro_saida_caixa', true, ['engine' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('registro_saida_caixa', true, true);
	}
}
