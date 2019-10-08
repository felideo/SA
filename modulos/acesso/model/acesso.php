<?php
namespace Model;

class Acesso extends \Framework\Model{

	function __construct() {
		parent::__construct();
	}

	public function run($acesso) {

		\Libs\Session::init();

		$this->sign_in($acesso);

		$this->load_permissions();
		$this->load_modulos_and_menus();


		if(isset($_SESSION['logado']) && $_SESSION['logado'] == true){
			return true;
		} else {
			return false;
		}
	}

	public function run_back($acesso) {
		\Libs\Session::init();

		$this->sign_in($acesso);

		if(isset($_SESSION['logado']) && $_SESSION['logado'] == true){
			$this->load_permissions();
			$this->load_modulos_and_menus();

			return true;
		} else {
			return false;
		}
	}

	private function sign_in($acesso){
		$usuario = $this->query
			->select('usuario.*')
			->from('usuario usuario')
			->where("usuario.email = '{$acesso['email']}'")
			->where("usuario.senha = '{$acesso['senha']}'")
			->where("usuario.ativo = 1")
			->fetchArray()[0];

		if(isset($usuario) && !empty($usuario) && count($usuario) > 0 && $usuario !== false){
			$usuario = [
				'id'          => $usuario['id'],
				'nome'        => $usuario['email'],
				'hierarquia'  => $usuario['hierarquia'],
				'super_admin' => $usuario['super_admin']
			];

			\Libs\Session::set('logado', true);
			\Libs\Session::set('usuario', $usuario);
		}
	}

	private function load_modulos_and_menus(){
		$modulos = $this->db->select('SELECT * FROM modulo WHERE ATIVO = 1 ORDER BY ordem');
		$submenus = $this->db->select('SELECT * FROM submenu WHERE ATIVO = 1');

		foreach ($modulos as $indice_01 => $modulo) {
			if($modulo['hierarquia'] == 0 && empty($_SESSION['usuario']['super_admin'])){
				continue;
			}

			$retorno_modulos[$modulo['modulo']] = $modulo;

			if(empty($modulo['id_submenu'])){
				$menus[$modulo['nome']][0] = $modulo;
			} else {
				foreach ($submenus as $indice_02 => $submenu) {

					$menus[$submenu['nome']]['icone'] = $submenu['icone'];
					$menus[$submenu['nome']]['nome_exibicao'] = $submenu['nome_exibicao'];

					if($modulo['id_submenu'] == $submenu['id']){
						$menus[$submenu['nome']]['modulos'][$modulo['modulo']] = $modulo;
					}
				}

			}
		}

		\Libs\Session::set('modulos', $retorno_modulos);
		\Libs\Session::set('menus', $menus);
	}

	private function load_permissions(){
		try {

			$hierarquia = empty($_SESSION['usuario']['hierarquia']) ? 'NULL' : $_SESSION['usuario']['hierarquia'];

			$select = 'SELECT hierarquia.id as id_hierarquia, hierarquia.nome,'
				. ' relacao.id as id_relacao,'
				. ' permissao.id as id_permissao, permissao.permissao, permissao.id_modulo,'
				. ' modulo.modulo'
				. ' FROM hierarquia hierarquia'
				. ' LEFT JOIN hierarquia_relaciona_permissao relacao'
				. ' ON relacao.id_hierarquia = hierarquia.id AND relacao.ativo = 1'
				. ' LEFT JOIN permissao permissao'
				. ' ON permissao.id = relacao.id_permissao'
				. ' LEFT JOIN modulo modulo'
				. ' ON modulo.id = permissao.id_modulo'
				. ' WHERE hierarquia.id = ' . $hierarquia;

			$permissoes = $this->db->select($select);

			if(empty($permissoes)){
				\Libs\Session::set('permissoes', null);
				return;
			}

			foreach($permissoes as $indice => $permissao){
				$retorno_permissoes[$permissao['modulo']][$permissao['permissao']] = $permissao;
			}

		}catch(Exception $e){
            if (ERROS) throw new Exception('<pre>' . $e->getMessage() . '</pre>');

		}

		\Libs\Session::set('permissoes', $retorno_permissoes);
	}
}