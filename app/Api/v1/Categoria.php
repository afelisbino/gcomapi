<?php

namespace App\Api\v1;

use App\Models\CategoriaModel;
use CodeIgniter\RESTful\ResourceController;

class Categoria extends ResourceController
{

    private $categoria;
    protected $format = 'json';

    public function __construct()
    {
       $this->categoria = new CategoriaModel();
    }

    public function index()
    {
        $ret = $this->categoria->findAll();

        $resp = array(
            'data' => array(),
            'recordsTotal' => count($ret),
            'recordsFiltered' => count($ret),
        );

        $l = 0;

        if(!empty($ret)){
            foreach($ret as $obj){
                $opc = "<button class='btn btn-primary' onclick='buscarCategoria({$obj['cat_id']})' title='Editar categoria'><span class='fas fa-edit'></span></button>";
                $opc .= "<button class='btn btn-danger' onclick='deletarCategoria({$obj['cat_id']})' title='Excluir categoria'><span class='fas fa-eraser'></span></button>";
    
                $resp['data'][$l][] = $opc;
                $resp['data'][$l][] = $obj['cat_nome'];
    
                $l++;
            }    
        }

        return $this->respond($resp, 200, 'Sucesso');
    
    }

    public function deleteCategory()
    {
        $dados = $this->request->getRawInput();

        if(empty($dados['cat_id']) || !isset($dados['cat_id'])){
            return $this->respond(['msg' => 'Categoria não encontrado', 'status' => false], 200, "Não encontrado");
        }
        else{
            $ret = $this->categoria->deleteCategory($dados['cat_id']);

            if($ret['status'] === true){
                return $this->respondDeleted($ret, "Sucesso");
            }
            else{
                return $this->respond($ret, 200, "Ok");
            }
        }
    }

    public function newCategory()
    {
        $dados = $this->request->getPost();

        if(!isset($dados['cat_nome']) || empty($dados['cat_nome'])){
            return $this->respond(['msg' => 'Informar o nome da categoria', 'status' => false], 200, "Não encontrado");
        }
        else{
            $ret = $this->categoria->saveCategory($dados);

            if($ret['status'] === true){
                return $this->respondCreated($ret, 'Sucesso');
            }
            else{
                return $this->respond($ret, '200', 'OK');
            }            
        }
    }

    public function updateCategory()
    {
        $dados = $this->request->getRawInput();

        if(empty($dados['cat_id']) || !isset($dados['cat_id'])){
            return $this->respond(['msg' => 'Categoria não informado', 'status' => false], 200, "Não informado");
        }
        else if(empty($dados['cat_nome']) || !isset($dados['cat_nome'])){
            return $this->respond(['msg' => 'Nome não informado', 'status' => false], 200, "Não informado");
        }
        else{
            $ret = $this->categoria->updateCategory($dados);
            
            if($ret['status'] === true){
                return $this->respondUpdated($ret, 'Sucesso');
            }
            else{
                return $this->respond($ret, '200', 'OK');
            }  
        }
    }

    public function searchCategory(){
        $dados = $this->request->getGet();
        
        if(empty($dados['cat_id']) || !isset($dados['cat_id'])){
            return $this->respond(['msg' => 'Categoria não encontrado', 'status' => false], 200, "OK");
        }
        else{
            $ret = $this->categoria->find($dados['cat_id']);

            if(empty($ret)){
                return $this->respond(['msg' => 'Categoria não encontrado', 'status' => false], 200, "OK");
            }
            else{
                return $this->respond($ret, 200, "Sucesso");
            }
        }
    }

    public function listCategory(){
        $ret = $this->categoria->findAll();

        return $this->respond($ret, 200, "Ok");
    }
}