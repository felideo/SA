<?php
namespace Framework;

class BigBang{
	public function __construct() {
		$url = $this->get_url();

		if(empty($url[0])) {
			require 'modulos/index/controller/index.php';
			$controller = new \Controller\Index();
			$controller->loadModel('index');
			$controller->index();
			return false;
		}

		$url = $this->load_friendly_url($url);
		$amp = $this->identificar_arquivo_metodo_parametro($url);

		if(file_exists($amp['arquivo'])) {
			require $amp['arquivo'];
		} else {
			$this->error();
			exit();
		}

		$this->load_friendly_url(strtolower($amp['classe']), $amp['metodo']);

		$instancia_controller = '\\Controller\\' . ucfirst($amp['classe']);
		$_SESSION['modulo_ativo'] = strtolower($amp['classe']);

		$controller = new $instancia_controller;

		if(!empty($amp['classe'])){
			$controller->loadModel($amp['classe']);
		}

		if(method_exists($controller, $amp['metodo'])) {
			$controller->{$amp['metodo']}(!empty($amp['parametros']) ? $amp['parametros'] : null);
		}

		$controller->index();
	}

	private function load_friendly_url($url){
		if(!isset($url[1])){
			return $url;
		}

	    $pdo = new \PDO('mysql:dbname=' . DB_NAME . ";host=" . DB_HOST, DB_USER, DB_PASS);
	    $sql = $pdo->prepare("SELECT controller, metodo, id_controller FROM `url` WHERE controller = '{$url[0]}' AND url = '{$url[1]}' AND ativo = 1");
		$sql->execute();

		$retorno = $sql->fetchAll(\PDO::FETCH_NUM);

		if(!empty($retorno)){
			$url = [
				$retorno[0][0],
				$retorno[0][1],
				$retorno[0][2],
			];
		}

		return $url;
	}



	private function get_url(){
		$url = isset($_GET['url']) ? $_GET['url'] : null;
		$url = rtrim($url, '/');
		$url = filter_var($url, FILTER_SANITIZE_URL);
		$url = explode('/', $url);

		return $url;
	}

	private function identificar_arquivo_metodo_parametro($url){
		$file = 'modulos';

		foreach($url as $indice => $value) {
			if(file_exists("{$file}/{$value}/controller/{$value}.php") && empty($arquivo)){
				$arquivo = "{$file}/{$value}/controller/{$value}.php";
				$class = $value;
			}else{
				$method[] = $url[$indice];
			}
		}

		if(isset($method)){
			$metodo = $method[0];
			unset($method[0]);
		}

		return [
			'arquivo' 		=> $arquivo,
			'classe' 		=> implode('_', array_map('ucfirst', explode('_', $class))),
			'metodo' 		=> isset($metodo) ? $metodo : null,
			'parametros' 	=> isset($method) ? array_values($method) : null

		];
	}

	private function error() {
		header('location: /error');
	}

}