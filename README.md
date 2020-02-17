# una
Simple PHP library for storing and sending OTPs / 2FAs

## Creating an OTP token
```
Use Una\Adapter;

/* Initialise the factory class with some defaults */
$factory = new Una\Factory([
    'storage'    => Adapter\Storage\BegoAdapter::instance([
        'table' => 'My-Table',
        'aws'   => [
            'region' => 'eu-west-1',
            'credentials'   => [
                'key'    => 'mykey',
                'secret' => 'mysecret',
            ]
        ],
    ]),
    'randomiser' => new Adapter\Randomiser\Numeric([
        'length' => 5
    ]),
    'hasher'     => new Adapter\Hash\Password()
]);

/* Create a fresh token */
$token = $factory->create()
    ->ttl(86400)
    ->notify(function($secret) {
        mail();    
    });

echo $token->raw();
echo $token->hashed();
```

Specifying a token's payload
```
$token = $factory->create()
    ->payload($userId)
    ->ttl(86400)
    ->notify(function($secret) {
        mail();    
    });
```
Token with complex payload
```
$token = $factory->create()
    ->payload([
        'userId' => $userId,
        'time'   => time()
    ])
    ->ttl(86400)
    ->notify(function($secret) {
        mail();    
    });
```

Specifying a secret
```
$token = $factory->create(12345)
    ->notify(function($secret) {
        mail();
    });

echo $token->raw(); //12345

echo $token->hashed();
```

Specifying a custom random generator. Custom generators implement the Una\Adapter\Randomisable interface.
```
use Una\Adapter\Randomiser\AlphaNumeric;

$token = $factory->create()
    ->randomise(new AlphaNumeric())
    ->notify(function($secret) {
        mail();    
    });

echo $token->raw(); //random alphanumeric value

echo $token->hashed();
```

Overriding the default ttl
```
use Una\Adapter\Randomiser\AlphaNumeric;

$token = $factory->create()
    ->ttl(86400) //24 hours
    ->notify(function($secret) {
        mail();    
    });

echo $token->raw(); //random alphanumeric value

echo $token->hashed();
```

## Verifying an OTP token
```
try {
    $tokens = $factory->fetch($userId, 'My-App');

    /* Verify input against any valid tokens issued */
    $tokens->verify($input);
    
    /* Successful, invalidate all tokens immediately */
    $tokens->invalidate();

    return true;
} catch (Una\Exception\Storage)
    return 'a usefull message';
} catch (Una\Exception\Notfound)
    return 'a usefull message';
} catch (Una\Exception\Mismatch)
    return 'a usefull message';
} catch (Una\Exception\TokenExpired)
    return 'a usefull message';
}
```
