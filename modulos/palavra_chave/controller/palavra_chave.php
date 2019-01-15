<?php
namespace Controller;

use Libs;

class Palavra_chave extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' 	=> 'palavra_chave',
		'name'		=> 'Palavras Chaves',
		'send'		=> 'Palavra Chave'
	];

	protected $datatable = [
		'colunas' => ['ID', 'Palavra Chave', 'Ações'],
		'select'  => ['id', 'palavra_chave'],
		'from'    => 'palavra_chave',
		'search'  => ['id', 'palavra_chave']
	];

	public function carregar_listagem_ajax(){
		$busca = [
			'order'  => carregar_variavel('order'),
			'search' => carregar_variavel('search'),
			'start'  => carregar_variavel('start'),
			'length' => carregar_variavel('length'),
		];

		$query = $this->model->carregar_listagem($busca, $this->datatable);

		$retorno = [];

		foreach ($query as $indice => $item) {
			$retorno[] = [
				$item['id'],
				$item['palavra_chave'],
				$this->view->default_buttons_listagem($item['id'], true, true, true)
			];
		}

		echo json_encode([
            "draw"            => intval(carregar_variavel('draw')),
            "recordsTotal"    => intval(count($retorno)),
            "recordsFiltered" => intval($this->model->db->select("SELECT count(id) AS total FROM {$this->modulo['modulo']} WHERE ativo = 1")[0]['total']),
            "data"            => $retorno
        ]);

		exit;
	}

	public function buscar_palavra_chave_select2(){
		$busca = carregar_variavel('busca');
		$retorno = $this->model->buscar_palavra_chave($busca);


		if(isset($busca['cadastrar_busca']) && !empty($busca['cadastrar_busca']) && $busca['cadastrar_busca'] == 'true' && $busca['nome'] != '%%'){
			if(empty($retorno)){
				$retorno = [];
			}

			$add_cadastro[0] = [
				'id'            => $busca['nome'],
				'palavra_chave' => "<strong>Cadastrar Nova Palavra Chave: </strong>" . $busca['nome']
			];

			$retorno = array_merge($add_cadastro, $retorno);
		}

		echo json_encode($retorno);
		exit;

	}
}