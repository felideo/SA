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

	public function insert_update($usuario, $where = []){
		$usuario['usuario']['email']      = $usuario['professor']['email'];
		$usuario['usuario']['hierarquia'] = 3;
		$usuario['professor']['nome']     = $usuario['pessoa']['nome'] . ' ' . $usuario['pessoa']['sobrenome'];

		unset($usuario['professor']['email']);

		if(empty($where['id'])){
			$usuario['usuario']['senha'] = \Libs\Hash::get_unic_hash();
			$usuario['usuario']['ativo'] = 1;
			$where['email']              = $usuario['usuario']['email'];
		}

		$usuario['usuario']['retorno'] = $this->model->insert_update(
			'usuario',
			$where,
			$usuario['usuario'],
			true
		);

		if(!empty($usuario['usuario']['retorno']['status'])){
			$usuario['pessoa']['id_usuario']    = $usuario['usuario']['retorno']['id'];
			$usuario['professor']['id_usuario'] = $usuario['usuario']['retorno']['id'];


			$usuario['pessoa']['retorno'] = $this->model->insert_update(
				'pessoa',
				['id_usuario' => $usuario['pessoa']['id_usuario']],
				$usuario['pessoa'],
				true
			);

			$usuario['professor']['retorno'] = $this->model->insert_update(
				'professor',
				['id_usuario' => $usuario['professor']['id_usuario']],
				$usuario['professor'],
				true
			);

			// $verificacao_usuario = $this->model->load_cadastro($usuario['usuario']['retorno']['id'])[0];


			// if(!empty($verificacao_usuario['ativo']) && empty($verificacao_usuario['bloqueado'])){
			// 	$email = new \Libs\Mail();

			// 	$msg = "Olá {$verificacao_usuario['pessoa'][0]['nome']} {$verificacao_usuario['pessoa'][0]['sobrenome']}<br><br>"
			// 		. " Voce foi cadastrado no sistema SWDB - http://swdb.felideo.com<br>"
			// 		. " Sua senha é: {$verificacao_usuario['senha']}<br><br>"
			// 		. " Para acessar use o link: http://swdb.felideo.com/acesso/admin";

			// 	$email->set_from(EMAIL_EMAIL)
			// 		->set_pass(EMAIL_SENHA)
			// 		->set_to(trim($verificacao_usuario['email']))
			// 		->set_assunto('Cadastro no SWDB')
			// 		->set_mensagem($msg)
			// 		->send_mail();
			// }
		}

		if(!empty($usuario['usuario']['retorno']['status'] && $usuario['pessoa']['retorno']['status'])){
			$usuario['status'] = $usuario['usuario']['retorno']['status'] && $usuario['pessoa']['retorno']['status'];
			return $usuario;
		}

		return $usuario;
	}

	public function middle_editar($id) {
		$cadastro = $this->model->carregar_professor($id[0])[0];
		$this->view->assign('cadastro', $cadastro);
	}

	public function middle_visualizar($id){
		$cadastro = $this->model->carregar_professor($id[0])[0];
		$this->view->assign('cadastro', $cadastro);
	}

	public function middle_delete($id) {
		$id_usuario = $this->model->full_load_by_id('professor', $id)['id_usuario'];

		$retorno           	= $this->model->delete($this->modulo['modulo'], ['id_usuario' => $id_usuario]);
		$retorno_usuario 	= $this->model->delete('usuario', ['id' => $id_usuario]);
		$retorno_pessoa    	= $this->model->delete('pessoa', ['id_usuario'  => $id_usuario]);

		return $retorno_usuario;
	}
}

