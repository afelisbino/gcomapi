<?php

namespace App\Models;

use CodeIgniter\Model;

class EntradaProdutoDetalheModel extends Model
{
    protected $table      = 'entrada_produto_detalhe';
    protected $primaryKey = 'enp_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'enp_qtd_entrada',
        'enp_vlr_compra',
        'pro_id',
        'epr_id'
    ];
}