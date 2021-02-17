<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Tablehistoricoproduto extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'hpr_id' => [
				'type' => 'int',
				'null' => false,
				'auto_increment' => true,
			],
			'hrp_msg' => [
				'type' => 'varchar',
				'constraint' => 100,
				'null' => false,
			],
			'hpr_data_historico' => [
				'type' => 'datetime',
				'null' => false
			],
			'pro_id' => [
				'type' => 'int',
				'null' => false
			]
		]);

		$this->forge->addPrimaryKey('hpr_id');
		$this->forge->addForeignKey('pro_id', 'produto', 'pro_id', 'cascade', 'cascade');

		$this->forge->createTable('historico_produto', true, ['engine' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('historico_produto', true, true);
	}
}