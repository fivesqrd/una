<?php

namespace Una\Adapter;

interface Hashable
{
    public function encode($text);

    public function verify($crypt);
}