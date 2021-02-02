<?php

namespace App\Models;

use CodeIgniter\Model;

class FornecedorModel extends Model
{
    protected $table      = 'fornecedor';
    protected $primaryKey = 'frn_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'frn_nome',
        'frn_doc'
    ];
}