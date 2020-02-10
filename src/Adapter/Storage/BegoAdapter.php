<?php

namespace Una\Adapter\Storage;

use Una\Adapter\Storable;
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

    public function delete($id)
    {
        return $this->_table->delete(
            new Bego\Item(['Id' => $id])
        );
    }

    public function fetch($id)
    {
        $item = $this->_table->query()
            ->key($id)
            /*
            ->condition(
                Bego\Condition::attribute('Expiry')->gt(gmdate('U'))
            )
            */
            ->fetch()
            ->first();

        return $item ? $item->attribute('Secret') : null;
    }

    public function save($hash, $ttl = 86400)
    {
        $result = $this->_table->put([
            'Id'          => uniqid(),
            'Secret'      => $hash,
            'Expiry'      => gmdate('U') + $ttl
        ]);

        return $result->attribute('Id');
    }
}