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
                $resp['data'][$l][] = getDataBR($obj['cxa_data_fechado']);
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
        $dados = $this->request->getRawInput();
        
        $caixa = $this->caixa->getCash(['cxa_id' => $dados['cxa_id']]);

        if(empty($caixa) || $caixa->cxa_status !== 'aberto'){
            return $this->respond(['status' => false, 'msg' => 'Não possui caixa aberto atualmente'], 200, "Ok");
        }

        $this->venda = new RegistroVendaModel();

        $venda = $this->venda->getTotalSalesValue(['cxa_id' => $dados['cxa_id']]);

        $update['cxa_data_fechamento'] = date('Y-m-d H:i:s');
        $update['cxa_total_fechamento'] = !empty($venda->total_venda) ? $venda->total_venda : 0.00;
        $update['cxa_id'] = $dados['cxa_id'];
        $update['cxa_status'] = 'fechado';

        if($this->caixa->save($update)){
            return $this->respondUpdated(['status' => true, 'msg' => 'Caixa fechado com sucesso!']);
        }
        else{
            return $this->respond(['status' => false, 'msg' => 'Falha ao realizar o fechamento'], 200, "Ok");
        }
    }

    public function totalCashOpen(){
        $ret = $this->caixa->getTotalCash(['cxa_status' => 'aberto']);

        if(!empty($ret)){
            $resp['cxa_data_abertura'] = getDataBR($ret->cxa_data_abertura);
            $resp['cxa_data_fechamento'] = empty($ret->cxa_data_fechamento) ? null : getDataBR($ret->cxa_data_fechamento);
            $resp['total_caixa'] = numeroMoeda($ret->total_caixa);

            return $this->respond($resp, 200, "Sucesso");
        }
        else{
            return $this->respond(['status' => false, 'msg' => "Informação não encontrado"], 200, "Ok");
        }
    }

    public function totalCashLast(){
        $ret = $this->caixa->getTotalCash(['cxa_status' => 'fechado']);

        if(!empty($ret)){
            $resp['cxa_data_abertura'] = getDataBR($ret->cxa_data_abertura);
            $resp['cxa_data_fechamento'] = empty($ret->cxa_data_fechamento) ? null : getDataBR($ret->cxa_data_fechamento);
            $resp['total_caixa'] = numeroMoeda($ret->total_caixa);

            return $this->respond($resp, 200, "Sucesso");
        }
        else{
            return $this->respond(['status' => false, 'msg' => "Informação não encontrado"], 200, "Ok");
        }
    }
}