<?php

if(!defined('ABSPATH'))
	return;


class Xoo_CP{

	protected static $instance = null;

	//Get instance
	public static function get_instance(){
		if(self::$instance === null){
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct(){

		//Front end
		include_once XOO_CP_PATH.'/includes/class-xoo-cp-public.php';
		Xoo_CP_Public::get_instance();

		//Core functions
		include_once XOO_CP_PATH.'/includes/class-xoo-cp-core.php';
		Xoo_CP_Core::get_instance();

	}

}

?>