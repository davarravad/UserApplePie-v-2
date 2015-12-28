<?php

namespace Helpers;

/**
 * Simple cookie class
 */

class Cookie {

	const FourYears = 126144000;
	const Lifetime = -1;

	/**
	 * Function to check if cookie exists
	 */
	public static function exists($key) {
		if (isset($_COOKIE[COOKIE_PREFIX.$key])) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Function to create the cookie for login user
	 */
	public static function set($key, $value, $expiry = self::OneYear, $path = "/", $domain = false){
		$retval = false;
		if (!headers_sent())
		{
			if ($domain === false)
				$domain = $_SERVER['HTTP_HOST'];

			if ($expiry === -1)
				$expiry = 1893456000; // Lifetime = 2030-01-01 00:00:00
			elseif (is_numeric($expiry))
				$expiry += time();
			else
				$expiry = strtotime($expiry);

				$retval = @setcookie(COOKIE_PREFIX.$key, $value, $expiry, $path, $domain);
			if ($retval)
				$_COOKIE[COOKIE_PREFIX.$key] = $value;
		}
		return $retval;
	}

	/**
	 * Function to get the cookie info
	 */
	public static function get($key, $default = ''){
		return (isset($_COOKIE[COOKIE_PREFIX.$key]) ? $_COOKIE[COOKIE_PREFIX.$key] : $default);
	}
   
	/**
	 * Function to check if cookie is set
	 */
	public static function is($key) {
		if (isset($_COOKIE[COOKIE_PREFIX.$key]))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Function to display the cookie 
	 */
	public static function display(){
		return $_COOKIE;
	}

	/**
	 * Function to destroy the cookie 
	 */
	public static function destroy($key, $value = '', $path = "/", $domain = ""){
		if(isset($_COOKIE[COOKIE_PREFIX.$key])){
			unset($_COOKIE[COOKIE_PREFIX.$key]);
			setcookie(COOKIE_PREFIX.$key, $value, time()-3600, $path, $domain);
		}
	}

}