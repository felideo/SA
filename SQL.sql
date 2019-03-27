CREATE TABLE `arquivo` (
	`id`       INT(11) 			NOT NULL AUTO_INCREMENT,
	`hash`     VARCHAR(32) 	NOT NULL,
	`nome`     VARCHAR(128) 	NOT NULL,
	`endereco` VARCHAR(256) 	NOT NULL,
	`tamanho`  DECIMAL	 		NOT NULL,
	`extensao` VARCHAR(16) 		NOT NULL,
	`ativo`    TINYINT(1) 		NOT NULL DEFAULT '1',
	PRIMARY    KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8;

CREATE TABLE `hierarquia` (
  `id`    			int(11) 		NOT NULL AUTO_INCREMENT,
  `nome`  			varchar(64) 	NOT NULL,
  `ativo` 			tinyint(1) 	NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `usuario` (
  `id`          	int(11) 		NOT NULL AUTO_INCREMENT,
  `email`       	varchar(256) 	NOT NULL,
  `senha`       	varchar(64) 	NOT NULL,
  `hierarquia`  	int(11) 		NULL,
  `super_admin` 	tinyint(1) 		NOT NULL DEFAULT '0',
  `ativo`       	tinyint(1) 		NOT NULL DEFAULT '1',
  PRIMARY       KEY (`id`),
  KEY           `hierarquia` (`hierarquia`),
  CONSTRAINT    `usuario_ibfk_1` FOREIGN KEY (`hierarquia`) REFERENCES `hierarquia` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT INTO `hierarquia`
  VALUES
	(1,'Administrador',1);

INSERT INTO `usuario`
  VALUES
	(1,'felideo@gmail.com','12345',NULL,1,1);

CREATE TABLE `submenu` (
  `id`            int(11) NOT NULL AUTO_INCREMENT,
  `nome`          varchar(64) NOT NULL,
  `icone`         varchar(64) NOT NULL DEFAULT 'fa-angle-right',
  `nome_exibicao` varchar(64) NOT NULL,
  `ativo`         tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY         KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `modulo` (
  `id`         int(11) NOT NULL AUTO_INCREMENT,
  `modulo`     varchar(64) NOT NULL,
  `nome`       varchar(64) NOT NULL,
  `id_submenu` int(11) DEFAULT NULL,
  `hierarquia` int(11) NOT NULL,
  `icone`      varchar(64) NOT NULL DEFAULT 'fa-angle-right',
  `oculto`     tinyint(1) NOT NULL DEFAULT '0',
  `ordem`      int(11) NOT NULL DEFAULT '1000',
  `ativo`      tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY      KEY (`id`),
  KEY          `id_submenu` (`id_submenu`),INSERT INTO gestaoja2_basico2.plano_assinatura_controle_desconto_periodo_inicial
VALUES(2070, 'gazetaonline', 'Preco Normal', 2026314, 70437, 4380, 120, '2018-04-16', '2028-04-13', 15.9000, 15.9000, 0, 3, 0, 1);
  CONSTRAINT   `modulo_ibfk_1` FOREIGN KEY (`id_submenu`) REFERENCES `submenu` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `permissao` (
  `id`        int(11) NOT NULL AUTO_INCREMENT,
  `id_modulo` int(11) NOT NULL,
  `permissao` varchar(64) NOT NULL,
  PRIMARY     KEY (`id`),
  KEY         `id_modulo` (`id_modulo`),
  CONSTRAINT  `permissao_ibfk_1` FOREIGN KEY (`id_modulo`) REFERENCES `modulo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `hierarquia_relaciona_permissao` (
  `id`            int(11) NOT NULL AUTO_INCREMENT,
  `id_hierarquia` int(11) NOT NULL,
  `id_permissao`  int(11) NOT NULL,
  `ativo`         tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY         KEY (`id`),
  KEY             `id_hierarquia` (`id_hierarquia`),
  KEY             `id_permissao` (`id_permissao`),
  CONSTRAINT      `hierarquia_relaciona_permissao_ibfk_1` FOREIGN KEY (`id_hierarquia`) REFERENCES `hierarquia` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT      `hierarquia_relaciona_permissao_ibfk_2` FOREIGN KEY (`id_permissao`) REFERENCES `permissao` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


INSERT INTO `submenu`
  VALUES
	(1,'desenvolvedor','fa-github',1,'Desenvolvedor');

INSERT INTO `modulo`
  VALUES
	(1,'modulo','Modulos',1,0,'fa-check-square-o',0,1000,1),
	(2,'usuario','Usuarios',NULL,1,'fa-users',0,1000,1),
	(3,'configuracao','Configurações',1,0,'fa-arrows-h',0,1000,1),
	(4,'submenu','Submenus',1,0,'fa-sitemap',0,1000,1),
	(5,'hierarquia','Hierarquias',NULL,1,'fa-sitemap',0,1000,1);

INSERT INTO `permissao`
  VALUES
	(1,1,'criar'),
	(2,1,'visualizar'),
	(3,1,'editar'),
	(4,1,'deletar'),
	(5,2,'criar'),
	(6,2,'visualizar'),
	(7,2,'editar'),
	(8,2,'deletar'),
	(9,3,'criar'),
	(10,3,'visualizar'),
	(11,3,'editar'),
	(12,3,'deletar'),
	(13,4,'criar'),
	(14,4,'visualizar'),
	(15,4,'editar'),
	(16,4,'deletar'),
	(17,5,'criar'),
	(18,5,'visualizar'),
	(19,5,'editar'),
	(20,5,'deletar');

CREATE TABLE `autor` (
	`id`    INT(11) 			NOT NULL AUTO_INCREMENT,
	`nome`  VARCHAR(256) 		NOT NULL,
	`email` VARCHAR(256) 		NULL,
	`link`  VARCHAR(256) 		NULL,
	`ativo` TINYINT(1) 			NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8;

CREATE TABLE `orientador` (
	`id`    INT(11) 			NOT NULL AUTO_INCREMENT,
	`nome`  VARCHAR(256) 		NOT NULL,
	`email` VARCHAR(256) 		NULL,
	`link`  VARCHAR(256) 		NULL,
	`ativo` TINYINT(1) 			NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8;

CREATE TABLE `idioma` (
	`id`     INT(11) 			NOT NULL AUTO_INCREMENT,
	`idioma` VARCHAR(64) 		NOT NULL,
	`ativo`  TINYINT(1) 		NOT NULL DEFAULT '1',
	PRIMARY  KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8;



CREATE TABLE `trabalho` (
	`id`            INT(11) 		NOT NULL AUTO_INCREMENT,
	`titulo`        TEXT 			NOT NULL,
	`ano`           INT(4) 			NOT NULL,
	`resumo`        TEXT 			NOT NULL,
	`id_idioma`     INT(11) 		NULL,
	`id_curso`     	INT(11) 		NOT NULL,
	`id_campus`     INT(11) 		NOT NULL,
	`ativo`        	TINYINT(1) 		NOT NULL DEFAULT '1',
	PRIMARY        KEY (`id`),
	FOREIGN        KEY (`id_idioma`) 	REFERENCES `idioma` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
	FOREIGN        KEY (`id_curso`) 	REFERENCES `curso` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
	FOREIGN        KEY (`id_campus`) 	REFERENCES `campus` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8;


CREATE TABLE `palavra_chave` (
	`id`            INT(11) 			NOT NULL AUTO_INCREMENT,
	`palavra_chave` VARCHAR(128) 		NOT NULL,
	`ativo`         TINYINT(1) 		NOT NULL DEFAULT '1',
	PRIMARY         KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8;


CREATE TABLE `trabalho_relaciona_palavra_chave` (
	`id`               INT(11) 			NOT NULL AUTO_INCREMENT,
	`id_trabalho`      INT(11) 			NOT NULL,
	`id_palavra_chave` INT(11) 			NOT NULL,
	`ativo`            TINYINT(1) 		NOT NULL DEFAULT '1',
	PRIMARY            KEY (`id`),
	FOREIGN            KEY (`id_trabalho`) 	REFERENCES `trabalho` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
	FOREIGN            KEY (`id_palavra_chave`) REFERENCES `palavra_chave` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8;


ALTER TABLE `usuario`
    ADD FOREIGN KEY (hierarquia) REFERENCES hierarquia(id);

CREATE TABLE `blame_cadastro_trabalho` (
	`id`                   INT(11) 			NOT NULL AUTO_INCREMENT,
	`id_usuario_cadastro`  INT(11) 			NULL,
	`id_usuario_aprovacao` INT(11) 			NULL,
	`data_cadastro`        DATETIME			NOT NULL,
	`data_aprovacao`       DATETIME			NULL,
	`operacao`             VARCHAR(128)		NOT NULl,
	`ativo`                TINYINT(1) 		NOT NULL DEFAULT '1',
	PRIMARY                KEY (`id`),
	FOREIGN                KEY (`id_usuario_cadastro`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
	FOREIGN                KEY (`id_usuario_aprovacao`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8;

CREATE TABLE `pagina_institucional` (
	`id`       INT(11) 			NOT NULL AUTO_INCREMENT,
	`titulo`   VARCHAR(512)		NULL,
	`conteudo` TEXT				NOT NULL,
	`ativo`    TINYINT(1) 		NOT NULL DEFAULT '1',
	PRIMARY    KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8;

ALTER TABLE `permissao`
	ADD COLUMN `ativo`       tinyint(1) 		NOT NULL DEFAULT '1';

CREATE TABLE `pessoa` (
  `id`          int(11) 		NOT NULL AUTO_INCREMENT,
  `id_usuario`  int(11) 		NOT NULL,
  `pronome`     varchar(64) 	NULL,
  `nome`        varchar(64) 	NOT NULL,
  `sobrenome`   varchar(256)	NOT NULL,
  `instituicao` varchar(512)	NOT NULL,
  `ativo`       tinyint(1) 		NOT NULL DEFAULT '1',
  PRIMARY       KEY (`id`),
  FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `configuracoes` (
	`id`        INT(11) 			NOT NULL AUTO_INCREMENT,
	`nome`      VARCHAR(512)		NULL DEFAULT "Scientific Work DB",
	`analytics` VARCHAR(16)			NULL DEFAULT "UA-36899868-1",
	`ativo`     TINYINT(1) 			NOT NULL DEFAULT '1',
	PRIMARY     KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8;






set foreign_key_checks = 0;
set foreign_key_checks = 1;

-------------------------------------------------------


CREATE TABLE `curso` (
	`id`            INT(11) 			NOT NULL AUTO_INCREMENT,
	`curso` 		VARCHAR(512) 		NOT NULL,
	`ativo`         TINYINT(1) 		NOT NULL DEFAULT '1',
	PRIMARY         KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8;

CREATE TABLE `campus` (
	`id`            INT(11) 			NOT NULL AUTO_INCREMENT,
	`campus` 		VARCHAR(512) 		NOT NULL,
	`ativo`         TINYINT(1) 		NOT NULL DEFAULT '1',
	PRIMARY         KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8;

CREATE TABLE `url` (
  `id`            int(11) 		NOT NULL AUTO_INCREMENT,
  `url`			  varchar(512)  NOT NULL UNIQUE,
  `id_controller` int(11) 		NULL,
  `controller`    varchar(256) 	NOT NULL,
  `ativo`         tinyint(1) 	NOT NULL DEFAULT '1',
  PRIMARY         KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `trabalho_relaciona_orientador` (
	`id`            INT(11) 			NOT NULL AUTO_INCREMENT,
	`id_trabalho`   INT(11) 			NOT NULL,
	`id_orientador` INT(11) 			NOT NULL,
	`ativo`         TINYINT(1) 		NOT NULL DEFAULT '1',
	PRIMARY         KEY (`id`),
	FOREIGN         KEY (`id_trabalho`) 	REFERENCES `trabalho` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
	FOREIGN         KEY (`id_orientador`) REFERENCES `orientador` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8;

CREATE TABLE `trabalho_relaciona_autor` (
	`id`          INT(11) 			NOT NULL AUTO_INCREMENT,
	`id_trabalho` INT(11) 			NOT NULL,
	`id_autor`    INT(11) 			NOT NULL,
	`ativo`       TINYINT(1) 		NOT NULL DEFAULT '1',
	PRIMARY       KEY (`id`),
	FOREIGN       KEY (`id_trabalho`) 	REFERENCES `trabalho` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
	FOREIGN       KEY (`id_autor`) REFERENCES `autor` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8;


CREATE TABLE `trabalho_relaciona_arquivo` (
	`id`          INT(11) 			NOT NULL AUTO_INCREMENT,
	`id_trabalho` INT(11) 			NOT NULL,
	`id_arquivo`  INT(11) 			NOT NULL,
	`ativo`       TINYINT(1) 		NOT NULL DEFAULT '1',
	PRIMARY       KEY (`id`),
	FOREIGN       KEY (`id_trabalho`) 	REFERENCES `trabalho` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
	FOREIGN       KEY (`id_arquivo`) REFERENCES `arquivo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8;

alter table SWDB.url add COLUMN metodo VARCHAR(256) not null AFTER controller;

CREATE TABLE `aluno` (
  	`id`          int(11) 			NOT NULL AUTO_INCREMENT,
  	`nome`        varchar(1024) 	NOT NULL,
  	`rgm`         varchar(32)		NOT NULL,
  	`id_usuario`  int(11) 			NULL,
  	`id_semestre` int(11) 			NULL,
  	`ativo`       tinyint(1) 		NOT NULL DEFAULT '1',
  	PRIMARY       KEY (`id`),
  	FOREIGN       KEY (`id_semestre`) REFERENCES `semestre` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  	FOREIGN       KEY (`id_usuario`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE `turma` (
  	`id`       int(11) 			NOT NULL AUTO_INCREMENT,
  	`periodo`  varchar(32) 		NOT NULL,
  	`semestre` int(11) 			NOT NULL,
  	`turma`    varchar(32) 		NULL,
  	`ativo`    tinyint(1) 		NOT NULL DEFAULT '1',
  	PRIMARY    KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `disciplina` (
	`id`            INT(11) 			NOT NULL AUTO_INCREMENT,
	`disciplina` 		VARCHAR(512) 		NOT NULL,
	`ativo`         TINYINT(1) 		NOT NULL DEFAULT '1',
	PRIMARY         KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8;

CREATE TABLE `professor` (
  	`id`          int(11) 			NOT NULL AUTO_INCREMENT,
  	`nome`        varchar(1024) 	NOT NULL,
  	`id_usuario`  int(11) 			NULL,
  	`ativo`       tinyint(1) 		NOT NULL DEFAULT '1',
  	PRIMARY       KEY (`id`),
  	FOREIGN       KEY (`id_usuario`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `semestre` (
  	`id`            int(11) 	  NOT NULL AUTO_INCREMENT,
  	`identificador` varchar(1024) NOT NULL,
  	`termino`   	date		  NULL,
  	`ativo`         tinyint(1) 	  NOT NULL DEFAULT '1',
  	PRIMARY         KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


alter table `semestre`
add COLUMN `inicio` date null;

CREATE TABLE `usuario_professor_curso_turma_disciplina_semestre` (
  	`id`            int(11) 			NOT NULL AUTO_INCREMENT,
  	`id_usuario`    int(11) 			NOT NULL,
  	`id_professor`  int(11) 			NOT NULL,
  	`id_curso`      int(11) 			NOT NULL,
  	`id_turma`      int(11) 			NOT NULL,
  	`id_disciplina` int(11) 			NOT NULL,
  	`id_semestre`   int(11) 			NOT NULL,
  	`ativo`         tinyint(1) 		NOT NULL DEFAULT '1',
  	PRIMARY         KEY (`id`),
  	FOREIGN         KEY (`id_usuario`) 		REFERENCES `usuario` 	(`id`) 	ON DELETE NO ACTION ON UPDATE NO ACTION,
  	FOREIGN         KEY (`id_professor`) 	REFERENCES `professor` 	(`id`) 	ON DELETE NO ACTION ON UPDATE NO ACTION,
  	FOREIGN         KEY (`id_curso`) 		REFERENCES `curso` 		(`id`) 	ON DELETE NO ACTION ON UPDATE NO ACTION,
  	FOREIGN         KEY (`id_turma`) 		REFERENCES `turma` 		(`id`) 	ON DELETE NO ACTION ON UPDATE NO ACTION,
  	FOREIGN         KEY (`id_disciplina`) 	REFERENCES `disciplina` (`id`) 	ON DELETE NO ACTION ON UPDATE NO ACTION,
  	FOREIGN         KEY (`id_semestre`) 	REFERENCES `semestre`   (`id`) 	ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `aluno_relaciona_semestre` (
  	`id`          int(11) 			NOT NULL AUTO_INCREMENT,
  	`id_aluno`    int(11) 			NOT NULL,
  	`id_usuario`  int(11) 			NOT NULL,
  	`id_ctdp` int(11) 			NOT NULL,
  	`ativo`       tinyint(1) 		NOT NULL DEFAULT '1',
  	PRIMARY       KEY (`id`),
  	FOREIGN       KEY (`id_aluno`) 		REFERENCES `aluno` 	(`id`) 	ON DELETE NO ACTION ON UPDATE NO ACTION,
  	FOREIGN       KEY (`id_usuario`) 	REFERENCES `usuario` 	(`id`) 	ON DELETE NO ACTION ON UPDATE NO ACTION,
  	FOREIGN       KEY (`id_ctdp`) 	REFERENCES `usuario_professor_curso_turma_disciplina_semestre`   (`id`) 	ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


alter table hierarquia add COLUMN permanente tinyint(1) not null DEFAULT 0 AFTER nome;











CREATE TABLE `aula` (
  	`id`       int(11) 			NOT NULL AUTO_INCREMENT,
  	`numero`   int(11) 			NOT NULL,
  	`titulo`   varchar(1024) 	NOT NULL,
  	`conteudo` TEXT 			NOT NULL,
  	`id_ctdp`  int(11) 			NOT NULL,
  	`ativo`    tinyint(1) 		NOT NULL DEFAULT '1',
  	PRIMARY    KEY (`id`),
	FOREIGN KEY (`id_ctdp`) 	REFERENCES `usuario_professor_curso_turma_disciplina_semestre`   (`id`) 	ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `contador_visita` (
  	`id`         int(11) 			NOT NULL AUTO_INCREMENT,
  	`id_usuario` int(11) 			NOT NULL,
  	`data`       datetime  			NOT NULL default now(),
  	`acao`       varchar(512) 		NOT NULL,
  	`ativo`      tinyint(1) 		NOT NULL DEFAULT '1',
  	PRIMARY      KEY (`id`),
	FOREIGN KEY (`id_usuario`) 	REFERENCES `usuario`   (`id`) 	ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

alter table aula add COLUMN arquivo varchar(1) not null DEFAULT 0 AFTER nome;

ALTER TABLE `aula`
	ADD COLUMN id_arquivo int(11) null,
   	ADD FOREIGN KEY (`id_arquivo`) REFERENCES arquivo(id) ON DELETE cascade ON UPDATE cascade;


alter table SWDB.hierarquia add COLUMN nivel int(11) not null AFTER nome;