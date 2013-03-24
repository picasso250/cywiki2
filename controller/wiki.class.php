<?php
if( !defined('IN') ) die('bad request');
include_once( AROOT . 'controller'.DS.'app.class.php' );

class wikiController extends appController
{
    function view()
    {
        $name = urldecode($name);
        $entry = Entry::has($name);
        if ($entry) {
            // show
            $watching = false;
            if ($GLOBALS['has_login']) {
                $watching = Watch::is($GLOBALS['user'], $entry);
            }
            render(compact('entry', 'watching'));
        } else {
            redirect("create?title=$name");
        }
    }

    function edit()
    {
        $id = _req('id');
        if (!$id) {
            redirect();
        }
        $entry = new Entry($id);

        $title = $entry->title;
        $content = $entry->latestVersion()->content;
        add_scripts(array('preview'));
        render(compact('entry', 'title', 'content'));
    }

    function _edit()
    {
        $title = _post('title');
        $content = _post('content');
        $reason = _post('reason');

        $entry->edit($GLOBALS['user'], $title, $content, $reason);
        redirect("wiki/$title");
    }

    function preview()
    {
        $content = _post('content');
        echo '<div class="wiki-content">', markdown_parse($content), '</div>';
    }

    function create()
    {
        add_scripts('preview');
        render_view('master');
    }

    function _create()
    {
        $title = _post('title');
        $content = _post('content');
        $reason = _post('reason');

        if ($title) {
            $GLOBALS['user']->createEntry($title, $content);
            redirect("wiki/$title");
        }
    }

    function list()
    {
        $entries = Entry::search()->find();
        render(compact('entries'));
    }

    function history()
    {
        $id = _req('id');
        $entry = new Entry($id);

        $r = _req('r') ?: 0;
        $l = _req('l') ?: 1;

        $versions = $entry->versions();
        $versionCount = count($versions);
        if ($versionCount < 2) {
            return;
        }

        if (isset($versions[$r]))
            $rightVer = $versions[$r];
        else 
            $rightVer = $rightVer[0];

        if (isset($versions[$l]))
            $leftVer = $versions[$l];
        else
            $leftVer = $versions[1];

        $rightHtml = nl2br($rightVer->content);

        $rightContent = $rightVer->content;
        $leftContent = $leftVer->content;

        $la = explode("\n", $leftContent);
        $ra = explode("\n", $rightContent);
        $dl = array_diff($la, $ra);
        $dr = array_diff($ra, $la);
        d($dl);
        d($dr);
        $leftHtml = '';

        render(compact('entry', 'r', 'l', 'id', 'versionCount', 'leftVer', 'rightVer', 'rightHtml'));
    }

    function unwatch()
    {
        if (!$GLOBALS['has_login'])
            redirect();
        $entry = new Entry($id);
        Watch::cancel($GLOBALS['user'], $entry);
        redirect("wiki/$entry->title");
    }

    function watch()
    {
        if (!$GLOBALS['has_login'])
            redirect();
        $entry = new Entry($id);
        Watch::create($GLOBALS['user'], $entry);
        redirect("wiki/$entry->title");
    }
}
    