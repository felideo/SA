<?php
namespace Model;

class Professor extends \Framework\Model{
	public function carregar_professor($id){
		$retorno = $this->query->select('
				professor.nome,
				usuario.email,
				pessoa.nome,
				pessoa.sobrenome
			')
			->from('professor professor')
			->leftJoin('usuario usuario ON usuario.id = professor.id_usuario AND usuario.ativo = 1')
			->leftJoin('pessoa pessoa ON pessoa.id_usuario = professor.id_usuario AND pessoa.ativo = 1')
			->where("professor.id = {$id} AND professor.ativo = 1")
	 		->fetchArray();

	 	return $retorno;
	}
}