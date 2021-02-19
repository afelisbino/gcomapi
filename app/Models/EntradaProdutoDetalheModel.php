<?php

namespace App\Models;

use CodeIgniter\Model;

class EntradaProdutoDetalheModel extends Model
{
    protected $table      = 'entrada_produto_detalhe';
    protected $primaryKey = 'epd_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'epd_qtd_entrada',
        'epd_vlr_compra',
        'pro_id',
        'epr_id'
    ];

    
}