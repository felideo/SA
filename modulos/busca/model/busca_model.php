<?php
namespace Models;

use Libs;
use \Libs\QueryBuilder\QueryBuilder;
// use \Libs\QueryBuilder\QueryBuilder;
// include "vendor/felideotrine/query-builder/FelideoTrine/QueryBuilder.php";
// use \Felideo\FelideoTrine;
/**
* Classe Index_Model
*/
class Busca_Model extends \Framework\Model{
	public function __construct() {
		parent::__construct();
	}

	public function efetuar_busca($busca){
		$all_empty = true;
		foreach ($busca as $filtro) {
			if(!empty($filtro)){
				$all_empty = false;
				break;
			}
		}

		if($all_empty){
			return null;
		}

		$query = new QueryBuilder($this->db);

		$query->select('
			organismo.id,
			organismo.nome,
			organismo.descricao,
			popular.nome,
			rel_org_trab.id,
			trabalho.id,
			rel_trab_pal.id,
			palavra.id
		')
		->from('organismo organismo')
		->leftJoin('nome_popular popular ON popular.id = organismo.id AND popular.ativo = 1')
		->leftJoin('organismo_relaciona_trabalho rel_org_trab ON rel_org_trab.id_organismo = organismo.id AND rel_org_trab.ativo = 1')
		->leftJoin('trabalho trabalho ON trabalho.id = rel_org_trab.id_trabalho AND trabalho.ativo = 1')
		->leftJoin('trabalho_relaciona_palavra_chave rel_trab_pal ON rel_trab_pal.id_trabalho = trabalho.id AND rel_trab_pal.ativo = 1')
		->leftJoin('palavra_chave palavra ON palavra.id = rel_trab_pal.id_palavra_chave AND palavra.ativo = 1');




		if(!empty($busca['id_taxon'])){
			$query->where("organismo.localizador LIKE '%-{$busca['id_taxon']}-%'");
		}

		if(!empty($busca['id_nome_popular'])){
			$query->where("popular.id = {$busca['id_nome_popular']}");
		}

		if(!empty($busca['numero_petalas'])){
			$query->where("organismo.numero_petalas = {$busca['numero_petalas']}");
		}

		if(!empty($busca['numero_estames'])){
			$query->where("organismo.numero_estames = {$busca['numero_estames']}");
		}

		if(!empty($busca['forma_folha'])){
			$query->where("organismo.forma_folha = '{$busca['forma_folha']}'");
		}

		if(!empty($busca['autor'])){
			$query->where("trabalho.id_autor = {$busca['autor']}");
		}

		if(!empty($busca['idioma'])){
			$query->whereIn("trabalho.id_idioma IN ({$busca['idioma']})");
		}

		if(!empty($busca['ano'])){
			$query->whereIn("trabalho.ano IN ({$busca['ano']})");
		}

		if(!empty($busca['palavra_chave'])){
			$query->whereIn("palavra.id IN ({$busca['palavra_chave']})");
		}

		$query->where("organismo.ativo = 1");

		return $query->fetchArray();
	}
}
