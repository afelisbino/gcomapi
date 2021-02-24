<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoricoSaidaProdutoModel extends Model{

    protected $table = 'historico_saida_produto';
    protected $primaryKey = 'hsp_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'hsp_data',
        'hsp_msg',
        'hsp_origem_registro',
        'hsp_tipo',
        'hsp_qtd_registro',
        'hsp_qtd_antigo',
        'hsp_qtd_atual',
        'est_id'
    ];

    public function registerProductMovement($est_id, $hsp_msg, $hsp_origem, $hsp_tipo, $hsp_qtd_registro, $hsp_qtd_antigo, $hsp_qtd_atual){

        $this->save(['est_id' => $est_id, 'hsp_msg' => $hsp_msg, 'hsp_origem_registro' => $hsp_origem, 'hsp_data' => date('Y-m-d H:i:s'), 'hsp_tipo' => $hsp_tipo, 'hsp_qtd_registro' => $hsp_qtd_registro, 'hsp_qtd_antigo' => $hsp_qtd_antigo, 'hsp_qtd_atual' => $hsp_qtd_atual]);
    }

    public function findHistory($where = array()){
        return $this->join('estoque', 'estoque.est_id = historico_saida_produto.est_id')->join('produto', 'produto.pro_id = estoque.pro_id')->where($where)->get()->getResultArray();
    }
}