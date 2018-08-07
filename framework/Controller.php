<?php
namespace Framework;

abstract class Controller {
	function __construct() {
		\Libs\Session::init();
		$this->view = new View();
	}

	public function get_controller($controller, $subcontroller = null){
		$subcontroller = (!empty($subcontroller) ? $subcontroller : $controller);
		$file          = "modulos/{$controller}/controller/{$subcontroller}.php";


		if(!file_exists($file)) {
			debug2('controler não existe, se fudeu, aproveita e coloca uma exception aqui');
		}

		$instancia_controller = '\\Controller\\' . ucfirst($subcontroller);
		require_once $file;
		return new $instancia_controller;
	}

	public function get_model($model, $submodel = null){
		$submodel = (!empty($submodel) ? $submodel : $model);
		$file          = "modulos/{$model}/model/{$submodel}.php";

		if(!file_exists($file)) {
			debug2('controler não existe, se fudeu, aproveita e coloca uma exception aqui');
		}

		$instancia_model = '\\Model\\' . ucfirst($submodel);

		require_once $file;
		return new $instancia_model;
	}









	public function loadModel($model) {
		$path = strtolower("modulos/{$model}/model/{$model}.php");

		if(file_exists($path)) {
			require $path;

			$modelName = '\\Model\\' . ucfirst($model);
			$this->model = new $modelName;
		}else{
			$this->model = new GenericModel();
		}
	}

	public function load_external_model($name) {
		$path = 'modulos/' . $name . '/model/' .  $name . '.php';

		if(file_exists($path)) {
			require $path;

			$modelName = '\\Model\\' . ucfirst($name);
			return new $modelName;
		}
	}

	public function check_if_exists($id){
		if(empty($this->model->db->select("SELECT id FROM {$this->modulo['modulo']} WHERE id = {$id} AND ativo = 1"))){
			$this->view->alert_js(ucfirst($this->modulo['send']) . ' não existe...', 'erro');
			header('location: /' . $this->modulo['modulo']);
			exit;
		}
	}

	public function print_sessao(){
		debug2($_SESSION);
		exit;
	}

	public function limpar_sessao(){
		session_unset();
		session_destroy();
		debug2('Sessão limpa! \o/');
		exit;
	}

	public function set_sessao($parametros){
		$_SESSION[$parametros[0]] = $parametros[1];
		debug2('$_SESSION[' . $parametros[0] . '] = ' . $parametros[1]);
		exit;
	}
}