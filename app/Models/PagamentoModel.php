<?php
/**
 * User: Emerson Patrik
 * Date: 12/11/2020
 * Time: 04:36:PM
 */

namespace App\Models;

use App\Libraries\Logging;
use CodeIgniter\Model;

class PagamentoModel extends Model
{
    protected $table = 'pagamento';
    protected $primaryKey = 'pag_id';
    protected $allowedFields = [
        'pag_data_cadastro', 
        'pag_data_pagamento', 
        'pag_data_processo', 
        'pag_valor', 
        'pag_forma_pagamento', 
        'pag_parcelas', 
        'pag_status', 
        'pag_retorno_processo', 
        'car_id', 
        'end_id', 
        'cli_id', 
        'pag_requisicao', 
        'pag_id_processo',
        'pag_fat_id',
        'pag_tentativa'
    ];
    private $logging;
    private $client;
    private $uuid;
    private $tentativa = 3;

    function __construct(){
        $this->logging = new Logging();
        $this->client = new ClienteModel();
        $this->uuid = new UuidModel();
    }

    /**
    * Adiciona novo pagamento na fila para processamento
    * 
    * @return array ['status', 'msg']
    */
    public function setPayments($uu_hash = null, $pag_valor = 0, $pag_data_pagamento = null, $pag_forma_pagamento = 'credito', $pag_parcelas = 1, $pag_fat_id = null){
        
        if(empty($uu_hash)){
            $ret['status'] = false;
            $ret['msg'] = "Chave de identificação {$uu_hash}, não informado!";

            $this->logging->logSession('pag_pendente', $ret['msg'], 'info');

            return $ret;
        }
        else{
            
            $Uuidhash = $this->uuid->getHashClient($uu_hash);

            if(empty($uu_hash)){
                $ret['status'] = false;
                $ret['msg'] = "Chave de identificação {$uu_hash}, não encontrado!";

                $this->logging->logSession('pag_pendente', $ret['msg'], 'info');

                return $ret;
            }
            else{

                if(empty($Uuidhash->cli_id)){
                    $ret['status'] = false;
                    $ret['msg'] = "Cliente não encontrado";
        
                    $this->logging->logSession('pag_pendente', $ret['msg'], 'info');

                    return $ret;
                }

                $verifyClient = $this->client->getId($Uuidhash->cli_id);

                if($verifyClient->cli_status == 'inativo'){
                    $ret['status'] = false;
                    $ret['msg'] = "Cliente está cancelado";
        
                    $this->logging->logSession('pag_pendente', $ret['msg'], 'warning');

                    return $ret;
                }
        
                if(empty($Uuidhash->end_id)){
                    $ret['status'] = false;
                    $ret['msg'] = 'Endereço de pagamento não encontrado';

                    $this->logging->logSession('pag_pendente', $ret['msg'], 'info');
        
                    return $ret;
                }
            }
        }

        if($pag_valor == 0){
            $ret['status'] = false;
            $ret['msg'] = 'Valor do pagamento dever ser maior que 0';

            $this->logging->logSession('pag_pendente', $ret['msg'], 'info');

            return $ret;
        }

        if(empty($pag_data_pagamento)){
            $ret['status'] = false;
            $ret['msg'] = 'Informar a data de vencimento';

            $this->logging->logSession('pag_pendente', $ret['msg'], 'info');

            return $ret;
        }

        $cartao = new CartaoModel();

        $cartaoAtivo = $cartao->getCustomerCard($Uuidhash->cli_id);

        if(empty($cartaoAtivo)){
            $ret['status'] = false;
            $ret['msg'] = 'Cartao de credito/debito não encontrado';

            $this->logging->logSession('pag_pendente', $ret['msg'], 'info');

            return $ret;
        }

        $verifyFat = $this->where(['fat_id' => $pag_fat_id])->find();

        if(!empty($verifyFat)){
            $insert['pag_data_cadastro'] = date('Y-m-d H:i:s');
            $insert['pag_data_pagamento'] = $pag_data_pagamento;
            $insert['pag_valor'] = $pag_valor;
            $insert['pag_forma_pagamento'] = $pag_forma_pagamento;
            $insert['pag_parcelas'] = $pag_parcelas;
            $insert['pag_status'] = 'pendente';
            $insert['uu_id'] = $Uuidhash->uu_id;
            $insert['pag_fat_id'] = $pag_fat_id;

            $this->save($insert);

            $ret['status'] = true;
            $ret['msg'] = "Pagamento adicionado na fila";

            $this->logging->logSession('pag_pendente', "Pagamento {$this->getInsertID()} adicionado na fila", 'info');
        }
        else{
            $ret['status'] = false;
            $ret['msg'] = "Pagamento {$pag_fat_id}, já existe!";

            $this->logging->logSession('pag_pendente', "Pagamento da fatura {$pag_fat_id}, já se encontra cadastrado ({$verifyFat['pag_id']}).", 'warning');
        }

        return $ret;

    }

    /**
     * Busca pagamentos pendentes ou com erro para processar pagamento
     * 
     * Quantidade maxima da fila 50 pagamentos
     * 
     * @return mixed
     * 
     */
    public function getListPendingPayments($pag_status){

        return $this->select('*')
            ->join('tb_uuid', 'tb_uuid.uu_id = tb_pagamento.uu_id', 'left')
            ->join('tb_cliente', 'tb_cliente.cli_id = tb_uuid.cli_id', 'inner')
            ->join('tb_endereco', 'tb_endereco.end_id = tb_uuid.end_id', 'inner')
            ->join('tb_cartao', 'tb_cartao.car_id = tb_uuid.car_id', 'inner')
            ->where('pag_status', $pag_status)
            ->where('pag_tentativa <=', $this->tentativa)
            ->where('car_status', 'ativo')
            ->where('pag_data_pagamento <=', date('Y-m-d'))
            ->limit(20)
            ->get()
            ->getResultObject();

    }

    /**
     * Busca todos os pagamentos processados pela data de vencimento
     * 
     * @param date vencimento
     */
    public function getPays($vencimento){
        
        $pays = $this->select('*')
            ->where('pag_status !=', 'pendente')
            ->where("date_format(pag_data_pagamento, '%Y-%m-%d')", $vencimento)
            ->get()
            ->getResultObject();

        $ret = [];

        if(!empty($pays)){
            $i = 0;

            foreach($pays as $obj){
                $ret[$i]['fatura'] = $obj->pag_fat_id;
                $ret[$i]['dt_processo'] = $obj->pag_data_processo;
                $ret[$i]['status'] = $obj->pag_status;
                $ret[$i]['tentativas'] = $obj->pag_tentativa;

                $i++;
            }
        }

        return $ret;
    }
}