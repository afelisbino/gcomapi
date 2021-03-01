<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Newcolumncaixa extends Migration
{
	public function up()
	{
		$this->forge->addColumn('caixa',[
			'cxa_total_fechamento' => [
				'type' => 'decimal',
				'constraint' => '22,2',
				'null' => true,
				'default' => 0.00
			]
		]);
	}

	public function down()
	{
		$this->forge->dropColumn('caixa', 'cxa_total_fechamento');
	}
}
