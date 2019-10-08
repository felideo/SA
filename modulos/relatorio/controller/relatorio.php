<?php
namespace Controller;

use Libs;

class Relatorio extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' 	=> 'relatorio',
		'name'		=> 'Relatorios',
		'send'		=> 'Relatorio'
	];

	public function index() {
		$front_controller = $this->get_controller('front');
		$front_controller->carregar_cabecalho_rodape();

		$query = carregar_variavel('query');


		if(!empty($query['busca_textual'])){
			$this->view->assign('query', $query['busca_textual']);
			$query = implode(' ', $this->validar_parametros_busca_textual($query['busca_textual']));
			$this->view->assign('retorno_busca_simples', $this->get_dados_trabalho($this->busca_textual($query)));
		}

		if(!empty($query['busca_avancada'])){
			$this->view->assign('retorno_busca_simples', $this->get_dados_trabalho($this->buscar_avancada($query)));
		}

		$this->view->assign('anos', $this->carregar_anos());

		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/front/busca');
	}

	private function carregar_anos(){
		$anos = [];

		foreach($this->model->select("SELECT ano FROM trabalho GROUP BY ano") as $indice => $ano){
			$anos[] = [
				'id'   => $ano['ano'],
				'text' => $ano['ano']
			];
		}

		return $anos;
	}

	public function busca_textual($termo){
		$elastic_search = new \Libs\ElasticSearch\ElasticSearch();
		return $elastic_search->busca_textual($termo);
	}

	public function buscar_avancada($termo){
		$elastic_search = new \Libs\ElasticSearch\ElasticSearch();
		return $elastic_search->buscar_avancada($termo);
	}

	private function get_dados_trabalho($encontrados){
		if(empty($encontrados['hits']['hits'])){
			return [
				'status'     => false,
				'resultados' => []
			];
		}

		$trabalhos = [];

		foreach($encontrados['hits']['hits'] as $indice => $item){
			// if($item['_score'] < 1){
			// 	continue;
			// }

			$trabalhos[] = $item['_id'];
		}

		$model_trabalho = $this->get_model('trabalho');

		if(empty($trabalhos)){
			return [
				'status'     => false,
				'resultados' => []
			];
		}

		return [
			'status'     => true,
			'resultados' => $model_trabalho->carregar_resultado_busca($trabalhos)
		];
	}

	private function validar_parametros_busca_textual($query){
		$query = \Libs\Strings::limparString($query);
		$query = explode(' ', $query);

		foreach($query as $indice => $palavra){
			if(strlen($palavra) < 4){
				unset($query[$indice]);
			}
		}

		if(count($query) < 2){
			$this->view->alert_js('Obrigatorio efetuar a busca com no minimo duas palavras! Palavra com 3 caracteres ou menos são desconsideradas!', 'erro');
			header('location: ' . $_SERVER['HTTP_REFERER']);
			exit;
		}

		return array_values($query);
	}




















	public function buscar_taxonomia_select2(){
		$busca       = carregar_variavel('busca');
		$taxon_model = $this->get_model('taxon');
		$retorno     = $taxon_model->buscar_taxon($busca);

		echo json_encode($retorno);
		exit;
	}

	public function buscar_nome_popular_select2(){
		$busca           = carregar_variavel('busca');
		$organismo_model = $this->get_model('organismo');
		$retorno         = $organismo_model->buscar_nome_popular($busca);

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