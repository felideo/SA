<?php
namespace Controller;

class Aula extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' 	=> 'aula',
		'name'		=> 'Aulas',
		'send'		=> 'Aula'
	];

	protected $datatable = [
		'colunas' => ['ID', 'Numero', 'Titulo', 'Disciplina', 'Ações'],
		'select'  => ['id', 'numero', 'titulo', 'id_ctdp'],
		'from'    => 'aula',
		'search'  => ['id', 'titulo']
	];

	public function index(){
		\Util\Auth::handLeLoggin();
		\Util\Permission::check($this->modulo['modulo'], "visualizar");

		$id_usuario = $_SESSION['usuario']['id'];

		$this->view->assign('permissao_criar', \Util\Permission::check_user_permission($this->modulo['modulo'], 'criar'));

		$ctdp = $this->get_model('aluno')->carregar_ctdp($id_usuario);
		$ctdp = $this->get_controller('aluno')->tratar_ctdp_lista($ctdp);

		$this->view->assign('ctdp', $ctdp);

		if(isset($this->datatable) && !empty($this->datatable)){
			$this->view->set_colunas_datatable($this->datatable['colunas']);
		}

		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/listagem/listagem');
	}

	protected function carregar_dados_listagem_ajax($busca){
		$query = $this->model->carregar_listagem($busca, $this->datatable);

		$retorno = [];

		$ctdp = $this->get_model('aluno')->carregar_ctdp();
		$ctdp = $this->get_controller('aluno')->tratar_ctdp_lista($ctdp);

		foreach ($query as $indice => $item) {
			if(!isset($ctdp[$item['id_ctdp']]['identificador'])){
				continue;
			}

			$retorno[] = [
				$item['id'],
				$item['numero'],
				$item['titulo'],
				$ctdp[$item['id_ctdp']]['identificador'],
				$this->view->default_buttons_listagem($item['id'], true, true, true)
			];
		}

		return $retorno;
	}

	public function editar($id) {
		\Util\Auth::handLeLoggin();
		\Util\Permission::check($this->modulo['modulo'], "editar");

		$this->check_if_exists($id[0]);

		$id_usuario = $_SESSION['usuario']['id'];
		$ctdp = $this->get_model('aluno')->carregar_ctdp($id_usuario);
		$ctdp = $this->get_controller('aluno')->tratar_ctdp_lista($ctdp);

		$this->view->assign('ctdp', $ctdp);

		$cadastro =  $this->model->full_load_by_id($this->modulo['modulo'], $id[0])[0];

		if(isset($cadastro['id_arquivo']) && !empty($cadastro['id_arquivo'])){
			$cadastro['id_arquivo'] = $this->model->full_load_by_id('arquivo', $cadastro['id_arquivo'])[0];
		}

		$this->view->assign('cadastro', $cadastro);
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/form/form');
	}

	public function visualizar($id){
		\Util\Auth::handLeLoggin();
		\Util\Permission::check($this->modulo['modulo'], "visualizar");

		$this->check_if_exists($id[0]);

		$id_usuario = $_SESSION['usuario']['id'];
		$ctdp = $this->get_model('aluno')->carregar_ctdp($id_usuario);
		$ctdp = $this->get_controller('aluno')->tratar_ctdp_lista($ctdp);

		$this->view->assign('ctdp', $ctdp);

		$cadastro =  $this->model->full_load_by_id($this->modulo['modulo'], $id[0])[0];

		if(isset($cadastro['id_arquivo']) && !empty($cadastro['id_arquivo'])){
			$cadastro['id_arquivo'] = $this->model->full_load_by_id('arquivo', $cadastro['id_arquivo'])[0];
		}


		// debug2($cadastro);
		// exit;

		if(!empty($cadastro['titulo'])){

			$contador_visita = 'Acessou aula N° ' . $cadastro['numero'] . ' - ' . $cadastro['titulo'] . ' da materia ' . $ctdp[$cadastro['id_ctdp']]['identificador'];
			$this->get_controller('contador_visita')->contar_visita($contador_visita);
		}

		$this->view->assign('cadastro', $cadastro);

		$this->view->lazy_view();
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/form/form');
	}
}