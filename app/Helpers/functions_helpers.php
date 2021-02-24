<?php
if (!function_exists('numeroFloat')){

    function numeroFloat( $valor ) {
	    $valor = str_replace('.', '', $valor);
	    $valor = str_replace(',', '.', $valor);
	    $valor = str_replace('R$', '', $valor);
	    return $valor;
	}
}

if(!function_exists('numeroMoeda')){
    function numeroMoeda($number, $prefixo=true){
		$formatter = new NumberFormatter('pt_BR', NumberFormatter::CURRENCY);
		if ($prefixo==false){
        	$formatter->setSymbol(NumberFormatter::CURRENCY_SYMBOL, '');
        }			
		return $formatter->formatCurrency($number, 'BRL');
	}
}

if(!function_exists('getDataBR')){
    function getDataBR($data){
		$dataAux = explode(' ', $data);
		if (count($dataAux)==1){
			return implode("/",array_reverse(explode("-",$dataAux[0])));
		}
		elseif (count($dataAux)==2) {
			return (implode("/",array_reverse(explode("-",$dataAux[0])))).' '.$dataAux[1];
		}
		return $data;
	}
}

if(!function_exists('getDataMysql')){
    function getDataMysql($data){
		$dataAux = explode(' ', $data);
		if (count($dataAux)==1){
			return implode("-",array_reverse(explode("/",$dataAux[0])));
		}
		elseif (count($dataAux)==2) {
			return (implode("-",array_reverse(explode("/",$dataAux[0])))).' '.$dataAux[1];
		}
		return $data;
		// return implode("-",array_reverse(explode("/",$data)));
	}
}

if (!function_exists('valid_email')) {
    /**
     * Validate email address
     *
     * @param    string $email
     * @return    bool
     */
    function valid_email($email)
    {
        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}

// LIMPA CARACTER NO CPF, CNPJ E TELEFONE
if (!function_exists('cleanDoc')) :
    /**
     * @param $valor
     * @return mixed|string
     */
    function cleanDoc($valor)
    {
        $valor = trim($valor);
        $valor = str_replace(".", "", $valor);
        $valor = str_replace(",", "", $valor);
        $valor = str_replace("-", "", $valor);
        $valor = str_replace("/", "", $valor);
        $valor = str_replace("(", "", $valor);
        $valor = str_replace(")", "", $valor);
        $valor = str_replace(" ", "", $valor);
        return $valor;
    }
endif;

// VALIDA CPF
if (!function_exists('validateCPF')) :
    /**
     * Valida CPF
     * @param null $cpf
     * @return bool
     */
    function validateCPF($cpf = null)
    {
        // Verifica se um número foi informado
        if (empty($cpf)) {
            return false;
        }

        // Elimina possivel mascara
        $cpf = clean($cpf);

        // Verifica se o numero de digitos informados é igual a 11
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verifica se nenhuma das sequências invalidas abaixo
        // foi digitada. Caso afirmativo, retorna falso
        else if (
            $cpf == '00000000000' ||
            $cpf == '11111111111' ||
            $cpf == '22222222222' ||
            $cpf == '33333333333' ||
            $cpf == '44444444444' ||
            $cpf == '55555555555' ||
            $cpf == '66666666666' ||
            $cpf == '77777777777' ||
            $cpf == '88888888888' ||
            $cpf == '99999999999'
        ) {
            return false;

            // Calcula os digitos verificadores para verificar se o
            // CPF é válido
        } else {

            for ($t = 9; $t < 11; $t++) {

                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf{
                        $c} * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf{
                    $c} != $d) {
                    return false;
                }
            }

            return true;
        }
    }
endif;

// CRIPTOGRAFIA
if (!function_exists('Cryptography')) :
    /**
     * Criptografia em strings
     * @param $action
     * @param $string
     * @return bool|string
     */
    function Cryptography($action, $string)
    {
        $output = false;

        $encrypt_method = "AES-256-CBC";
        $secret_key = 'Recurring_Payments*@*';
        $secret_iv = 'Recurring_Payments*@07';

        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a
        // warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        if ($action == 'encrypt') :
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);

        else :
            if ($action == 'decrypt') :
                $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
            endif;

        endif;

        return $output;
    }
endif;

// CONFIGURA OS MESES DE ACORDO COM A NUMERAÇÃO PASSADA
if (!function_exists('Month')) :
    /**
     * @param $m
     * @return string
     */
    function Month($m)
    {
        switch ($m) {
            case 1:
                $month = 'Janeiro';
                break;
            case 2:
                $month = 'Fevereiro';
                break;
            case 3:
                $month = 'Março';
                break;
            case 4:
                $month = 'Abril';
                break;
            case 5:
                $month = 'Maio';
                break;
            case 6:
                $month = 'Junho';
                break;
            case 7:
                $month = 'Julho';
                break;
            case 8:
                $month = 'Agosto';
                break;
            case 9:
                $month = 'Setembro';
                break;
            case 10:
                $month = 'Outubro';
                break;
            case 11:
                $month = 'Novembro';
                break;
            case 12:
                $month = 'Dezembro';
                break;
            default:
                $month = 'Erro';
        }

        return $month;
    }
endif;

if (!function_exists('mb_str_pad')) :
    function mb_str_pad($input, $pad_length, $pad_string = ' ', $pad_type = STR_PAD_RIGHT, $encoding = "UTF-8")
    {
        $diff = strlen($input) - mb_strlen($input, $encoding);
        return str_pad($input, $pad_length + $diff, $pad_string, $pad_type);
    }
endif;

if (!function_exists('ocultarTel')) :
    function ocultarTel($telefone)
    { //Função para mascarar telefone com ****

        $tel = substr_replace($telefone, '****', 3, 8);
        $tel .= substr($telefone, 7);

        return $tel;
    }
endif;

if (!function_exists('passGenerator')) :
    function passGenerator($tamanho, $maiusculas, $minusculas, $numeros, $simbolos)
    {
        $ma = "ABCDEFGHIJKLMNOPQRSTUVYXWZ"; // $ma contem as letras maiúsculas
        $mi = "abcdefghijklmnopqrstuvyxwz"; // $mi contem as letras minusculas
        $nu = "0123456789"; // $nu contem os números
        $si = "!$+&*"; // $si contem os símbolos

        $senha = '';

        if ($maiusculas) {
            // se $maiusculas for "true", a variável $ma é embaralhada e adicionada para a variável $senha
            $senha .= str_shuffle($ma);
        }

        if ($minusculas) {
            // se $minusculas for "true", a variável $mi é embaralhada e adicionada para a variável $senha
            $senha .= str_shuffle($mi);
        }

        if ($numeros) {
            // se $numeros for "true", a variável $nu é embaralhada e adicionada para a variável $senha
            $senha .= str_shuffle($nu);
        }

        if ($simbolos) {
            // se $simbolos for "true", a variável $si é embaralhada e adicionada para a variável $senha
            $senha .= str_shuffle($si);
        }

        // retorna a senha embaralhada com "str_shuffle" com o tamanho definido pela variável $tamanho
        return substr(str_shuffle($senha), 0, $tamanho);
    }
endif;

if (!function_exists('mascaraDocumento')) :
    function mascaraDocumento($cpfCnpj)
    {
        // Deixando somente números
        $cpfCnpj = preg_replace('/[^0-9]/', '', $cpfCnpj);

        $tipoDoc = strlen($cpfCnpj);
        $tamanhoString = 11;

        // Verificando se é um cpf, nao vai funcionar se tiver mais que 9 filiais, estou procurando esse padrão 0001 do cnpj
        if ($tipoDoc <= 11 && substr($cpfCnpj, -6, -3) != '000') {
            $mask = '###.###.###-##'; // mascara de cpf
        } else {
            $mask = '##.###.###/####-##'; // mascara cnpj
            $tamanhoString = 14;
        }

        // Completando com zero a esquerda
        $cpfCnpj = str_pad($cpfCnpj, $tamanhoString, '0', STR_PAD_LEFT);
        // Vamos substituir de acordo com a mascara
        foreach (str_split($cpfCnpj) as $numero) {
            $mask = preg_replace('/\#/', $numero, $mask, 1);
        }

        return $mask;
    }
endif;

if (!function_exists('retira_acentos')) :
    function retira_acentos($texto)
    {
        $array1 = [
            "á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô",
            "õ", "ö", "ú", "ù", "û", "ü", "ç", "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë",
            "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç",
        ];

        $array2 = [
            "a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o",
            "o", "o", "u", "u", "u", "u", "c", "A", "A", "A", "A", "A", "E", "E", "E", "E",
            "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C",
        ];

        return str_replace($array1, $array2, $texto);
    }
endif;

if (!function_exists('retira_caracteres_especiais')) :
    function retira_caracteres_especiais($texto)
    {
        $array1 = ["%", "!", "@", "#", "&", "|", "*", "(", ")", "º", "?"];
        return str_replace($array1, ' ', $texto);
    }
endif;

if (!function_exists('convertCharset')) :
    function convertCharset($str)
    {
        if (mb_detect_encoding($str, 'UTF-8', true) === false) {
            $str = utf8_encode($str);
        }

        return $str;
    }
endif;

if (!function_exists('somenteNumeros')) :
    function somenteNumeros($number)
    {
        $number_clean = preg_replace("/[^0-9]*/", "", $number);
        return $number_clean;
    }
endif;

if (!function_exists('mascara_telefone')) :
    /**
     * Adiciona máscara telefonica para um número
     * @access public
     * @param  string
     * @return O mesmo número informado caso não atenda os requisitos da máscara ou uma string com a máscara
     */
    function mascara_telefone($numero)
    {

        $numero = trim($numero);

        if ($numero == '') {
            return $numero;
        }

        return preg_replace('/([0-9]{2})([0-9]{4,5})([0-9]{4})/', "($1) $2-$3", $numero);
    }
endif;

if (!function_exists('telefone')) :
    function telefone($numero)
    {
        $ddd_cliente = substr($numero, 0, 2);
        $numero_cliente = substr($numero, 2);

        return ["ddd" => $ddd_cliente, "tel" => $numero_cliente];
    }
endif;

if (!function_exists('mascara_cep')) :
    function mascara_cep($numero)
    {
        $numero = trim($numero);

        if ($numero == '') {
            return $numero;
        }

        return preg_replace('/([0-9]{5})([0-9]{3})/', "$1-$2", $numero);
    }
endif;

// retorna nome e sobrenome
if (!function_exists('nome_sobrenome')) :
    function nome_sobrenome($nome, $meio = true)
    {
        $nome = trim(strtolower($nome));
        if ($meio)
            $nome = str_replace([' da ', ' dos ', ' de '], ' ', $nome);
        $nome = preg_replace('/( ){2,}/', '$1', $nome);
        $tmp = explode(' ', $nome);
        if (count($tmp) > 1) {
            $tmp2 = $tmp[0] . ' ';
            if ($meio && count($tmp) > 2)
                $tmp2 .= $tmp[count($tmp) - 2] . ' ';
            $tmp2 .= $tmp[count($tmp) - 1];
            return ucwords($tmp2);
        } else
            return ucwords($nome);
    }
endif;


// valida cnpj
if (!function_exists('validaCnpj')) :
    function validaCnpj($cnpj)
    {
        if (empty($cnpj))
            return false;
        $j = 0;
        for ($i = 0; $i < (strlen($cnpj)); $i++) {
            if (is_numeric($cnpj[$i])) {
                $num[$j] = $cnpj[$i];
                $j++;
            }
        }
        if (count($num) != 14)
            return false;
        if ($num[0] == 0 && $num[1] == 0 && $num[2] == 0 && $num[3] == 0 && $num[4] == 0 && $num[5] == 0 && $num[6] == 0 && $num[7] == 0 && $num[8] == 0 && $num[9] == 0 && $num[10] == 0 && $num[11] == 0)
            $isCnpjValid = false;
        else {
            $j = 5;
            for ($i = 0; $i < 4; $i++) {
                $multiplica[$i] = $num[$i] * $j;
                $j--;
            }
            $soma = array_sum($multiplica);
            $j = 9;
            for ($i = 4; $i < 12; $i++) {
                $multiplica[$i] = $num[$i] * $j;
                $j--;
            }
            $soma = array_sum($multiplica);
            $resto = $soma % 11;
            if ($resto < 2)
                $dg = 0;
            else $dg = 11 - $resto;
            if ($dg != $num[12])
                $isCnpjValid = false;
        }

        if (!isset($isCnpjValid)) {
            $j = 6;
            for ($i = 0; $i < 5; $i++) {
                $multiplica[$i] = $num[$i] * $j;
                $j--;
            }
            $soma = array_sum($multiplica);
            $j = 9;
            for ($i = 5; $i < 13; $i++) {
                $multiplica[$i] = $num[$i] * $j;
                $j--;
            }
            $soma = array_sum($multiplica);
            $resto = $soma % 11;
            if ($resto < 2)
                $dg = 0;
            else $dg = 11 - $resto;
            if ($dg != $num[13])
                $isCnpjValid = false;
            else $isCnpjValid = true;
        }
        return $isCnpjValid;
    }
endif;

if (!function_exists('mascara_doc')) :
    function mascara_doc($doc)
    {
        if (strlen($doc) == 11) :
            return substr_replace($doc, '******', 3, 6);
        //return '***'.substr($doc, 3, -5).'*****';
        //return strrev(preg_replace('/\d/', '*',  strrev($doc), 8));
        endif;

        if (strlen($doc) > 11) :
            return substr_replace($doc, '*********', 3, 9);
        //return '**'.substr($doc, 2, -6).'******';
        //return strrev(preg_replace('/\d/', '*',  strrev($doc), 10));
        endif;
    }
endif;

if (!function_exists('mascara_cartao')) :
    function mascara_cartao($card)
    {
        return substr_replace($card, '********', 4, 8);
        //return '************'.substr($card, 12);
        //return strrev(preg_replace('/\d/', '*',  strrev($card), 12));
    }
endif;

if (!function_exists('getPalavrasAbreviadas')) :
    function getPalavrasAbreviadas()
    {

        return [
            "Avenida"      => "av",
            "av."          => "av",
            "rua"          => "r",
            "praça"        => "pc",
            "praca"        => "pc",
            "senhor"       => "sr",
            "senhora"      => "sra",
            "doutor"       => "dr",
            "doutora"      => "dra",
            "padre"        => "pe",
            "santa"        => "sta",
            "santo"        => "sto",
            "pastor"       => "pr",
            "presidente"   => "pres",
            "professor"    => "prof",
            "professora"   => "prof",
            "major"        => "maj",
            "general"      => "gal",
            "jardim"       => "j",
            "jdim"         => "j",
            "vila"         => "v",
            "santa"        => "sta",
            "sao"          => "s",
            "são"          => "s",
            "residencial"  => "res",
            "res."         => "res",
            "parque."      => "pq",
            "pq."          => "pq",
            "nova"         => "n",
            "novo"         => "n",
            "nosso"        => "n",
            "distrito"     => "dis",
            "condominio"   => "cond",
            "conjunto"     => "cj",
            "habitacional" => "hab",
            "cidade"       => "cid",
            "chacara"      => "chac",
            "travessa"     => "trav",
            "fundos"       => "fd",
            "fundo"        => "fd",
            "apartamento"  => "apto",
            "apartament"   => "apto",
            "engenheiro"   => "eng",
            "casa"         => "cs",
            "comendador"   => "com",

        ];
    }
endif;

if (!function_exists('abreviar')) :
    function abreviar($string)
    {

        $palavrasAbreviadas = getPalavrasAbreviadas();

        foreach ($palavrasAbreviadas as $palavra => $abrev) {
            $string = str_ireplace($palavra, $abrev, strtolower($string));
        }

        return mb_strtoupper($string);
    }
endif;

if (!function_exists('tratarEndereco')) :
    function tratarEndereco($name, $tamanho)
    {

        if (strlen($name) <= $tamanho) {
            return strtoupper(retira_acentos(retira_caracteres_especiais($name)));
        }

        $name = preg_replace('(\([^\)]+\))', '', $name);

        $split_name1 = explode(" ", $name);

        $podeRemover = ['de', 'da', 'do', 'das', 'dos'];

        // remove a primeira palavra se for alguma do array
        $ruas = ['rua', 'r', 'r.', 'avenida', 'av', 'av.', 'alameda'];
        if (in_array(strtolower($split_name1[0]), $ruas)) {
            unset($split_name1[0]);
        }

        // se o ultima palavra for um algorismo romano pode manter
        $manter = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XV', 'XIX', 'XX', 'XXX', 'XL', 'L', 'LLL', 'LX', 'LXX', 'LXXX', 'XC'];
        $stringFinal = null;
        if (in_array(strtoupper($split_name1[count($split_name1) - 1]), $manter)) {
            $stringFinal = $split_name1[count($split_name1) - 1];
            unset($split_name1[count($split_name1) - 1]);
        }

        if (is_numeric($split_name1[count($split_name1) - 1])) {
            $stringFinal = $split_name1[count($split_name1) - 1];
            unset($split_name1[count($split_name1) - 1]);
        }

        $name = implode(" ", $split_name1);
        $name = trim($name);

        //como os nomes são separados por espaço em branco então vamos criar o array
        //a partir dos espaços
        $split_name = explode(" ", $name);
        //so vamos abreviar o nome se
        //ele tiver pelo menos 2 sobrenomes
        if (count($split_name) > 2) {

            //esse for inicia a partir da segunda
            // posição do array para o
            //primeiro nome ser desconsiderado
            $limite = count($split_name) - 1;
            for ($i = 1; $i < $limite; $i++) {

                //claro que como existem dos|de|da|das
                //(Cristina DOS Santos) podemos
                //omitir ou exibir sem abrevirar
                //essas preposições, aqui no
                //caso eu as mantenho sem alteração

                if (in_array(strtolower($split_name[$i]), $podeRemover)) {
                    unset($split_name[$i]);
                } else {
                    $split_name[$i] = substr($split_name[$i], 0, 1) . "";
                }
            }
        }

        //aqui será impresso o nome resultante com a junção
        //do array em favor de se obter
        //return abreviar(implode(" ",$split_name));
        $novo = implode(" ", $split_name);
        if (!empty($stringFinal)) {
            $novo .= ' ' . $stringFinal;
        }
        $textoNovo = abreviar(retira_acentos(retira_caracteres_especiais($novo)));

        // se for maior q o permitido quebra a string
        if (strlen($textoNovo) > $tamanho) {
            $textoNovo = substr($textoNovo, 0, $tamanho);
        }

        return $textoNovo;
    }
endif;

if (!function_exists('tratarEndereco2')) :
    function tratarEndereco2($name, $numero, $complemento, $tamanho)
    {
        $name = retira_acentos($name); // nao pode ter acentos para aplicar o regex
        $complemento = abreviar($complemento);
        $end = $name . ',' . $numero;
        if ($complemento) {
            $end .= '-' . $complemento;
        }

        if (strlen($end) <= $tamanho) {
            return strtoupper(retira_caracteres_especiais($end));
        }

        //>>>>>>>>remover range de cep do edm e lie
        // $name = preg_replace('(\([^\)]+\))', '', $name); // remove tudo nos parenteses
        $name = preg_replace('(\((([\d])|(ate))[^\)]+\))', '', $name); //(1890 ate 7888), (115 ao 200), (9000 par), ...
        $name = preg_replace('(- de.*)', '', $name); // - de 100 a 200
        $name = preg_replace('(- ate.*)', '', $name); // - ate 900

        $end = $name . ',' . $numero;
        if ($complemento) {
            $end .= '-' . $complemento;
        }
        if (strlen($end) <= $tamanho) {
            return strtoupper(retira_caracteres_especiais($end));
        }


        $name = strtoupper(retira_caracteres_especiais($name)); // remover caracteres especiais

        //>>>>>>>>remover preposiçoes
        $podeRemover = ['de', 'da', 'do', 'das', 'dos'];

        $split_name = explode(" ", $name);
        $limite = count($split_name) - 1;
        for ($i = 1; $i < $limite; $i++) {

            if (in_array(strtolower($split_name[$i]), $podeRemover)) {
                unset($split_name[$i]);
            }
        }

        $name = implode(" ", $split_name);
        $end = $name . ',' . $numero;
        if ($complemento) {
            $end .= '-' . $complemento;
        }
        if (strlen($end) <= $tamanho) {
            return $end;
        }

        //>>>>>>>>abreviar algumas palavras
        $name = abreviar(retira_acentos(retira_caracteres_especiais($name)));
        $end = $name . ',' . $numero;
        if ($complemento) {
            $end .= '-' . $complemento;
        }
        if (strlen($end) <= $tamanho) {
            return $end;
        }

        //>>>>>>>>abreviar nomes do meio
        // se o ultima palavra for um algorismo romano pode manter
        $split_name1 = explode(" ", $name);
        $manter = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XV', 'XIX', 'XX', 'XXX', 'XL', 'L', 'LLL', 'LX', 'LXX', 'LXXX', 'XC'];
        $stringFinal = null;
        if (in_array(strtoupper($split_name1[count($split_name1) - 1]), $manter)) {
            $stringFinal = $split_name1[count($split_name1) - 1];
            unset($split_name1[count($split_name1) - 1]);
        }

        // se o ultima palavra for um numero pode manter
        if (is_numeric($split_name1[count($split_name1) - 1])) {
            $stringFinal = $split_name1[count($split_name1) - 1];
            unset($split_name1[count($split_name1) - 1]);
        }

        $name = implode(" ", $split_name1);
        $name = trim($name);

        $palavrasAbreviadas = getPalavrasAbreviadas();
        $primeiro_nome = '';
        $split_name = explode(" ", $name);
        $limite = count($split_name) - 1; // não altero o ultimo nome
        if (count($split_name) > 2) { // se o nome tiver + d 2 palavras
            for ($i = 0; $i < $limite; $i++) {

                /*
				if (in_array(strtolower($split_name[$i]), $palavrasAbreviadas)){
					continue;
				}*/

                // procuro uma palavra com mais de 3 digitos para manter como primeiro nome
                if (strlen($split_name[$i]) > 3 && $primeiro_nome == '' && !in_array(strtolower($split_name[$i]), $palavrasAbreviadas)) {
                    //echo "primeiro nome = ".$split_name[$i].'<br>';
                    $primeiro_nome = $split_name[$i];
                }


                // se não for o primeiro nome eu abrevio para primeira letra
                if ($split_name[$i] != $primeiro_nome && !in_array(strtolower($split_name[$i]), $palavrasAbreviadas)) {
                    //echo 'vou abreviar = '.$split_name[$i].'<br>';
                    $split_name[$i] = substr($split_name[$i], 0, 1) . "";
                }
            }

            // junto tudo de novo para formar o nome abreviado
            $name = implode(" ", $split_name);
            if (!empty($stringFinal)) {
                $name .= ' ' . $stringFinal;
            }

            $end = $name . ',' . $numero;
            if ($complemento) {
                $end .= '-' . $complemento;
            }
            if (strlen($end) <= $tamanho) {
                return $end;
            }
        }

        if ($complemento) {
            $complemento = preg_replace('(LT.)', 'L', $complemento);
            $complemento = preg_replace('(QD.)', 'Q', $complemento);
            $end = $name . ',' . $numero . '-' . $complemento;
            if (strlen($end) <= $tamanho) {
                return strtoupper(retira_caracteres_especiais($end));
            }
        }

        // abreviar o primeiro nome
        if (!empty($primeiro_nome)) {
            $primeiraLetra = substr($primeiro_nome, 0, 1) . "";
            $end = str_replace($primeiro_nome, $primeiraLetra, $end);
            if (strlen($end) <= $tamanho) {
                return $end;
            }
        }

        return $end;
    }
endif;

//pra formatar o cpf assim ***.***.888-** ou 888.***.***-**
