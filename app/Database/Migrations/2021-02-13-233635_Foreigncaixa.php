<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Foreigncaixa extends Migration
{
	public function up()
	{
		$this->forge->addColumn('registro_venda', [
			'cxa_id' => [
				'type' => 'int',
				'null' => false
			]
		]);

		$this->forge->modifyColumn('registro_caixa', ['CONSTRAINT fk_registro_venda_caixa1 FOREIGN KEY(cxa_id) REFERENCES table(caixa)']);
	}

	public function down()
	{
		$this->forge->dropColumn('registro_venda', 'cxa_id');
		$this->forge->dropForeignKey('registro_venda', 'cxa_id');
	}
}
