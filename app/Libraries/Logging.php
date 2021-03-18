<?php

namespace App\Libraries;

class Logging
{
	private $log_directory = "../app/Logs/";

	/** Gera o log e monta as pastas com os arquivos logs
	 * ANO/ANO-MES/DIA-01.log
	 *
	 * Chamada da função
	 * $log = new Logging();
	 * $log->logSession('clientes', 'aqui vai a mensagem', 'info')
	 *
	 * @param $folder
	 * @param $mensage
	 * @param null $level
	 */
	public function logSession($folder, $mensage, $level = null)
	{
		// Caminho da pasta
		$directory = $this->log_directory . $folder;

		$year = date('Y');
		$month = date('m');
		$day = date('d');

		// Caminho pasta Log
		if (!is_dir($this->log_directory)) :
			mkdir($this->log_directory, 0777, true);
		endif;

		// Caminho pasta escolhida $folder
		if (!is_dir($directory)) :
			mkdir($directory, 0777, true);
		endif;

		// Caminho pasta ano
		if (!is_dir($directory . '/' . $year)) :
			mkdir($directory . '/' . $year, 0777, true);
		endif;

		// Caminho pasta ano/mes
		if (!is_dir($directory . '/' . $year . '/' . $year . '-' . $month)) :
			mkdir($directory . '/' . $year . '/' . $year . '-' . $month, 0777, true);
		endif;

		// caminho completo
		$directory = $directory . '/' . $year . '/' . $year . '-' . $month;

		// variável que vai armazenar o nível do log (INFO, WARNING ou ERROR)
		$levelStr = '';

		// verifica o nível do log
		switch ($level) {
			case 'info':
				// nível de informação
				$levelStr = 'INFO';
				break;

			case 'warning':
				// nível de aviso
				$levelStr = 'WARNING';
				break;

			case 'error':
				// nível de erro
				$levelStr = 'ERROR';
				break;
		}

		// data atual
		$date = date('Y-m-d H:i:s');

		$mensage = sprintf("[%s] [%s] - %s%s", $date, $levelStr, $mensage, PHP_EOL);

		$log = fopen($directory . '/DIA-' . $day . ".log", "a+");

		fwrite($log, $mensage); // Escreve

		fclose($log); // Fecha o arquivo
	}
}
