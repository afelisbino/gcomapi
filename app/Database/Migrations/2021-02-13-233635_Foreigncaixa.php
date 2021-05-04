<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Foreigncaixa extends Migration
{
	public function up()
	{
		$this->forge->addForeignKey('cxa_id', 'registro_venda', 'cxa_id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
		$this->forge->dropColumn('registro_venda', 'cxa_id');
		$this->forge->dropForeignKey('registro_venda', 'cxa_id');
	}
}
