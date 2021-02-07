<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TableCategoria extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'cat_id' => [
				'type' => 'INT',
				'null' => false,
				'auto_increment' => true,
			],
			'cat_nome' => [
				'type' => 'VARCHAR',
				'constraint' => 50,
				'null' => false,
			],
		]);

		$this->forge->addPrimaryKey('cat_id');
		$this->forge->createTable('categoria', true, ['engine' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('categoria', true, true);
	}
}
