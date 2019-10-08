<?php
namespace Model;

use Libs;
use \Libs\QueryBuilder\QueryBuilder;

class Usuario extends \Framework\Model{

	public function __construct() {
		parent::__construct();
	}

	public function carregar_listagem($busca, $datatable = null){
		$select = "SELECT"
			. " 	usuario.id,"
			. " 	usuario.email,"
			. " 	usuario.hierarquia,"
			. " 	usuario.ativo,"
			. " 	pessoa.nome,"
			. " 	pessoa.sobrenome"
			. " FROM"
			. " 	usuario usuario"
			. " LEFT JOIN pessoa pessoa ON pessoa.id_usuario = usuario.id AND pessoa.ativo = 1"
			. " WHERE"
			. " 	usuario.ativo = 1";

		if(isset($busca['search']['value']) && !empty($busca['search']['value'])){
			$select .= " AND usuario.id LIKE '%{$busca['search']['value']}%'";
			$select .= " OR usuario.email LIKE '%{$busca['search']['value']}%'";
			$select .= " OR usuario.hierarquia LIKE '%{$busca['search']['value']}%'";
		}

		if(isset($busca['order'][0])){
			if($busca['order'][0]['column'] == 0){
				$select .= " ORDER BY usuario.id {$busca['order'][0]['dir']}";
			}elseif($busca['order'][0]['column'] == 1){
				$select .= " ORDER BY usuario.email {$busca['order'][0]['dir']}";
			}elseif($busca['order'][0]['column'] == 2){
				$select .= " ORDER BY usuario.hierarquia {$busca['order'][0]['dir']}";
			}
		}

		if(isset($busca['start']) && isset($busca['length'])){
			$select .= " LIMIT {$busca['start']}, {$busca['length']}";
		}

		return $this->db->select($select);
	}

	public function create($table, $data) {

		$data += [
			'ativo' => 1,
		];

		// $data['senha'] = \Libs\Hash::create('sha1', $data['senha'], HASH_PASSWORD_KEY);

		return $this->insert($table, $data);
	}

	public function load_user_by_email($email){
		try {
			$select = "SELECT * FROM usuario WHERE email = '{$email}' AND ativo = 1";

			return $this->db->select($select);
		}catch(Exception $e){
            if (ERROS) throw new Exception('<pre>' . $e->getMessage() . '</pre>');
		}
	}

	public function check_token($token){
		try {
			$select = "SELECT * FROM usuario WHERE token = '{$token}'";
			return $this->db->select($select);
		}catch(Exception $e){
            if (ERROS) throw new Exception('<pre>' . $e->getMessage() . '</pre>');
		}
	}

	public function load_cadastro($id){
		return $this->query->select('
			usuario.*,
			pessoa.*
		')
			->from('usuario usuario')
			->leftJoin('pessoa pessoa ON pessoa.id_usuario = usuario.id AND pessoa.ativo = 1')
			->where("usuario.id = {$id} AND usuario.ativo = 1")
			->fetchArray();
	}

	public function carregar_usuario_por_id($id){
		$query = new QueryBuilder($this->db);
		$retorno = $query->select('usuario.*, pessoa.*')
		->from('usuario usuario')
		->leftJoin('pessoa pessoa ON pessoa.id_usuario = usuario.id AND pessoa.ativo = 1')
		->where("usuario.ativo = 1 AND usuario.id = {$id}")
		->fetchArray('first');

		return $retorno;
	}
}