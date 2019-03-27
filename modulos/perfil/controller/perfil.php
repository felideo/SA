<?php
namespace Controller;

class Perfil extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' 	=> 'perfil',
		'name'		=> 'Perfil',
		'send'		=> 'Perfil'
	];

	public function index(){
		\Libs\Auth::handLeLoggin();

		$cadastro = $this->model->carregar_dados_usuario($_SESSION['usuario']['id']);
		$this->view->assign('cadastro', $cadastro);

		$this->view->render('back/cabecalho_rodape_sidebar', 'perfil/view/perfil');
	}

}