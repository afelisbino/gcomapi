<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TableSaidaProduto extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'spr_id' => [
				'type' => 'int',
				'null' => false,
				'auto_increment' => true
			],
			'spr_qtd' => [
				'type' => 'int',
				'null' => false,
				'default' => 0
			],
			'spr_sub_total' => [
				'type' => 'decimal',
				'constraint' => '22,2',
				'null' => false,
				'default' => 0.00
			],
			'pro_id' => [
				'type' => 'int',
				'null' => false,
			],
			'rgv_id' => [
				'type' => 'int',
				'null' => false,
			]
		]);

		$this->forge->addPrimaryKey('spr_id');

		$this->forge->addForeignKey('pro_id', 'produto', 'pro_id', 'cascade', 'cascade');
		$this->forge->addForeignKey('rgv_id', 'registro_venda', 'rgv_id', 'cascade', 'cascade');

		$this->forge->createTable('saida_produto', true, ['engine' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('saida_produto', true, true);
	}
}
