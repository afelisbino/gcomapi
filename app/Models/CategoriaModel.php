<?php

namespace App\Models;

use App\Libraries\Logging;
use CodeIgniter\Model;

class CategoriaModel extends Model{

    protected $table      = 'categoria';
    protected $primaryKey = 'cat_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'cat_nome'
    ];

    private $logging;

    public function saveCategory($dados){

        $this->logging = new Logging();
        $verify = $this->select()->where($dados)->get()->getRow();

        if(!empty($verify)){
            return array('status' => false, 'msg' => 'Categoria já existe');
        }
        else{

            if($this->save($dados)){
                return array('status' => true, 'msg' => 'Categoria cadastrada com sucesso');
            }
            else{
                $this->logging->logSession('categoria', 'Erro ao cadastrar categoria: ' . $this->errors(), 'error');
                return array('status' => false, 'msg' => 'Erro ao cadastrar, tente novamente');
            }
        }
    }

    public function updateCategory($dados){

        $this->logging = new Logging();
        $verify = $this->find($dados['cat_id']);

        if(empty($verify)){

            return array('status' => false, 'msg' => 'Categoria não existe');
        }
        else{
            if($this->save($dados)){
                return array('msg' => 'Categoria alterado com sucesso', 'status' => true);
            }
            else{
                $this->logging->logSession('categoria', 'Erro ao atualizar categoria: ' . $this->errors(), 'error');
                return array('status' => false, 'msg' => 'Erro ao alterar, tente novamente');
            }
        }
    }

    public function deleteCategory($id){

        $this->logging = new Logging();
        $verify = $this->find($id);

        if(empty($verify)){
            return array('status' => false, 'msg' => 'Categoria não existe');
        }
        else{
            if($this->delete($id, false)){
                return array('msg' => 'Categoria deletado com sucesso', 'status' => true);
            }
            else{
                $this->logging->logSession('categoria', 'Erro ao excluir categoria: ' . $this->errors(), 'error');
                return array('status' => false, 'msg' => 'Erro ao deletar, tente novamente');
            }
        }
    }
}