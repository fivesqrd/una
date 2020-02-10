<?php

namespace Una\Adapter;

interface Storable
{
    public function delete($id);

    public function fetch($id);

    public function save($secret, $ttl = 86400);
}