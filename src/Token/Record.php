<?php

namespace Una\Token;

use Una\Adapter;

class Record
{
    protected $_hasher;

    protected $_item;

    public function __construct($hasher, $item)
    {
        $this->_hasher = $hasher;
        $this->_item = $item;
    }

    public function payload()
    {
        return $this->_item->attribute('Payload');
    }

    public function verify($secret)
    {
        if (!$this->hasher->verify($this->_item->attribute('Secret'), $secret)) {
            return false;
        }
        
        /* Invalidate token */
        $spec['storage']->delete($this->_item);

        return true;

    }
}