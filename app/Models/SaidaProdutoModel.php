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

    public function getItemSale($rgv_id){
        return $this->join('produto', 'produto.pro_id = saida_produto.pro_id', 'left')->where(['rgv_id' => $rgv_id])->get()->getResultArray();
    }
}