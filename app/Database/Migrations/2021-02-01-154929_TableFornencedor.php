<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TableFornencedor extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'frn_id' => [
				'type' => 'INT',
				'null' => false,
				'auto_increment' => true,
			],
			'frn_nome' => [
				'type' => 'varchar',
				'constraint' => 50,
				'null' => false,
			],
			'frn_doc' => [
				'type' => 'varchar',
				'constraint' => 14,
				'null' => false
			],
		]);

		$this->forge->addPrimaryKey('frn_id');
		$this->forge->addUniqueKey('frn_doc');

		$this->forge->createTable('fornecedor', true, ['engine' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('fornecedor', true, true);
	}
}
