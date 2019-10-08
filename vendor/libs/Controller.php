<?php
namespace Libs;

/**
*
*/
abstract class Controller {

	function __construct() {
		$this->view = new View();
	}

	public function loadModel($name) {
		$path = 'models/' . $name . '_model.php';

		if(file_exists($path)) {
			require 'models/' . $name . '_model.php';

			$modelName = '\\Models\\' . $name . '_Model';
			$this->model = new $modelName;
		}
	}

	public function load_external_model($name) {
		$path = 'models/' . $name . '_model.php';

		if(file_exists($path)) {
			require 'models/' . $name . '_model.php';

			$modelName = '\\Models\\' . $name . '_Model';
			return new $modelName;
		}
	}

	public function check_if_exists($id){
		if(empty($this->model->db->select("SELECT id FROM {$this->modulo['modulo']} WHERE id = {$id} AND ativo = 1"))){
			$this->view->alert_js(ucfirst($this->modulo['send']) . ' não existe...', 'erro');
			header('location: /' . $this->modulo['modulo']);
		}
	}
}