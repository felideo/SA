<?php
namespace Libs;

/**
 * classe Hash
 * @param string $algo Algorítimo (md5, sha1, whirlpool, etc)
 * @param string $data Variável para codificar
 * @param string $salt O escudo
 * @return string A protegida/codificada $data
 */
class Mail {
	private $from;
	private $replay_to;
	private $to;
	private $mensagem;
	private $assunto;
	private $cco;

	public function set_from($from){
		$this->from = $from;
		return $this;
	}

	public function set_replay_to($replay_to){
		$this->replay_to = $replay_to;
		return $this;
	}

	public function set_to($to){
		$this->to = $to;
		return $this;
	}

	public function set_mensagem($mensagem){
		$this->mensagem = $mensagem;
		return $this;
	}

	public function set_assunto($assunto){
		$this->assunto = $assunto;
		return $this;
	}

	public function set_cco($cco){
		$this->cco = $cco;
		return $this;
	}


	public function send_mail(){
		$headers = "From: {$this->from}\r\n";

		if(isset($this->set_replay_to)){
			$headers .= "Reply-To: {$this->replay_to}\r\n";
		}else{
			$headers .= "Reply-To: {$this->from}\r\n";
		}

		if(isset($this->cco)){
           $headers .= "Bcc: {$this->cco}\r\n";
		}

        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    	$headers .= "MIME-Version: 1.0\r\n";
    	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

 		$retorno = mail($this->to, $this->assunto, nl2br($this->mensagem), $headers);

 		return $retorno;
	}
}