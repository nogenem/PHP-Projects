<?php
/**
 * Verifica chaves de arrays
 *
 * Verifica se a chave existe no array e se ela tem algum valor.
 * Obs.: Essa função está no escopo global, pois, vamos precisar muito da mesma.
 *
 * @param array  $array O array
 * @param string $key   A chave do array
 * @return string|null  O valor da chave do array ou nulo
 */
function chk_array($array, $key) {
	if ( isset( $array[ $key ] ) && !empty( $array[ $key ] ) ) 
		return $array[ $key ];

	return null;
} // chk_array

/**
 * Função para carregar automaticamente todas as classes padrão
 * Ver: http://php.net/manual/pt_BR/function.autoload.php.
 * Nossas classes estão na pasta classes/.
 * O nome do arquivo deverá ser class-NomeDaClasse.php.
 * Por exemplo: para a classe ManagSiteMVC, o arquivo vai chamar class-ManagSiteMVC.php
 */
function classAutoLoader($class_name) {
	$file = file_build_path(PRIVATE_ABSPATH, 'classes', "class-{$class_name}.php");
	
	if ( !file_exists( $file ) ) {
		require_once file_build_path(PRIVATE_ABSPATH, 'includes', '404.php');
		return;
	}

    require_once $file;
} // classAutoLoader
spl_autoload_register('classAutoLoader');

/* =================== My Functions ======================= */

function file_build_path() {
    return join(DIRECTORY_SEPARATOR, func_get_args());
}

function alert($msg){
	echo '<script> alert(', $msg, '); </script>';
}

function print_array($array){
	echo '<pre>';
	if(is_null($array))
		echo 'NULL';
	elseif(is_array($array) or is_object($array))
		print_r($array);
	else
		echo $array;
	echo '</pre>';
}

function custom_setcookie($name, $value, $days){
	$domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;

	if(!is_numeric($days)) $days = 0; //days tem q ser um numero
	if($days < 0) //numero negativo
		$date = "-{$days} days";
	else
		$date = "+{$days} days";
	
	setcookie($name, $value, strtotime( $date ), '/', $domain, false);
}

function set_array_cookie($array_name, $array, $days=30){
	foreach ($array as $key => $value) {
		if(is_array($value)) $value = serialize($value);
		custom_setcookie("{$array_name}[{$key}]", $value, $days);
	}
}

function delete_array_cookie($array_name, $array){
	$keys = array_keys($array);
	foreach ($keys as $key) 
		custom_setcookie("{$array_name}[{$key}]", '', -1);
}

function format_date($date, $actual_date_format, $new_date_format){
	if(in_array($date, [0,NULL,'0000-00-00'])) return '';
	$new_date = DateTime::createFromFormat($actual_date_format, $date);//'d/m/Y'
	if(!is_object($new_date)) return '';
	return $new_date->format($new_date_format);//'Y-m-d'
}

/**
 * Formata a msg para ser mostrada nas view
 *
 * @param String $type 		tipo de msg, [danger, success]
 * @param String $text 		texto que sera exibido
 */
function format_msg($type, $text){
	if(!in_array($type, ['danger', 'success'])){
		return 'ERROR: format_msg => Param $type should be "danger" or "success"!';
	}
	
	$msg = '<p class="bg-%s" style="padding: 10px;"><b>%s</b></p>';
	return sprintf($msg, $type, $text);
}

/**
 * Formata a palavra/frase passada dependendo do 'type' passado
 *
 * @param String $phrase 	
 * @param String $type 		tipo de formataçao ['first-up', 'undo?', 'which']
 */
function correct_phrase($phrase, $type='first-up'){
	if($type==='which')
		return rtrim(str_replace(' ', '_', strtolower($phrase)), 's');
	elseif($type==='to-url') 
		return strtolower(str_replace('_', '-', $phrase.'s'));
	elseif($type==='remove-s')
		return rtrim($phrase, 's');

	return ucwords(str_replace('_', ' ', $phrase)); //else, padrao = 'first-up'
}

function format_midia_edit_url($midia){
	$url = HOME_URI .'%s/index/edit/%s/%d';
	$midia_which = correct_phrase($midia['midia_which'], 'to-url');

	return sprintf($url, $midia_which, $midia['midia_tab'], $midia['midia_id']);
}

function get_article($word){
	return preg_match('/^[AaEeIiOoUu]/', $word) ? 'an' : 'a';
}

/**
 *	Uma checagem basica se uma data é valida
 */
function is_valid_date($date){ 
	return !in_array($date, [NULL,0,'0000-00-00','',[]]);
}

/**
 *	usa Curl para pegar a page html
 */
function get_data($url) {
	$ch = curl_init();
	$timeout = 0;
	
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

	$data = curl_exec($ch);

	if (curl_errno($ch)) 
		print_array("Error: " . curl_error($ch)); 

	curl_close($ch);
	return $data;
}

/**
 *
 */
function correct_url($url){
	if(strpos($url, 'http') === false)
		return 'http://' .$url;

	return $url;
}

/**
 *
 */
function get_host($url){
	$tmp = parse_url($url);
	if(!chk_array($tmp, 'host'))
		return $url;
	return $tmp['host'];
}