<?php
namespace Controller;

class Error extends \Framework\Controller {

	private $modulo = [
		'modulo' 	=> 'error',
		'name'		=> 'Error',
		'send'		=> 'Errors'
	];

	function __construct() {
		parent::__construct();
	}

	public function index() {
		$this->view->render('back/cabecalho_rodape', $this->modulo['modulo'] . '/view/error/error');
	}
}