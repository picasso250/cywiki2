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
        $r = self::search()->by('user', $user)->by('entry', $entry)->find(1);
        return $r ? $r[0] : false;
    }

    public static function cancel(User $user, Entry $entry)
    {
        $watch = self::is($user, $entry);
        if ($watch) {
            $watch->del();
        }
    }
}
