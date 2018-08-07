<?php
namespace Model;

class Semestre extends \Framework\Model{
	public function semestre($id){
		$retorno = $this->query->select('
				semestre.identificador,
				semestre.termino,
				semestre.inicio,

				ctdp.id_usuario,
				ctdp.id_professor,
				ctdp.id_curso,
				ctdp.id_turma,
				ctdp.id_disciplina,
				ctdp.id_semestre,
			')
			->from('semestre semestre')
			->leftJoin('usuario_professor_curso_turma_disciplina_semestre ctdp ON ctdp.id_semestre = semestre.id AND ctdp.ativo = 1')
			->where("semestre.id = {$id} AND semestre.ativo = 1")
			->fetchArray();

		return $retorno;
	}
}
