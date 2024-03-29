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
        $id = v('id');
        if (!$id) {
            redirect();
        }
        $entry = new Entry($id);

        $title = $entry->title;
        $content = $entry->latestVersion()->content;
        add_script(('preview'));
        render(compact('entry', 'title', 'content'));
    }

    function _edit()
    {
        $title = v('title');
        $content = v('content');
        $reason = v('reason');

        $entry->edit($GLOBALS['user'], $title, $content, $reason);
        redirect("wiki/$title");
    }

    function preview()
    {
        $content = v('content');
        echo '<div class="wiki-content">', markdown_parse($content), '</div>';
    }

    function create()
    {
        $title = v('title');
        add_script('preview');
        render(compact('title'));
    }

    function _create()
    {
        $title = v('title');
        $content = v('content');
        $reason = v('reason');

        if ($title) {
            $info = compact('title', 'content');
            $info = g('user');
            Entry::create($info);
            redirect("wiki/$title");
        }
    }

    function all()
    {
        $entries = Entry::search()->find();
        render(compact('entries'));
    }

    function history()
    {
        $id = v('id');
        $entry = new Entry($id);

        $r = v('r') ?: 0;
        $l = v('l') ?: 1;

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
    