<?php
namespace Controller;

use Libs;

class Curso extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' 	=> 'curso',
		'name'		=> 'Cursos',
		'send'		=> 'Curso'
	];

	protected $datatable = [
		'colunas' => ['ID', 'Curso', 'Ações'],
		'select'  => ['id', 'curso'],
		'from'    => 'curso',
		'search'  => ['id', 'curso']
	];

	protected function carregar_dados_listagem_ajax($busca){
		$query = $this->model->carregar_listagem($busca, $this->datatable);

		$retorno = [];

		foreach ($query as $indice => $item) {
			$retorno[] = [
				$item['id'],
				$item['curso'],
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
				'curso'             => "<strong>Cadastrar Novo curso: </strong>" . $busca['nome']
			];

			$retorno = array_merge($add_cadastro, $retorno);
		}

		echo json_encode($retorno);
		exit;
	}
}