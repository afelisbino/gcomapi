<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoriaModel extends Model{

    protected $table      = 'categoria';
    protected $primaryKey = 'cat_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'cat_nome'
    ];

    public function saveCategory($dados){

        $verify = $this->select()->where($dados)->get()->getRow();

        if(!empty($verify)){
            return array('status' => false, 'msg' => 'Categoria já existe');
        }
        else{

            if($this->save($dados)){
                return array('status' => true, 'msg' => 'Categoria cadastrada com sucesso');
            }
            else{
                return array('status' => false, 'msg' => 'Erro ao cadastrar, tente novamente');
            }
        }
    }

    public function updateCategory($dados){

        $verify = $this->find($dados['cat_id']);

        if(empty($verify)){

            return array('status' => false, 'msg' => 'Categoria não existe');
        }
        else{
            if($this->save($dados)){
                return array('msg' => 'Categoria alterado com sucesso', 'status' => true);
            }
            else{
                return array('status' => false, 'msg' => 'Erro ao alterar, tente novamente');
            }
        }
    }

    public function deleteCategory($id){

        $verify = $this->find($id);

        if(empty($verify)){
            return array('status' => false, 'msg' => 'Categoria não existe');
        }
        else{
            if($this->delete($id, false)){
                return array('msg' => 'Categoria deletado com sucesso', 'status' => true);
            }
            else{
                return array('status' => false, 'msg' => 'Erro ao deletar, tente novamente');
            }
        }
    }
}