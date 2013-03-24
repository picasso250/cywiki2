<?php
if( !defined('IN') ) die('bad request');
include_once( AROOT . 'controller'.DS.'app.class.php' );

class defaultController extends appController
{
    function __construct()
    {
        parent::__construct();
    }
    
    function index()
    {
        $data['title'] = $data['top_title'] = '首页';
        $recents = Entry::recents(10);
        render_view('master', compact('recents'));



        
        $data['tweets'] = $tweets;
        render( $data );
    }
}
    