<?php

namespace Una;

use Una\Adapter;

class Factory
{
    protected $_spec = [
        'storage'    => null,
        'randomiser' => null,
        'hasher'     => null,
    ];

    public function __construct($spec)
    {
        $this->_spec = $spec;
    }

    public function create($secret = null)
    {
        return new Token($this->spec(), $secret);
    }

    public function verify($id, $secret)
    {
        $spec = $this->spec();

        if ($spec['storage']->fetch($id) != $spec['hasher']->verify($secret)) {
            return false;
        }

        /* Invalidate token */
        $spec['storage']->delete($id);

        return true;
    }

    public function spec()
    {
        $defaults = [
            'randomiser' => new Adapter\Randomiser\Numeric([
                'length' => 5
            ]),
            'hasher'     => new Adapter\Hash\Password()
        ];

        return array_merge($defaults, $this->_spec);
    }
}