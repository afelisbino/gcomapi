<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Novascolunasvenda extends Migration
{
	public function up()
	{
		$this->forge->addColumn('registro_venda', [
			'rgv_fiado' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'null' => false,
				'default' => 0
			],
			'cli_id' => [
				'type' => 'INT',
				'null' => true
			]
		]);

		$this->forge->modifyColumn('registro_venda', ['CONSTRAINT fk_registro_venda_cliente1 FOREIGN KEY(cli_id) REFERENCES table(cliente)']);
	}

	public function down()
	{
		$this->forge->dropColumn('registro_venda', ['rgv_fiado', 'cli_id']);
		$this->forge->dropForeignKey('registro_venda', 'cli_id');
	}
}
