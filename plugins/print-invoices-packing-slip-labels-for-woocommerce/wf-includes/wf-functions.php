<?php
//check class dependencies exist or not
if(!class_exists("Wf_Dependencies"))
{
	require_once('class-wf-dependencies.php');
}

//check woocommerce is active function exist
if(!function_exists('wf_is_woocommerce_active'))
{
	function wf_is_woocommerce_active()
	{
		return Wf_Dependencies::woocommerce_active_check();
	}
}
