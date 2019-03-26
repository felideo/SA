<?php
namespace Controller;

use Libs;

class Turma extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' 	=> 'turma',
		'name'		=> 'Turmas',
		'send'		=> 'Turma'
	];

	protected $datatable = [
		'colunas' => ['ID', 'Periodo', 'Semestre', 'Turma', 'Ações'],
		'select'  => ['id', 'periodo', 'semestre', 'turma'],
		'from'    => 'turma',
		'search'  => ['id', 'periodo', 'semestre', 'turma']
	];

	protected function carregar_dados_listagem_ajax($busca){
		$query = $this->model->carregar_listagem($busca, $this->datatable);

		$retorno = [];

		foreach ($query as $indice => $item) {
			$retorno[] = [
				$item['id'],
				$item['periodo'],
				$item['semestre'],
				$item['turma'],
				$this->view->default_buttons_listagem($item['id'], true, true, true)
			];
		}

		return $retorno;
	}
}