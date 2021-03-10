<?php

namespace App\Api\v1;

use App\Models\CaixaModel;
use App\Models\EstoqueModel;
use App\Models\RegistroVendaModel;
use App\Models\SaidaProdutoModel;
use CodeIgniter\RESTful\ResourceController;

class Venda extends ResourceController{

    protected $format = 'json';

    private $venda;
    private $estoque;
    private $caixa;
    private $item;

    public function __construct(){
        $this->venda = new RegistroVendaModel();
        $this->item = new SaidaProdutoModel();

        helper('functions_helpers');
    }

    public function index(){
        $ret = $this->venda->findAll();

        $resp = array(
            'data' => array(),
            'recordsTotal' => count($ret),
            'recordsFiltered' => count($ret),
        );

        $l = 0;

        if(!empty($ret)){
            foreach($ret as $obj){
                $opc = "<button class='btn btn-primary' onclick='buscarItens({$obj['rgv_id']})' title='Visualizar itens'><span class='fas fa-eye'></span></button>";

                $resp['data'][$l][] = $opc;
                $resp['data'][$l][] = getDataBR($obj['rgv_data']);
                $resp['data'][$l][] = numeroMoeda($obj['rgv_vlr_total']);
                $resp['data'][$l][] = $obj['rgv_forma_pag'];

                $l++;
            }
        }

        return $this->respond($resp, 200, 'Sucesso');
    }

    public function findDetailSale(){

        $dados = $this->request->getGet();

        if(empty($dados['rgv_id'])){
            return $this->respond(['status' => false, 'msg' => 'Selecione a venda que deseja consultar!'], 200, "Ok");
        }

        $ret = $this->item->getItemSale($dados['rgv_id']);

        $resp = array(
            'data' => array(),
            'recordsTotal' => count($ret),
            'recordsFiltered' => count($ret),
        );

        $l = 0;

        if(!empty($ret)){
            foreach($ret as $obj){

                $resp['data'][$l][] = ucfirst($obj['pro_nome']);
                $resp['data'][$l][] = $obj['spr_qtd'];
                $resp['data'][$l][] = numeroMoeda($obj['spr_sub_total']);

                $l++;
            }
        }

        return $this->respond($resp, 200, 'Sucesso');
    }

    public function newSale(){
        $dados = $this->request->getJSON();

        $this->caixa = new CaixaModel();
        $this->estoque = new EstoqueModel();

        $caixa = $this->caixa->getCash(['cxa_status' => 'aberto']);

        if(empty($caixa)){
            return $this->respond(['status' => false, 'msg' => "Caixa não se encontra aberto, favor realize a abertura!"], 200, "Ok");
        }

        $insertSale['cxa_id'] = $caixa->cxa_id;
        $insertSale['rgv_data'] = date('Y-m-d H:i:s');
        $insertSale['rgv_forma_pag'] = $dados->rgv_forma_pag;
        $insertSale['rgv_vlr_total'] = $dados->rgv_vlr_total;

        if(!$dados->rgv_fiado){
            $insertSale['rgv_status'] = 'finalizado';
            $insertSale['rgv_fiado'] = 0;
        }
        else{
            $insertSale['rgv_status'] = 'aberto';
            $insertSale['rgv_fiado'] = 1;
            $insertSale['cli_id'] = $dados->cli_id;
        }
        
        if(!$this->venda->save($insertSale)){
            return $this->respond(['status' => false, 'msg' => "Falha ao salvar a compra, entre em contato com o desenvolvedor!"], 200, "Ok");
        }
        
        $rgv_id = $this->venda->getInsertID();

        foreach($dados->itens as $obj){

            $insertIten['rgv_id'] = $rgv_id;
            $insertIten['pro_id'] = $obj->pro_id;
            $insertIten['spr_qtd'] = $obj->spr_qtd;
            $insertIten['spr_sub_total'] = $obj->spr_sub_total;

            $this->estoque->registerStoreOutput($obj->pro_id, $obj->spr_qtd, "Venda");

            $this->item->save($insertIten);
        }

        return $this->respondCreated(['status' => true, 'msg' => 'Compra registrada com sucesso!']);
    }

    public function totalSaleToday(){
        $ret = $this->venda->getTotalSalesValue(["date_format(rgv_data, '%Y-%m-%d')" => date('Y-m-d')]);

        if(!empty($ret)){
            return $this->respond($ret, 200, "Sucesso");
        }
        else{
            return $this->respond(['status' => false, 'msg' => 'Nenhuma venda registrada hoje'], 200, "Ok");
        }
    }

    public function payPayments(){
        $dados = $this->request->getRawInput();

        $this->caixa = new CaixaModel();

        $caixa = $this->caixa->getCash(['cxa_status' => 'aberto']);

        if(empty($caixa)){
            return $this->respond(['status' => false, 'msg' => "Caixa se encontra fechado!"], 202, "Ok");
        }

        $fiado = $this->venda->find($dados['rgv_id']);

        if($fiado['rgv_status'] === 'aberto'){
            $update['rgv_id'] = $dados['rgv_id'];
            $update['rgv_status'] = 'finalizado';
            $update['cxa_id'] = $caixa->cxa_id;

            if($this->venda->save($update)){
                return $this->respondUpdated(['status' => true, 'msg' => "Venda paga com sucesso"]);
            }
            else{
                return $this->respond(['status' => false, 'msg' => "Erro ao finalizar venda"], 202, "Ok");
            }
        }
        else{
            return $this->respond(['status' => false, 'msg' => "Venda já se encontra finalizado"], 202, "Ok");
        }
    }
}