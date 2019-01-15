<?php
namespace Util;

class URL {
	public static function modulo_url($url) {
		$url = explode('/', $url);
		return $url;
	}
}