<?php

/**
 * @author  ryan <cumt.xiaochi@gmail.com>
 */

class Watch extends CoreModel
{
    public static function create(User $user, Entry $entry)
    {
        return parent::create(array(
            'user' => $user->id,
            'entry' => $entry->id));
    }

    public static function is(User $user, Entry $entry)
    {
        $conds = array('user=? AND entry=?' => array($user->id, $entry->id));
        $info = Sdb::fetchRow('*', self::table(), $conds);
        return $info ? new self($info) : false;
    }

    public static function cancel(User $user, Entry $entry)
    {
        $watch = self::is($user, $entry);
        if ($watch) {
            $watch->del();
        }
    }
}
