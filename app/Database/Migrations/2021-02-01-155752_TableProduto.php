<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TableProduto extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'pro_id' => [
				'type' => 'INT',
				'null' => false,
				'auto_increment' => true
			],
			'pro_codigo' => [
				'type' => 'varchar',
				'constraint' => 100,
				'null' => false
			],
			'pro_nome' => [
				'type' => 'varchar',
				'constraint' => '45',
				'null' => false
			],
			'pro_valor_venda' => [
				'type' => 'decimal',
				'constraint' => '22,2',
				'null' => false,
				'default' => 0.00
			],
			'cat_id' => [
				'type' => 'int',
				'null' => false
			],
			'frn_id' => [
				'type' => 'int',
				'null' => false
			]
		]);

		$this->forge->addPrimaryKey('pro_id');
		$this->forge->addUniqueKey('pro_codigo');

		$this->forge->addForeignKey('cat_id', 'categoria', 'cat_id', 'cascade', 'cascade');
		$this->forge->addForeignKey('frn_id', 'fornecedor', 'frn_id', 'cascade', 'cascade');

		$this->forge->createTable('produto', true, ['engine' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('produto', true, true);
	}
}
