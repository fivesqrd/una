# una
Simple PHP library for storing and sending OTPs / 2FAs

## Creating an OTP token
```
$factory = new Una\Factory([
    'storage'    => '',
    'transport'  => '',
    'randomiser' => '',
    'hasher'     => '',
    'scope'      => ''
]);

$token = $factory->create()
    ->scope('My-App')
    ->ttl(86400)
    ->notify(function($secret) {
        mail();    
    });

echo $token->raw();
echo $token->hashed();

$token = $factory->create()
    ->randomise(new Numeric(['length' => 5]))
    ->scope('My-App')
    ->ttl(86400)
    ->notify(function($secret) {
        mail();    
    });

echo $token->raw();

echo $token->hashed();
```

Specifying a secret
```
$token = $factory->create(12345)
    ->scope('My-App')
    ->notify(function($secret) {
        mail();
    });

echo $token->raw(); //12345

echo $token->hashed();
```

Specifying a random generator
```
use Una\Adapter\Randomiser\AlphaNumeric;

$token = $factory->create()
    ->scope('My-App')
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
    ->scope('My-App')
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
