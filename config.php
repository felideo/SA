<?php
// Configuração do Fuso Horário
date_default_timezone_set('America/Sao_Paulo');

$protocolo = !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
$url       = $protocolo . $_SERVER['HTTP_HOST'] . '/';

// Sempre use barra (/) no final das URLs
define('URL', $url);

// Configuração com Banco de Dados
define('DB_TYPE', 'mysql');
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'sa');
define('DB_USER', 'felideo');
define('DB_PASS', 'c6270d0637e0a99ad14a9a459c35f9f0');

define('DEVELOPER', true);
define('PREVENT_CACHE', true);

define('APP_NAME', 'Sistema de Aula');

define('DEPLOY_KEY', 'ce85b99cc46752fffee35cab9a7b0278abb4c2d2055cff685af4912c49490f8d');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (function_exists('xdebug_disable')){
	xdebug_disable();
}