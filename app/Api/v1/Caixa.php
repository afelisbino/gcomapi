<?php

namespace App\Api\v1;

use App\Models\CaixaModel;
use App\Models\HistoricoCaixaModel;
use App\Models\RegistroVendaModel;
use CodeIgniter\RESTful\ResourceController;

class Caixa extends ResourceController{

    protected $format = 'json';

    private $caixa;
    private $venda;
    private $historico_caixa;

    public function __construct(){
        $this->caixa = new CaixaModel();
        $this->historico_caixa = new HistoricoCaixaModel();

        helper('functions_helpers');
    }

    private function getTotalCash($cxa_id){
        $this->venda = new RegistroVendaModel();

        $total_venda = $this->venda->getTotalSalesValue(['cxa_id' => $cxa_id, 'rgv_status' => 'finalizado']);

        $total_entrada = $this->historico_caixa->getTotalInputCash($cxa_id);
        $total_saida = $this->historico_caixa->getTotalOutputCash($cxa_id);

        return (($total_venda->total_venda + $total_entrada->total_input) - $total_saida->total_output);
    }

    public function index(){
        $ret = $this->caixa->findAll();

        $resp = array(
            'data' => array(),
            'recordsTotal' => count($ret),
            'recordsFiltered' => count($ret),
        );

        $l = 0;

        if(!empty($ret)){
            foreach($ret as $obj){
                
                $resp['data'][$l][] = getDataBR($obj['cxa_data_abertura']);
                $resp['data'][$l][] = getDataBR($obj['cxa_data_fechamento']);
                $resp['data'][$l][] = numeroMoeda($obj['cxa_total_fechamento']);
                $resp['data'][$l][] = $obj['cxa_status'];

                $l++;
            }
        }

        return $this->respond($resp, 200, 'Sucesso');
    }

    public function open(){
        $caixa = $this->caixa->getCash(['cxa_status' => 'aberto']);

        if(!empty($caixa)){
            return $this->respond(['status' => false, 'msg' => 'Caixa se encontra aberto desde dia '.getDataBR($caixa->cxa_data_abertura)], 200, "Ok");
        }
        else{
            $insert['cxa_data_abertura'] = date('Y-m-d H:i:s');
            $insert['cxa_status'] = 'aberto';

            if($this->caixa->save($insert)){
                return $this->respondCreated(['status' => true, 'msg' => 'Caixa aberto com sucesso!']);
            }
            else{
                return $this->respond(['status' => false, 'msg' => 'Falha ao realizar a abertura'], 200, "Ok");
            }
        }
    }

    public function close(){
        $caixa = $this->caixa->getCash(['cxa_status' => 'aberto']);

        if(empty($caixa) || $caixa->cxa_status !== 'aberto'){
            return $this->respond(['status' => false, 'msg' => 'Não possui caixa aberto atualmente'], 200, "Ok");
        }

        $total_fechamento = $this->getTotalCash($caixa->cxa_id);

        $update['cxa_data_fechamento'] = date('Y-m-d H:i:s');
        $update['cxa_total_fechamento'] = !empty($total_fechamento) ? $total_fechamento : 0.00;
        $update['cxa_id'] = $caixa->cxa_id;
        $update['cxa_status'] = 'fechado';

        if($this->caixa->save($update)){
            return $this->respondUpdated(['status' => true, 'msg' => 'Caixa fechado com sucesso!']);
        }
        else{
            return $this->respond(['status' => false, 'msg' => 'Falha ao realizar o fechamento'], 200, "Ok");
        }
    }

    public function statusCaixa(){
        $status = $this->caixa->getCash(['cxa_status' => 'aberto']);

        $resp = [];

        if(!empty($status)){
            $ret = $this->caixa->getInfoCashOpen($status->cxa_id);

            $resp['cxa_status'] = ucfirst($ret->cxa_status);
            $resp['cxa_data_abertura'] = getDataBR($ret->cxa_data_abertura);
            $resp['cxa_data_fechamento'] = empty($ret->cxa_data_fechamento) ? null : getDataBR($ret->cxa_data_fechamento);

            $total_fechamento = $this->getTotalCash($status->cxa_id);

            $resp['total_caixa'] = empty($total_fechamento) ? numeroMoeda(0.00) : numeroMoeda($total_fechamento);
        }
        else{
            $ret = $this->caixa->getTotalCashClose();
            
            $resp['cxa_status'] = ucfirst($ret->cxa_status);
            $resp['cxa_data_abertura'] = getDataBR($ret->cxa_data_abertura);
            $resp['cxa_data_fechamento'] = empty($ret->cxa_data_fechamento) ? null : getDataBR($ret->cxa_data_fechamento);
            $resp['total_caixa'] = numeroMoeda($ret->cxa_total_fechamento);
        }

        return $this->respond($resp, 200, "Sucesso");
    }

    public function inputCash(){

        $dados = $this->request->getPost();

        if(empty($dados['hcx_vlr']) || !isset($dados['hcx_vlr'])){
            return $this->respond(['status' => false, 'msg' => "Informe o valor da entrada!"], 202, 'Ok');
        }

        $ret = $this->historico_caixa->setInputCash(numeroFloat($dados['hcx_vlr']), $dados['hcx_msg']);

        return $this->respondCreated($ret, "Sucesso");
    }

    public function outputCash(){

        $dados = $this->request->getPost();

        if(empty($dados['hcx_vlr']) || !isset($dados['hcx_vlr'])){
            return $this->respond(['status' => false, 'msg' => "Informe o valor da saida!"], 202, 'Ok');
        }

        $ret = $this->historico_caixa->setOutputCash(numeroFloat($dados['hcx_vlr']), $dados['hcx_msg']);

        return $this->respondCreated($ret, "Sucesso");
    }
}