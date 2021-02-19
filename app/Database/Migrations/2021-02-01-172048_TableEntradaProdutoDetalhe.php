<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TableEntradaProdutoDetalhe extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'epd_id' => [
				'type' => 'int',
				'null' => false,
				'auto_increment' => true
			],
			'epd_qtd_entrada' => [
				'type' => 'int',
				'null' => false,
				'default' => 0
			],
			'epd_vlr_compra' => [
				'type' => 'decimal',
				'constraint' => '22,2',
				'null' => false,
				'default' => 0.00
			],
			'pro_id' => [
				'type' => 'int',
				'null' => false
			],
			'epr_id'=> [
				'type' => 'int',
				'null' => false
			]
		]);

		$this->forge->addPrimaryKey('epd_id');

		$this->forge->addForeignKey('pro_id', 'produto', 'pro_id', 'cascade', 'cascade');
		$this->forge->addForeignKey('epr_id', 'entrada_produto', 'epr_id', 'cascade', 'cascade');

		$this->forge->createTable('entrada_produto_detalhe', true, ['engine' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('entrada_produto_detalhe', true, true);
	}
}
