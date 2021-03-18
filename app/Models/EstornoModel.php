<?php

namespace App\Models;

use CodeIgniter\Model;

class EstornoModel extends Model
{
    protected $table = 'estorno';
    protected $primaryKey = 'est_id';
    protected $allowedFields = [
        'est_data_cadastro',
        'est_data_processo',
        'est_status',
        'est_retorno_processo',
        'pag_id'
    ];

    public function getPaymentsMade(){

        return $this->select("date_format(pag_data_processo, '%Y-%m-%d') as pag_data_processo, pag_valor, pag_forma_pagamento, pag_id_processo, est_id, tb_pagamento.pag_id")
            ->join('tb_pagamento', 'tb_pagamento.pag_id = tb_estorno.pag_id')
            ->where('est_status', 'pendente')
            ->get()
            ->getResultObject();
    }
}