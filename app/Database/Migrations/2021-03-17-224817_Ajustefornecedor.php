<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Ajustefornecedor extends Migration
{
	public function up()
	{
		$this->forge->modifyColumn('fornecedor', [
			'frn_doc' => [
				'name' => 'frn_doc',
				'type' => 'varchar',
				'constraint' => 14,
				'null' => true,
				'unique' => true
			]
		]);
	}

	public function down()
	{
		$this->forge->modifyColumn('fornecedor', [
			'frn_doc' => [
				'name' => 'frn_doc',
				'type' => 'varchar',
				'constraint' => 14,
				'null' => false,
				'unique' => true
			]
		]);
	}
}
