<?php

/**
 * @author  ryan <cumt.xiaochi@gmail.com>
 */

class User extends CoreModel
{
    public static function create($email, $password)
    {
        $arr = array(
            'email' => $email,
            'password' => md5($password),
            'created=NOW()');
        return parent::create($arr);
    }

    public static function has($email)
    {
        return false !== Sdb::fetchRow('*', self::table(), array('email = ?' => $email));
    }

    public static function hasName($name)
    {
        $info = Sdb::fetchRow('*', self::table(), array('name=?' => $name));
        return $info ? new self($info) : false;
    }

    public static function check($email, $password)
    {
        $r = self::search()->by('email', $email)->by('password', md5($password))->find(1);
        if ($r) {
            print_r($r);
            exit;
            return $r[0];
        } else {
            return false;
        }
    }

    public function checkPassword($password)
    {
        return md5($password) === $this->password;
    }

    public function changePassword($new_password)
    {
        $this->update(array('password' => md5($new_password)));
    }

    public function login()
    {
        $_SESSION['se_user_id'] = $this->id;
    }

    public function logout()
    {
        $_SESSION['se_user_id'] = 0;
    }

    // get the current user who has logined in
    public static function current()
    {
        if (isset($_SESSION['se_user_id']) && $_SESSION['se_user_id']) {
            return new self($_SESSION['se_user_id']);
        } else {
            return false;
        }
    }

    public function createdEntries()
    {
        // $this->updateCreatedEntries();
        return Entry::search()->filterBy('creator', $this)->sort('id DESC')->find();
    }

    public function editedEntries()
    {
        return Entry::search()
            ->join(Version::search()->filterBy('editor', $this))
            ->sort('updated DESC')
            ->distinct()
            ->find();
    }

    public function updateCreatedEntries()
    {
        $ids = Sdb::fetchAll('id', Entry::table());
        foreach ($ids as $id) {
            $v = Sdb::fetchRow('*', Version::table(), array('entry=?' => $id), array('id ASC'));
            $e = new Entry($id);
            $e->update('creator', $v['editor']);
        }
    }
}
