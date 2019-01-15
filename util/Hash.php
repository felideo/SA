<?php
namespace Util;

class Hash {
	public static function get_unic_hash() {
		return md5(uniqid(rand(), true));
	}
}