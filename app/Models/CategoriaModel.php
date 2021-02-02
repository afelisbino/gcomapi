<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoriaModel extends Model{

    protected $table      = 'categoria';
    protected $primaryKey = 'cat_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'cat_nome'
    ];
}