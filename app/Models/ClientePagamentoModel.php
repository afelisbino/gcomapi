<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientsModel extends Model
{
    protected $table = 'cliente';
    protected $primaryKey = 'cli_id';
    protected $allowedFields = [
        'cli_id', 
        'cli_nome', 
        'cli_tipo', 
        'cli_doc', 
        'cli_rg_ie', 
        'cli_status', 
        'cli_telefone', 
        'cli_email', 
        'cli_data', 
        'cli_uuid'
    ];

    public function getId($id)
    {
        return $this->where('cli_id', $id)
            ->get()
            ->getRow();
    }

    public function getUuid($uuid)
    {
        return $this->where('cli_uuid', $uuid)
            ->get()
            ->getRow();
    }

    public function getDoc($doc)
    {
        return $this->where('cli_doc', $doc)
            ->get()
            ->getRow();
    }

    public function getRgIe($rgie)
    {
        return $this->where('cli_rg_ie', $rgie)
            ->get()
            ->getRow();
    }

    public function getEmail($email)
    {
        return $this->where('cli_email', $email)
            ->get()
            ->getRow();
    }

    public function getId_Uuid($id, $uuid)
    {
        return $this->where('cli_id', $id)
            ->where('cli_uuid', $uuid)
            ->get()
            ->getRow();
    }
}