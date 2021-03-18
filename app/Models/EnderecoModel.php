<?php

namespace App\Models;

use CodeIgniter\Model;

class EnderecoModel extends Model
{
    protected $table = 'endereco';
    protected $primaryKey = 'end_id';
    protected $allowedFields = [
        'end_id', 
        'end_con_id', 
        'end_logradouro', 
        'end_numero', 
        'end_bairro', 
        'end_complemento', 
        'end_cidade', 
        'end_uf', 
        'end_cep', 
        'cli_id'
    ];

    /**
     * Retorna endereço pelo id
     * @param $end_con_id
     * @return mixed
     */
    public function getId($end_id)
    {
        return $this->where('end_id', $end_id)
            ->get()
            ->getRow();
    }

    /**
     * Retorna todos os endereços do cliente
     * @param $id_client
     * @return array
     */
    public function getAddressClientId($id_client)
    {
        return $this->where('cli_id', $id_client)
            ->get()
            ->getResultObject();
    }

    /**
     * Retorna um único contrato vinculado com endereço do cliente
     * @param $end_con_id
     * @return mixed
     */
    public function getCon($end_con_id)
    {
        return $this->where('end_con_id', $end_con_id)
            ->get()
            ->getRow();
    }

    /**
     * Retorna único registro de acordo com cli_id e end_id
     * @param $cli_id
     * @param $end_id
     * @return mixed
     */
    public function getIdClientIdAddr($cli_id, $end_id)
    {
        return $this->where('end_id', $end_id)
            ->where('cli_id', $cli_id)
            ->get()
            ->getRow();
    }
}
