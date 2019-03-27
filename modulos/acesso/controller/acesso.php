<?php
namespace Controller;

class Acesso extends \Framework\Controller {

	protected $modulo = [
		'modulo' 	=> 'acesso',
		'name'		=> 'Acesso',
		'send'		=> 'Acesso'
	];

	public function __construct() {
		parent::__construct();
		$this->view->modulo = $this->modulo;
	}

	public function aluno($parametros = null){
		$aluno = $this->get_model('aluno')->carregar_aluno(['rgm' => $parametros[0]]);

		debug2($aluno);

		$this->view->assign('aluno', $aluno);
		$this->view->render('back/cabecalho_rodape', $this->modulo['modulo'] . '/view/back/aluno/completar_cadastro/completar_cadastro');
	}

	public function login(){
		$this->view->render('front/cabecalho_rodape', 'front/acesso/login/login');
	}

	public function admin($parametros){
		$this->view->render('back/cabecalho_rodape', $this->modulo['modulo'] . '/view/back/login');
	}

	public function cadastro(){
		$this->view->render('front/cabecalho_rodape', 'front/acesso/cadastro/cadastro');
	}

	public function run() {
		$retorno = $this->model->run(carregar_variavel('acesso'));

		if($retorno == true){
			$this->view->alert_js('Login efetuado com sucesso!', 'sucesso');
			header('location: ../index');
		}else{
			$this->view->alert_js('Usúario ou Senha inválido...', 'erro');
			header('location: ../login');
		}
	}

	public function run_back(){
		$retorno = $this->model->run_back(carregar_variavel('acesso'));

		if($retorno == true){
			$this->get_controller('contador_visita')->contar_visita('Acessou o Sistema');
			header('location: ../painel_controle');
		}else{
			$this->view->alert_js('Usúario ou Senha inválido...', 'erro');
			header('location: ' . $_SERVER['HTTP_REFERER']);
		}
	}

	public function usuario_create(){

		$usuario = carregar_variavel('usuario');

		$insert_db = [
			'email'      => $usuario['email'],
			'senha'      => $usuario['senha'],
			'hierarquia' => 2
		];

		$retorno_usuario = $this->model->insert('usuario', $insert_db);

		if($retorno_usuario['status'] == 1 && !empty($retorno_usuario['id'])){
			unset($insert_db);

			$insert_db = [
				'id_usuario'  => $retorno_usuario['id'],
				'pronome'     => $usuario['pronome'],
				'nome'        => $usuario['nome'],
				'sobrenome'   => $usuario['sobrenome'],
				'instituicao' => $usuario['instituicao'],
				'atuacao'     => $usuario['atuacao'],
    			'lattes' 	  => $usuario['lattes'],
    			'grau'   	  => $usuario['grau'],
			];

			$retorno_pessoa = $this->model->insert('pessoa', $insert_db);
		}

		if($retorno_usuario['status'] == 1 && $retorno_pessoa['status'] == 1 && !empty($retorno_usuario) && !empty($retorno_pessoa)){
			$acesso = [
				'email' => $usuario['email'],
				'senha' =>	$usuario['senha']
			];

			$this->view->alert_js('Cadastro efetuado com sucesso!!!', 'sucesso');

			$retorno = $this->model->run($acesso);
		} else {
			$this->view->alert_js('Ocorreu um erro ao efetuar o cadastro, por favor tente novamente...', 'erro');
		}

		header('location: /index');
	}
}

