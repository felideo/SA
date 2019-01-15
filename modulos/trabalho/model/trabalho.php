<?php
namespace Model;

use Libs;

class trabalho extends \Framework\Model{
	public function __construct() {
		parent::__construct();
	}

	public function carregar_listagem($busca, $datatable = null){

		$query = $this->query;

			$query->select('
				trabalho.titulo,
				trabalho.ano,
				trabalho.status,
				campus.campus,
				curso.curso,
				rel_autor.id,
				rel_orientador.id,
				autor.nome,
				orientador.nome,
			')
			->from('trabalho trabalho')
			->leftJoin('campus campus ON campus.id = trabalho.id_campus and campus.ativo = 1')
			->leftJoin('curso curso ON curso.id = trabalho.id_curso and curso.ativo = 1')
			->leftJoin('trabalho_relaciona_autor rel_autor ON rel_autor.id_trabalho = trabalho.id and rel_autor.ativo = 1')
			->leftJoin('trabalho_relaciona_orientador rel_orientador ON rel_orientador.id_trabalho = trabalho.id and rel_orientador.ativo = 1')
			->leftJoin('autor autor ON autor.id = rel_autor.id_autor AND autor.ativo = 1')
			->leftJoin('orientador orientador ON orientador.id = rel_orientador.id_orientador AND orientador.ativo = 1')
			->where('trabalho.ativo = 1');
			// ->whereIn($this->db->select("SELECT id from trabalho WHERE ativo = 1 LIMIT {$busca['start']}, {$busca['length']}"))

			if(isset($busca['search']['value']) && !empty($busca['search']['value'])){
				$query->where("trabalho.id LIKE '%{$busca['search']['value']}%'", 'AND')
					->where("trabalho.titulo LIKE '%{$busca['search']['value']}%'", 'OR');
			}

			if(isset($busca['order'][0])){
				if($busca['order'][0]['column'] == 0){
					$query->orderBy("trabalho.id {$busca['order'][0]['dir']}");
				}elseif($busca['order'][0]['column'] == 1){
					$query->orderBy("trabalho.titulo {$busca['order'][0]['dir']}");
				}
			}



			// $query->limitFrom(10, 1);

			$retorno = $query->fetchArray();

		// if(isset($busca['start']) && isset($busca['length'])){
		// 	$select .= " LIMIT {$busca['start']}, {$busca['length']}";
		// }

		return $retorno;
	}

	public function carregar_trabalho($id){
		$query = $this->query;

			$query->select('
				trabalho.titulo,
				trabalho.ano,
				trabalho.resumo,

				campus.campus,
				curso.curso,
				rel_autor.id,
				rel_orientador.id,
				rel_trabalho.id,
				rel_palavra.id,
				autor.nome,
				autor.email,
				autor.link,
				orientador.nome,
				orientador.email,
				orientador.link,

				arquivo.hash,
				arquivo.nome,
				arquivo.endereco,
				arquivo.tamanho,
				arquivo.extensao,

				palavra.palavra_chave
			')
			->from('trabalho trabalho')
			->leftJoin('campus campus ON campus.id = trabalho.id_campus and campus.ativo = 1')
			->leftJoin('curso curso ON curso.id = trabalho.id_curso and curso.ativo = 1')
			->leftJoin('trabalho_relaciona_autor rel_autor ON rel_autor.id_trabalho = trabalho.id and rel_autor.ativo = 1')
			->leftJoin('trabalho_relaciona_orientador rel_orientador ON rel_orientador.id_trabalho = trabalho.id and rel_orientador.ativo = 1')
			->leftJoin('autor autor ON autor.id = rel_autor.id_autor AND autor.ativo = 1')
			->leftJoin('orientador orientador ON orientador.id = rel_orientador.id_orientador AND orientador.ativo = 1')
			->leftJoin('trabalho_relaciona_arquivo rel_trabalho ON rel_trabalho.id_trabalho = trabalho.id and rel_trabalho.ativo = 1')
			->leftJoin('arquivo arquivo ON arquivo.id = rel_trabalho.id_arquivo AND arquivo.ativo = 1')
			->leftJoin('trabalho_relaciona_palavra_chave rel_palavra ON rel_palavra.id_trabalho = trabalho.id and rel_palavra.ativo = 1')
			->leftJoin('palavra_chave palavra ON palavra.id = rel_palavra.id_palavra_chave and palavra.ativo = 1')
			->where("trabalho.id = {$id}");

			return $query->fetchArray();
	}
}