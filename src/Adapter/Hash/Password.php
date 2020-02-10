<?php

namespace Una\Adapter\Hash;

use Una\Adapter\Hashable;

class Password implements Hashable
{
    public function encode($cleartext)
    {
        return password_hash($cleartext, PASSWORD_DEFAULT);
    }

    public function verify($hash, $cleartext)
    {
        return password_verify($cleartext, $hash);
    }
}