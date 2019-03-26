<?php
namespace Controllers;

use Libs;

class busca extends \Framework\Controller {

	private $modulo = [
		'modulo' 	=> 'busca',
		'name'		=> 'Busca',
		'send'		=> 'Buscar'
	];

	public function __construct() {
		parent::__construct();
		$this->view->modulo = $this->modulo;
	}

	public function index() {
		header('location: /busca/buscar');

	}

	public function buscar(){
		$organismo_model             = $this->load_external_model('organismo');
		$min_max_n_petalas_e_estames = $organismo_model->get_min_max_n_petalas_e_estames();

		$this->view->min_max_n_petalas_e_estames = $min_max_n_petalas_e_estames;
		$this->view->render('front/cabecalho_rodape' ,'front/busca/busca');
	}

	public function buscar_taxonomia_select2(){
		$busca       = carregar_variavel('busca');
		$taxon_model = $this->load_external_model('taxon');
		$retorno     = $taxon_model->buscar_taxon($busca);

		echo json_encode($retorno);
		exit;
	}

	public function buscar_nome_popular_select2(){
		$busca           = carregar_variavel('busca');
		$organismo_model = $this->load_external_model('organismo');
		$retorno         = $organismo_model->buscar_nome_popular($busca);

		echo json_encode($retorno);
		exit;
	}

	public function buscar_ano_select2(){
		$busca = carregar_variavel('busca');
		$retorno = $this->model->db->select("SELECT id, ano FROM trabalho WHERE ano LIKE '%{$busca['nome']}%' AND ativo = 1");

		foreach ($retorno as &$item) {
			$item['id'] = $item['ano'];
		}
		echo json_encode($retorno);
		exit;

	}

	public function buscar_hierarquia_ajax(){
		$busca = carregar_variavel('id_clado');

		echo json_encode($this->model->buscar_hierarquia($busca));
		exit;
	}

	public function efetuar_busca(){
		$busca = carregar_variavel('busca');

		$retorno = $this->model->efetuar_busca($busca);

		ob_clean();

		echo json_encode($retorno);
		exit;
	}



}