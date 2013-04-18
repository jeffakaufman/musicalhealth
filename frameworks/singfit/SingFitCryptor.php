<?php
# -*- coding: utf-8 -*-

//
//  Copyright (C) 2011 Sonoma Wire Works. All rights reserved.
//

require_once dirname(__FILE__)."/../vendor/cryptastic.php";

define('__kSingFitCryptorWeakKey', '*&^%');
define('__kSingFitCryptorMidKey', '+=}&^%[#|%#2*@3');
define('__kSingFitCryptorMaxKey', '@5*&d^%[wq|]%#28@*9#%|#[}=+%^&*$');

class SingFitCryptor extends cryptastic
{
	private static $_cryptor_instance_ptr = null;
	
	final private function __construct() { 
		/*
		* singleton protection cannot call new or extends 
		* e.g the constructor is final and private. 
		*/
	}
	
	final public static function sharedCryptor() {
		if(null === self::$_cryptor_instance_ptr) {
			self::$_cryptor_instance_ptr = new SingFitCryptor;
		}
		return self::$_cryptor_instance_ptr;
	}
	
	final public function encryptBytes($bytes, $key = __kSingFitCryptorMaxKey, $base64 = false) {
		return $this->encrypt($bytes, $key, $base64);
	}
	
	final public function decryptBytes($bytes, $key = __kSingFitCryptorMaxKey, $base64 = false) {
		return $this->decrypt($bytes, $key, $base64);
	}
};

function SingFitEncrypt($bytes, $key = __kSingFitCryptorMidKey) {
	$cryptor = SingFitCryptor::sharedCryptor();
	return urlencode($cryptor->encryptBytes($bytes, $key, true));
}

function SingFitDecrypt($bytes, $key = __kSingFitCryptorMidKey) {
	$cryptor = SingFitCryptor::sharedCryptor();
	$decrypt = $cryptor->decryptBytes(urldecode($bytes), $key, true);
	if (strlen($decrypt)) {
		return $decrypt;
	}
	$decrypt = $cryptor->decryptBytes($bytes, $key, true);
	if (strlen($decrypt)) {
		return $decrypt;
	}
	return null;
}

/* EOF */ ?>