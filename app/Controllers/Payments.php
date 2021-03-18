<?php

namespace App\Controllers;

use App\Libraries\Getnet;
use App\Libraries\Logging;
use App\Models\EstornoModel;
use App\Models\PagamentoModel;

class Payments extends BaseController{

    private $reversal;
    private $payments;
    private $logging;
    private $getNet;

    public function __construct()
    {

        $this->reversal = new EstornoModel();
        $this->payments = new PagamentoModel();
        $this->logging = new Logging();
        $this->getNet = new Getnet('homolog');

        helper(['functions_helpers']);
    }

    /**
     * Executa fila de pagamentos pendente na GETNET
     */
    public function processPayment($pag_status){

        $pag_pendentes = $this->payments->getListPendingPayments($pag_status);
        
        if(!empty($pag_pendentes)){

            foreach($pag_pendentes as $pagObj){
                //Dados do pagamento
                $aux['pag_id'] = $pagObj->pag_id;
                $aux['pag_valor'] = $pagObj->pag_valor;
                $aux['pag_forma_pagamento'] = $pagObj->pag_forma_pagamento;
                $aux['pag_parcelas'] = $pagObj->pag_parcelas;
                //Dados do endereço do cliente
                $aux['end_logradouro'] = Cryptography('decrypt',$pagObj->end_logradouro);
                $aux['end_numero'] = Cryptography('decrypt',$pagObj->end_numero);
                $aux['end_complemento'] = Cryptography('decrypt',$pagObj->end_complemento);
                $aux['end_bairro'] = Cryptography('decrypt',$pagObj->end_bairro);
                $aux['end_cidade'] = Cryptography('decrypt',$pagObj->end_cidade);
                $aux['end_uf'] = $pagObj->end_uf;
                $aux['end_cep'] = Cryptography('decrypt',$pagObj->end_cep);
                //Dados do cliente
                $nome_tmp = explode(' ', Cryptography('decrypt',$pagObj->cli_nome));

                $aux['cli_primeiro_nome'] = $nome_tmp[0];
                $aux['cli_ultimo_nome'] = $nome_tmp[count($nome_tmp) - 1];
                $aux['cli_nome_completo'] = Cryptography('decrypt',$pagObj->cli_nome);
                $aux['cli_doc'] = Cryptography('decrypt',$pagObj->cli_doc);
                $aux['cli_tipo'] = $pagObj->cli_tipo == 'pf' ? 'CPF' : "CNPJ";
                $aux['cli_email'] = Cryptography('decrypt',$pagObj->cli_email);
                $aux['cli_telefone'] = Cryptography('decrypt',$pagObj->cli_telefone);
                //Dados do cartao
                $aux['car_numero'] = Cryptography('decrypt',$pagObj->car_numero);
                $aux['car_cod_seg'] = Cryptography('decrypt',$pagObj->car_cod_seg);
                $aux['car_nome_impresso'] = Cryptography('decrypt',$pagObj->car_nome_impresso);
                $aux['car_validade_mes'] = $pagObj->car_validade_mes;
                $aux['car_validade_ano'] = substr($pagObj->car_validade_ano, -2);

                $retGetNet = $this->getNet->processPayment($aux);
                                
                if($retGetNet['status'] === true){
                    //Para pagamentos bem sucedidos
                    $update['pag_id'] = $pagObj->pag_id;
                    $update['pag_status'] = "pago";
                    $update['pag_retorno_processo'] = Cryptography('encrypt',$retGetNet['msg']);
                    $update['pag_data_processo'] = date('Y-m-d H:i:s');
                    $update['pag_requisicao'] = Cryptography('encrypt',$retGetNet['req']);
                    $update['pag_id_processo'] = $retGetNet['id_processo'];
                    $update['pag_tentativa'] = $pagObj->pag_tentativa + 1;

                    $this->retSuccessProcess($update, "Pagamento {$pagObj->pag_id} processado com sucesso");
                }
                else{
                    //Para pagamentos não processados
                    $update['pag_id'] = $pagObj->pag_id;
                    $update['pag_status'] = "erro";
                    $update['pag_retorno_processo'] = Cryptography('encrypt',$retGetNet['msg']);
                    $update['pag_data_processo'] = date('Y-m-d H:i:s');
                    $update['pag_requisicao'] = Cryptography('encrypt',$retGetNet['req']);
                    $update['pag_tentativa'] = $pagObj->pag_tentativa + 1;

                    $this->retFailProcess($update, "Erro ao processar o pagamento {$pagObj->pag_id}");
                }
            }
        }
    }

    private function retSuccessProcess($dados, $log = null){
        $this->payments->save($dados);

        $this->logging->logSession('pag_processo', $log, 'info');
    }

    private function retFailProcess($dados, $log = null){
        $this->payments->save($dados);

        $this->logging->logSession('pag_processo', $log, 'error');
    }

    /**
     * Processa fila de estorno de pagamento 
     */
    public function processCancel(){

        $cancel_pendente = $this->reversal->getPaymentsMade();

        if(!empty($cancel_pendente)){
            foreach($cancel_pendente as $obj){
                if(!empty($obj->pag_id_processo)){
                    
                    $update['est_id'] = $obj->est_id;

                    $cancel = $this->getNet->requestReversalPayment($obj->pag_id_processo, $obj->pag_valor);

                    if($cancel['status'] == true){

                        $update['est_status'] = 'concluido';
                        $update['est_data_processo'] = date('Y-m-d H:i:s');
                        $update['est_retorno_processo'] = Cryptography('encrypt',$cancel['msg']);

                        $this->logging->logSession('estorno', "Pagamento {$obj->pag_id}, cancelado com sucesso!", 'info');
                    }
                    else{

                        $update['est_status'] = 'erro';
                        $update['est_data_processo'] = date('Y-m-d H:i:s');
                        $update['est_retorno_processo'] = Cryptography('encrypt',$cancel['msg']);
                    }
                    
                    $this->reversal->save($update);
                }
                else{

                    $this->logging->logSession('estorno', "Impossivel estornar o pagamento {$obj->pag_id}, não consta o ID do processo de pagamento!", 'error');
                }       
            }
        }
    }
}