<?php
// Evita que usuários acesse este arquivo diretamente
if (!defined('ABSPATH')) exit;
 
// Inicia a sessão
session_start();

// Verifica o modo para debugar
if (!defined('DEBUG') || DEBUG === false ) {

	// Esconde todos os erros
	error_reporting(0);
	ini_set("display_errors", 0); 
	
} else {

	// Mostra todos os erros
	error_reporting(E_ALL);
	ini_set("display_errors", 1); 
	
}

// Twig Template
require_once join(DIRECTORY_SEPARATOR, ['vendor', 'autoload.php']);

// Funções globais
require_once join(DIRECTORY_SEPARATOR, [PRIVATE_ABSPATH, 'functions', 'global-functions.php']);

// Carrega a aplicação
$anisite_mvc = new ManagSiteMVC();

