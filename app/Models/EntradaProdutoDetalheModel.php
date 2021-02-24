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

    public function findDetailsInput($epr_id){
        return $this->join('entrada_produto', 'entrada_produto.epr_id = entrada_produto_detalhe.epr_id')->join('produto', 'produto.pro_id = entrada_produto_detalhe.pro_id')->where(['entrada_produto_detalhe.epr_id' => $epr_id])->get()->getResultArray();
    }
}