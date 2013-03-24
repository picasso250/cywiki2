<?php
if( !defined('IN') ) die('bad request');
include_once( AROOT . 'controller'.DS.'app.class.php' );

class userController extends appController
{
    function login()
    {
        $username = _post('username');
        $password = _post('password');

        $msg = '';
        if ($username && $password) {
            $user = User::check($username, $password);
            if ($user) {
                $user->login();
                redirect(_req('back'));
            } else {
                $msg = $GLOBALS['config']['error']['info']['USERNAME_OR_PASSWORD_INCORRECT'];
            }
        }

        add_scripts('jquery.validate.min');
        render(compact('username', 'msg'));
    }

    function logout()
    {
        if ($GLOBALS['has_login']) {
            $GLOBALS['user']->logout();
        }

        redirect();
    }

    function register()
    {
        $email = _post('email');
        $password = _post('password');

        if ($email && $password) {
            $user = User::create($email, $password);
            if ($user) {
                $user->update('name', $email);
                $user->login();
                redirect('my');
            }
        }

        add_scripts('jquery.validate.min');
        render(compact('email'));
    }

    function not_taken()
    {
        $email = _req('email');
        echo User::has($email) ? 'false' : 'true';
    }

    function my()
    {
        $user = $GLOBALS['user'];

        if ($GLOBALS['action'] === 'edit') {
            $name = _post('name');
            if ($name) {
                $user->update(compact('name'));
            }

            $newPass = _post('newPass');
            if ($newPass && $user->checkPassword(_post('oldPass'))) {
                $user->changePassword($newPass);
            }

            redirect($GLOBALS['controller']);
        }
        render(compact('user'));
    }

    function mywiki()
    {
        if (!$GLOBALS['has_login'])
            return;
        $user = $GLOBALS['user'];
        $createdEntries = $user->createdEntries();
        $editedEntries = $user->editedEntries();

        render(compact('createdEntries', 'editedEntries'));
    }

    function view()
    {
        if (is_numeric($a)) {
            $user = new User($a);
        } else {
            $user = User::hasName($a);
        }
        render(compact('user'));
    }
}
    