<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model{
    protected $table = "cliente";
    protected $primaryKey = "cli_id";
    protected $allowedFields = ['cli_nome', 'cli_uuid'];
    protected $returnType = 'array';

    public function getCliente($where = array()){
        $this->where($where);
        return $this->get()->getRow();
    }
}