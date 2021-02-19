<?php

namespace App\Models;

use CodeIgniter\Model;

class RegistroSaidaCaixaModel extends Model{

    protected $table      = 'registro_saida_caixa';
    protected $primaryKey = 'rsc_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'rsc_data_saida',
        'rsc_vlr',
        'cxa_id'
    ];

    private $caixa;

    public function registerExitCash($dados){

        $this->caixa = new CaixaModel();

        $cashOpen = $this->caixa->getCashOpen();

        if(!empty($cashOpen)){

            $dados['cxa_id'] = $cashOpen->cxa_id;
            $dados['rsc_data_saida'] = date('Y-m-d H:i:s');

            if($this->save($dados)){
                return array('msg' => 'Saida registrado com sucesso', 'status' => true);
            }
            else{
                return array('msg' => 'Erro ao registrar saida', 'status' => false);
            }
        }
        else{
            return array('msg' => 'Caixa se encontra fechado, inicie o caixa para registrar saida', 'status' => false);
        }
    }    
}