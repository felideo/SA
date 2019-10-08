<?php
namespace Controller;

use Libs;
use Libs\URL;

class Trabalho extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' 	=> 'trabalho',
		'name'		=> 'Trabalhos',
		'send'		=> 'trabalho'
	];

	protected $datatable = [
		'colunas' => ['ID', 'Titulo', 'Ano', 'Curso', 'Campi', 'Autor', 'Orientador', 'Status', 'Ações'],
	];


	public function carregar_listagem_ajax(){
		$busca = [
			'order'  => carregar_variavel('order'),
			'search' => carregar_variavel('search'),
			'start'  => carregar_variavel('start'),
			'length' => carregar_variavel('length'),
		];

		$query = $this->model->carregar_listagem($busca);

		$retorno_tratado = [];

		foreach ($query as $indice => $retorno) {
			if(!isset($retorno_tratado[$retorno['id']])){
				$retorno_tratado[$retorno['id']] = $retorno;
			}else{
				$retorno_tratado[$retorno['id']]['palavra'] .= ', ' . $retorno['palavra'];
			}
		}

		$retorno = [];

		foreach ($retorno_tratado as $indice => $item) {
			$autor      = '';
			$orientador = '';

			foreach ($item['trabalho_relaciona_autor'] as $indice => $um_autor){
				$autor .= isset($um_autor['autor'][0]['nome']) ? $um_autor['autor'][0]['nome'] . ' ' : ' ';
			}

			foreach ($item['trabalho_relaciona_orientador'] as $indice => $um_orientador){
				$orientador .= isset($um_orientador['orientador'][0]['nome']) ? $um_orientador['orientador'][0]['nome'] . ' ' : ' ';
			}

			$botao_aprovar = \Util\Permission::check_user_permission($this->modulo['modulo'], "aprovar") && ($item['status'] == 0 || $item['status'] == 2) ?
				"<a href='/{$this->modulo['modulo']}/aprovar/{$item['id']}' title='Visualizar'><i class='botao_listagem fa fa-check-circle fa-fw'></i></a>" :
				'';

			$botao_reprovar = \Util\Permission::check_user_permission($this->modulo['modulo'], "reprovar") && ($item['status'] == 0 || $item['status'] == 1) ?
				"<a href='/{$this->modulo['modulo']}/reprovar/{$item['id']}' title='Visualizar'><i class='botao_listagem fa fa-times-circle fa-fw'></i></a>" :
				'';

			if($item['status'] == 0){
				$status = 'Pendente';
			}elseif($item['status'] == 1){
				$status = 'Aprovado';
			}elseif($item['status'] == 2){
				$status = 'Reprovado';
			}

			$retorno[] = [
				isset($item['id']) && !empty($item['id']) ? $item['id'] : '',
				isset($item['titulo']) && !empty($item['titulo']) ? $item['titulo'] : '',
				isset($item['ano']) && !empty($item['ano']) ? $item['ano'] : '',
				isset($item['curso'][0]['curso']) && !empty($item['curso'][0]['curso']) ? $item['curso'][0]['curso'] : '',
				isset($item['campus'][0]['campus']) && !empty($item['campus'][0]['campus']) ? $item['campus'][0]['campus'] : '',
				isset($autor) && !empty($autor) ? $autor : '',
				isset($orientador) && !empty($orientador) ? $orientador : '',
				isset($status) && !empty($status) ? $status : '',

				$this->view->default_buttons_listagem($item['id'], true, true, true) . $botao_aprovar . $botao_reprovar
			];
		}

		ob_clean();

		echo json_encode([
            "draw"            => intval(carregar_variavel('draw')),
            "recordsTotal"    => intval(count($retorno)),
            "recordsFiltered" => intval($this->model->db->select("SELECT count(id) AS total FROM {$this->modulo['modulo']} WHERE ativo = 1")[0]['total']),
            "data"            => $retorno
        ]);

		exit;
	}

	public function create(){
		\Util\Auth::handLeLoggin();
		\Util\Permission::check($this->modulo['modulo'], "criar");

		$trabalho = carregar_variavel('trabalho');

		$insert_trabalho_db              = $trabalho['trabalho'];
		$insert_trabalho_db['id_curso']  = $this->tratar_curso($insert_trabalho_db['id_curso']);
		$insert_trabalho_db['id_campus'] = $this->tratar_campus($insert_trabalho_db['id_campus']);

		$retorno_trabalho = $this->model->insert('trabalho', $insert_trabalho_db);


		if(!empty($retorno_trabalho['status'])){
			$trabalho['trabalho']['id'] = $retorno_trabalho['id'];

			$url = new URL;
			$retorno_url = $url->setId($retorno_trabalho['id'])
				->setUrl($insert_trabalho_db['titulo'])
				->setController($this->modulo['modulo'])
				->cadastrarUrlAmigavel();

			$this->cadastrar_orientador($trabalho['orientador'], $retorno_trabalho['id']);
			$this->cadastrar_autor($trabalho['autor'], $retorno_trabalho['id']);
			$this->cadastrar_palavra_chave($trabalho['palavras_chave'], $retorno_trabalho['id']);
			$this->cadastrar_relacao_trabalho_arquivo($trabalho['arquivo'], $retorno_trabalho['id']);
		}

		if($retorno_trabalho['status']){
			$this->view->alert_js(ucfirst($this->modulo['modulo']) . ' cadastrado com sucesso!!!', 'sucesso');
		} else {
			$this->view->alert_js('Ocorreu um erro ao efetuar o cadastro do ' . strtolower($this->modulo['modulo']) . ', por favor tente novamente...', 'erro');
		}

		header('location: /' . $this->modulo['modulo']);
	}

	public function update($parametros){
		\Util\Auth::handLeLoggin();
		\Util\Permission::check($this->modulo['modulo'], "criar");

		$trabalho = carregar_variavel('trabalho');

		$insert_trabalho_db              = $trabalho['trabalho'];
		$insert_trabalho_db['id_curso']  = $this->tratar_curso($insert_trabalho_db['id_curso']);
		$insert_trabalho_db['id_campus'] = $this->tratar_campus($insert_trabalho_db['id_campus']);


		debug2($parametros);
		debug2($insert_trabalho_db);
		exit;

		$retorno_trabalho = $this->model->insert('trabalho', $insert_trabalho_db);


		if(!empty($retorno_trabalho['status'])){
			$trabalho['trabalho']['id'] = $retorno_trabalho['id'];

			$url = new URL;
			$retorno_url = $url->setId($retorno_trabalho['id'])
				->setUrl($insert_trabalho_db['titulo'])
				->setController($this->modulo['modulo'])
				->cadastrarUrlAmigavel();

			$this->cadastrar_orientador($trabalho['orientador'], $retorno_trabalho['id']);
			$this->cadastrar_autor($trabalho['autor'], $retorno_trabalho['id']);
			$this->cadastrar_palavra_chave($trabalho['palavras_chave'], $retorno_trabalho['id']);
			$this->cadastrar_relacao_trabalho_arquivo($trabalho['arquivo'], $retorno_trabalho['id']);
		}

		if($retorno_trabalho['status']){
			$this->view->alert_js(ucfirst($this->modulo['modulo']) . ' cadastrado com sucesso!!!', 'sucesso');
		} else {
			$this->view->alert_js('Ocorreu um erro ao efetuar o cadastro do ' . strtolower($this->modulo['modulo']) . ', por favor tente novamente...', 'erro');
		}

		header('location: /' . $this->modulo['modulo']);
	}

	public function visualizar($id){
		\Util\Auth::handLeLoggin();
		\Util\Permission::check($this->modulo['modulo'], "visualizar");

		$this->check_if_exists($id[0]);

		$this->view->assign('cadastro', $this->model->carregar_trabalho($id[0])[0]);

		$this->view->lazy_view();
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/form/form');
	}

	public function editar($id){
		\Util\Auth::handLeLoggin();
		\Util\Permission::check($this->modulo['modulo'], "editar");

		$this->check_if_exists($id[0]);

		$this->view->assign('cadastro', $this->model->carregar_trabalho($id[0])[0]);
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/form/form');
	}

	private function tratar_curso($curso){
		if(is_numeric($curso)){
			return $curso;
		}

		$insert_db['curso'] = $curso;

		$retorno = $this->model->insert('curso', $insert_db);

		if(!empty($retorno['status'])){
			return $retorno['id'];
		}else{
			$this->view->warning_js('Ocorreu um erro ao cadastrar o curso. Por favor edite o trabalho para corrigir', 'erro');
		}
	}

	private function tratar_campus($campus){
		if(is_numeric($campus)){
			return $campus;
		}

		$insert_db['campus'] = $campus;

		$retorno = $this->model->insert('campus', $insert_db);

		if(!empty($retorno['status'])){
			return $retorno['id'];
		}else{
			$this->view->warning_js('Ocorreu um erro ao cadastrar o campus. Por favor edite o trabalho para corrigir', 'erro');
		}
	}

	private function cadastrar_orientador($orientadores, $id_trabalho){
		foreach($orientadores as $indice => $orientador){
			if(is_numeric($orientador['orientador'])){
				$this->cadrastrar_relacao_trabalho_orientador($orientador['orientador'], $id_trabalho);
				continue;
			}

			$insert_db = [
				'nome'  => $orientador['orientador'],
				'email' => $orientador['email'],
				'link'  => $orientador['site'],
			];

			$retorno = $this->model->insert('orientador', $insert_db);

			if(!empty($retorno['status'])){
				$this->cadrastrar_relacao_trabalho_orientador($retorno['id'], $id_trabalho);
			}else{
				$this->view->warning_js('Ocorreu um erro ao cadastrar o orientador. Por favor edite o trabalho para corrigir', 'erro');
			}
		}
	}

	private function cadrastrar_relacao_trabalho_orientador($id_orientador, $id_trabalho){
		$insert_db = [
			'id_trabalho' => $id_trabalho,
			'id_orientador' => $id_orientador
		];

		$retorno = $this->model->insert('trabalho_relaciona_orientador', $insert_db);

		if(empty($retorno['status'])){
			$this->view->warning_js('Ocorreu um erro ao relacionar um orientador ao trabalho. Por favor edite o trabalho para corrigir', 'erro');
		}
	}

	private function cadastrar_autor($autores, $id_trabalho){
		foreach($autores as $indice => $autor){
			if(is_numeric($autor['autor'])){
				$this->cadrastrar_relacao_trabalho_autor($autor['autor'], $id_trabalho);
				continue;
			}

			$insert_db = [
				'nome'  => $autor['autor'],
				'email' => $autor['email'],
				'link'  => $autor['site'],
			];

			$retorno = $this->model->insert('autor', $insert_db);

			if(!empty($retorno['status'])){
				$this->cadrastrar_relacao_trabalho_autor($retorno['id'], $id_trabalho);
			}else{
				$this->view->warning_js('Ocorreu um erro ao cadastrar o autor. Por favor edite o trabalho para corrigir', 'erro');
			}
		}
	}

	private function cadrastrar_relacao_trabalho_autor($id_autor, $id_trabalho){
		$insert_db = [
			'id_trabalho' => $id_trabalho,
			'id_autor'    => $id_autor
		];

		$retorno = $this->model->insert('trabalho_relaciona_autor', $insert_db);

		if(empty($retorno['status'])){
			$this->view->warning_js('Ocorreu um erro ao relacionar um autor ao trabalho. Por favor edite o trabalho para corrigir', 'erro');
		}
	}

	private function cadastrar_palavra_chave($palavras_chave, $id_trabalho){
		$palavras_chave = explode(',', $palavras_chave);

		foreach($palavras_chave as $indice => $palavra){
			if(is_numeric($palavra)){
				$this->cadrastrar_relacao_trabalho_palavra_chave($palavra, $id_trabalho);
				continue;
			}

			$insert_db = [
				'palavra_chave'  => $palavra,
			];

			$retorno = $this->model->insert('palavra_chave', $insert_db);

			if(!empty($retorno['status'])){
				$this->cadrastrar_relacao_trabalho_palavra_chave($retorno['id'], $id_trabalho);
			}else{
				$this->view->warning_js('Ocorreu um erro ao cadastrar a palvra-chave. Por favor edite o trabalho para corrigir', 'erro');
			}
		}
	}

	private function cadrastrar_relacao_trabalho_palavra_chave($palavra_chave, $id_trabalho){
		$insert_db = [
			'id_trabalho'      => $id_trabalho,
			'id_palavra_chave' => $palavra_chave
		];

		$retorno = $this->model->insert('trabalho_relaciona_palavra_chave', $insert_db);

		if(empty($retorno['status'])){
			$this->view->warning_js('Ocorreu um erro ao relacionar a palavra-chave ao trabalho. Por favor edite o trabalho para corrigir', 'erro');
		}
	}

	private function cadastrar_relacao_trabalho_arquivo($arquivos, $id_trabalho){
		foreach($arquivos as $indice => $arquivo){
			if(!is_numeric($arquivo)){
				$this->view->warning_js('Ocorreu um erro ao relacionar o arquivo ao trabalho. Por favor edite o trabalho para corrigir', 'erro');
				continue;
			}

			$insert_db = [
				'id_trabalho' => $id_trabalho,
				'id_arquivo'  => $arquivo
			];

			$retorno = $this->model->insert('trabalho_relaciona_arquivo', $insert_db);

			if(empty($retorno['status'])){
				$this->view->warning_js('Ocorreu um erro ao relacionar o arquivo ao trabalho. Por favor edite o trabalho para corrigir', 'erro');
			}
		}
	}

	public function visualizar_front($id){
		$this->check_if_exists($id[0]);
		$cadastro = $this->model->carregar_trabalho($id[0])[0];
		$this->view->assign('cadastro', $cadastro);

		// debug2($cadastro);



		$this->view->render('front/cabecalho_rodape', $this->modulo['modulo'] . '/view/front/front');

		debug2($id);
		exit;

	}

	public function aprovar($parametros){
		$retorno_aprovar = $this->model->update('trabalho', ['id' => $parametros[0]], ['status' => 1]);

		if($retorno_aprovar['status']){
			$this->view->alert_js(ucfirst($this->modulo['modulo']) . ' aprovado com sucesso!!!', 'sucesso');
		} else {
			$this->view->alert_js('Ocorreu um erro ao aprovar o ' . strtolower($this->modulo['modulo']) . ', por favor tente novamente...', 'erro');
		}
	}
	public function reprovar($parametros){
		$retorno_reprovar = $this->model->update('trabalho', ['id' => $parametros[0]], ['status' => 2]);

		if($retorno_reprovar['status']){
			$this->view->alert_js(ucfirst($this->modulo['modulo']) . ' reprovado com sucesso!!!', 'sucesso');
		} else {
			$this->view->alert_js('Ocorreu um erro ao reprovar o ' . strtolower($this->modulo['modulo']) . ', por favor tente novamente...', 'erro');
		}
	}


}