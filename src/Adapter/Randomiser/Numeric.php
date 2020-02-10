<?php

namespace Una\Adapter\Randomiser;

use Una\Adapter\Randomisable;

class Numeric implements Randomisable
{
    protected $_length = 5;

    public function __construct($options = [])
    {
        if (array_key_exists('length', $options)) {
            $this->_length = $options['length'];
        }
    }

    public function generate()
    {
        $min = pow(10, $this->_length - 1);
        $max = pow(10, $this->_length) - 1;
        
        return random_int($min, $max);
    }
}