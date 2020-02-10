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

    protected $_id;

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

    public function identity($value)
    {
        $this->_identity = $value;
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

        $this->_id = $this->_storage->save(
            $hasher->encode($this->_secret), $this->_ttl
        );

        return $this;
    }

    public function notify($callable)
    {
        /* We need a stored value if we're going to send it out */

        if (!$this->_id) {
            $this->store();
        }

        if (is_callable($callable)) {
            $this->_reference = call_user_func($callable, $this);
        }

        if ($callable instanceof Adapter\Sendable) {
            $callable->send($this);
        }

        return $this;
    }

    public function raw()
    {
        return $this->_secret;
    }
    
    public function id()
    {
        return $this->_id;
    }

    public function reference()
    {
        return $this->_reference;
    }
}