<?php

namespace App\Api\v1;

use App\Models\ProdutoModel;
use CodeIgniter\RESTful\ResourceController;

class Produto extends ResourceController{

    private $produto;
    protected $format = 'json';

    public function __construct(){
        $this->produto = new ProdutoModel();
    }

    public function index(){
        $ret = $this->produto->findAll();

        if(empty($ret)){
            return $this->respond(['msg' => 'Nenhum produto cadastrado!', 'status' => false], 200, 'Ok');
        }
        else{
            return $this->respond($ret, 200, 'Sucesso');
        }
    }

    public function newProduct(){
        $dados = $this->request->getPost();

        if(!isset($dados['pro_codigo']) || empty($dados['pro_codigo'])){
            return $this->respond(['msg' => 'Codigo de barras não informado', 'status' => false], 200, "Não informado");
        }
        else if(!isset($dados['pro_nome']) || empty($dados['pro_nome'])){
            return $this->respond(['msg' => 'Nome não informado', 'status' => false], 200, "Não informado");
        }
        else if(!isset($dados['pro_valor_venda']) || empty($dados['pro_valor_venda'])){
            return $this->respond(['msg' => 'Valor do produto não informado', 'status' => false], 200, "Não informado");
        }
        else if(!isset($dados['cat_id']) || empty($dados['cat_id'])){
            return $this->respond(['msg' => 'Categoria não informado', 'status' => false], 200, "Não informado");
        }
        else if(!isset($dados['frn_id']) || empty($dados['frn_id'])){
            return $this->respond(['msg' => 'Fornecedor não informado', 'status' => false], 200, "Não informado");
        }
        else{

            $ret = $this->produto->saveProduct($dados);
            
            if($ret['status'] == true){
                return $this->respondCreated($ret, 'Sucesso');            
            }
            else{
                return $this->respond($ret, 200, 'Ok');
            }
        }
    }

    public function updateProduct(){
        $dados = $this->request->getRawInput();

        if(!isset($dados['pro_id']) || empty($dados['pro_id'])){
            return $this->respond(['msg' => 'Produto não encotrado', 'status' => false], 200, "Não encontrado");
        }
        else if(!isset($dados['pro_nome']) || empty($dados['pro_nome'])){
            return $this->respond(['msg' => 'Nome não informado', 'status' => false], 200, "Não informado");
        }
        else if(!isset($dados['pro_valor_venda']) || empty($dados['pro_valor_venda'])){
            return $this->respond(['msg' => 'Valor do produto não informado', 'status' => false], 200, "Não informado");
        }
        else if(!isset($dados['cat_id']) || empty($dados['cat_id'])){
            return $this->respond(['msg' => 'Categoria não informado', 'status' => false], 200, "Não informado");
        }
        else if(!isset($dados['frn_id']) || empty($dados['frn_id'])){
            return $this->respond(['msg' => 'Fornecedor não informado', 'status' => false], 200, "Não informado");
        }
        else{
            $ret = $this->produto->updateProduct($dados);

            if($ret['status'] === true){
                return $this->respondUpdated($ret, 'Sucesso');
            }
            else{
                return $this->respond($ret, 200, "Ok");
            }            
        }
    }

    public function deleteProduct(){

        $dados = $this->request->getRawInput();

        if(!isset($dados['pro_id']) || empty($dados['pro_id'])){
            return $this->respond(['msg' => 'Produto não informado', 'status' => false], 200, "Não encontrado");
        }
        else{
            $ret = $this->produto->deleteProduct($dados['pro_id']);

            if($ret['status'] === true){
                return $this->respondDeleted($ret, 'Sucesso');
            }
            else{
                return $this->respond($ret, 200, "Ok");
            }
        }
    }

    public function searchProductId(){

        $dados = $this->request->getGet();

        if(!isset($dados['pro_id']) || empty($dados['pro_id'])){
            return $this->respond(['msg' => 'Produto não encotrado', 'status' => false], 200, "Não encontrado");
        }
        else{
            $ret = $this->produto->findProduct(['pro_id' => $dados['pro_id']]);

            if(empty($ret)){
                return $this->respond(['msg' => 'Produto não encotrado', 'status' => false], 200, "Não encontrado");
            }
            else{
                return $this->respond($ret, 200, "Ok");
            }
        }
    }

    public function searchProductCodBarra(){
        $dados = $this->request->getGet();

        if(!isset($dados['barcode']) || empty($dados['barcode'])){
            return $this->respond(['msg' => 'Produto não encotrado', 'status' => false], 200, "Não encontrado");
        }
        else{
            $ret = $this->produto->findProduct(['pro_codigo' => $dados['barcode']]);

            if(empty($ret)){
                return $this->respond(['msg' => 'Produto não encotrado', 'status' => false], 200, "Não encontrado");
            }
            else{
                return $this->respond($ret, 200, "Ok");
            }
        }
    }
}