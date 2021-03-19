<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Novacolunaproduto extends Migration
{
	public function up()
	{
		$this->forge->addColumn('produto', [
			'pro_foto' => [
				'type' => 'text',
				'null' => true,
			]
		]);
	}

	public function down()
	{
		$this->forge->dropColumn('produto', 'pro_foto');
	}
}
