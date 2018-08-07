<?php
namespace Model;

use Libs;

class Aluno extends \Framework\Model{
	public function carregar_ctdp($id_usuario = null){
		$data_limite = date('Y-m-d');

		$this->query->select('
			ctdp.*,
			professor.*,
			curso.*,
			turma.*,
			disciplina.*,
			semestre.*,
			rel_aluno.*
		')
		->from('usuario_professor_curso_turma_disciplina_semestre ctdp')
		->leftJoin('professor professor ON professor.id = ctdp.id_professor AND professor.ativo = 1')
		->leftJoin('curso curso ON curso.id = ctdp.id_curso AND curso.ativo = 1')
		->leftJoin('turma turma ON turma.id = ctdp.id_turma AND turma.ativo = 1')
		->leftJoin('disciplina disciplina ON disciplina.id = ctdp.id_disciplina AND disciplina.ativo = 1')
		->leftJoin('semestre semestre ON semestre.id = ctdp.id_semestre AND semestre.ativo = 1')
		->leftJoin('aluno_relaciona_semestre rel_aluno ON rel_aluno.id_ctdp = ctdp.id AND rel_aluno.ativo = 1')
		->where("ctdp.ativo = 1 AND semestre.termino >= '{$data_limite}'");

		// debug2($_SESSION['usuario']);
		// exit;

		// debug2($id_usuario);

		if($_SESSION['usuario']['hierarquia'] == 5){
			$this->query->andWhere("rel_aluno.id_usuario = {$_SESSION['usuario']['id']}");
		}

		if((isset($id_usuario) && !empty($id_usuario)) && $_SESSION['usuario']['hierarquia'] == 3){
				$this->query->andWhere("ctdp.id_usuario = {$id_usuario}");
		}

		return $this->query->fetchArray();
	}


	public function carregar_aluno($id){
		$this->query->select('
				rel_semestre.id_ctdp,
				aluno.nome,
				aluno.rgm,
				usuario.email,
				ctdp.id,
				curso.curso,
				turma.periodo,
				turma.semestre,
				turma.turma,
				disciplina.disciplina,
				semestre.identificador,

			')
			->from('aluno_relaciona_semestre rel_semestre')
			->leftJoin('aluno aluno ON aluno.id = rel_semestre.id_aluno AND aluno.ativo = 1')
			->leftJoin('usuario usuario ON usuario.id = rel_semestre.id_usuario AND usuario.ativo = 1')
			->leftJoin('usuario_professor_curso_turma_disciplina_semestre ctdp'
				. ' ON ctdp.id = rel_semestre.id_ctdp'
				. ' AND ctdp.ativo = 1'
			)

			->leftJoin('curso curso ON curso.id = ctdp.id_curso AND curso.ativo = 1')
			->leftJoin('turma turma ON turma.id = ctdp.id_turma AND turma.ativo = 1')
			->leftJoin('disciplina disciplina ON disciplina.id = ctdp.id_disciplina AND disciplina.ativo = 1')
			->leftJoin('semestre semestre ON semestre.id = ctdp.id_semestre AND semestre.ativo = 1')

			->where("rel_semestre.id_aluno = {$id} AND rel_semestre.ativo = 1");

		// if(!empty($filtros)){
		// 	foreach($filtros as $indice => $filtro){
		// 		$this->query->andWhere("aluno.{$indice} = '{$filtro}'");
		// 	}
		// }

		return $this->query->fetchArray();
	}
}