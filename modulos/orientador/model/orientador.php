<?php
namespace Model;
use Libs;

class Orientador extends \Framework\Model{
	public function buscar_orientador($busca){
		$select = "SELECT"
			. " 	orientador.id,"
			. " 	orientador.nome,"
			. " 	orientador.email,"
			. " 	orientador.link"
			. " FROM"
			. " 	orientador orientador"
			. " WHERE"
			. " 	orientador.nome LIKE '%{$busca['nome']}%'"
			. " AND orientador.ativo = 1";

			if(isset($busca['page_limit'])){
				$select .= " LIMIT {$busca['page_limit']}";
			}

		return $this->db->select($select);
	}
}
