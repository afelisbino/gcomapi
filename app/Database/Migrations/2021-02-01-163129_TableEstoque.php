<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TableEstoque extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'est_id' => [
				'type' => 'int',
				'null' => false,
				'auto_increment' => true
			],
			'est_qtd_atual' => [
				'type' => 'int',
				'null' => false,
				'default' => 0
			],
			'est_qtd_minimo' => [
				'type' => 'int',
				'null' => false,
				'default' => 0
			],
			'pro_id' => [
				'type' => 'int',
				'null' => false
			]
		]);

		$this->forge->addPrimaryKey('est_id');
		$this->forge->addForeignKey('pro_id', 'produto', 'pro_id', 'cascade', 'cascade');

		$this->forge->createTable('estoque', true, ['engine' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('estoque', true, true);
	}
}
