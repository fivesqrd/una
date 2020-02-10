<?php

use PHPUnit\Framework\TestCase;
use Aws\DynamoDb;
use Una\Adapter;

/**
 * @covers \Una\Factory
 */
class TableTest extends TestCase
{
    protected $_storage;

    protected function setUp()
    {

    }

    public function testTokenCreateWithBegoStorage()
    {
        $config = [
            'table' => 'test',
            'aws'   => [
                'version' => '2012-08-10',
                'region'  => 'eu-west-1',
                'credentials' => [
                    'key'    => 'test',
                    'secret' => 'test',
                ],
            ]
        ];

        $factory = new Una\Factory([
            'storage'    => Adapter\Storage\BegoAdapter::instance($config),
            'randomiser' => new Adapter\Randomiser\Numeric(),
            'hasher'     => new Adapter\Hash\Password()
        ]);

        $this->assertInstanceOf(
            Una\Token::class, $factory->create()
        );
    }
}
