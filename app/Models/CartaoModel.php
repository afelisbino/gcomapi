<?php

namespace App\Models;

use CodeIgniter\Model;

class CartaoModel extends Model
{
    protected $table = 'cartao';
    protected $primaryKey = 'car_id';
    protected $allowedFields = [
        'car_id', 
        'car_nome_impresso', 
        'car_numero', 
        'car_cod_seg', 
        'car_validade_mes', 
        'car_validade_ano', 
        'car_status', 
        'cli_id'
    ];

    public function getId($id)
    {
        return $this->where('car_id', $id)
            ->get()
            ->getRow();
    }

    /**
     * Retorna todos os cartões do cliente
     * @param $id_client
     * @return array
     */
    public function getCustomerCardsId($id_client)
    {
        return $this->where('cli_id', $id_client)
            ->get()
            ->getResultObject();
    }

    /**
     * Retorna apenas o cartão ativo do cliente
     * @param $id_card
     * @param $id_client
     * @return mixed
     */
    public function getCustomerCard($id_client)
    {
        return $this->where('car_status', 'ativo')
            ->where('cli_id', $id_client)
            ->get()
            ->getRow();
    }

    /**
     * Retorna cartão pelo número
     * @param $number
     * @return mixed
     */
    public function getCardNumber($number)
    {
        return $this->where('car_numero', $number)
            ->get()
            ->getRow();
    }
}