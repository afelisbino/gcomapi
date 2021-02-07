<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Database extends Migration
{
	public function up()
	{
		$this->forge->createDatabase('gcom', true);
	}

	public function down()
	{
		$this->forge->dropDatabase('gcom');
	}
}
