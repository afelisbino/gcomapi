<?php
namespace App\Models;

use CodeIgniter\Model;

class CaixaModel extends Model{

    protected $table      = 'caixa';
    protected $primaryKey = 'cxa_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'cxa_data_abertura',
        'cxa_data_fechamento',
        'cxa_total_fechamento',
        'cxa_status'
    ];

    public function getCash($where = array()){
        return $this->where($where)->get()->getRow();
    }

    public function getTotalCash($where){
        $this->select("sum(rgv_vlr_total) as total_caixa, cxa_status, cxa_data_abertura, cxa_data_fechamento");
        $this->join("registro_venda", "registro_venda.cxa_id = caixa.cxa_id");
        $this->where($where);
        $this->orderBy('caixa.cxa_id', 'DESC');
        $this->limit(1);

        return $this->get()->getRow();
    }
}