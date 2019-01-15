<?php
namespace Controller;

class Semestre extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' 	=> 'semestre',
		'name'		=> 'Semestre',
		'send'		=> 'Semestre'
	];

	protected $datatable = [
		'colunas' => ['ID', 'Identificador', 'Termino', 'Ações'],
		'select'  => ['id', 'identificador', 'termino'],
		'from'    => 'semestre',
		'search'  => ['id', 'identificador', 'termino']
	];

	public function index(){
		\Util\Auth::handLeLoggin();
		\Util\Permission::check($this->modulo['modulo'], "visualizar");

		$this->view->assign('permissao_criar', \Util\Permission::check_user_permission($this->modulo['modulo'], 'criar'));

		if(isset($this->datatable) && !empty($this->datatable)){
			$this->view->set_colunas_datatable($this->datatable['colunas']);
		}

		$this->view->assign('cursos', $this->model->load_active_list('curso'));
		$this->view->assign('disciplinas', $this->model->load_active_list('disciplina'));
		$this->view->assign('turmas', $this->model->load_active_list('turma'));
		$this->view->assign('professores', $this->model->load_active_list('professor'));

		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/listagem/listagem');
	}

	protected function carregar_dados_listagem_ajax($busca){
		$query = $this->model->carregar_listagem($busca, $this->datatable);

		$retorno = [];

		foreach ($query as $indice => $item) {
			$retorno[] = [
				$item['id'],
				$item['identificador'],
				date('d/m/Y',  strtotime($item['termino'])),
				$this->view->default_buttons_listagem($item['id'], true, true, true)
			];
		}

		return $retorno;
	}

	public function create(){
		\Util\Auth::handLeLoggin();
		\Util\Permission::check($this->modulo['modulo'], "criar");

		$dados                        = carregar_variavel($this->modulo['modulo']);
		$dados['semestre']['termino'] = transformar_data($dados['semestre']['termino']);
		$dados['semestre']['inicio']  = transformar_data($dados['semestre']['inicio']);


		$retorno = $this->insert_update($dados);

		if($retorno['semestre']['retorno']['status']){
			$this->view->alert_js(ucfirst($this->modulo['modulo']) . ' cadastrado com sucesso!!!', 'sucesso');
		} else {
			$this->view->alert_js('Ocorreu um erro ao efetuar o cadastro do ' . strtolower($this->modulo['modulo']) . ', por favor tente novamente...', 'erro');
		}

		header('location: /' . $this->modulo['modulo']);
	}

	private function insert_update($insert_db){
		$verificar_semestre['termino']  = $insert_db['semestre']['termino'];
		$insert_db['semestre']['ativo'] = 1;

		if(isset($insert_db['semestre']['id']) && !empty($insert_db['semestre']['id'])){
			$verificar_semestre['id'] = $insert_db['semestre']['id'];
		}

		$insert_db['semestre']['retorno'] = $this->model->insert_update(
			'semestre',
			$verificar_semestre,
			$insert_db['semestre'],
			true
		);

		$desativa_ctdp = $this->model->insert_update(
			'usuario_professor_curso_turma_disciplina_semestre',
			['id_semestre' => $insert_db['semestre']['retorno']['id']],
			['ativo' => 0],
			true
		);

		$id_usuarios = [];

		foreach($insert_db['ctdp'] as &$ctdp){
			if(!isset($id_usuarios[$ctdp['id_professor']])){
				$id_usuarios[$ctdp['id_professor']] = $this->model->full_load_by_id('professor', $ctdp['id_professor'])[0]['id_usuario'];
			}

			$ctdp['id_usuario']  = $id_usuarios[$ctdp['id_professor']];
			$ctdp['id_semestre'] = $insert_db['semestre']['retorno']['id'];
			$ctdp['ativo']       = 1;


			$ctdp['retorno'] = $this->model->insert_update(
				'usuario_professor_curso_turma_disciplina_semestre',
				$ctdp,
				$ctdp,
				true
			);
		}

		return $insert_db;
	}

	public function visualizar($id){
		\Util\Auth::handLeLoggin();
		\Util\Permission::check($this->modulo['modulo'], "visualizar");

		$this->check_if_exists($id[0]);

		$cadastro            = $this->model->semestre($id[0])[0];
		$cadastro['termino'] = date('d/m/Y',  strtotime($cadastro['termino']));
		$cadastro['inicio']  = date('d/m/Y',  strtotime($cadastro['inicio']));

		$this->view->assign('cursos', $this->model->load_active_list('curso'));
		$this->view->assign('disciplinas', $this->model->load_active_list('disciplina'));
		$this->view->assign('turmas', $this->model->load_active_list('turma'));
		$this->view->assign('professores', $this->model->load_active_list('professor'));

		$this->view->assign('cadastro', $cadastro);
		$this->view->lazy_view();
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/form/form');
	}

	public function editar($id){
		\Util\Auth::handLeLoggin();
		\Util\Permission::check($this->modulo['modulo'], "visualizar");

		$this->check_if_exists($id[0]);

		$cadastro            = $this->model->semestre($id[0])[0];
		$cadastro['termino'] = date('d/m/Y',  strtotime($cadastro['termino']));
		$cadastro['inicio']  = date('d/m/Y',  strtotime($cadastro['inicio']));


		$this->view->assign('cursos', $this->model->load_active_list('curso'));
		$this->view->assign('disciplinas', $this->model->load_active_list('disciplina'));
		$this->view->assign('turmas', $this->model->load_active_list('turma'));
		$this->view->assign('professores', $this->model->load_active_list('professor'));

		$this->view->assign('cadastro', $cadastro);
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/form/form');
	}

	public function update($id){
		\Util\Auth::handLeLoggin();
		\Util\Permission::check($this->modulo['modulo'], "criar");

		$dados                        = carregar_variavel($this->modulo['modulo']);
		$dados['semestre']['id']      = $id[0];
		$dados['semestre']['termino'] = transformar_data($dados['semestre']['termino']);
		$dados['semestre']['inicio']  = transformar_data($dados['semestre']['inicio']);


		$retorno = $this->insert_update($dados);

		if($retorno['semestre']['retorno']['status']){
			$this->view->alert_js(ucfirst($this->modulo['modulo']) . ' cadastrado com sucesso!!!', 'sucesso');
		} else {
			$this->view->alert_js('Ocorreu um erro ao efetuar o cadastro do ' . strtolower($this->modulo['modulo']) . ', por favor tente novamente...', 'erro');
		}

		header('location: /' . $this->modulo['modulo']);
	}

	public function delete($id) {
		\Util\Auth::handLeLoggin();
		\Util\Permission::check($this->modulo['modulo'], "deletar");

		$this->check_if_exists($id[0]);

		$retorno      = $this->model->delete($this->modulo['modulo'], ['id' => $id[0]]);
		$retorno_ctdp = $this->model->delete('usuario_professor_curso_turma_disciplina_semestre', ['id_semestre' => $id[0]]);

		if($retorno['status']){
			$this->view->alert_js(ucfirst($this->modulo['modulo']) . ' removido com sucesso!!!', 'sucesso');
		} else {
			$this->view->alert_js('Ocorreu um erro ao efetuar a remoção do ' . strtolower($this->modulo['modulo']) . ', por favor tente novamente...', 'erro');
		}

		header('location: /' . $this->modulo['modulo']);
	}
}