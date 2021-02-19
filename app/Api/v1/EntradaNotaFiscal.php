<?php

namespace App\Api\v1;

use App\Models\EntradaProdutoModel;
use CodeIgniter\RESTful\ResourceController;

class EntradaNotaFiscal extends ResourceController{

    protected $format = 'json';

    private $entrada_produto;

    public function __construct(){
        $this->entrada_produto = new EntradaProdutoModel();
    }

    public function index(){
        $ret = $this->entrada_produto->findAll();

        if(empty($ret)){
            return $this->respond(['msg' => 'Nenhuma categoria cadastrado!', 'status' => false], 200, 'Ok');
        }
        else{
            return $this->respond($ret, 200, 'Sucesso');
        }
    }
}