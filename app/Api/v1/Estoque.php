<?php

namespace App\Api\v1;

use App\Models\EstoqueModel;
use CodeIgniter\RESTful\ResourceController;

class Estoque extends ResourceController{

    private $estoque;
    protected $format = 'json';

    public function __construct(){
        $this->estoque = new EstoqueModel();
    }

    public function index(){
        return $this->respond($this->estoque->findAllStorage(), 200, 'Ok');
    }

    public function searchStore(){
        $dados = $this->request->getGet();

        if(!isset($dados['pro_id']) || empty($dados['pro_id'])){
            return $this->respond(['msg' => 'Informe o produto', 'status' => false], 200, 'Ok');
        }
        else{
            $ret = $this->estoque->findStorage(['pro_id' => $dados['pro_id']]);

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

    public function updateStore(){

        $dados = $this->request->getRawInput();

        if(!isset($dados['est_id']) || empty($dados['est_id'])){
            return $this->respond(['msg' => 'Informe o produto', 'status' => false], 200, 'Ok');
        }
        else if(!isset($dados['est_qtd_atual']) || empty($dados['est_qtd_atual'])){
            return $this->respond(['msg' => 'Informe a quantidade do produto atualmente', 'status' => false], 200, 'Ok');
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
}