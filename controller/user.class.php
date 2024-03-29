<?php
if( !defined('IN') ) die('bad request');
include_once( AROOT . 'controller'.DS.'app.class.php' );

class userController extends appController
{
    function login()
    {
        $username = v('username');
        $password = v('password');

        $msg = '';
        if ($username && $password) {
            $user = User::check($username, $password);
            if ($user) {
                $user->login();
                redirect(v('back'));
            } else {
                $msg = $GLOBALS['config']['error']['info']['USERNAME_OR_PASSWORD_INCORRECT'];
            }
        }

        add_script('jquery.validate.min');
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
        $email = v('email');
        $password = v('password');

        if ($email && $password) {
            $user = User::create($email, $password);
            if ($user) {
                $user->update('name', $email);
                $user->login();
                redirect('my');
            }
        }

        add_script('jquery.validate.min');
        render(compact('email'));
    }

    function not_taken()
    {
        $email = v('email');
        echo User::has($email) ? 'false' : 'true';
    }

    function my()
    {
        $user = $GLOBALS['user'];

        if ($GLOBALS['action'] === 'edit') {
            $name = v('name');
            if ($name) {
                $user->update(compact('name'));
            }

            $newPass = v('newPass');
            if ($newPass && $user->checkPassword(v('oldPass'))) {
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
    