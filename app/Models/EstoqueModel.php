<?php

namespace App\Models;

use App\Libraries\Logging;
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

    private $historico_estoque;
    private $logging;

    public function __construct(){
        $this->logging = new Logging();
    }

    public function findAllStorage($where = array()){

        return $this->join('produto', 'produto.pro_id = estoque.pro_id')
            ->join('categoria', 'produto.cat_id = categoria.cat_id')
            ->join('fornecedor', 'produto.frn_id = fornecedor.frn_id')
            ->where($where)->get()->getResultArray();
    }

    public function findStorage($where = array()){
        return $this->join('produto', 'produto.pro_id = estoque.pro_id')->where($where)->get()->getRow();
    }

    public function registerStoreOutput($pro_id, $qtd_saida, $origem, $hsp_msg = null){
        $this->historico_estoque = new HistoricoSaidaProdutoModel();

        $estoque = $this->findStorage(['estoque.pro_id' => $pro_id]);

        if(!empty($estoque) && $estoque->est_qtd_atual >= $qtd_saida){

            $update['est_qtd_atual'] = ($estoque->est_qtd_atual - $qtd_saida);
            $update['est_id'] = $estoque->est_id;

            if($this->save($update)){
                $msg = empty($hsp_msg) ? "Saida de {$qtd_saida} do produto {$estoque->pro_nome}" : $hsp_msg;
                
                $this->historico_estoque->registerProductMovement($estoque->est_id, $msg, $origem, "saida", $qtd_saida, $estoque->est_qtd_atual, $update['est_qtd_atual']);

                return ['status' => true, 'msg' => 'Movimentação registrado com sucesso'];
            }
            else{
                $this->logging->logSession('estoque', 'Erro ao registrar a movimentação de saida: ' . $this->errors());
                return ['status' => false, 'msg' => 'Erro ao registrar a movimentação'];
            }
        }
    }

    public function registerStoreInput($pro_id, $qtd_entrada, $origem){
        $this->historico_estoque = new HistoricoSaidaProdutoModel();

        $estoque = $this->findStorage(['estoque.pro_id' => $pro_id]);

        if(!empty($estoque)){

            $update['est_qtd_atual'] = ($estoque->est_qtd_atual + $qtd_entrada);
            $update['est_id'] = $estoque->est_id;

            if($this->save($update)){

                $msg = "Entrada de {$qtd_entrada}un. do produto {$estoque->pro_nome}";
                
                $this->historico_estoque->registerProductMovement($estoque->est_id, $msg, $origem, "entrada", $qtd_entrada, $estoque->est_qtd_atual, $update['est_qtd_atual']);

                return ['status' => true, 'msg' => 'Movimentação registrado com sucesso'];
            }
            else{
                $this->logging->logSession('estoque', 'Erro ao registrar a movimentação de entrada: ' . $this->errors());
                return ['status' => false, 'msg' => 'Erro ao registrar a movimentação'];
            }
        }
        else{
            return ['status' => false, 'msg' => 'Erro ao registrar a movimentação'];
        }
    }

    public function getMinimumStore(){
        $this->join('produto', 'produto.pro_id = estoque.pro_id', 'left');
        $this->where('est_qtd_atual <= est_qtd_minimo');
        return $this->get()->getResultArray();
    }
}