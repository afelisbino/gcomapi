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
	}

	public function down()
	{
		$this->forge->dropColumn('registro_venda', 'cxa_id');
	}
}
