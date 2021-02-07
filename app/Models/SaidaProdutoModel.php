<?php

namespace App\Models;

use CodeIgniter\Model;

class SaidaProdutoModel extends Model
{
    protected $table      = 'saida_produto';
    protected $primaryKey = 'spr_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'spr_qtd',
        'spr_sub_total',
        'pro_id',
        'rgv_id'
    ];
}