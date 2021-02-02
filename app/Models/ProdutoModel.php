<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdutoModel extends Model
{
    protected $table      = 'produto';
    protected $primaryKey = 'pro_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'pro_codigo', 
        'pro_nome', 
        'pro_valor_venda', 
        'cat_id', 
        'frn_id'
    ];
}