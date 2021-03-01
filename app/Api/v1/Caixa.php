<?php

namespace App\Api\v1;

use App\Models\CaixaModel;
use App\Models\RegistroVendaModel;
use CodeIgniter\RESTful\ResourceController;

class Caixa extends ResourceController{

    protected $format = 'json';

    private $caixa;
    private $venda;

    public function __construct(){
        $this->caixa = new CaixaModel();

        helper('functions_helpers');
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

        $this->venda = new RegistroVendaModel();

        $venda = $this->venda->getTotalSalesValue(['cxa_id' => $caixa->cxa_id]);

        $update['cxa_data_fechamento'] = date('Y-m-d H:i:s');
        $update['cxa_total_fechamento'] = !empty($venda->total_venda) ? $venda->total_venda : 0.00;
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
            $ret = $this->caixa->getTotalCashOpen($status->cxa_id);

            $resp['cxa_status'] = ucfirst($ret->cxa_status);
            $resp['cxa_data_abertura'] = getDataBR($ret->cxa_data_abertura);
            $resp['cxa_data_fechamento'] = empty($ret->cxa_data_fechamento) ? null : getDataBR($ret->cxa_data_fechamento);
            $resp['total_caixa'] = empty($ret->total_caixa) ? numeroMoeda(0.00) : numeroMoeda($ret->total_caixa);
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
}