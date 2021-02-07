<?php

namespace App\Models;

use CodeIgniter\Model;

class EntradaProdutoModel extends Model
{
    protected $table      = 'entrada_produto';
    protected $primaryKey = 'epr_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'epr_data_cadastro',
        'epr_nfs_numero',
        'epr_vlr_nota',
        'epr_vlr_prod_total',
        'frn_id'
    ];
}