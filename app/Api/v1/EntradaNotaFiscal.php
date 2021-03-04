<?php

namespace App\Api\v1;

use App\Models\EntradaProdutoDetalheModel;
use App\Models\EntradaProdutoModel;
use App\Models\EstoqueModel;
use App\Models\FornecedorModel;
use App\Models\ProdutoModel;
use CodeIgniter\RESTful\ResourceController;

class EntradaNotaFiscal extends ResourceController{

    protected $format = 'json';

    private $entrada_produto;
    private $entrada_produto_detalhe;
    private $estoque;
    private $fornecedor;
    private $produto;

    public function __construct(){
        helper('functions_helpers');

        $this->entrada_produto = new EntradaProdutoModel();
        $this->entrada_produto_detalhe = new EntradaProdutoDetalheModel();
    }

    public function index(){
        $ret = $this->entrada_produto->findAllInput();

        $resp = array(
            'data' => array(),
            'recordsTotal' => count($ret),
            'recordsFiltered' => count($ret),
        );

        $l = 0;

        if(!empty($ret)){
            foreach($ret as $obj){
                $opc = "<button class='btn btn-primary' onclick='buscarDetalhesNota({$obj['epr_id']})' title='Mostrar detalhes da nota'><span class='fas fa-eye'></span></button>";

                $resp['data'][$l][] = $opc;
                $resp['data'][$l][] = getDataBR($obj['epr_data_cadastro']);
                $resp['data'][$l][] = $obj['epr_nfs_numero'];
                $resp['data'][$l][] = numeroMoeda($obj['epr_vlr_nota']);
                $resp['data'][$l][] = numeroMoeda($obj['epr_vlr_prod_total']);
                $resp['data'][$l][] = $obj['frn_nome'];

                $l++;
            }
        }

        return $this->respond($resp, 200, 'Sucesso');
    }

    public function newNf(){
        $dados = $this->request->getJSON();

        $this->estoque = new EstoqueModel();
        $this->produto = new ProdutoModel();
        
        $nf['epr_nfs_numero'] = $dados->epr_nfs_numero;
        $nf['epr_vlr_nota'] = numeroFloat($dados->epr_vlr_nota);
        $nf['epr_data_cadastro'] =  date('Y-m-d H:i:s');
        $nf['frn_id'] = $dados->frn_id;

        $this->entrada_produto->save($nf);

        $epr_id = $this->entrada_produto->getInsertID();
        $total_produtos = 0;

        foreach($dados->produtos as $obj){

            $produto = $this->produto->findProduct(['pro_codigo' => $obj->pro_codigo]);

            if(!empty($produto)){
                $nfp['epr_id'] = $epr_id;
                $nfp['pro_id'] = $produto->pro_id;
                $nfp['epd_qtd_entrada'] = $obj->epd_qtd_entrada;
                $nfp['epd_vlr_compra'] = numeroFloat($obj->epd_vlr_compra);

                $this->entrada_produto_detalhe->save($nfp);

                $this->estoque->registerStoreInput($produto->pro_id, $obj->epd_qtd_entrada, "Entrada nota fiscal");

                $total_produtos += (numeroFloat($obj->epd_vlr_compra) * $obj->epd_qtd_entrada);
            }
        }

        $update['epr_vlr_prod_total'] = $total_produtos;
        $update['epr_id'] = $epr_id;

        if($this->entrada_produto->save($update)){
            return $this->respondCreated(['status' => true, 'msg' => 'Entrada de nota fiscal cadastrada com sucesso'], 'Sucesso');
        }
        else{
            return $this->respond(['status' => false, 'msg' => 'Erro ao cadastrar nota fiscal, tente novamente'], 200, 'Sucesso');
        }
    }

    public function viewInputDetail(){

        $dados = $this->request->getGet();

        $ret = $this->entrada_produto_detalhe->findDetailsInput($dados['epr_id']);

        $resp = array(
            'data' => array(),
            'recordsTotal' => count($ret),
            'recordsFiltered' => count($ret),
        );

        $l = 0;

        if(!empty($ret)){
            foreach($ret as $obj){
                $resp['data'][$l][] = $obj['pro_codigo'];
                $resp['data'][$l][] = $obj['pro_nome'];
                $resp['data'][$l][] = $obj['epd_qtd_entrada'];
                $resp['data'][$l][] = numeroMoeda($obj['epd_vlr_compra']);
                $resp['data'][$l][] = numeroMoeda(($obj['epd_vlr_compra'] * $obj['epd_qtd_entrada']));

                $l++;
            }
        }

        return $this->respond($resp, 200, 'Sucesso');
    }
}