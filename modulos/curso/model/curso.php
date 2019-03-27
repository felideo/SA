<?php
namespace Model;
use Libs;

class Curso extends \Framework\Model{
	public function buscar_curso($busca){
		$select = "SELECT"
			. " 	curso.id,"
			. " 	curso.curso"
			. " FROM"
			. " 	curso curso"
			. " WHERE"
			. " 	curso.curso LIKE '%{$busca['nome']}%'"
			. " AND curso.ativo = 1";

			if(isset($busca['page_limit'])){
				$select .= " LIMIT {$busca['page_limit']}";
			}

		return $this->select($select);
	}
}
