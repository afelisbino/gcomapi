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

    public function getInfoCashOpen($cxa_id){
        $this->select("cxa_status, cxa_data_abertura, cxa_data_fechamento");
        $this->where('caixa.cxa_id', $cxa_id);

        return $this->get()->getRow();
    }

    public function getInfoCashClose(){
        $this->where(['cxa_status' => 'fechado']);
        $this->orderBy("cxa_id", "DESC");
        $this->limit(1);

        return $this->get()->getRow();
    }

    public function getAllCash($data_inicial = null, $data_final = null){
        if(!empty($data_inicial) && !empty($data_final)){
            $this->where("cxa_data_abertura between '".$data_inicial."' and '".$data_final."'");
        }
        $this->orderBy('cxa_id', 'DESC');
        return $this->get()->getResultArray();
    }
}