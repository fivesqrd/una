<?php

namespace Una\Adapter;

interface Storable
{
    public function delete($id);

    public function verify($hasher, $id, $secret);

    public function save($secret, $payload, $ttl = 86400);
}