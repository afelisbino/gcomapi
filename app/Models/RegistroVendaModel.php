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
        'rgv_status'
    ];
}