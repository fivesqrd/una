<?php

namespace Una\Adapter\Randomiser;

use Una\Adapter\Randomisable;

class AlphaNumeric implements Randomisable
{
    protected $_length = 5;

    protected $_dictionary = 'aAbBcCdDfFgGhHjJkKlLmMnNpPqQrRsStTvVwWxXzZ123456789';

    public function __construct($options = [])
    {
        if (array_key_exists('length', $options)) {
            $this->_length = $options['length'];
        }

        if (array_key_exists('dictionary', $options)) {
            $this->_dictionary = $options['dictionary'];
        }
    }

    public function generate()
    {
        $string = null;
        $max = strlen($this->_dictionary) - 1;

        for ($i = 0; $i < $this->_length; $i++) {
            $key = random_int(0, $max);
            $string .= $this->_dictionary[$key];
        }

        return $string;
    }
}