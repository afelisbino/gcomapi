<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Historicocaixa extends Migration
{
	public function up()
	{
		$this->forge->dropTable('registro_saida_caixa', true, true);
		
		$this->forge->addField([
			'hcx_id' => [
				'type' => 'INT',
				'null' => false,
				'auto_increment' => true
			],
			'hcx_data' => [
				'type' => 'datetime',
				'null' => false,
			],
			'hcx_vlr' => [
				'type' => 'decimal',
				'constraint' => '22,2',
				'null' => false,
				'default' => 0.00
			],
			'hcx_tipo' => [
				'type' => 'enum',
				'constraint' => ['entrada', 'saida'],
				'null' => false,
				'default' => 'entrada'
			],
			'hcx_msg' => [
				'type' => 'varchar',
				'constraint' => 100,
				'null' => true,
			],
			'cxa_id' => [
				'type' => 'INT',
				'null' => false
			]
		]);

		$this->forge->addPrimaryKey('hcx_id');
		$this->forge->addForeignKey('cxa_id', 'caixa', 'cxa_id', 'NO ACTION', 'NO ACTION');

		$this->forge->createTable('historico_caixa', 'true', ['ENGINE' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('historico_caixa', true, true);
	}
}
