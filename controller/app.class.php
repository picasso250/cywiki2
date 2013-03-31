<?php
if( !defined('IN') ) die('bad request');
include_once( CROOT . 'controller' . DS . 'core.class.php' );

class appController extends coreController
{
	function __construct()
	{
		// 载入默认的
		parent::__construct();
        session_start();
        if ($user = User::current()) {
            $_GLOBALS['has_login'] = $_GLOBALS['user'] = $user;
        }
	}

	// login check or something
	
}

