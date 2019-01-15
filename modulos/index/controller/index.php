<?php
namespace Controller;

class Index extends \Framework\Controller {

	private $modulo = [
		'modulo' 	=> 'index',
		'name'		=> 'Index',
		'send'		=> 'Index'
	];

	public function index(){
		header('location: /acesso/admin');

		$this->view->render('front/cabecalho_rodape', $this->modulo['modulo'] . '/view/index/index');
	}
}