<?php
/**
 * User: Adriano Silva
 * Date: 24/11/2020
 * Time: 10:25:AM
 */

namespace App\Models;

use CodeIgniter\Model;
use Config\App;

class UuidModel extends Model
{
    protected $table = 'tb_uuid';
    protected $primaryKey = 'uu_id';
    protected $allowedFields = ['uu_id', 'uu_hash',	'cli_id', 'end_id', 'car_id', 'uu_status'];

    /**
     * Retorna únicos registro da hash
     * @param $hash
     * @return mixed
     */
    public function getHashClient($hash)
    {
        return $this->where('uu_hash', $hash)
            ->get()
            ->getRow();
    }

    /**
     * Retorna todas hash de único cliente
     * @param $cli_id
     * @return array
     */
    public function getCli_Id($cli_id)
    {
        return $this->where('cli_id', $cli_id)
            ->get()
            ->getResultObject();
    }

    /**
     * Retorna a hash do cartao e endereço
     * @param $cli_id
     * @param $end_id
     * @return mixed
     */
    public function getCli_End($cli_id, $end_id)
    {
        return $this->where('cli_id', $cli_id)
            ->where('end_id', $end_id)
            ->get()
            ->getRow();
    }

	/**
	 * Retorna os endereços de um único cliente
	 * @param $cli_id
	 * @return array
	 */
	public function address_join($cli_id)
	{
		return $this->db->table($this->table . ' AS uu')
			->select('uu.*, addr.*')
			->join('tb_endereco AS addr', 'uu.end_id = addr.end_id', 'inner')
			->where('uu.cli_id', $cli_id)
			->get()
			->getResultObject();

    }
}