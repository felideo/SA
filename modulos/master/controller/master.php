<?php
namespace Controller;

use Libs;

class Master extends \Framework\Controller {
	function index(){
	}

	function logout() {
		\Libs\Session::destroy();
		header('location: /index');
		exit;
	}

	function limpar_alertas_ajax(){
		unset($_SESSION['alertas']);
		echo json_encode("Alertas limpos");
		exit;
	}

	function limpar_notificacoes_ajax(){
		unset($_SESSION['notificacoes']);
		echo json_encode("Notificacoes limpas");
		exit;
	}
}