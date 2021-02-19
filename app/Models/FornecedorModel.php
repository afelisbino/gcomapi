<?php

namespace App\Models;

use CodeIgniter\Model;

class FornecedorModel extends Model{
    
    protected $table      = 'fornecedor';
    protected $primaryKey = 'frn_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'frn_nome',
        'frn_doc'
    ];

    public function saveProvider($dados){

        $verify = $this->where('frn_doc', $dados['frn_doc'])->get()->getRow();

        if(!empty($verify)){
            return array('status' => false, 'msg' => 'Fornecedor já existe');
        }
        else{
            if($this->save($dados)){
                return array('status' => true, 'msg' => 'Fornecedor cadastrado com sucesso');
            }
            else{
                return array('status' => false, 'msg' => 'Erro ao cadastrar, tente novamente');
            }
        }
    }

    public function updateProvider($dados){

        $verify = $this->find($dados['frn_id']);

        if(empty($verify)){

            return array('status' => false, 'msg' => 'Fornecedor não existe');
        }
        else{
            if($verify['frn_nome'] != $dados['frn_nome'] || $verify['frn_doc'] != $dados['frn_doc']){
                if($this->save($dados)){
                    return array('msg' => 'Fornecedor alterado com sucesso', 'status' => true);
                }
                else{
                    return array('status' => false, 'msg' => 'Erro ao alterar, tente novamente');
                }
            }
            else{
                return array('status' => false, 'msg' => 'Fornecedor já foi alterado anteriormente');
            }
        }
    }

    public function deleteProvider($id){

        $verify = $this->find($id);

        if(empty($verify)){
            return array('status' => false, 'msg' => 'Fornecedor não existe');
        }
        else{
            if($this->delete($id, false)){
                return array('msg' => 'Fornecedor deletado com sucesso', 'status' => true);
            }
            else{
                return array('status' => false, 'msg' => 'Erro ao deletar, tente novamente');
            }
        }
    }

    public function findProvider($where = array()){
        return $this->where($where)->get()->getRow();
    }
}