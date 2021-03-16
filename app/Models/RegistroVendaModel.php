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
        'rgv_fiado',
        'cli_id',
        'cxa_id'
    ];

    public function getTotalSalesValue($where){
        $this->select("sum(rgv_vlr_total) as total_venda, count(rgv_id) as qtd_venda");
        $this->where($where);
        return $this->get()->getRow();
    }

    public function getAllSale($data_inicial = null, $data_final = null){
       
        if(!empty($data_inicial) || !empty($data_final)){
            $this->where("date_format(rgv_data, '%Y-%m-%d') between '".$data_inicial."' and '".$data_final."'");
        }
        
        $this->orderBy('rgv_id', 'DESC');
        return $this->get()->getResultArray();
    }

    public function getAllSpunOpen(){
        $this->join('cliente', 'cliente.cli_id = registro_venda.cli_id', 'left');
        $this->where('rgv_fiado', 1);
        $this->where('rgv_status', 'aberto');
        $this->orderBy('cliente.cli_id', 'asc');

        return $this->get()->getResultArray();
    }
}