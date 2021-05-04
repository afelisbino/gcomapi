<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TableRegistroVenda extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'rgv_id' => [
				'type' => 'int',
				'null' => false,
				'auto_increment' => true
			],
			'rgv_data' => [
				'type' => 'datetime',
				'null' => false,
			],
			'rgv_vlr_total' => [
				'type' => 'decimal',
				'constraint' => '22,2',
				'null' => true,
				'default' => 0.00
			],
			'rgv_forma_pag' => [
				'type' => 'enum',
				'null' => true,
				'constraint' => ['dinheiro', 'debito', 'credito'],
			],
			'rgv_status' => [
				'type' => 'enum',
				'null' => false,
				'constraint' => ['aberto', 'finalizado'],
				'default' => 'aberto'
			],
			'cxa_id' => [
				'type' => 'int',
				'null' => false,
			]

		]);

		$this->forge->addPrimaryKey('rgv_id');

		$this->forge->createTable('registro_venda', true, ['engine' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('registro_venda', true, true);
	}
}
