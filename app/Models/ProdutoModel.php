<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdutoModel extends Model
{
    private $estoque;
    
    protected $table      = 'produto';
    protected $primaryKey = 'pro_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'pro_codigo', 
        'pro_nome', 
        'pro_valor_venda', 
        'cat_id', 
        'frn_id'
    ];

    public function findProduct($where = array()){

        return $this->select('*')
            ->join('categoria', 'categoria.cat_id = produto.cat_id')
            ->join('fornecedor', 'fornecedor.frn_id = produto.frn_id')
            ->where($where)->get()->getRow();
    }

    public function saveProduct($dados){

        $verify = $this->where('pro_codigo', $dados['pro_codigo'])->get()->getRow();

        if(empty($verify)){
            if($this->save($dados)){

                $this->estoque = new EstoqueModel();
    
                $estoque['est_qtd_atual'] = 0;
                $estoque['est_qtd_minimo'] = 0;
                $estoque['pro_id'] = $this->getInsertID();
    
                if($this->estoque->save($estoque)){
                    return array('msg' => 'Produto cadastrado com sucesso', 'status' => true);
                }
                else{
                    return array('msg' => 'Produto cadastrado com sucesso, sem estoque inicializado', 'status' => true);
                }
            }
            else{
                return array('msg' => 'Produto não cadastrado', 'status' => false);
            }
        }
        else{
            return array('msg' => 'Produto já cadastrado', 'status' => false);
        }
    }

    public function updateProduct($dados){
        $verify = $this->find($dados['pro_id']);

        if(!empty($verify)){
            if($this->save($dados)){
                    
                return array('msg' => 'Produto alterado com sucesso', 'status' => true);            
            }
            else{
                return array('msg' => 'Não foi possivel atualizar o produto', 'status' => false);
            }
        }
        else{
            return array('msg' => 'Produto não encontrado', 'status' => false);
        }
    }

    public function deleteProduct($id){

        $this->estoque = new EstoqueModel();

        $verify = $this->find($id);

        if(!empty($verify)){
            $estoque = $this->estoque->findStorage(['pro_id' => $verify['pro_id']]);

            if(!empty($estoque)){
                if($this->estoque->delete($estoque->est_id, false)){
                    if($this->delete($id, false)){
                        return array('msg' => 'Produto deletado com sucesso', 'status' => true);
                    }
                    else{
                        return array('status' => false, 'msg' => 'Erro ao deletar, tente novamente');
                    }
                }
            }
            else if($this->delete($id, false)){
                return array('msg' => 'Produto deletado com sucesso', 'status' => true);
            }
            else{
                return array('status' => false, 'msg' => 'Erro ao deletar, tente novamente');
            }
        }
        else{
            return array('msg' => 'Produto não encontrado', 'status' => false);
        }
    }
}