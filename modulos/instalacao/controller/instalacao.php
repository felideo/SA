<?php
namespace Controller;

use Libs;

class Instalacao extends \Framework\Controller {

	protected $modulo = [
		'modulo' 	=> 'instalacao',
		'name'		=> 'Instalação',
		'send'		=> 'Instalar'
	];

	function __construct() {
		parent::__construct();
		$this->view->assign('modulo', $this->modulo);
	}

	public function index() {
		$this->view->render('back/cabecalho_rodape', $this->modulo['modulo'] . '/view/back/instalacao');
	}

	public function install() {
		$instalacao = carregar_variavel('instalacao');

		$this->create_config_file($instalacao);

		$retorno  = $this->model->create_database($database, $user);

		if(!empty($retorno['sucesso']) && $retorno['sucesso'] == true){
			$this->view->alert_js('Aplicação instalada com sucesso!!!', 'sucesso');
			header('location: ' . URL . 'login');
		} else {
			$this->view->alert_js('Ocorreu um erro ao efetuar a instalação da aplicação, por favor tente novamente...', 'erro');
			header('location: ' . URL . 'install');
		}
	}

	private function create_config_file($instalacao){
		$config_file = "<?php\n"
			. "// Configuração do Fuso Horário\n\n"
			. "date_default_timezone_set('America/Sao_Paulo');\n\n"

			. "// Sempre use barra (/) no final das URLs\n"
			. "define('URL', 'http://swdb.localhost');\n"
			. "// define('URL', 'http://leaflivedb.felideo.com.br/');\n\n"

			. "define('LIBS', 'libs/');\n\n"

			. "// Configuração com Banco de Dados\n"
			. "define('DB_TYPE', 'mysql');\n"
			. "define('DB_HOST', '127.0.0.1');\n"
			. "define('DB_NAME', 'SWDB');\n"
			. "define('DB_USER', 'root');\n"
			. "define('DB_PASS', 'lilith');\n\n"

			. "define('DEVELOPER', true);\n"
			. "define('PREVENT_CACHE', true);\n\n"

			. "define('APP_NAME', 'Scientific Work DB');\n\n"

			. "error_reporting(E_ALL);\n"
			. "ini_set('display_errors', 1);\n\n"

			. "if (function_exists('xdebug_disable')){\n"
			. "	xdebug_disable();\n"
			. "}\n";
	}
}