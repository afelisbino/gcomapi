<?php

namespace App\Libraries;

class Getnet
{
	private $url;
	private $seller_id;
	private $client_id;
	private $client_secret;
	private $token_autenticacao;
	private $logging;

	/**
	 * Seta o ambiente de trabalho e inicializa serviço na getnet
	 *
	 * @param string ambiente (homolog/producao)
	 *
	 * @return null
	 */
	function __construct($ambiente = 'producao')
	{

		$this->logging = new Logging();

		switch ($ambiente) {
			case 'homolog':
				$this->url = 'https://api-sandbox.getnet.com.br/';
				$this->seller_id = '26fe5d0a-0779-4e9d-9f2a-681d5e334824';
				$this->client_id = 'c3f1df76-6fcc-4bd4-9e24-b593a651b797';
				$this->client_secret = '911803f6-2d25-4227-a154-7ed7ee11bccb';
				break;
			case 'producao':
				$this->url = '';
				$this->seller_id = '';
				$this->client_id = '';
				$this->client_secret = '';
				break;
			default:
				$this->logging->logSession('getnet', "Ambiente de execução inválido", "warning");
				return;
		}

		$this->getTokenAccess();
	}

	/**
	 * Valida credenciais na GETNET e salva token de autorização
	 *
	 * @return array status (true/false)
	 */
	private function getTokenAccess()
	{

		$key = base64_encode($this->client_id . ':' . $this->client_secret);
		$url = $this->url . "auth/oauth/v2/token";

		$headers = [
			'Content-Type: application/x-www-form-urlencoded',
			'Cache-Control: no-cache',
			'authorization: Basic ' . $key,
		];

		$curl = curl_init($url);

		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, 'scope=oob&grant_type=client_credentials');

		$resp = curl_exec($curl);
		curl_close($curl);

		if (!empty($resp)) {

			$respObj = json_decode($resp);

			if (isset($respObj->error)) {
				$this->logging->logSession('getnet', "Erro ao validar credenciais ({$respObj->error_description}).", "error");
			} else {
				$this->token_autenticacao = $respObj->token_type . ' ' . $respObj->access_token;

				$this->logging->logSession('getnet', "Token liberado pela getnet", "info");
			}
		} else {
			$this->logging->logSession('getnet', "Erro ao tentar validar credenciais", "error");
		}
	}

	/**
	 * Verifica se o cartao informado Master ou Visa é valido ou não
	 *
	 * @param array cartao [car_numero, car_nome_impresso, car_validade_mes, car_validade_ano, car_cod_seg]
	 *
	 * @return array [status, cartao_status, id_verificacao, msg]
	 */
	public function checkValidationCard(array $dados_cartao)
	{

		$number_token = $this->getTokenCard($dados_cartao['car_numero']);

		if ($number_token['status'] === true) {
			$token_card = $number_token['token'];
		} else {
			$ret['status'] = false;
			$ret['msg'] = $number_token['msg'];

			return $ret;
		}

		$url = $this->url . "v1/cards/verification";

		$headers = [
			'Content-type: application/json; charset=utf-8',
			'authorization: ' . $this->token_autenticacao,
		];

		$payloads = [
			'number_token'     => $token_card,
			'cardholder_name'  => $dados_cartao['car_nome_impresso'],
			'expiration_month' => $dados_cartao['car_validade_mes'],
			'expiration_year'  => $dados_cartao['car_validade_ano'],
			'security_code'    => $dados_cartao['car_cod_seg'],
		];

		$curl = curl_init($url);

		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_ENCODING, "");
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payloads));

		$resp = curl_exec($curl);
		curl_close($curl);

		if (!empty($resp)) {

			$respObj = json_decode($resp);

			if (isset($respObj->status)) {
				$ret['status'] = true;
				$ret['cartao_status'] = $respObj->status;
				$ret['id_verificacao'] = $respObj->verification_id;
			} else {
				$ret['status'] = false;
				$ret['msg'] = "Erro ao verificar o cartao";

				$this->logging->logSession('getnet', "Erro ao verificar cartao ({$respObj->message}).", "error");
			}
		} else {
			$ret['status'] = false;
			$ret['msg'] = "Erro ao verificar o cartao";

			$this->logging->logSession('getnet', "Erro na comunicação com a GETNET", "error");
		}

		return $ret;
	}

	/**
	 * Gera um token do cartao para realizar transações seguras
	 *
	 * @return mixed|array status(true/false),token
	 */
	private function getTokenCard($car_numero = null)
	{

		if (empty($car_numero)) {
			$ret['status'] = false;

			$this->logging->logSession('getnet', "Numero do cartao informado está inválido", "warning");

			return $ret;
		}

		$url = $this->url . "v1/tokens/card";

		$headers = [
			"Content-type: application/json; charset=utf-8",
			"authorization: " . $this->token_autenticacao,
		];

		$payloads = [
			'card_number' => $car_numero,
		];

		$curl = curl_init($url);

		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_ENCODING, "");
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payloads));

		$resp = curl_exec($curl);
		curl_close($curl);

		if (!empty($resp)) {

			$respObj = json_decode($resp);

			if (isset($respObj->number_token)) {
				$ret['status'] = true;
				$ret['token'] = $respObj->number_token;
			} else {
				$ret['status'] = false;
				$ret['msg'] = "Erro ao autenticar o cartao";

				$this->logging->logSession('getnet', "Erro ao gerar token do cartao ({$respObj->message}).", "error");
			}
		} else {
			$ret['status'] = false;
			$ret['msg'] = "Erro ao autenticar o cartao";

			$this->logging->logSession('getnet', "Erro na comunicação com a GETNET", "error");
		}

		return $ret;
	}

	/**
	 * Executa o processo de pagamento na GETNET
	 *
	 * @return array [status (true, false), msg, req, id_processo]
	 */
	public function processPayment(array $dados_pagamento)
	{

		if (empty($dados_pagamento)) {
			$ret['status'] = false;
			$ret['msg'] = "Dados do pagamento inválido";

			$this->logging->logSession('getnet', "Dados do pagamento vazio", "warning");

			return $ret;
		}

		$token_card = $this->getTokenCard($dados_pagamento['car_numero']);

		if ($token_card['status'] === true) {
			$number_token = $token_card['token'];
		} else {
			$ret['status'] = false;
			$ret['msg'] = $token_card['msg'];

			return $ret;
		}

		$d['seller_id'] = $this->seller_id;

		$d['amount'] = (int) ($dados_pagamento['pag_valor'] * 100); //Valor transformado para centavos

		$d['currency'] = 'BRL';

		$d['order']['order_id'] = $dados_pagamento['pag_id'];
		$d['order']['sales_tax'] = 0;
		$d['order']['product_type'] = 'service';

		$d['customer']['customer_id'] = $dados_pagamento['cli_doc'];
		$d['customer']['name'] = $dados_pagamento['cli_nome_completo'];
		$d['customer']['first_name'] = $dados_pagamento['cli_primeiro_nome'];
		$d['customer']['last_name'] = $dados_pagamento['cli_ultimo_nome'];
		$d['customer']['email'] = $dados_pagamento['cli_email'];
		$d['customer']['document_type'] = $dados_pagamento['cli_tipo'];
		$d['customer']['document_number'] = $dados_pagamento['cli_doc'];
		$d['customer']['billing_address']['street'] = $dados_pagamento['end_logradouro'];
		$d['customer']['billing_address']['number'] = $dados_pagamento['end_numero'];

		if (!empty($dados_pagamento['end_complemento'])) {
			$d['customer']['billing_address']['complement'] = $dados_pagamento['end_complemento'];
		}

		$d['customer']['billing_address']['district'] = $dados_pagamento['end_bairro'];
		$d['customer']['billing_address']['city'] = $dados_pagamento['end_cidade'];
		$d['customer']['billing_address']['state'] = $dados_pagamento['end_uf'];
		$d['customer']['billing_address']['country'] = 'Brasil';
		$d['customer']['billing_address']['postal_code'] = $dados_pagamento['end_cep'];

		$d['device']['device_id'] = $dados_pagamento['pag_id'];

		if ($dados_pagamento['pag_forma_pagamento'] === 'credito') {

			$d['credit']['delayed'] = false;
			$d['credit']['authenticated'] = false;
			$d['credit']['pre_authorization'] = false;
			$d['credit']['save_card_data'] = false;
			$d['credit']['transaction_type'] = $dados_pagamento['pag_parcelas'] == 1 ? 'FULL' : 'INSTALL_NO_INTEREST';
			$d['credit']['number_installments'] = $dados_pagamento['pag_parcelas'];
			$d['credit']['soft_descriptor'] = 'C-lig';

			$d['credit']['card']['cardholder_name'] = $dados_pagamento['car_nome_impresso'];
			$d['credit']['card']['security_code'] = $dados_pagamento['car_cod_seg'];
			$d['credit']['card']['expiration_month'] = $dados_pagamento['car_validade_mes'];
			$d['credit']['card']['expiration_year'] = $dados_pagamento['car_validade_ano'];
			$d['credit']['card']['number_token'] = $number_token;
		} else if ($dados_pagamento['pag_forma_pagamento'] === 'debito') {

			$d['debit']['cardholder_mobile'] = $dados_pagamento['cli_telefone'];
			$d['debit']['soft_descriptor'] = 'C-lig';

			$d['debit']['card']['cardholder_name'] = $dados_pagamento['car_nome_impresso'];
			$d['debit']['card']['security_code'] = $dados_pagamento['car_cod_seg'];
			$d['debit']['card']['expiration_month'] = $dados_pagamento['car_validade_mes'];
			$d['debit']['card']['expiration_year'] = $dados_pagamento['car_validade_ano'];
			$d['debit']['card']['number_token'] = $number_token;
		}

		return $this->payUp($d, $dados_pagamento['pag_forma_pagamento']);
	}

	/**
	 * Realiza a transação do pagamento na GETNET
	 *
	 * @return array [status (true, false), msg, req, id_processo]
	 */
	private function payUp(array $payloads, $forma_pag)
	{

		if ($forma_pag == 'credito') {
			$url = $this->url . "v1/payments/credit";
		} else {
			$url = $this->url . "v1/payments/debit";
		}

		$headers = [
			'Content-type: application/json; charset=utf-8',
			'authorization: ' . $this->token_autenticacao,
		];

		$curl = curl_init($url);

		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_ENCODING, "");
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payloads));

		$resp = curl_exec($curl);
		curl_close($curl);

		if (!empty($resp)) {

			$respObj = json_decode($resp);

			if (isset($respObj->status)) {
				if ($respObj->status === 'APPROVED') {
					$ret['status'] = true;
					$ret['msg'] = json_encode($respObj);
					$ret['req'] = json_encode($payloads);
					$ret['id_processo'] = $respObj->payment_id;
				} else {
					$ret['status'] = false;
					$ret['msg'] = json_encode($respObj);
					$ret['req'] = json_encode($payloads);

					$this->logging->logSession('getnet', "Pagamento não processado ({$respObj->status} - {$respObj->reason_message})", "error");
				}
			} else {
				$ret['status'] = false;
				$ret['msg'] = json_encode($respObj);
				$ret['req'] = json_encode($payloads);

				$this->logging->logSession('getnet', "Erro ao processar pagamento no {$forma_pag} ({$respObj->message}).", "error");
			}
		} else {
			$ret['status'] = false;
			$ret['msg'] = "Erro ao processar pagamento";
			$ret['req'] = json_encode($payloads);

			$this->logging->logSession('getnet', "Erro ao conectar na GETNET", "error");
		}

		return $ret;
	}

	/**
	 * Cancela pagamento realizado em demais dias
	 *
	 * @param string id_processo
	 * @param float valor_pago
	 *
	 * @return array [status, msg]
	 */
	public function requestReversalPayment(string $id_pagamento, $valor_pago)
	{

		if (empty($id_pagamento)) {
			$ret['status'] = false;
			$ret['msg'] = "Pagamento informado é inválido";

			$this->logging->logSession('getnet', "ID do pagamento vazio", "warning");

			return $ret;
		}

		$headers = [
			'Content-type: application/json; charset=utf-8',
			'authorization: ' . $this->token_autenticacao,
		];

		$payloads = [
			'payment_id'    => $id_pagamento,
			'cancel_amount' => (int) ($valor_pago * 100),
		];

		$url = $this->url . "v1/payments/cancel/request";

		$curl = curl_init($url);

		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_ENCODING, "");
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payloads));

		$resp = curl_exec($curl);
		curl_close($curl);

		if (!empty($resp)) {

			$respObj = json_decode($resp);

			if (isset($respObj->status)) {

				if ($respObj->status === 'ACCEPTED') {
					$ret['status'] = true;
					$ret['msg'] = json_encode($respObj);
				} else {
					$ret['status'] = false;
					$ret['msg'] = json_encode($respObj);

					$this->logging->logSession('getnet', "Estorno de pagamento não completo ({$respObj->status} - {$respObj->debit_cancel->message})", "info");
				}
			} else {
				$ret['status'] = false;
				$ret['msg'] = json_encode($respObj);

				$this->logging->logSession('getnet', "Erro ao processar o estorno do pagamento ({$respObj->message}).", "error");
			}
		} else {
			$ret['status'] = false;
			$ret['msg'] = "Erro ao processar estorno";

			$this->logging->logSession('getnet', "Erro ao conectar na GETNET", "error");
		}

		return $ret;
	}
}