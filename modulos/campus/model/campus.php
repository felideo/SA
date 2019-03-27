<?php
namespace Model;
use Libs;

class Campus extends \Framework\Model{
	public function buscar_campus($busca){
		$select = "SELECT"
			. " 	campus.id,"
			. " 	campus.campus"
			. " FROM"
			. " 	campus campus"
			. " WHERE"
			. " 	campus.campus LIKE '%{$busca['nome']}%'"
			. " AND campus.ativo = 1";

			if(isset($busca['page_limit'])){
				$select .= " LIMIT {$busca['page_limit']}";
			}

		return $this->select($select);
	}
}
