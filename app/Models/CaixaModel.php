<?php
namespace App\Models;

use CodeIgniter\Model;

class CaixaModel extends Model{

    protected $table      = 'caixa';
    protected $primaryKey = 'cxa_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'cxa_data_abertura',
        'cxa_data_fechado',
        'cxa_total_fechamento',
        'cxa_status'
    ];

    public function openCashRegister(){
        $insert['cxa_data_abertura'] = date('Y-m-d H:i:s');
        $insert['cxa_data_fechado'] = null;
        $insert['cxa_total_fechamento'] = null;
        $insert['cxa_status'] = 'aberto';

        $this->save($insert);
    }

    public function closeCashRegister($totalFechamento, $cxa_id){
        $update['cxa_data_fechado'] = date('Y-m-d H:i:s');
        $update['cxa_total_fechamento'] = $totalFechamento;
        $update['cxa_id'] = $cxa_id;
        $update['cxa_status'] = 'fechado';

        $this->save($update);
    }

    public function getCashOpen(){
        return $this->where(['cxa_status' => 'aberto'])->get()->getRow();
    }
}