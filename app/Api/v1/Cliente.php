<?php

namespace App\Api\v1;

use App\Models\ClienteModel;
use CodeIgniter\RESTful\ResourceController;

class Cliente extends ResourceController{

    protected $format = 'json';

    private $cliente;

    public function __construct(){
        $this->cliente = new ClienteModel();
    }

    public function listClient(){
        return $this->respond($this->cliente->findAll(), 200, 'Sucesso');
    }

    public function newClient(){
        $dados = $this->request->getPost();

        if(!empty($dados['cli_nome']) || !isset($dados['cli_nome'])){
            if($this->cliente->save($dados)){
                return $this->respondCreated(['status' => true, 'msg' => 'Cliente cadastrado com sucesso!']);
            }
            else{
                return $this->respond(['status' => false, 'msg' => 'Erro ao cadastrar novo cliente'], 202, 'Ok');
            }
        }
        else{
            return $this->respond(['status' => false, 'msg' => 'Informe o nome do novo cliente'], 202, 'Ok');
        }
    }
}