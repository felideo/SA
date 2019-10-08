<?php
namespace Model;
use Libs;

class Autor extends \Framework\Model{
	public function buscar_autor($busca){
		$select = "SELECT"
			. " 	autor.id,"
			. " 	autor.nome,"
			. " 	autor.email,"
			. " 	autor.link"
			. " FROM"
			. " 	autor autor"
			. " WHERE"
			. " 	autor.nome LIKE '%{$busca['nome']}%'"
			. " AND autor.ativo = 1";

			if(isset($busca['page_limit'])){
				$select .= " LIMIT {$busca['page_limit']}";
			}

		return $this->db->select($select);
	}
}
