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
        return $this->spec('storage')->verify(
            $this->spec('hasher'), $id, $secret
        );
    }

    public function spec($key = null)
    {
        $defaults = [
            'randomiser' => new Adapter\Randomiser\Numeric([
                'length' => 5
            ]),
            'hasher'     => new Adapter\Hash\Password()
        ];

        $spec = array_merge($defaults, $this->_spec);

        if ($key) {
            return $spec[$key];
        }

        return $spec;
    }
}