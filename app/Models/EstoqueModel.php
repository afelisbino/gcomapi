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

    public function findAllStorage($where = array()){

        return $this->join('produto', 'produto.pro_id = estoque.pro_id')
            ->join('categoria', 'produto.cat_id = categoria.cat_id')
            ->join('fornecedor', 'produto.frn_id = fornecedor.frn_id')
            ->where($where)->get()->getResultArray();
    }

    public function findStorage($where = array()){
        return $this->where($where)->get()->getRow();
    }
}