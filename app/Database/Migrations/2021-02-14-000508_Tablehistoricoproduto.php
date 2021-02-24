<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Tablehistoricoproduto extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'hsp_id' => [
				'type' => 'int',
				'null' => false,
				'auto_increment' => true,
			],
			'hsp_msg' => [
				'type' => 'varchar',
				'constraint' => 100,
				'null' => false,
			],
			'hsp_data' => [
				'type' => 'datetime',
				'null' => false
			],
			'hsp_origem_registro' => [
				'type' => 'varchar',
				'constraint' => 45,
				'null' => false
			],
			'hsp_tipo' => [
				'type' => 'enum',
				'constraint' => ['entrada', 'saida'],
				'null' => false,
				'default' => 'saida'
			],
			'hsp_qtd_registro' => [
				'type' => 'int',
				'null' => false,
				'default' => 0
			],
			'hsp_qtd_antigo' => [
				'type' => 'int',
				'null' => false,
				'default' => 0
			],
			'hsp_qtd_atual' => [
				'type' => 'int',
				'null' => false,
				'default' => 0
			],
			'est_id' => [
				'type' => 'int',
				'null' => false
			]
		]);

		$this->forge->addPrimaryKey('hsp_id');
		$this->forge->addForeignKey('est_id', 'estoque', 'est_id', 'cascade', 'cascade');

		$this->forge->createTable('historico_saida_produto', true, ['engine' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('historico_saida_produto', true, true);
	}
}