<?php

namespace App\Api\v1;

use App\Models\CategoriaModel;
use App\Models\FornecedorModel;
use App\Models\ProdutoModel;
use CodeIgniter\RESTful\ResourceController;

class Produto extends ResourceController{

    private $produto;
    private $categoria;
    private $fornecedor;

    protected $format = 'json';

    public function __construct(){
        helper('functions_helpers');

        $this->produto = new ProdutoModel();
        $this->fornecedor = new FornecedorModel();
        $this->categoria = new CategoriaModel();
    }

    public function index(){
        $ret = $this->produto->findAll();

        $resp = array(
            'data' => array(),
            'recordsTotal' => count($ret),
            'recordsFiltered' => count($ret),
        );

        $l = 0;

        if(!empty($ret)){

            foreach($ret as $obj){
                $opc = "<button class='btn btn-primary' onclick='buscarProduto({$obj['pro_id']})' title='Editar produto'><span class='fas fa-edit'></span></button>";
                $opc .= "<button class='btn btn-danger' onclick='deletarProduto({$obj['pro_id']})' title='Excluir produto'><span class='fas fa-eraser'></span></button>";

                $categoria = $this->categoria->find($obj['cat_id']);
                $fornecedor = $this->fornecedor->find($obj['frn_id']);

                $resp['data'][$l][] = $opc;
                $resp['data'][$l][] = $obj['pro_codigo'];
                $resp['data'][$l][] = ucfirst($obj['pro_nome']);
                $resp['data'][$l][] = numeroMoeda($obj['pro_valor_venda'], true);
                $resp['data'][$l][] = ucfirst($categoria['cat_nome']);
                $resp['data'][$l][] = ucfirst($fornecedor['frn_nome']);

                $l++;
            }
        }

        return $this->respond($resp, 200, 'Sucesso');
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

            $dados['pro_valor_venda'] = numeroFloat($dados['pro_valor_venda']);

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
        else if(!isset($dados['pro_codigo']) || empty($dados['pro_codigo'])){
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
            $ret = $this->produto->findProduct(['produto.pro_id' => $dados['pro_id']]);

            if(empty($ret)){
                return $this->respond(['msg' => 'Produto não encontrado', 'status' => false], 200, "Não encontrado");
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
            $ret = $this->produto->findProduct(['produto.pro_codigo' => $dados['barcode']]);

            if(empty($ret)){
                return $this->respond(['msg' => 'Produto não encontrado', 'status' => false], 200, "Não encontrado");
            }
            else{
                return $this->respond($ret, 200, "Ok");
            }
        }
    }

    public function listAllProduct(){
        $ret = $this->produto->findAll();

        return $this->respond($ret, 200, "Ok");
    }
}