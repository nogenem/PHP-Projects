<?php
/**
 * Configuração geral
 */

// Caminho para a raiz
define( 'ABSPATH', dirname( __FILE__ ) );

// Caminho para a pasta private
define( 'PRIVATE_ABSPATH', ABSPATH .DIRECTORY_SEPARATOR. 'private' );

// Caminho para a pasta de uploads
define( 'UP_ABSPATH',  join(DIRECTORY_SEPARATOR, [ABSPATH, 'views', '_uploads']) );

// URL da home
define( 'HOME_URI', 'http://localhost/_ManagSite/v1/' );

// URL da pasta private
define( 'PRIVATE_URI', HOME_URI . 'private/' );

// Nome do host da base de dados
define( 'HOSTNAME', 'localhost' );

// Nome do DB
define( 'DB_NAME', 'managsite' );

// Usuário do DB
define( 'DB_USER', 'root' );

// Senha do DB
define( 'DB_PASSWORD', '' );

// Charset da conexão PDO
define( 'DB_CHARSET', 'utf8' );

// Se você estiver desenvolvendo, modifique o valor para true
define( 'DEBUG', true );

// Carrega o loader, que vai carregar a aplicação inteira
require_once ABSPATH .DIRECTORY_SEPARATOR. 'loader.php';