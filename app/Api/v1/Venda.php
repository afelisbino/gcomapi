<?php

namespace App\Api\v1;

use App\Libraries\Logging;
use App\Models\CaixaModel;
use App\Models\ClienteModel;
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
    private $cliente;
    private $logging;

    public function __construct(){
        $this->venda = new RegistroVendaModel();
        $this->item = new SaidaProdutoModel();
        $this->logging = new Logging();

        helper('functions_helpers');
    }

    public function index(){
        $dados = $this->request->getGet();

        if(!empty($dados['rgv_data_inicio']) || !empty($dados['rgv_data_fim'])){
            $ret = $this->venda->getAllSale($dados['rgv_data_inicio'], $dados['rgv_data_fim']);
        }
        else{
            $ret = $this->venda->getAllSale();
        }   

        $resp = array(
            'data' => array(),
            'recordsTotal' => count($ret),
            'recordsFiltered' => count($ret),
        );

        $l = 0;

        if(!empty($ret)){
            foreach($ret as $obj){
                $opc = "<button class='btn btn-primary' onclick='buscarItens({$obj['rgv_id']})' title='Visualizar itens'><span class='fas fa-eye'></span></button>";
                $opc .= "<button class='btn btn-danger' onclick='excluirVenda({$obj['rgv_id']})' title='Excluir venda'><span class='fas fa-trash'></span></button>";

                $resp['data'][$l][] = $opc;
                $resp['data'][$l][] = getDataBR($obj['rgv_data']);
                $resp['data'][$l][] = numeroMoeda($obj['rgv_vlr_total']);
                $resp['data'][$l][] = ucfirst($obj['rgv_forma_pag']);
                $resp['data'][$l][] = $obj['rgv_fiado'] == 1 ? "Sim" : "Não";

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

        if($dados->rgv_fiado == false){
            $insertSale['rgv_status'] = 'finalizado';
            $insertSale['rgv_fiado'] = $dados->rgv_fiado;
        }
        else{
            $insertSale['rgv_status'] = 'aberto';
            $insertSale['rgv_fiado'] = $dados->rgv_fiado;

            $this->cliente = new ClienteModel();

            $cli = $this->cliente->getCliente(['cli_nome' => ucfirst($dados->cli_nome)]);

            if(empty($cli)){
                if($this->cliente->save(['cli_nome' => lcfirst($dados->cli_nome)])){
                    $insertSale['cli_id'] = $this->cliente->getInsertID();
                }
                else{
                    $this->logging->logSession('cliente', "Erro ao cadastrar cliente: " . $this->cliente->errors(), 'error');
                }
                
            }
            else{
                $insertSale['cli_id'] = $cli->cli_id;
            }
        }
        
        if(!$this->venda->save($insertSale)){
            $this->logging->logSession('venda', "Erro ao salvar nova venda: " . $this->venda->errors(), 'error');
            return $this->respond(['status' => false, 'msg' => "Falha ao salvar a compra, entre em contato com o desenvolvedor!"], 200, "Ok");
        }
        
        $rgv_id = $this->venda->getInsertID();

        foreach($dados->itens as $obj){

            $insertIten['rgv_id'] = $rgv_id;
            $insertIten['pro_id'] = $obj->pro_id;
            $insertIten['spr_qtd'] = $obj->spr_qtd;
            $insertIten['spr_sub_total'] = $obj->spr_sub_total;

            $this->estoque->registerStoreOutput($obj->pro_id, $obj->spr_qtd, "Venda");

            if(!$this->item->save($insertIten)) $this->logging->logSession('venda', "Erro ao salvar itens da venda(ID {$rgv_id}) : " . $this->item->errors(), 'error');
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

    public function listSaleSpun(){
        $ret = $this->venda->getAllSpunOpen();

        $resp = array(
            'data' => array(),
            'recordsTotal' => count($ret),
            'recordsFiltered' => count($ret),
        );

        $l = 0;

        if(!empty($ret)){
            foreach($ret as $obj){
                $opc = "<button class='btn btn-success' onclick='pagarCompraFiado({$obj['rgv_id']})' title='Pagar venda'><span class='fas fa-check'></span></button>";
                $opc .= "<button class='btn btn-primary' onclick='visualizarCompra({$obj['rgv_id']})' title='Visualizar a compra'><span class='fas fa-eye'></span></button>";

                $resp['data'][$l][] = $opc;
                $resp['data'][$l][] = ucfirst($obj['cli_nome']);
                $resp['data'][$l][] = getDataBR($obj['rgv_data']);
                $resp['data'][$l][] = numeroMoeda($obj['rgv_vlr_total']);
                $resp['data'][$l][] = ucfirst($obj['rgv_status']);
                $resp['data'][$l][] = numeroMoeda($this->venda->getTotalSaleClient($obj['cli_id'])->total_compras);

                $l++;
            }
        }

        return $this->respond($resp, 200, 'Sucesso');
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
                $this->logging->logSession('venda', "Erro ao finalizar venda fiado (ID {$dados['rgv_id']}) : " . $this->venda->errors(), 'error');
                return $this->respond(['status' => false, 'msg' => "Erro ao finalizar venda"], 202, "Ok");
            }
        }
        else{
            return $this->respond(['status' => false, 'msg' => "Venda já se encontra finalizado"], 202, "Ok");
        }
    }

    public function deleteSale(){
        $rgv_id = $this->request->getRawInput('rgv_id');

        if(empty($rgv_id)){
            return $this->respond(['status' => false, 'msg' => 'Venda não localizada!'], 400, "Erro");
        }

        if($this->venda->delete($rgv_id, false)){
            return $this->respondCreated(['status' => true, 'msg' => 'Venda excluida com sucesso!'], "Sucesso");
        }
        else{
            return $this->respond(['status' => false, 'msg' => 'Falha ao excluir venda!'], 400, "Erro");
        }
    }
}