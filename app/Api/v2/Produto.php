<?php

namespace App\Api\v2;

use App\Models\CategoriaModel;
use App\Models\FornecedorModel;
use App\Models\ProdutoModel;
use CodeIgniter\RESTful\ResourceController;

class Produto extends ResourceController{

    protected $format = 'json';

    private $produto;

    public function __construct(){
        $this->produto = new ProdutoModel();

        helper(['upload_helper']);
    }

    public function newProduct(){
        $dados = $this->request->getPost();
        $foto = $this->request->getFiles();

        if(!isset($dados['pro_nome']) || empty($dados['pro_nome'])){
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

            if(isset($foto['pro_foto']) && !empty($foto['pro_foto'])){
                $imagem = salvarArquivo($foto['pro_foto'], "produto/");

                if($imagem['status'] === true){
                    $dados['pro_foto'] = $imagem['dir'] . $imagem['nome'];
                }
                else{
                    return $this->respond($imagem, 200, "Ok");
                }
            }

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
        $foto = $this->request->getFiles();

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
            if(isset($foto['pro_foto']) && !empty($foto['pro_foto'])){
                $imagem = salvarArquivo($foto['pro_foto'], "produto/");

                if($imagem['status'] === true){
                    $dados['pro_foto'] = $imagem['dir'] . $imagem['nome'];
                }
                else{
                    return $this->respond($imagem, 200, "Ok");
                }
            }

            $ret = $this->produto->updateProduct($dados);

            if($ret['status'] === true){
                return $this->respondUpdated($ret, 'Sucesso');
            }
            else{
                return $this->respond($ret, 200, "Ok");
            }            
        }
    }
}