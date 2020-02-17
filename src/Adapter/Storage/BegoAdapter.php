<?php

namespace Una\Adapter\Storage;

use Una\Adapter\Storable;
use Una\Exception;
use Aws\DynamoDb;
use Bego;

class BegoAdapter implements Storable
{
    protected $_table;

    public static function instance($config)
    {
        if (!isset($config['aws']['version'])) {
            $config['aws']['version'] = '2012-08-10';
        }

        $db = new Bego\Database(
            new DynamoDb\DynamoDbClient($config['aws']), new \Aws\DynamoDb\Marshaler()
        );

        $table = $db->table(
            new BegoModel($config['table'])
        );

        return new static($table);
    }

    public function __construct($table)
    {
        $this->_table = $table;
    }

    public function delete($item)
    {
        return $this->_table->delete($item);
    }

    public function fetch($id)
    {
        $item = $this->_table->query()
            ->key($id)
            ->fetch()
            ->first();

        if (!$item) {
            throw new Exception\MissingToken(
                'Token invalid or already used'
            );
        }

        if ($item->attribute('Expiry') < gmdate('U')) {
            throw new Exception\ExpiredToken(
                'Token expired at ' . date('r', $item->attribute('Expiry'))
            );
        }

        return $item;
    }

    public function verify($hasher, $id, $secret)
    {
        $item = $this->fetch($id);

        if (!$hasher->verify($item->attribute('Secret'), $secret)) {
            throw new Exception\VerificationFailed(
                'Verification failed'
            );
        }

        /* Invalidate token immediately */
        $this->delete($item);

        return $item->attribute('Payload');
    }

    public function save($hash, $payload, $ttl = 86400)
    {
        $result = $this->_table->put([
            'Id'          => uniqid(),
            'Secret'      => $hash,
            'Payload'     => $payload,
            'Expiry'      => gmdate('U') + $ttl
        ]);

        return $result->attribute('Id');
    }
}