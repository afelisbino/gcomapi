<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model{
    protected $table = "cliente";
    protected $primaryKey = "cli_id";
    protected $allowedField = ['cli_nome'];
}