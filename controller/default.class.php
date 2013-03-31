<?php
if( !defined('IN') ) die('bad request');
include_once( AROOT . 'controller'.DS.'app.class.php' );

class defaultController extends appController
{
    function index()
    {
        d($_GLOBALS);
        d(g('user'));
        $data['title'] = $data['top_title'] = 'CY-wiki 首页';
        $recents = Entry::recents(10);
        render(compact('recents'));
    }
}
    