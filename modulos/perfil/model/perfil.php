<?php
namespace Model;
use \Libs\QueryBuilder\QueryBuilder;


class Perfil extends \Framework\Model{
	public function carregar_dados_usuario($id_usuario){
		return $this->query->select('
				usuario.*,
				pessoa.*,
				professor.*
				aluno.*,
			')
			->from('usuario usuario')
			->leftJoin('pessoa pessoa ON pessoa.id_usuario = usuario.id AND pessoa.ativo = 1')
			->leftJoin('professor professor ON professor.id_usuario = usuario.id AND professor.ativo = 1')
			->leftJoin('aluno aluno ON aluno.id_usuario = usuario.id AND aluno.ativo = 1')
			->where("usuario.id = {$id_usuario}")
			->fetchArray();
	}
}
