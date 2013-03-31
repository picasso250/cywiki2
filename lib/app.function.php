<?php

function url($url = null) {
    $root = '/';
    if (!is_string($url)) {
        return $root;
    } elseif (strpos('://', $url) !== false) {
        return $url;
    } elseif ($url[0] != '/') {
        return $root.$url;
    } else {
        return $url;
    }
}

function add_script($script)
{
    $GLOBALS['js'][] = $script;
}

function redirect($url = null)
{
    header('Location: '.url($url));
    exit;
}

// little function to help us print_r() or dump() things
function d($param, $var_dump = 0) 
{
    if (defined('DEBUG')) $debug = DEBUG;
    else $debug = 1;
    if (!$debug) {
        return;
    }
 
    $is_ajax = isset($GLOBALS['is_ajax']) && $GLOBALS['is_ajax']; // is ajax
    $is_cli = PHP_SAPI === 'cli';                                 // is cli mode
    $html_mode = !($is_ajax || $is_cli);                            // will display in html?

    if ($html_mode) echo '<p><pre>';
    echo PHP_EOL;
    if ($var_dump || empty($param) || is_bool($param) || is_string($param)) {
        var_dump($param);
    } else {
        print_r($param);
    }
    if ($html_mode) echo '</p></pre>';
    echo PHP_EOL;
}