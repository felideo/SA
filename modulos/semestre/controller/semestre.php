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

	public function middle_index() {
		$this->view->assign('cursos', $this->model->load_active_list('curso'));
		$this->view->assign('disciplinas', $this->model->load_active_list('disciplina'));
		$this->view->assign('turmas', $this->model->load_active_list('turma'));
		$this->view->assign('professores', $this->model->load_active_list('professor'));
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

	public function insert_update($dados, $where = null){
		$insert_db                        = carregar_variavel($this->modulo['modulo']);
		$insert_db['semestre']['termino'] = transformar_data($dados['semestre']['termino']);
		$insert_db['semestre']['inicio']  = transformar_data($dados['semestre']['inicio']);

		if(!empty($where)){
			$verificar_semestre['id'] = $where['id'];
		}

		if(empty($where)){
			$verificar_semestre['termino']  = $insert_db['semestre']['termino'];
		}

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

		return $insert_db['semestre']['retorno'];
	}


	public function middle_visualizar($id){
		$cadastro            = $this->model->semestre($id)[0];
		$cadastro['termino'] = date('d/m/Y',  strtotime($cadastro['termino']));
		$cadastro['inicio']  = date('d/m/Y',  strtotime($cadastro['inicio']));

		$this->view->assign('cursos', $this->model->load_active_list('curso'));
		$this->view->assign('disciplinas', $this->model->load_active_list('disciplina'));
		$this->view->assign('turmas', $this->model->load_active_list('turma'));
		$this->view->assign('professores', $this->model->load_active_list('professor'));
		$this->view->assign('cadastro', $cadastro);
	}

	public function middle_editar($id){
		$cadastro            = $this->model->semestre($id)[0];
		$cadastro['termino'] = date('d/m/Y',  strtotime($cadastro['termino']));
		$cadastro['inicio']  = date('d/m/Y',  strtotime($cadastro['inicio']));

		$this->view->assign('cursos', $this->model->load_active_list('curso'));
		$this->view->assign('disciplinas', $this->model->load_active_list('disciplina'));
		$this->view->assign('turmas', $this->model->load_active_list('turma'));
		$this->view->assign('professores', $this->model->load_active_list('professor'));
		$this->view->assign('cadastro', $cadastro);
	}

	public function middle_delete($id) {
		$retorno      = $this->model->delete($this->modulo['modulo'], ['id' => $id]);
		$retorno_ctdp = $this->model->delete('usuario_professor_curso_turma_disciplina_semestre', ['id_semestre' => $id]);

		return $retorno;

	}
}