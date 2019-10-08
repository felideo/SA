<?php
namespace Libs;

/**
* classe Bootstrap
*/
class bootstrap {
	function __construct() {
		$url = $this->get_url();

		if(empty($url[0])) {
			require 'controllers/index/index.php';
			$controller = new \Controllers\Index();
			$controller->loadModel('index');
			$controller->index();
			return false;
		}

		$amp = $this->identificar_arquivo_metoro_parametro($url);

		if(file_exists($amp['arquivo'])) {
			require $amp['arquivo'];
		} else {
			$this->error();
			exit();
		}

		$instancia_controller = '\\controllers\\' . $amp['classe'];
		$controller = new $instancia_controller;

		if(!empty($amp['classe'])){
			$controller->loadModel($amp['classe']);
		}

		if(method_exists($controller, $amp['metodo'])) {
			if(!empty($amp['parametros'])){
				$controller->{$amp['metodo']}($amp['parametros']);
			} else {
				$controller->{$amp['metodo']}();
			}
		} else {
			$controller->index();
		}

	}

	private function get_url(){
		$url = isset($_GET['url']) ? $_GET['url'] : null;
		$url = rtrim($url, '/');
		$url = filter_var($url, FILTER_SANITIZE_URL);
		$url = explode('/', $url);

		return $url;
	}

	private function identificar_arquivo_metoro_parametro($url){
		$file = 'controllers/';

		foreach ($url as $indice => $value) {


			$file .= $value . '/';

			// if($indice == (count($url) - 1)) {
			// 	$file .= $url[$indice] . ".php";
			// 	$arquivo = $file;
			// 	$class = $url[$indice];
			// } else {
				if(file_exists($file . $url[$indice] . '.php')){
					$arquivo = $file . $url[$indice] . '.php';
					$class = $url[$indice];
				}

				if(!file_exists($file . $url[$indice] . '.php')){
					$method[] = $url[$indice];
				}
			// }
		}

		if(isset($method)){
			$metodo = $method[0];
			unset($method[0]);
		}

		return [
			'arquivo' 		=> $arquivo,
			'classe' 		=> $class,
			'metodo' 		=> isset($metodo) ? $metodo : null,
			'parametros' 	=> isset($method) ? array_values($method) : null

		];
	}

	/**
	 * método Error
	 * É chamado quando um controlador ou seu método ñ existir
	 */
	private function error() {
		header('location: /error');
	}

}