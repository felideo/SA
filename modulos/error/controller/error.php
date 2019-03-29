<?php
namespace Controller;

class Error extends \Framework\Controller {

	protected $modulo = [
		'modulo' 	=> 'error',
		'name'		=> 'Error',
		'send'		=> 'Error'
	];

	private $pagina_errada = '';

	public function index() {
		$this->view->assign('pagina_errada', $this->pagina_errada);
		$this->view->render('back/cabecalho_rodape', $this->modulo['modulo'] . '/view/error/error');
	}

	public function setPaginaErrada($pagina_errada){
		$this->pagina_errada = $pagina_errada;
	}
}