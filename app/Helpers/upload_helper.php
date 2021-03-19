<?php

use App\Libraries\Logging;

/**
 * @param $_FILES $arquivo
 * @param string $path = nome da pasta on de será salvo os arquivos
 * 
 * @return array 
 *         $resp['status'] = true/false
 *         $resp['dir'] = pasta onde será salvo
 *         $resp['nome'] = nome do arquivo
 *         $resp['msg'] = mensagem do erro
 */
function retira_acentos($texto){
    $array1 = array(
        "á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô",
        "õ", "ö", "ú", "ù", "û", "ü", "ç", "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë",
        "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç"
    );

    $array2 = array(
        "a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o",
        "o", "o", "u", "u", "u", "u", "c", "A", "A", "A", "A", "A", "E", "E", "E", "E",
        "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C"
    );

    return str_replace($array1, $array2, $texto);
}

function upload_dir() {
    return APPPATH . 'Assets/Upload/';
}

function salvarArquivo($arquivo = null, $path = null){

    $logging = new Logging();

    if(empty($arquivo)){
        $resp["status"] = false;
        $resp['msg'] = "Nenhum arquivo enviado";

        $logging->logSession('upload', 'Nenhum arquivo enviado', 'warning');

        return $resp;
    }

    $resp = array();

    $upload_path =  upload_dir() . $path ;

    $aqv = explode('.', retira_acentos($arquivo['name']));

    $extensao = '';

    if (isset($aqv[1])) {
        $extensao = $aqv[1];
    }
    
    $upload_name = $aqv[0] . '_' . date('Ymdhis') . '.' . $extensao;

    if(!empty($arquivo['tmp_name'])){

        $up = copy($arquivo['tmp_name'], $upload_path . $upload_name);

        if ($up) {
            $resp['status'] = true;
            $resp['dir'] = $upload_path . $upload_name;	
        } 
        else {
            $resp["status"] = false;
            $resp['msg'] = "Erro ao salvar o documento";

            $logging->logSession('upload', 'Não foi possivel salvar arquivo', 'error');
        }
    }
    else{
        $resp["status"] = false;
        $resp['msg'] = "Erro ao salvar o documento";

        $logging->logSession('upload', 'Não foi possivel salvar arquivo', 'error');
    }

    return $resp;
}