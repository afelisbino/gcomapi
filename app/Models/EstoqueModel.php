<?php

namespace App\Models;

use CodeIgniter\Model;

class EstoqueModel extends Model
{
    protected $table      = 'estoque';
    protected $primaryKey = 'est_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'est_qtd_atual',
        'est_qtd_minimo',
        'pro_id'
    ];
}