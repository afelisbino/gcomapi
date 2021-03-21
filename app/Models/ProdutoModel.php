<?php

namespace App\Models;

use App\Libraries\Logging;
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
        'pro_foto',
        'cat_id', 
        'frn_id'
    ];

    private $logging;

    public function findProduct($where = array()){

        return $this->join('categoria', 'categoria.cat_id = produto.cat_id')
            ->join('fornecedor', 'fornecedor.frn_id = produto.frn_id')
            ->where($where)->get()->getRow();
    }

    public function saveProduct($dados){
        $this->logging = new Logging();
        
        if(empty($dados['pro_codigo'])){
            $this->where('pro_nome', $dados['pro_nome']);
            $dados['pro_codigo'] = null;
        }
        else{
            $this->where('pro_codigo', $dados['pro_codigo']);
        }

        $verify = $this->get()->getRow();

        if(empty($verify)){
            if($this->save($dados)){

                $this->estoque = new EstoqueModel();

                $estoque['est_qtd_atual'] = $dados['est_qtd_atual'];
                $estoque['est_qtd_minimo'] = 0;
                $estoque['pro_id'] = $this->getInsertID();
    
                if($this->estoque->save($estoque)){
                    return array('msg' => 'Produto cadastrado com sucesso', 'status' => true);
                }
                else{
                    $this->logging->logSession('estoque', "Erro ao iniciar estoque do produto (ID {$estoque['pro_id']}): " . $this->estoque->errors(), 'error');
                    return array('msg' => 'Produto cadastrado com sucesso, sem estoque inicializado', 'status' => true);
                }
            }
            else{
                $this->logging->logSession('produto', "Erro ao cadastrar produto: " . $this->errors(), 'error');
                return array('msg' => 'Produto não cadastrado', 'status' => false);
            }
        }
        else{
            return array('msg' => 'Produto já cadastrado', 'status' => false);
        }
    }

    public function updateProduct($dados){
        $this->logging = new Logging();
        $verify = $this->find($dados['pro_id']);

        if(!empty($verify)){
            if($this->save($dados)){

                $this->estoque = new EstoqueModel();

                $estoquePro = $this->estoque->findStorage(['pro_id' => $dados['pro_id']]);
                
                if(!empty($estoquePro)){
                    $estoque['pro_id'] = $dados['pro_id'];
                    $estoque['est_id'] = $estoquePro['est_id'];
                    $estoque['est_qtd_atual'] = $dados['est_qtd_atual'];

                    if($this->estoque->save($estoque)) {
                        return array('msg' => 'Produto alterado com sucesso', 'status' => true);
                    }
                    else{
                        $this->logging->logSession('produto', "Erro ao atualizar produto: " . $this->estoque->errors(), 'error');
                        return array('msg' => 'Não foi possivel atualizar o produto', 'status' => false);
                    } 
                }
                else{
                    $estoque['pro_id'] = $dados['pro_id'];
                    $estoque['est_qtd_atual'] = $dados['est_qtd_atual'];
                    $estoque['est_qtd_minimo'] = 0;

                    if($this->estoque->save($estoque)) {
                        return array('msg' => 'Produto alterado com sucesso', 'status' => true);
                    }
                    else{
                        $this->logging->logSession('produto', "Erro ao atualizar produto: " . $this->estoque->errors(), 'error');
                        return array('msg' => 'Não foi possivel atualizar o produto', 'status' => false);
                    } 
                }

                return array('msg' => 'Produto alterado com sucesso', 'status' => true);            
            }
            else{
                $this->logging->logSession('produto', "Erro ao atualizar produto: " . $this->errors(), 'error');
                return array('msg' => 'Não foi possivel atualizar o produto', 'status' => false);
            }
        }
        else{
            return array('msg' => 'Produto não encontrado', 'status' => false);
        }
    }

    public function deleteProduct($id){
        $this->logging = new Logging();
        $this->estoque = new EstoqueModel();

        $verify = $this->find($id);

        if(!empty($verify)){
            $estoque = $this->estoque->findStorage(['produto.pro_id' => $verify['pro_id']]);

            if(!empty($estoque)){
                if($this->estoque->delete($estoque->est_id, false)){
                    if($this->delete($id, false)){
                        return array('msg' => 'Produto deletado com sucesso', 'status' => true);
                    }
                    else{
                        $this->logging->logSession('produto', "Erro ao excluir produto: " . $this->errors(), 'error');
                        return array('status' => false, 'msg' => 'Erro ao deletar, tente novamente');
                    }
                }
                else{
                    $this->logging->logSession('estoque', "Erro ao excluir estoque do produto (ID {$verify['pro_id']}): " . $this->estoque->errors(), 'error');
                }
            }
            else if($this->delete($id, false)){
                return array('msg' => 'Produto deletado com sucesso', 'status' => true);
            }
            else{
                $this->logging->logSession('produto', "Erro ao excluir produto: " . $this->errors(), 'error');
                return array('status' => false, 'msg' => 'Erro ao deletar, tente novamente');
            }
        }
        else{
            return array('msg' => 'Produto não encontrado', 'status' => false);
        }
    }
}