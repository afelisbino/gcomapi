<?php

namespace App\Api\v1;

use App\Models\EstoqueModel;
use App\Models\HistoricoSaidaProdutoModel;
use CodeIgniter\RESTful\ResourceController;

class Estoque extends ResourceController{

    private $estoque;
    private $historico_saida_produto;

    protected $format = 'json';

    public function __construct(){
        helper('functions_helpers');

        $this->estoque = new EstoqueModel();
    }

    public function index(){
        $ret = $this->estoque->findAllStorage();

        $resp = array(
            'data' => array(),
            'recordsTotal' => count($ret),
            'recordsFiltered' => count($ret),
        );

        $l = 0;

        if(!empty($ret)){
            foreach($ret as $obj){
                $opc = "<button class='btn btn-primary' onclick='buscarEstoque({$obj['pro_id']})' title='Editar estoque'><span class='fas fa-edit'></span></button>";
                $opc .= "<button class='btn btn-danger' onclick='registrarSaida({$obj['pro_id']})' title='Registrar saida'><span class='fas fa-minus-circle'></span></button>";
                
                $resp['data'][$l][] = $opc;
                $resp['data'][$l][] = $obj['pro_codigo'];
                $resp['data'][$l][] = $obj['pro_nome'];
                $resp['data'][$l][] = $obj['est_qtd_atual'];
                $resp['data'][$l][] = $obj['est_qtd_minimo'];
    
                $l++;
            }   
        }

        return $this->respond($resp, 200, "Sucesso");
    }

    public function searchStore(){
        $dados = $this->request->getGet();

        if(!isset($dados['pro_id']) || empty($dados['pro_id'])){
            return $this->respond(['msg' => 'Informe o produto', 'status' => false], 200, 'Ok');
        }
        else{
            $ret = $this->estoque->findStorage(['produto.pro_id' => $dados['pro_id']]);

            if(empty($ret)){
                return $this->respond(['msg' => 'Produto sem estoque criado', 'status' => false], 200, 'Ok'); 
            }
            else{
                return $this->respond($ret, 200, 'Sucesso');
            }
        }
    }

    public function newStore(){
        $dados = $this->request->getPost();

        if(!isset($dados['pro_id']) || empty($dados['pro_id'])){
            return $this->respond(['msg' => 'Informe o produto', 'status' => false], 200, 'Ok');
        }
        else if(!isset($dados['est_qtd_atual']) || empty($dados['est_qtd_atual'])){
            return $this->respond(['msg' => 'Informe a quantidade do produto atualmente', 'status' => false], 200, 'Ok');
        }
        else if(!isset($dados['est_qtd_minimo']) || empty($dados['est_qtd_minimo'])){
            return $this->respond(['msg' => 'Informe a quantidade minima do produto', 'status' => false], 200, 'Ok');
        }
        else{
            $verify = $this->estoque->findStorage(['pro_id' => $dados['pro_id']]);

            if(!empty($verify)){
                return $this->respond(['msg' => 'Produto já possui estoque ativo', 'status' => false]);
            }
            else{

                if($this->estoque->save($dados)){
                    return $this->respondCreated(['status' => true, 'msg' => 'Estoque criado com sucesso'], 'Sucesso');
                }
                else{
                    return $this->respond(['msg' => "Erro ao criar estoque desse produto, tente novamente", 'status' => false], 200, 'Ok');
                }
            }
        }
    }

    public function updateStoreMin(){

        $dados = $this->request->getRawInput();

        if(!isset($dados['est_id']) || empty($dados['est_id'])){
            return $this->respond(['msg' => 'Informe o produto', 'status' => false], 200, 'Ok');
        }
        else if(!isset($dados['est_qtd_minimo']) || empty($dados['est_qtd_minimo'])){
            return $this->respond(['msg' => 'Informe a quantidade minima do produto', 'status' => false], 200, 'Ok');
        }
        else{
            $verify = $this->estoque->findStorage(['est_id' => $dados['est_id']]);

            if(!empty($verify)){

                if($this->estoque->save($dados)){
                    return $this->respondUpdated(['msg' => 'Estoque atualizado', 'status' => true]);
                }
                else{
                    return $this->respond(['msg' => 'Erro ao atualizar o estoque, tente novamente', 'status' => false], 200, 'Ok');
                }
            }
            else{
                return $this->respond(['msg' => 'Erro ao encontrar o estoque, tente novamente', 'status' => false], 200, 'Ok');
            }
        }
    }

    public function outputProduct(){
        $dados = $this->request->getRawInput();

        if(!isset($dados['pro_id']) || empty($dados['pro_id'])){
            return $this->respond(['msg' => 'Informe o produto', 'status' => false], 200, 'Ok');
        }
        else if(!isset($dados['est_qtd_saida']) || empty($dados['est_qtd_saida'])){
            return $this->respond(['msg' => 'Informe a quantidade de saida', 'status' => false], 200, 'Ok');
        }
        else if(!isset($dados['hsp_origem']) || empty($dados['hsp_origem'])){
            return $this->respond(['msg' => "Origem da movimentação não informado", 'status' => false], 200, 'Ok');
        }
        else{
            $verify = $this->estoque->findStorage(['estoque.pro_id' => $dados['pro_id']]);

            if($verify->est_qtd_atual >= $dados['est_qtd_saida']){
                return $this->respondUpdated($this->estoque->registerStoreOutput($dados['pro_id'], $dados['est_qtd_saida'], $dados['hsp_origem'], $dados['hsp_msg']));
            }
            else{
                return $this->respond(['status' => false, 'msg' => 'Não foi possivel registrar saida do estoque'], 200, 'Ok');
            }
        }
    }

    public function inputProduct(){
        $dados = $this->request->getRawInput();

        if(!isset($dados['pro_id']) || empty($dados['pro_id'])){
            return $this->respond(['msg' => 'Informe o produto', 'status' => false], 200, 'Ok');
        }
        else if(!isset($dados['est_qtd_entrada']) || empty($dados['est_qtd_entrada'])){
            return $this->respond(['msg' => 'Informe a quantidade de entrada', 'status' => false], 200, 'Ok');
        }
        else if(!isset($dados['hsp_origem']) || empty($dados['hsp_origem'])){
            return $this->respond(['msg' => "Origem da movimentação não informado", 'status' => false], 200, 'Ok');
        }
        else{
            return $this->respondUpdated($this->estoque->registerStoreInput($dados['pro_id'], $dados['est_qtd_entrada'], $dados['hsp_origem']));
        }
    }

    public function listStoreHistory(){

        $this->historico_saida_produto = new HistoricoSaidaProdutoModel();

        $dados = $this->request->getGet();

        if(isset($dados['hsp_tipo']) && $dados['hsp_tipo'] !== 'todos'){
            $ret = $this->historico_saida_produto->findHistory(['hsp_tipo' => $dados['hsp_tipo']]);
        }
        else{
            $ret = $this->historico_saida_produto->findHistory();
        }

        $resp = array(
            'data' => array(),
            'recordsTotal' => count($ret),
            'recordsFiltered' => count($ret),
        );

        $l = 0;

        if(!empty($ret)){
            foreach($ret as $obj){
                
                $resp['data'][$l][] = getDataBR($obj['hsp_data']);
                $resp['data'][$l][] = $obj['pro_nome'];
                $resp['data'][$l][] = $obj['hsp_msg'];
                $resp['data'][$l][] = $obj['hsp_qtd_antigo'];
                $resp['data'][$l][] = $obj['hsp_qtd_registro'];
                $resp['data'][$l][] = $obj['hsp_qtd_atual'];
                $resp['data'][$l][] = ucfirst($obj['hsp_origem_registro']);
                $resp['data'][$l][] = ucfirst($obj['hsp_tipo']);
    
                $l++;
            }   
        }

        return $this->respond($resp, 200, "Sucesso");
    }
}