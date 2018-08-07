<?php
namespace Controller;

use Libs;

class Disciplina extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' 	=> 'disciplina',
		'name'		=> 'Disciplinas',
		'send'		=> 'Disciplina'
	];

	protected $datatable = [
		'colunas' => ['ID', 'Disciplina', 'Ações'],
		'select'  => ['id', 'disciplina'],
		'from'    => 'disciplina',
		'search'  => ['id', 'disciplina']
	];

	protected function carregar_dados_listagem_ajax($busca){
		$query = $this->model->carregar_listagem($busca, $this->datatable);

		$retorno = [];

		foreach ($query as $indice => $item) {
			$retorno[] = [
				$item['id'],
				$item['disciplina'],
				$this->view->default_buttons_listagem($item['id'], true, true, true)
			];
		}

		return $retorno;
	}

	public function buscar_curso_select2(){
		$busca = carregar_variavel('busca');

		$retorno = $this->model->buscar_curso($busca);

		if(isset($busca['cadastrar_busca']) && !empty($busca['cadastrar_busca']) && $busca['cadastrar_busca'] == 'true' && $busca['nome'] != '%%'){
			$add_cadastro[0] = [
				'id'               => $busca['nome'],
				'disciplina'             => "<strong>Cadastrar Novo disciplina: </strong>" . $busca['nome']
			];

			$retorno = array_merge($add_cadastro, $retorno);
		}

		echo json_encode($retorno);
		exit;
	}
}