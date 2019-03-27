<?php
namespace Controller;

use Libs;

class Aluno extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' 	=> 'aluno',
		'name'		=> 'Aluno',
		'send'		=> 'Alunos'
	];

	protected $datatable = [
		'colunas' => ['ID', 'Nome', 'RGM', 'Ações'],
		'select'  => ['id', 'nome', 'rgm', 'id_usuario'],
		'from'    => 'aluno',
		'search'  => ['id', 'nome', 'rgm']
	];

	public function index(){
		\Libs\Auth::handLeLoggin();
		\Libs\Permission::check($this->modulo['modulo'], "visualizar");

		$id_usuario = $_SESSION['usuario']['id'];

		$this->view->assign('permissao_criar', \Libs\Permission::check_user_permission($this->modulo['modulo'], 'criar'));

		$ctdp = $this->model->carregar_ctdp($id_usuario);
		$ctdp = $this->tratar_ctdp_lista($ctdp);

		$this->view->assign('ctdp', $ctdp);

		if(isset($this->datatable) && !empty($this->datatable)){
			$this->view->set_colunas_datatable($this->datatable['colunas']);
		}

		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/listagem/listagem');
	}

	public function tratar_ctdp_lista($ctdp){
		$retorno = [];

		if(empty($ctdp)){
			return $retorno;
		}

		foreach($ctdp as $indice => $item){
			$implode = [
				$item['disciplina'][0]['disciplina'],
				$item['turma'][0]['semestre'] . $item['turma'][0]['turma'],
				$item['curso'][0]['curso'],
				$item['semestre'][0]['identificador'],
			];

			$retorno[$item['id']] =[
				'id_semestre'   => $item['id'],
				'identificador' => implode(' - ', $implode)
			];
		}

		return $retorno;
	}

	protected function carregar_dados_listagem_ajax($busca){
		$query = $this->model->carregar_listagem($busca, $this->datatable);

		$retorno = [];

		foreach ($query as $indice => $item) {
			$botao_contador_visitas = "<a href='/aluno/visualizar_acessos/{$item['id_usuario']}' title='Visualizar Acessos do Aluno'><i class='botao_listagem  fa fa-align-justify fa-fw'></i></a>";

			$retorno[] = [
				$item['id'],
				$item['nome'],
				$item['rgm'],
				$this->view->default_buttons_listagem($item['id'], true, true, true) . $botao_contador_visitas
			];
		}

		return $retorno;
	}

	public function visualizar_acessos($id){
		\Libs\Auth::handLeLoggin();
		// \Libs\Permission::check($this->modulo['modulo'], "visualizar");
		$this->view->set_colunas_datatable(['Ação', 'Data']);
		$this->view->assign('listagem_alternativa', $this->modulo['modulo'] . '/carregar_dados_listagem_visualizar_acessos_ajax/'  . $id[0]);
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/listagem/listagem_acessos');
	}

	public function carregar_dados_listagem_visualizar_acessos_ajax($id){
		$cadastros =  $this->model->full_load_by_column('contador_visita', 'id_usuario', $id[0]);
		$cadastros = \Libs\Arrays::ordenarPorColuna($cadastros, 'data', 'desc');

		$retorno = [];

		// ZZZ: melhorar depois

		foreach($cadastros as $indice => $item){
			$retorno[] = [
				$item['acao'],
				date('d/m/Y H:i:s', strtotime($item['data']))
			];
		}

		$busca = [
			'order'  => carregar_variavel('order'),
			'search' => carregar_variavel('search'),
			'start'  => carregar_variavel('start'),
			'length' => carregar_variavel('length'),
		];

		echo json_encode([
            "draw"            => '',
            "recordsTotal"    => intval(count($retorno)),
            "recordsFiltered" => intval(count($retorno)),
            "data"            => $retorno
        ]);

		// echo json_encode($retorno);
		exit;
	}

	public function get_alunos_from_file(){
		$arquivo = Libs\CSV::importarParaArray(Libs\Dominio::getDominio() . '/' . carregar_variavel('arquivo'), ',', true);

		unset($arquivo[0]);

		$arquivo = array_values($arquivo);

		echo json_encode($arquivo);
		exit;
	}

	public function create($parametros = null){
		$alunos = carregar_variavel($this->modulo['modulo']);
		$alunos = $this->insert_update_aluno($alunos);

		$this->view->alert_js(ucfirst($this->modulo['modulo']) . 's cadastrado com sucesso!!!', 'sucesso');
		header('location: /' . $this->modulo['modulo']);
	}

	private function insert_update_aluno($alunos){
		foreach($alunos['aluno'] as $indice => $aluno){
			$insert_db = [
				'usuario' => [
					'email'      => $aluno['email_aluno'],
					'senha'      => \Libs\Hash::get_unic_hash(),
					'hierarquia' => 5,
				],
				'aluno' => [
					'nome'        => $aluno['nome'],
					'rgm'         => $aluno['rgm'],
					'ativo'       => 1
				],
				'aluno_relaciona_semestre' =>[
					'id_ctdp' => $alunos['id_ctdp'],
				]
			];

			$insert_db['usuario']['retorno'] = $this->model->insert_update(
				'usuario',
				['email' => $insert_db['usuario']['email']],
				$insert_db['usuario'],
				true
			);

			$insert_db['aluno']['id_usuario']                    = $insert_db['usuario']['retorno']['id'];
			$insert_db['aluno_relaciona_semestre']['id_usuario'] = $insert_db['usuario']['retorno']['id'];

			$insert_db['aluno']['retorno'] = $this->model->insert_update(
				'aluno',
				['id_usuario' => $insert_db['usuario']['retorno']['id']],
				$insert_db['aluno'],
				true
			);

			$insert_db['aluno_relaciona_semestre']['id_aluno'] = $insert_db['aluno']['retorno']['id'];

			$insert_db['aluno_relaciona_semestre']['retorno'] = $this->model->insert_update(
				'aluno_relaciona_semestre',
				$insert_db['aluno_relaciona_semestre'],
				$insert_db['aluno_relaciona_semestre'],
				true
			);

			$alunos['aluno'][$indice] = $insert_db;
			unset($insert_db);
		}

		return $alunos;
	}

	public function visualizar($id){
		\Libs\Auth::handLeLoggin();
		\Libs\Permission::check($this->modulo['modulo'], "visualizar");

		$this->check_if_exists($id[0]);

		$id_usuario = $_SESSION['usuario']['id'];

		$this->view->assign('permissao_criar', \Libs\Permission::check_user_permission($this->modulo['modulo'], 'criar'));

		$ctdp = $this->model->carregar_ctdp($id_usuario);
		$ctdp = $this->tratar_ctdp_lista($ctdp);

		$this->view->assign('ctdp', $ctdp);

		$cadastro = $this->model->carregar_aluno($id[0]);
		$cadastro['id'] = $cadastro[0]['aluno'][0]['id'];
		$this->view->assign('cadastro', $cadastro);

		$this->view->lazy_view();
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/form/form_aluno');
	}


	public function editar($id){
		\Libs\Auth::handLeLoggin();
		\Libs\Permission::check($this->modulo['modulo'], "visualizar");

		$this->check_if_exists($id[0]);

		$id_usuario = $_SESSION['usuario']['id'];

		$this->view->assign('permissao_criar', \Libs\Permission::check_user_permission($this->modulo['modulo'], 'criar'));

		$ctdp = $this->model->carregar_ctdp($id_usuario);
		$ctdp = $this->tratar_ctdp_lista($ctdp);

		$this->view->assign('ctdp', $ctdp);

		$cadastro = $this->model->carregar_aluno($id[0]);
		$cadastro['id'] = $cadastro[0]['aluno'][0]['id'];
		$this->view->assign('cadastro', $cadastro);

		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/form/form_aluno');
	}

	public function update($id){
		$alunos = carregar_variavel($this->modulo['modulo']);
		$alunos = $this->insert_update_aluno($alunos);

		$this->view->alert_js(ucfirst($this->modulo['modulo']) . ' editado com sucesso!!!', 'sucesso');
		header('location: /' . $this->modulo['modulo']);
	}
}