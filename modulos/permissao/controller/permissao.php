<?php
namespace Controller;

use Libs;

class Permissao extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' 	=> 'permissao',
		'name'		=> 'Permissão',
		'send'		=> 'Permissão'
	];

	protected $datatable = [
		'colunas' => ['ID', 'Modulo', 'Permissão', 'Ações'],
		'select'  => ['id', 'id_modulo', 'permissao'],
		'from'    => 'permissao',
		'search'  => ['id', 'id_modulo', 'permissao']
	];

	public function index() {
		\Util\Auth::handLeLoggin();
		\Util\Permission::check($this->modulo['modulo'], "visualizar");

		$this->view->assign('permissao_criar', \Util\Permission::check_user_permission($this->modulo['modulo'], 'criar'));

		if(isset($this->datatable) && !empty($this->datatable)){
			$this->view->set_colunas_datatable($this->datatable['colunas']);
		}

		$this->view->assign('modulos', $this->model->load_active_list('modulo'));
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/listagem/listagem');
	}

	public function editar($id) {
		\Util\Auth::handLeLoggin();
		\Util\Permission::check($this->modulo['modulo'], "editar");

		$this->check_if_exists($id[0]);

		$this->view->assign('modulos', $this->model->load_active_list('modulo'));
		$this->view->assign('cadastro', $this->model->full_load_by_id($this->modulo['modulo'], $id[0])[0]);
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/form/form');
	}

	public function visualizar($id){
		\Util\Auth::handLeLoggin();
		\Util\Permission::check($this->modulo['modulo'], "visualizar");

		$this->check_if_exists($id[0]);

		$this->view->assign('modulos', $this->model->load_active_list('modulo'));
		$this->view->assign('cadastro', $this->model->full_load_by_id($this->modulo['modulo'], $id[0])[0]);

		$this->view->lazy_view();
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/form/form');
	}

	protected function carregar_dados_listagem_ajax($busca){
		$query = $this->model->carregar_listagem($busca, $this->datatable);

		$retorno = [];

		foreach ($query as $indice => $item) {
			$retorno[] = [
				$item['id'],
				$item['modulo'][0]['nome'],
				$item['permissao'],
				$this->view->default_buttons_listagem($item['id'], true, true, true)
			];
		}

		return $retorno;
	}

	public function buscar_autor_select2(){
		$busca = carregar_variavel('busca');

		$retorno = $this->model->buscar_autor($busca);

		if(isset($busca['cadastrar_busca']) && !empty($busca['cadastrar_busca']) && $busca['cadastrar_busca'] == 'true' && $busca['nome'] != '%%'){
			$add_cadastro[0] = [
				'id'               => $busca['nome'],
				'nome'             => "<strong>Cadastrar Novo Autor: </strong>" . $busca['nome']
			];

			$retorno = array_merge($add_cadastro, $retorno);
		}

		echo json_encode($retorno);
		exit;
	}
}