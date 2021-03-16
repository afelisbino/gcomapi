<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoricoCaixaModel extends Model{
    
    protected $table = 'historico_caixa';
    protected $primaryKey =  'hcx_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'hcx_data',
        'hcx_vlr',
        'hcx_tipo',
        'hcx_msg',
        'cxa_id'
    ];

    private $caixa;

    public function __construct(){
        $this->caixa = new CaixaModel();
    }

    public function setInputCash($hcx_vlr, $hcx_msg = null){

        $cash = $this->caixa->getCash(['cxa_status' => 'aberto']);

        if(empty($cash)){
           return ['status' => false, 'msg' => 'Caixa se encontra fechado!']; 
        }

        $insert['hcx_data'] = date('Y-m-d H:i:s');
        $insert['hcx_vlr'] = $hcx_vlr;
        $insert['hcx_tipo'] = 'entrada';
        $insert['hcx_msg'] = empty($hcx_msg) ? null : $hcx_msg;
        $insert['cxa_id'] = $cash->cxa_id;

        if($this->save($insert)){
            return ['status' => true, 'msg' => 'Entrada registrado com sucesso!'];
        }
        else{
            return ['status' => false, 'msg' => 'Falha ao registrar entrada de caixa!'];
        }
    }

    public function setOutputCash($hcx_vlr, $hcx_msg = null){

        $cash = $this->caixa->getCash(['cxa_status' => 'aberto']);

        if(empty($cash)){
           return ['status' => false, 'msg' => 'Caixa se encontra fechado!']; 
        }

        $insert['hcx_data'] = date('Y-m-d H:i:s');
        $insert['hcx_vlr'] = $hcx_vlr;
        $insert['hcx_tipo'] = 'saida';
        $insert['hcx_msg'] = empty($hcx_msg) ? null : $hcx_msg;
        $insert['cxa_id'] = $cash->cxa_id;

        if($this->save($insert)){
            return ['status' => true, 'msg' => 'Saída registrado com sucesso!'];
        }
        else{
            return ['status' => false, 'msg' => 'Falha ao registrar saída de caixa!'];
        }
    }

    public function getTotalInputCash($cxa_id){
        $this->select('sum(hcx_vlr) as total_input');
        $this->where(['cxa_id' => $cxa_id, 'hcx_tipo' => 'entrada']);

        return $this->get()->getRow();
    }

    public function getTotalOutputCash($cxa_id){
        $this->select('sum(hcx_vlr) as total_output');
        $this->where(['cxa_id' => $cxa_id, 'hcx_tipo' => 'saida']);

        return $this->get()->getRow();
    }

    public function findHistoryCash($where = array()){
        $this->where($where);
        return $this->get()->getResultArray();
    }
}