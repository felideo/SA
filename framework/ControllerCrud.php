<?php
namespace Framework;

class ControllerCrud extends \Framework\Controller {
	protected $modulo     = [];
	protected $datatable    = [];

	function __construct() {
		parent::__construct();
		$this->view->modulo = $this->modulo;
		$this->view->assign('modulo', $this->modulo);
	}

	public function index(){
		\Util\Auth::handLeLoggin();
		\Util\Permission::check($this->modulo['modulo'], "visualizar");

		$this->view->assign('permissao_criar', \Util\Permission::check_user_permission($this->modulo['modulo'], 'criar'));

		if(isset($this->datatable) && !empty($this->datatable)){
			$this->view->set_colunas_datatable($this->datatable['colunas']);
		}

		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/listagem/listagem');
	}

	public function carregar_listagem_ajax(){
		$busca = [
			'order'  => carregar_variavel('order'),
			'search' => carregar_variavel('search'),
			'start'  => carregar_variavel('start'),
			'length' => carregar_variavel('length'),
		];

		$retorno = $this->carregar_dados_listagem_ajax($busca);

		echo json_encode([
            "draw"            => intval(carregar_variavel('draw')),
            "recordsTotal"    => intval(count($retorno)),
            "recordsFiltered" => intval($this->model->db->select("SELECT count(id) AS total FROM {$this->datatable['from']} WHERE ativo = 1")[0]['total']),
            "data"            => $retorno
        ]);

		exit;
	}

	public function create(){
		\Util\Auth::handLeLoggin();
		\Util\Permission::check($this->modulo['modulo'], "criar");

		$retorno = $this->model->insert($this->modulo['modulo'], carregar_variavel($this->modulo['modulo']));

		if($retorno['status']){
			$this->view->alert_js(ucfirst($this->modulo['modulo']) . ' cadastrado com sucesso!!!', 'sucesso');
		} else {
			$this->view->alert_js('Ocorreu um erro ao efetuar o cadastro do ' . strtolower($this->modulo['modulo']) . ', por favor tente novamente...', 'erro');
		}

		header('location: /' . $this->modulo['modulo']);
	}

	public function editar($id) {
		\Util\Auth::handLeLoggin();
		\Util\Permission::check($this->modulo['modulo'], "editar");

		$this->check_if_exists($id[0]);

		$this->view->assign('cadastro', $this->model->full_load_by_id($this->modulo['modulo'], $id[0])[0]);
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/form/form');
	}

	public function update($id) {
		\Util\Auth::handLeLoggin();
		\Util\Permission::check($this->modulo['modulo'], "editar");

		$this->check_if_exists($id[0]);

		$retorno = $this->model->update($this->modulo['modulo'], ['id' => $id[0]], carregar_variavel($this->modulo['modulo']));

		if($retorno['status']){
			$this->view->alert_js(ucfirst($this->modulo['modulo']) . ' editado com sucesso!!!', 'sucesso');
		} else {
			$this->view->alert_js('Ocorreu um erro ao efetuar a edição do ' . strtolower($this->modulo['modulo']) . ', por favor tente novamente...', 'erro');
		}

		header('location: /' . $this->modulo['modulo']);
	}

	public function delete($id) {
		\Util\Auth::handLeLoggin();
		\Util\Permission::check($this->modulo['modulo'], "deletar");

		$this->check_if_exists($id[0]);

		$retorno = $this->model->delete($this->modulo['modulo'], ['id' => $id[0]]);

		if($retorno['status']){
			$this->view->alert_js(ucfirst($this->modulo['modulo']) . ' removido com sucesso!!!', 'sucesso');
		} else {
			$this->view->alert_js('Ocorreu um erro ao efetuar a remoção do ' . strtolower($this->modulo['modulo']) . ', por favor tente novamente...', 'erro');
		}

		header('location: /' . $this->modulo['modulo']);
	}

	public function visualizar($id){
		\Util\Auth::handLeLoggin();
		\Util\Permission::check($this->modulo['modulo'], "visualizar");

		$this->check_if_exists($id[0]);

		$this->view->assign('cadastro', $this->model->full_load_by_id($this->modulo['modulo'], $id[0])[0]);

		$this->view->lazy_view();
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/form/form');
	}
}