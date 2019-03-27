<?php
namespace Controller;

class Professor extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' 	=> 'professor',
		'name'		=> 'Professor',
		'send'		=> 'Professores'
	];

	protected $datatable = [
		'colunas' => ['ID', 'Nome', 'Ações'],
		'select'  => ['id', 'nome'],
		'from'    => 'professor',
		'search'  => ['id', 'nome']
	];

	public function index(){
		\Libs\Auth::handLeLoggin();
		\Libs\Permission::check($this->modulo['modulo'], "visualizar");

		$this->view->set_colunas_datatable($this->datatable['colunas']);
		$this->view->assign('permissao_criar', \Libs\Permission::check_user_permission($this->modulo['modulo'], 'criar'));
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/listagem/listagem');
	}

	protected function carregar_dados_listagem_ajax($busca){
		$query = $this->model->carregar_listagem($busca, $this->datatable);

		$retorno = [];

		foreach ($query as $indice => $item) {
			$retorno[] = [
				$item['id'],
				$item['nome'],
				$this->view->default_buttons_listagem($item['id'], true, true, true)
			];
		}

		return $retorno;
	}

	public function create(){
		\Libs\Auth::handLeLoggin();
		\Libs\Permission::check($this->modulo['modulo'], "criar");

		$dados = carregar_variavel($this->modulo['modulo']);

		$insert_db = [
			'usuario'   => [
				'email'      => $dados['professor']['email'],
				'senha'      => \Libs\Hash::get_unic_hash(),
				'hierarquia' => 3,
			],
			'professor' => [
				'nome'        => $dados['pessoa']['nome'] . ' ' . $dados['pessoa']['sobrenome'],
			],
			'pessoa' => [
				'nome'        => $dados['pessoa']['nome'],
				'sobrenome'   => $dados['pessoa']['sobrenome'],
			],
		];

		$insert_db['usuario']['retorno'] = $this->model->insert('usuario', $insert_db['usuario']);

		if($insert_db['usuario']['retorno']['status']){
			$insert_db['professor']['id_usuario'] = $insert_db['usuario']['retorno']['id'];
			$insert_db['pessoa']['id_usuario']    = $insert_db['usuario']['retorno']['id'];
			$insert_db['professor']['retorno']    = $this->model->insert('professor', $insert_db['professor']);
			$insert_db['pessoa']['retorno']       = $this->model->insert('pessoa', $insert_db['pessoa']);
		}

		if($insert_db['usuario']['retorno']['status']){
			$this->view->alert_js(ucfirst($this->modulo['modulo']) . ' e usuario cadastrado com sucesso!!!', 'sucesso');
		} else {
			$this->view->alert_js('Ocorreu um erro ao efetuar o cadastro do ' . strtolower($this->modulo['modulo']) . ', por favor tente novamente...', 'erro');
		}

		header('location: /' . $this->modulo['modulo']);
	}

	public function visualizar($id){
		\Libs\Auth::handLeLoggin();
		\Libs\Permission::check($this->modulo['modulo'], "visualizar");

		$this->check_if_exists($id[0]);

		$cadastro = $this->model->carregar_professor($id[0])[0];

		$this->view->assign('cadastro', $cadastro);
		$this->view->lazy_view();
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/form/form');
	}

	public function editar($id){
		\Libs\Auth::handLeLoggin();
		\Libs\Permission::check($this->modulo['modulo'], "visualizar");

		$this->check_if_exists($id[0]);

		$cadastro = $this->model->carregar_professor($id[0])[0];

		$this->view->assign('cadastro', $cadastro);
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/form/form');
	}

	public function update($id){
		\Libs\Auth::handLeLoggin();
		\Libs\Permission::check($this->modulo['modulo'], "criar");

		$dados = carregar_variavel($this->modulo['modulo']);

		$insert_db = [
			'usuario'   => [
				'email'      => $dados['professor']['email'],
				'senha'      => \Libs\Hash::get_unic_hash(),
				'hierarquia' => 3,
			],
			'professor' => [
				'nome'        => $dados['pessoa']['nome'] . ' ' . $dados['pessoa']['sobrenome'],
			],
			'pessoa' => [
				'nome'        => $dados['pessoa']['nome'],
				'sobrenome'   => $dados['pessoa']['sobrenome'],
			],
		];

		$insert_db['usuario']['retorno'] = $this->model->insert_update(
			'usuario',
			['email' => $insert_db['usuario']['email']],
			$insert_db['usuario'],
			true
		);

		if($insert_db['usuario']['retorno']['status']){
			$insert_db['professor']['id_usuario'] = $insert_db['usuario']['retorno']['id'];
			$insert_db['pessoa']['id_usuario']    = $insert_db['usuario']['retorno']['id'];

			$insert_db['professor']['retorno'] = $this->model->insert_update(
				'professor',
				['id_usuario' => $insert_db['professor']['id_usuario']],
				$insert_db['professor'],
				true
			);
			$insert_db['pessoa']['retorno'] = $this->model->insert_update(
				'pessoa',
				['id_usuario' => $insert_db['pessoa']['id_usuario']],
				$insert_db['pessoa'],
				true
			);
		}

		if($insert_db['usuario']['retorno']['status']){
			$this->view->alert_js(ucfirst($this->modulo['modulo']) . ' e usuario editado com sucesso!!!', 'sucesso');
		} else {
			$this->view->alert_js('Ocorreu um erro ao efetuar a edição do ' . strtolower($this->modulo['modulo']) . ', por favor tente novamente...', 'erro');
		}

		header('location: /' . $this->modulo['modulo']);
	}

	public function delete($id) {
		\Libs\Auth::handLeLoggin();
		\Libs\Permission::check($this->modulo['modulo'], "deletar");

		$this->check_if_exists($id[0]);
		$id_usuario = $this->model->full_load_by_id('professor', $id[0])[0]['id_usuario'];

		$retorno           = $this->model->delete($this->modulo['modulo'], ['id' => $id[0]]);
		$retorno_professor = $this->model->delete('usuario', ['id' => $id_usuario]);
		$retorno_pessoa    = $this->model->delete('pessoa', ['id_usuario' => $id_usuario]);

		if($retorno['status']){
			$this->view->alert_js(ucfirst($this->modulo['modulo']) . ' e usuario removido com sucesso!!!', 'sucesso');
		} else {
			$this->view->alert_js('Ocorreu um erro ao efetuar a remoção do ' . strtolower($this->modulo['modulo']) . ', por favor tente novamente...', 'erro');
		}

		header('location: /' . $this->modulo['modulo']);
	}
}