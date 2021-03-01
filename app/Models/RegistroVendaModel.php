<?php

namespace App\Models;

use CodeIgniter\Model;

class RegistroVendaModel extends Model
{
    protected $table      = 'registro_venda';
    protected $primaryKey = 'rgv_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'rgv_data',
        'rgv_vlr_total',
        'rgv_forma_pag',
        'rgv_status',
        'cxa_id'
    ];

    public function getTotalSalesValue($where){
        $this->select("sum(rgv_vlr_total) as total_venda, count(rgv_id) as qtd_venda");
        $this->where($where);
        return $this->get()->getRow();
    }
}