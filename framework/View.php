<?php
namespace Framework;

class View {
	private $dwoo;
	private $assign;

	function __construct(){
		$this->dwoo   = new \Dwoo\Core();
		$this->dwoo->setCompileDir('template_compile');
		$this->assign = new \Dwoo\Data();

		$this->assign('app_name', APP_NAME);
		$this->assign('_SESSION', $_SESSION);
	}

	public function assign($index, $data){
		$this->assign->assign($index, $data);
	}

	public function getAssign($data){
		return $this->assign->get($data);
	}

	public function render($header_footer, $body) {
		$template = new \Dwoo\Template\File('modulos/' . $body . '.html');

		if(strpos($header_footer, 'sidebar')){
			$this->mount_sidebar();
		}

		$header = new \Dwoo\Template\File('views/' 		. $header_footer 	. '/header.html');
		$body   = new \Dwoo\Template\File('modulos/' 	. $body 			. '.html');
		$footer = new \Dwoo\Template\File('views/' 		. $header_footer 	. '/footer.html');

		echo $this->dwoo->get($header, $this->assign);
		echo $this->dwoo->get($body, $this->assign);
		echo $this->dwoo->get($footer, $this->assign);

		if(isset($this->lazy_view) && !empty($this->lazy_view)){
			$lazy_view = new \Dwoo\Template\File('views/back/form_padrao/lazy_view.html');
			echo $this->dwoo->get($lazy_view, $this->assign);
		}

		exit;
	}

	private function mount_sidebar(){
		$array_menu = [];
		$submenus_com_permissao = [];

		$active = ($_SESSION['modulo_ativo'] == 'painel_controle') ? "active" : " ";

		$indice = \Util\Hash::get_unic_hash();

		$array_menu[$indice]['menu'] = "<li class='{$active}'>\n\t"
			. "<a href='/painel_controle'>\n\t\t"
			. "<span aria-hidden='true' class='fa fa-dashboard fa-fw'></span>\n\t\t"
            . "<span class='nav-label'>Painel de Controle</span>\n\t"
			. "</a>\n"
			. "</li>\n";

		$array_menu[$indice]['ordem'] = 1;

		foreach ($_SESSION['menus'] as $indice_01 => $menu){
			if(count($menu) == 1){
				if($_SESSION['usuario']['super_admin'] == 1 || isset($_SESSION['permissoes'][$menu[0]['modulo']])){

						$active = $menu[0]['modulo'] == $_SESSION['modulo_ativo'] ? "active" : " ";

						$menu_ordem  = $menu[0]['ordem'];
						$string_menu = "<li class=' {$active} '>\n\t"
							. " <a href='/{$menu[0]['modulo']}'>\n\t\t"
							. 		"<span aria-hidden='true' class='icon fa {$menu[0]['icone']} fa-fw'></span>\n\t\t";

							$menu_submenu_nome = isset($menu[0]['submenu']) && !empty($menu[0]['submenu']) ? $menu[0]['submenu'] : $menu[0]['nome'];

                         	$string_menu .= "<span class='nav-label'>{$menu_submenu_nome}</span>\n\t"
	            			. "</a>\n"
	            			. "</li>\n";
	            }
           	}elseif(count($menu) > 1){
           		if(!isset($menu['modulos']) || empty($menu['modulos'])){
           			continue;
           		}

				foreach ($menu['modulos'] as $indice_02 => $submenu){
					if($_SESSION['usuario']['super_admin'] == 1 || isset($_SESSION['permissoes'][$submenu['modulo']])){
						$submenus_com_permissao[] = $indice_01;

					}
				}
			}

			if(isset($string_menu)){
				$array_menu[\Util\Hash::get_unic_hash()] =[
					'menu'  => $string_menu,
					'ordem' => $menu_ordem,
				] ;
			}

			if(isset($string_menu)){
				unset($string_menu);
			}
		}

		if(!empty($submenus_com_permissao)){
			$submenus_com_permissao = array_unique($submenus_com_permissao);

			foreach ($submenus_com_permissao as $indice_03 => $submenus){
				$active = $menu[0]['modulo'] == $_SESSION['modulo_ativo'] ? "active" : " ";
				$menu_submenu = "<li  class=' {$active} '>\n\t"
         			. " <a href='#'>\n\t\t"
					. 		"<span aria-hidden='true' class='icon fa glyphicon {$_SESSION['menus'][$submenus]['icone']} fa-fw'></span>\n\t\t"
                    . 		"<span class='nav-label'>{$_SESSION['menus'][$submenus]['nome_exibicao']}</span>\n\t\t"
                    .		"<span class='fa arrow'></span>\n\t"
         			. " </a>\n\t"
     				. " <ul class='nav nav-second-level'>\n\t\t";

					$menu_ordem = 99999999999;
					foreach($_SESSION['menus'][$submenus]['modulos'] as $indice_04 => $submenu){
						if($_SESSION['usuario']['super_admin'] == 1 || isset($_SESSION['permissoes'][$submenu['modulo']])){

							if($submenu['ordem'] < $menu_ordem){
								$menu_ordem = $submenu['ordem'];
							}

 	                        $menu_submenu .= "<li class=' {$active} '>\n\t\t\t"
                         		. 	" <a href='/{$submenu['modulo']}'>\n\t\t\t\t"
								. 		"<span aria-hidden='true' class='icon fa glyphicon {$submenu['icone']} fa-fw'></span>\n\t\t\t\t"
 	                            . 		"<span class='nav-label'>{$submenu['nome']}</span>\n\t\t\t"
 	                            . 	" </a>\n\t\t"
 	                        	. "</li>\n\t";
						}
					}

                 	$menu_submenu .= "</ul>\n"
         				. "</li>";

        		$array_menu[\Util\Hash::get_unic_hash()] =[
					'menu'  => $menu_submenu,
					'ordem' => $menu_ordem,
				] ;

        		unset($menu_submenu);
			}
		}

		$array_menu = \Libs\Arrays::ordenarPorColuna($array_menu, 'ordem', 'asc');
		foreach($array_menu as $indice_05 => $item){
			$array_menu[$indice_05] = $array_menu[$indice_05]['menu'];
		}

		$array_menu = implode(' ', $array_menu);

		$this->assign('sidebar_painel_administrativo', $array_menu);
	}

	public function set_colunas_datatable($colunas){

		foreach ($colunas as $indice => $coluna) {
			if($indice == 0){
				$retorno_coluna[] = "<th aria-sort='ascending' colspan='1' rowspan='1' tabindex='0' class='sorting_asc'>{$coluna}</th>";
			} else {
				$retorno_coluna[] = "<th colspan='1' rowspan='1' tabindex='0' class='sorting'>{$coluna}</th>";

			}
		}

		$this->assign('colunas_datatable', $retorno_coluna);
	}


	public function warn_js($mensagem, $status){
		switch ($status) {
			case 'atencao':
				$status = 'warn';
				$icone = '<i class="fa fa-warning"></i>';
				break;

			case 'erro':
				$status = 'error';
				$icone = '<i class="fa fa-frown-o"></i>';
				break;

			case 'sucesso':
				$status = 'success';
				$icone = '<i class="fa fa-check"></i>';
				break;

			case 'info':
				$status = 'info';
				$icone = '<i class="fa fa-info-circle"></i>';
				break;

			case 'base':
				$status = 'base';
				$icone = '<i class="fa fa-send"></i>';
				break;
		}

		$mensagem = $icone . ' ' . $mensagem;

		$_SESSION['notificacoes'][] = ""
			. " setTimeout(function(){\n\t"
			. " 	$.notify('{$mensagem}', {\n\t\t"
	        . " 	    style: 'appkit',\n\t\t"
	        . " 	    className: '$status',\n\t\t"
	        . " 	    hideAnimation: 'fadeOut',\n\t\t"
	        . " 	    showAnimation: 'fadeIn',\n\t\t"
	        . " 		autoHideDelay: 5000,\n\t"
	        . " 	})\n"
	        . " }, 1000);\n\n";

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

	public function lazy_view(){
		$this->lazy_view = true;
	}



	public function default_buttons_listagem($id, $visualizar = true, $editar = true, $excluir = true){
		$protocolo = !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
		$url       = $protocolo . $_SERVER['HTTP_HOST'] . '/';

		$botao_visualizar = '';
		$botao_editar     = '';
		$botao_excluir    = '';

		if($visualizar){
			$botao_visualizar = \Util\Permission::check_user_permission($this->modulo['modulo'], "visualizar") ?
				"<a href='/{$this->modulo['modulo']}/visualizar/{$id}' title='Visualizar'><i class='botao_listagem fa fa-eye fa-fw'></i></a>" :
				'';
			}

		if($editar){
			$botao_editar = \Util\Permission::check_user_permission($this->modulo['modulo'], "editar") ?
				"<a href='/{$this->modulo['modulo']}/editar/{$id}' title='Editar'><i class='botao_listagem fa fa-pencil fa-fw'></i></a>" :
				 '';
		}

		if($excluir){
			$botao_excluir = \Util\Permission::check_user_permission($this->modulo['modulo'], "deletar") ?
				"<a class='validar_deletar' href='#' data-id_registro='{$id}' data-redirecionamento='{$url}/{$this->modulo['modulo']}/delete/{$id}' title='Deletar'><i class='botao_listagem  fa fa-trash-o fa-fw'></i></a>" :
				'';
		}

		return $botao_visualizar . $botao_editar . $botao_excluir;
	}
}