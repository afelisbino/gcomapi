<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TableEntradaProduto extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'epr_id' => [
				'type' => 'int',
				'null' => false,
				'auto_increment' => true
			],
			'epr_data_cadastro' => [
				'type' => 'datetime',
				'null' => false
			],
			'epr_nfs_numero' => [
				'type' => 'varchar',
				'constraint' => 50,
				'null' => true
			],
			'epr_vlr_nota' => [
				'type' => 'decimal',
				'constraint' => '22,2',
				'null' => false,
				'default' => 0.00
			],
			'epr_vlr_prod_total' => [
				'type' => 'decimal',
				'constraint' => '22,2',
				'null' => true,
				'default' => 0.00
			],
			'frn_id' => [
				'type' => 'int',
				'null' => false
			],
		]);

		$this->forge->addPrimaryKey('epr_id');
		$this->forge->addForeignKey('frn_id', 'fornecedor', 'frn_id', 'cascade', 'cascade');

		$this->forge->createTable('entrada_produto', true, ['engine' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('entrada_produto', true, true);
	}
}
