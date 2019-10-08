<?php
namespace Controller;

use Libs;

class Contador_visita extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' 	=> 'contador_visita',
		'name'		=> 'Contador de Visitas',
		'send'		=> 'Contador de Visitas'
	];

	protected $datatable = [
		'colunas' => [],
		'select'  => [],
		'from'    => '',
		'search'  => []
	];

	protected function carregar_dados_listagem_ajax($busca){
		$query = $this->model->carregar_listagem($busca, $this->datatable);

		$retorno = [];

		foreach ($query as $indice => $item) {
			$retorno[] = [
				$item['id'],
				$item['titulo'],
				$this->view->default_buttons_listagem($item['id'], true, true, true)
			];
		}

		return $retorno;
	}

	public function contar_visita($acao){
		$insert_db = [
			'id_usuario' => $_SESSION['usuario']['id'],
			'acao'       => $acao
		];

		if(empty($this->model)){
			$this->model = $this->get_model($this->modulo['modulo']);
		}

		$this->model->insert($this->modulo['modulo'], $insert_db);
	}
}
