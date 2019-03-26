<?php
namespace Model;

use Libs;

class Instalacao {
	public function __construct() {
	}

	public function create_database($dados, $usuario){

	    try {
	        $db = new \PDO("mysql:host=" . $dados['host'], $dados['user'], $dados['password']);
	        $create = "CREATE DATABASE `" . $dados['name'] . "` CHARACTER SET utf8 COLLATE utf8_general_ci;";
	        $retorno = [
		    	"sucesso" 	=> $db->exec($create),
		    	"erro" 		=> $db->errorCode(),
		    	"info" 		=> $db->errorInfo()
		    ];
	    } catch (PDOException $e) {
	        die("DB ERROR: ". $e->getMessage());
	    }

	    debug2($retorno);

	    if($retorno['sucesso'] == true){
	    	$this->create_tables_and_inserts($dados, $usuario);
	    }

	    return $retorno;
	}

	private function create_tables_and_inserts($dados, $usuario){

		try {
		 	$db = new \PDO("mysql:dbname=" . $dados['name'] . ";host=" . $dados['host'], $dados['user'], $dados['password']);

		    $create = "CREATE TABLE `usuario` (
							`id`           			int(11) NOT NULL AUTO_INCREMENT,
							`email`					varchar(256) NOT NULL,
							`senha` 				varchar(64) NOT NULL,
							`hierarquia` 			int(11) NOT NULL,
							`ativo` 				tinyint(1) NOT NULL DEFAULT '1',
							PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

		    $db->exec($create);

		    $create = "CREATE TABLE `modulo` (
						`id` 					int(11) 	NOT NULL AUTO_INCREMENT,
						`modulo` 				varchar(64) NOT NULL,
						`nome` 					varchar(64) NOT NULL,
						`hierarquia` 			int(11) 	NOT NULL,
						`icone` 				varchar(64) NOT NULL DEFAULT 'fa-angle-right',
						`oculto` 				tinyint(1) 	NOT NULL DEFAULT '0',
						`ordem`					int(11) 	NOT NULL DEFAULT '1000',
						`ativo` 				tinyint(1) 	NOT NULL DEFAULT '1',
						PRIMARY KEY (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

		    $db->exec($create);

		    $create = "CREATE TABLE `configuracao` (
		    			`id` 					int(11) NOT NULL AUTO_INCREMENT,
		    			`nome_aplicacao` 		varchar(64) NOT NULL,
		    			`id_google_analytics` 	varchar(64) NOT NULL,
		    			PRIMARY KEY (`id`)
		    		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

		    $db->exec($create);

		    $create = "INSERT INTO `usuario` VALUES ('1', '" . $usuario['usuario'] . "', '" . $usuario['senha'] . "', '0', '1');";

		    $db->exec($create);

		    $create = "INSERT INTO `modulo` (`id`, `modulo`, `nome`, `hierarquia`, `icone`, `oculto`, `ativo`) VALUES
					(1, 'modulo', 'Modulos', 0, 'fa-check-square-o', 0, 1),
					(2, 'usuario', 'Usuarios', 5, 'fa-users', 0, 1),
					(3, 'configuracao', 'Configuracoes', 1, 'fa-arrows-h', 0, 1);";

		    $db->exec($create);
		} catch(PDOException $e) {
		    die("DB ERROR: ". $e->getMessage());
		}
	}
}


