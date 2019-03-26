<?php
namespace Libs;
use Libs\Strings;

class URL {
	private $id;
	private $url;
	private $model;
	private $controller;

	public function __construct(){
		$this->model = new \Framework\GenericModel;
	}

	public function cadastrarUrlAmigavel(){
		$this->get_url_amigavel();
		$this->tratar_preexistencia();

		$retorno = $this->cadastrarUrl();

		if(!empty($retorno['status'])){
			return true;
		}

		return false;
	}

	private function get_url_amigavel(){
		$this->url = Strings::limparStringCompleto($this->url);
		$this->url = Strings::remover_acentos($this->url);
		$this->url = Strings::removerCaracteresMultiplicados('-', $this->url);
	}

	private function tratar_preexistencia(){
		$ja_existe     = true;
		$diferenciador = 1;
		$url_unica     = $this->url;

		while(!empty($ja_existe)) {
			$query = $this->model->db->select("SELECT id FROM url WHERE  controller = '{$this->controller}' AND url = '{$url_unica}'");

			if(!empty($query)){
				$url_unica = $this->url . '-' . $diferenciador;
				$diferenciador++;
			}else{
				$ja_existe = false;
			}
		}

		$this->url = $url_unica;
	}

	private function cadastrarUrl(){
		$insert_db = [
			'url'           => $this->url,
			'controller'    => $this->controller,
			'id_controller' => $this->id,
		];

		return $this->model->insert('url', $insert_db);
	}

	public function setId($id){
		$this->id = $id;
		return $this;
	}

	public function setUrl($url){
		$this->url = $url;
		return $this;
	}

	public function setController($controller){
		$this->controller = $controller;
		return $this;
	}
}