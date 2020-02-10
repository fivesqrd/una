<?php

use PHPUnit\Framework\TestCase;
use Aws\DynamoDb;
use Una\Adapter;

/**
 * @covers \Una\Factory
 */
class TokenTest extends TestCase
{
    protected $_storage;

    protected function setUp()
    {

    }

    public function testStaticSecret()
    {
        $spec = [
            //'storage'    => Adapter\Storage\BegoAdapter::instance($config),
            'randomiser' => new Adapter\Randomiser\Numeric(),
            'hasher'     => new Adapter\Hash\Password()
        ];

        $token = new Una\Token($spec, 1234);

        $this->assertEquals(1234, $token->raw());
    }

    public function testRandomSecret()
    {
        $spec = [
            //'storage'    => Adapter\Storage\BegoAdapter::instance($config),
            'randomiser' => new Adapter\Randomiser\Numeric(),
            'hasher'     => new Adapter\Hash\Password()
        ];

        $token = new Una\Token($spec);

        $this->assertTrue(
            is_int($token->randomise()->raw())
        );
    }
}
