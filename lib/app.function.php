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
