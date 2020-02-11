<?php

namespace Una;

use Una\Adapter;

class Token
{
    protected $_storage;

    protected $_randomiser;

    protected $_hasher;

    protected $_ttl = 900; /* 15 minutes */

    protected $_secret;

    protected $_payload;

    protected $_key;

    protected $_reference;

    public function __construct($spec, $secret = null)
    {
        $this->_storage    = $spec['storage'];
        $this->_randomiser = $spec['randomiser'];
        $this->_hasher     = $spec['hasher'];
        $this->_secret     = $secret;
    }

    public function randomise(Adapter\Randomisable $randomisable = null)
    {
        $randomiser = $randomisable ?: $this->_randomiser;

        $this->_secret = $randomiser->generate();

        return $this;
    }

    public function ttl($value)
    {
        $this->_ttl = $value;
        return $this;
    }

    public function payload($value)
    {
        $this->_payload = $value;
        return $this;
    }

    public function store(Adapter\Hashable $hashable = null)
    {
        /* Fallback to the default hasher if none specified */

        $hasher = $hashable ?: $this->_hasher;

        /* We need a secret if we're going to store it */

        if (!$this->_secret) {
            $this->randomise();
        }

        /* Persist the secret and keep a reference of the id */

        $this->_key = $this->_storage->save(
            $hasher->encode($this->_secret), $this->_payload, $this->_ttl
        );

        return $this;
    }

    public function notify($callable)
    {
        /* We need a stored value if we're going to send it out */

        if (!$this->_key) {
            $this->store();
        }

        if (is_callable($callable)) {
            $this->_reference = call_user_func($callable, $this);
        }

        if ($callable instanceof Adapter\Sendable) {
            $this->_reference = $callable->send($this);
        }

        return $this;
    }

    public function properties()
    {
        return [
            'payload'   => $this->_payload,
            'secret'    => $this->_secret,
            'reference' => $this->_reference,
            'ttl'       => $this->_ttl,
            'key'       => $this->_key,
        ];
    }

    public function raw()
    {
        return $this->_secret;
    }
    
    public function key()
    {
        return $this->_key;
    }

    public function reference()
    {
        return $this->_reference;
    }
}