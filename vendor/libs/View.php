<?php
namespace Libs;

/**
*	classe View
*/
class View {
	function __construct(){
	}


	public function render($header_footer, $body) {
		if(!file_exists('views/' . $header_footer . '/header.php')){
			$e = new \Exception('Cabeçalho: views/' . $header_footer . '/header.php não existe!');
			debug2($e->getMessage());
            debug2($e->getTrace());
            exit;
		}

		if(!file_exists('views/' . $header_footer . '/footer.php')){
			$e = new \Exception('Rodape: views/render/' . $header_footer . '/footer.php não existe!');
			debug2($e->getMessage());
            debug2($e->getTrace());
            exit;
		}

		if(!file_exists('views/' . $body . '.php')){
			$e = new \Exception('View não existe!');
			debug2($e->getMessage());
            debug2($e->getTrace());
            exit;
		}

		require 'views/' . $header_footer . '/header.php';
		require 'views/' . $body . '.php';
		require 'views/' . $header_footer . '/footer.php';
	}

	// public function render($name, $noInclude = false) {
	// 	if($noInclude == true){
	// 		require 'views/' . $name . '.php';
	// 	} else {
	// 		require 'views/render/render/header.php';
	// 		require 'views/' . $name . '.php';
	// 		require 'views/render/render/footer.php';
	// 	}
	// }

	public function clean_render($name) {
		require 'views/render/clean_render/header.php';
		require 'views/' . $name . '.php';
		require 'views/render/clean_render/footer.php';
	}

	public function front_render($name) {
		require 'views/render/front_render/header.php';
		require 'views/' . $name . '.php';
		require 'views/render/front_render/footer.php';
	}

	public function sub_render($name, $name_2 = null) {
		if(!is_null($name_2)){
			require 'views/render/render/header.php';
			require 'views/' . $name . '/' . $name_2 . '.php';
			require 'views/render/render/footer.php';
		} else {
		 	require 'views/render/render/header.php';
			require 'views/' . $name . '/' . $name . '.php';
			require 'views/render/render/footer.php';
		}
	}

	public function set_colunas_datatable($colunas){

		foreach ($colunas as $indice => $coluna) {
			if($indice == 0){
				$retorno_coluna[] = "<th aria-sort='ascending' colspan='1' rowspan='1' tabindex='0' class='sorting_asc'>{$coluna}</th>";
			} else {
				$retorno_coluna[] = "<th colspan='1' rowspan='1' tabindex='0' class='sorting'>{$coluna}</th>";

			}
		}

		$this->colunas_datatable = $retorno_coluna;
	}

	public function alert_js($mensagem, $status){
		switch ($status) {
			case 'atencao':
				$status = 'warning';
				$title = "Atenção!";
				break;

			case 'erro':
				$status = 'error';
				$title = "Erro!";
				break;

			case 'sucesso':
				$status = 'success';
				$title = "Sucesso!";
				break;

			case 'info':
				$status = 'info';
				$title = "Info!";
				break;
		}

		$_SESSION['alertas'] = ""
			. " 	swal({\n"
			. "			title: '{$title}',\n"
			. "  		\ttext: '{$mensagem}',\n"
			. "  		\ttype: '{$status}',\n"
			. "  		\tconfirmButtonText: 'OK'\n"
			. "		},\n"
			. " 	\tfunction(){\n"
			. "			console.log('lerolero');\n"
			. " 		\t$.ajax({\n"
			. "			\turl: '/master/limpar_alertas_ajax',\n"
			. " 		\tsuccess: function(retorno){\n"
			. "				\tconsole.log(retorno);\n"
			. "   		\t}\n"
			. "		\t})\n"
			. "		});";
	}

	public function default_buttons_listagem($id, $visualizar = true, $editar = true, $excluir = true){
		$botao_visualizar = '';
		$botao_editar     = '';
		$botao_excluir    = '';

		if($visualizar){
			$botao_visualizar = \Util\Permission::check_user_permission($this->modulo['modulo'], $this->modulo['modulo'] . "_" . "visualizar") ?
				"<a href='/{$this->modulo['modulo']}/visualizar/{$id}' title='Visualizar'><i class='fa fa-eye fa-fw'></i></a>" :
				'';
			}

		if($editar){
			$botao_editar = \Util\Permission::check_user_permission($this->modulo['modulo'], $this->modulo['modulo'] . "_" . "editar") ?
				"<a href='/{$this->modulo['modulo']}/editar/{$id}' title='Editar'><i class='fa fa-pencil fa-fw'></i></a>" :
				 '';
		}

		if($excluir){
			$botao_excluir = \Util\Permission::check_user_permission($this->modulo['modulo'], $this->modulo['modulo'] . "_" . "deletar") ?
				"<a href='/{$this->modulo['modulo']}/delete/{$id}' title='Deletar'><i class='fa fa-trash-o fa-fw'></i></a>" :
				'';
		}

		return $botao_visualizar . $botao_editar . $botao_excluir;
	}

}