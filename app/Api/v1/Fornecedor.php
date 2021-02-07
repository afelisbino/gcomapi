<?php

namespace App\Api\v1;

use App\Models\FornecedorModel;
use CodeIgniter\RESTful\ResourceController;

class Fornecedor extends ResourceController
{

    private $fornecedor;
    protected $format = 'json';

    public function __construct()
    {
        $this->fornecedor = new FornecedorModel();

        helper(['functions_helpers']);
    }

    public function index(){
        $resp = $this->fornecedor->findProvider();

        if(empty($resp)){
            return $this->respond(['msg' => 'Nenhum fornecedor cadastrado!', 'status' => false], 200, 'Ok');
        }
        else{
            $ret = [];
            $i = 0;

            foreach($resp as $obj){

                $ret[$i]['frn_doc'] = mascaraDocumento($obj->frn_doc);
                $ret[$i]['frn_nome'] = $obj->frn_nome;
                $ret[$i]['frn_id'] = $obj->frn_id;

                $i++;
            }

            return $this->respond($ret, 200, 'Sucesso');
        }
        
    }

    public function deleteProvider(){

        $dados = $this->request->getRawInput();

        if(empty($dados['frn_id']) || !isset($dados['frn_id'])){
            return $this->respond(['msg' => 'Fornecedor não encontrado', 'status' => false], 200, "Não encontrado");
        }
        else{
            $ret = $this->fornecedor->deleteProvider($dados['frn_id']);

            if($ret['status'] === true){
                return $this->respondDeleted($ret, "Sucesso");
            }
            else{
                return $this->respond($ret, 200, "Ok");
            }
        }
    }

    public function newProvider(){
        $dados = $this->request->getPost();

        if(!isset($dados['frn_nome']) || empty($dados['frn_nome'])){
            return $this->respond(['msg' => 'Informar o nome do fornecedor', 'status' => false], 200, "Não encontrado");
        }
        else if(!isset($dados['frn_doc']) || empty($dados['frn_doc'])){
            return $this->respond(['msg' => 'Informar o documento do fornecedor', 'status' => false], 200, "Não encontrado");
        }
        else{

            $dados['frn_doc'] = cleanDoc($dados['frn_doc']);

            if(validaCnpj($dados['frn_doc'])){

                $ret = $this->fornecedor->saveProvider($dados);

                if($ret['status'] === true){
                    return $this->respondCreated($ret, 'Sucesso');
                }
                else{
                    return $this->respond($ret, '200', 'OK');
                }
            }
            else{
                return $this->respond(['msg' => 'Documento não é valido', 'status' => false], 200, "Doc invalido");
            }            
        }
    }

    public function updateProvider(){
        $dados = $this->request->getRawInput();

        if(!isset($dados['frn_nome']) || empty($dados['frn_nome'])){
            return $this->respond(['msg' => 'Nome do fornecedor não informado', 'status' => false], 200, "Não informado");
        }
        else if(!isset($dados['frn_doc']) || empty($dados['frn_doc'])){
            return $this->respond(['msg' => 'Documento do fornecedor não informado', 'status' => false], 200, "Não informado");
        }
        else if(!isset($dados['frn_id']) || empty($dados['frn_id'])){
            return $this->respond(['msg' => 'Fornecedor não encontrado', 'status' => false], 200, "Não encontrado");
        }
        else{

            $dados['frn_doc'] = cleanDoc($dados['frn_doc']);

            if(validaCnpj($dados['frn_doc'])){

                $ret = $this->fornecedor->updateProvider($dados);

                if($ret['status'] === true){
                    return $this->respondUpdated($ret, 'Sucesso');
                }
                else{
                    return $this->respond($ret, '200', 'OK');
                }
            }
            else{
                return $this->respond(['msg' => 'Documento não é valido', 'status' => false], 200, "Doc invalido");
            }
        }
    }

    public function searchProvider(){
        $dados = $this->request->getGet();

        if(!empty($dados['frn_doc']) || isset($dados['frn_doc'])){
            $resp = $this->fornecedor->findProvider(['frn_doc' => cleanDoc($dados['frn_doc'])]);

            if(empty($resp)){
                return $this->respond(['msg' => 'Fornecedor não encontrado', 'status' => false], 200, "OK");
            }
            else{
                $ret = [];
                $i = 0;

                foreach($resp as $obj){

                    $ret[$i]['frn_doc'] = mascaraDocumento($obj->frn_doc);
                    $ret[$i]['frn_nome'] = $obj->frn_nome;
                    $ret[$i]['frn_id'] = $obj->frn_id;

                    $i++;
                }

                return $this->respond($ret, 200, 'Sucesso');
            }
        }
        else if(!empty($dados['frn_id']) || isset($dados['frn_id'])){
            $resp = $this->fornecedor->findProvider(['frn_id' => $dados['frn_id']]);

            if(empty($resp)){
                return $this->respond(['msg' => 'Fornecedor não encontrado', 'status' => false], 200, "OK");
            }
            else{
                $ret = [];
                $i = 0;

                foreach($resp as $obj){

                    $ret[$i]['frn_doc'] = mascaraDocumento($obj->frn_doc);
                    $ret[$i]['frn_nome'] = $obj->frn_nome;
                    $ret[$i]['frn_id'] = $obj->frn_id;

                    $i++;
                }

                return $this->respond($ret, 200, 'Sucesso');
            }
        }
        else{
            return $this->respond(['msg' => 'Fornecedor não informado', 'status' => false], 200, 'Ok');
        }
    }
}
