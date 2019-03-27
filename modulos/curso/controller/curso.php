<?php
namespace Controller;

use Libs;

class Curso extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' 	=> 'curso',
	];

	protected $datatable = [
		'colunas' => ['ID <i class="fa fa-search"></i>', 'Curso <i class="fa fa-search"></i>', 'Ações'],
		'select'  => ['id', 'curso'],
		'from'    => 'curso',
		'search'  => ['id', 'curso'],
		'ordenacao_desabilitada' => '2'
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

	public function middle_delete($id) {
		$curso_utilizado = $this->model->query->select('
				trabalho.id
			')
			->from('trabalho trabalho')
			->where("trabalho.id_curso = {$id}")
			->fetchArray();

		if(!empty($curso_utilizado)){
			$msg = 'O curso esta relacionado ao(s) trabalho(s) ID numero: ';

			foreach($curso_utilizado as $indice => $trabalho){
				$msg .= $trabalho['id'] . ', ';
			}

			$msg = rtrim($msg, ', ');
			$msg .= '. Exclusão negada! Remova manualmente este curso de todos os trabalhos antes de tentar excluir-lo.';

			$this->view->alert_js($msg, 'erro');
			header('location: /' . $this->modulo['modulo']);
			exit;
		}

		return $this->model->delete($this->modulo['modulo'], ['id' => $id]);
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

		if(empty($retorno)){
			$retorno = false;
		}

		echo json_encode($retorno);
		exit;
	}
}