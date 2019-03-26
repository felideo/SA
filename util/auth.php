<?php
namespace Util;

class Auth {
	public static function handLeLoggin() {
		$url = \Util\URL::modulo_url($_GET['url']);

		if(isset($_SESSION) && !empty($_SESSION['logado'])){
			if($url[0] == 'login'){
				$logged = $_SESSION['logado'];
				header('location: ' . URL . 'painel_controle');
				exit;
			}
		} else {
			if($url[0] == 'login'){
				$logged = false;
			} else {
				$logged = false;
				header('location: ' . $_SERVER['HTTP_REFERER']);
				exit;
			}
		}
	}
}