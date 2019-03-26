<?php
namespace Controller;

use Libs;

class Orientador extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' 	=> 'orientador',
		'name'		=> 'Orientadores',
		'send'		=> 'Orientador'
	];

	protected $datatable = [
		'colunas' => ['ID', 'Nome', 'Email', 'Link/Lattes', 'Ações'],
		'select'  => ['id', 'nome', 'email', 'link'],
		'from'    => 'orientador',
		'search'  => ['id', 'nome', 'email', 'link']
	];

	protected function carregar_dados_listagem_ajax($busca){
		$query = $this->model->carregar_listagem($busca, $this->datatable);

		$retorno = [];

		foreach ($query as $indice => $item) {
			$retorno[] = [
				$item['id'],
				$item['nome'],
				$item['email'],
				$item['link'],
				$this->view->default_buttons_listagem($item['id'], true, true, true)
			];
		}

		return $retorno;
	}

	public function buscar_orientador_select2(){
		$busca = carregar_variavel('busca');

		$retorno = $this->model->buscar_orientador($busca);

		if(isset($busca['cadastrar_busca']) && !empty($busca['cadastrar_busca']) && $busca['cadastrar_busca'] == 'true' && $busca['nome'] != '%%'){
			$add_cadastro[0] = [
				'id'               => $busca['nome'],
				'nome'             => "<strong>Cadastrar Novo Orientador: </strong>" . $busca['nome']
			];

			$retorno = array_merge($add_cadastro, $retorno);
		}

		echo json_encode($retorno);
		exit;
	}
}