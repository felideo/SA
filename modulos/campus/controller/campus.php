<?php
namespace Controller;

use Libs;

class Campus extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' 	=> 'campus',
		'name'		=> 'Campi',
		'send'		=> 'Campus'
	];

	protected $datatable = [
		'colunas' => ['ID', 'Campus', 'Ações'],
		'select'  => ['id', 'campus'],
		'from'    => 'campus',
		'search'  => ['id', 'campus']
	];

	protected function carregar_dados_listagem_ajax($busca){
		$query = $this->model->carregar_listagem($busca, $this->datatable);

		$retorno = [];

		foreach ($query as $indice => $item) {
			$retorno[] = [
				$item['id'],
				$item['campus'],
				$this->view->default_buttons_listagem($item['id'], true, true, true)
			];
		}

		return $retorno;
	}

	public function buscar_campus_select2(){
		$busca = carregar_variavel('busca');

		$retorno = $this->model->buscar_campus($busca);

		if(isset($busca['cadastrar_busca']) && !empty($busca['cadastrar_busca']) && $busca['cadastrar_busca'] == 'true' && $busca['nome'] != '%%'){
			$add_cadastro[0] = [
				'id'               => $busca['nome'],
				'campus'             => "<strong>Cadastrar Novo campus: </strong>" . $busca['nome']
			];

			$retorno = array_merge($add_cadastro, $retorno);
		}

		echo json_encode($retorno);
		exit;
	}
}