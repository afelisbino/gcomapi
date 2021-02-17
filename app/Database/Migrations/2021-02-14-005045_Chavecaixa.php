<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Chavecaixa extends Migration
{
	public function up()
	{
		$this->forge->modifyColumn('registro_caixa', ['CONSTRAINT fk_registro_venda_caixa1 FOREIGN KEY(cxa_id) REFERENCES table(caixa)']);
	}

	public function down()
	{
		$this->forge->dropForeignKey('registro_venda', 'cxa_id');
	}
}
