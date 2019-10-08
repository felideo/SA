<?php
namespace Util;

class Permission {
	public static function check($modulo, $permissao) {
		if($_SESSION['usuario']['super_admin'] != 1){
			if(empty($_SESSION['permissoes'][$modulo]) || empty($_SESSION['permissoes'][$modulo][$permissao])){
				$view = new \Libs\View();
				$view->alert_js('Vocã não possui permissão para efetuar esta ação...', 'erro');
				header('location: ' . $_SERVER['HTTP_REFERER']);
				exit;
			}
		}
	}

	public static function check_user_permission($modulo, $permissao) {
		if($_SESSION['usuario']['super_admin'] != 1){
			if(empty($_SESSION['permissoes'][$modulo]) || empty($_SESSION['permissoes'][$modulo][$permissao])){
				return false;
			}
		}

		return true;
	}
}