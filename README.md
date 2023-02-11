# Coretrek IDP SDK

SDK to interact with the Coretrek IDP service with Laravel support.

## Installation

First require the package through composer.

```bash
composer require coretrekas/idp
```

### Manually setup the sdk

Generate or create a token instance based on your credentials.

```php
$token = \Coretrek\Idp\Token::make('https://idp-server.com', 'client_id', 'client_secret', ['*']);

// Or...

$token = new \Coretrek\Idp\Token('Bearer', 'ACCESS-TOKEN', 10000);

// Properties.
$token->type;
$token->accessToken;
$token->expiresIn;

$token->toArray();

// Returns:
// [
//     'token_type' => 'Bearer',
//     'expires_in' => 10000,
//     'access_token' => 'ACCESS-TOKEN',
// ]
```

You should store the token in your application to prevent it from beeing generate each time.
The token is valid for the amount of seconds defined in `expires_in` part of the token.
The `toArray()` or `toJson()` method can help with storing the token.

When you have a valid token instance you can instantiate the sdk.

```php
$sdk = new \Coretrek\Idp\Sdk($token, 'https://idp-server.com');
```

### Setup Sdk with laravel

With laravel the service provider will automatically be found by the framework.
It will also automatically take care of storing the token and refreshing it when needed by the cache driver of your choice.

### Usage

#### Users

List all users.

```php
// Plain sdk
$sdk->users()->list();
$sdk->users()->list($perPage = 25);
$sdk->users()->list($filters = ['first_name' => 'John']);
$sdk->users()->list($perPage = 25, $filters = ['first_name' => 'John']);

// Laravel facade
\Coretrek\Idp\Facades\Sdk::users()->list();
```

Get a single user.

```php
// Plain sdk
$sdk->users()->show('986b24bb-655a-4fc5-9608-8a8aba83b2dd');
$sdk->users()->show('986b24bb-655a-4fc5-9608-8a8aba83b2dd', $includes = ['groups']);

// Laravel facade
\Coretrek\Idp\Facades\Sdk::users()->show('986b24bb-655a-4fc5-9608-8a8aba83b2dd');
```

Create a new user.

```php
// Plain sdk
$sdk->users()->create([
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email' => 'john@example.com',
    'mobile' => '+4795916561',
    'password' => 'Password1',
    'locale' => 'nb',
    'email_verified_at' => '2023-01-01 10:00:00',
    'two_factor_secret' => 'abc123',
    'two_factor_recovery_codes' => 'abc123 acd 321',
    'meta' => [
        'foo' => 'bar',
    ],
]);

// Laravel facade
\Coretrek\Idp\Facades\Sdk::users()->create();
```

Update user.

```php
// Plain sdk
$sdk->users()->update([
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email' => 'john@example.com',
    'mobile' => '+4795916561',
    'password' => 'Password1',
    'locale' => 'nb',
    'email_verified_at' => '2023-01-01 10:00:00',
    'two_factor_secret' => 'abc123',
    'two_factor_recovery_codes' => 'abc123 acd 321',
    'meta' => [
        'foo' => 'bar',
    ],
]);

// Laravel facade
\Coretrek\Idp\Facades\Sdk::users()->update();
```

Delete a user.

```php
// Plain sdk
$sdk->users()->delete('986b24bb-655a-4fc5-9608-8a8aba83b2dd');

// Laravel facade
\Coretrek\Idp\Facades\Sdk::users()->delete('986b24bb-655a-4fc5-9608-8a8aba83b2dd');
```

#### Groups

List all groups.

```php
// Plain sdk
$sdk->groups()->list();
$sdk->groups()->list($perPage = 25);
$sdk->groups()->list($filters = ['identifier' => 123]);
$sdk->groups()->list($perPage = 25, $filters = ['identifier' => 123]);

// Laravel facade
\Coretrek\Idp\Facades\Sdk::groups()->list();
```

Get a single group.

```php
// Plain sdk
$sdk->groups()->show('986b24bb-655a-4fc5-9608-8a8aba83b2dd');
$sdk->groups()->show('986b24bb-655a-4fc5-9608-8a8aba83b2dd', $includes = ['users']);

// Laravel facade
\Coretrek\Idp\Facades\Sdk::groups()->show('986b24bb-655a-4fc5-9608-8a8aba83b2dd');
```

Create a new group.

```php
// Plain sdk
$sdk->groups()->create([
    'name' => 'Group A',
    'description' => 'Description of the group A',
    'identifier' => '000000000',
    'meta' => [
        'foo' => 'bar',
    ],
]);

// Laravel facade
\Coretrek\Idp\Facades\Sdk::groups()->create();
```

Update group.

```php
// Plain sdk
$sdk->users()->update([
    'name' => 'Group A',
    'description' => 'Description of the group A',
    'identifier' => '000000000',
    'meta' => [
        'foo' => 'bar',
    ],
]);

// Laravel facade
\Coretrek\Idp\Facades\Sdk::users()->update();
```

Delete a group.

```php
// Plain sdk
$sdk->groups()->delete('986b24bb-655a-4fc5-9608-8a8aba83b2dd');

// Laravel facade
\Coretrek\Idp\Facades\Sdk::groups()->delete('986b24bb-655a-4fc5-9608-8a8aba83b2dd');
```

#### Group and user relation

Add a user to the group

```php
// Plain sdk
$sdk->groups()->addUser('986b24bb-655a-4fc5-9608-8a8aba83b2dd', '8a8aba83b2dd-655a-4fc5-9608-986b24bb', $meta = ['foo' => 'bar']);

// Laravel facade
\Coretrek\Idp\Facades\Sdk::groups()->addUser('986b24bb-655a-4fc5-9608-8a8aba83b2dd', '8a8aba83b2dd-655a-4fc5-9608-986b24bb', $meta = ['foo' => 'bar']);
```

Update user meta in a group

```php
// Plain sdk
$sdk->groups()->updateUser('986b24bb-655a-4fc5-9608-8a8aba83b2dd', '8a8aba83b2dd-655a-4fc5-9608-986b24bb', $meta = ['foo' => 'bar']);

// Laravel facade
\Coretrek\Idp\Facades\Sdk::groups()->updateUser('986b24bb-655a-4fc5-9608-8a8aba83b2dd', '8a8aba83b2dd-655a-4fc5-9608-986b24bb', $meta = ['foo' => 'bar']);
```

Remove user from a group.

```php
// Plain sdk
$sdk->groups()->removeUser('986b24bb-655a-4fc5-9608-8a8aba83b2dd', '8a8aba83b2dd-655a-4fc5-9608-986b24bb');

// Laravel facade
\Coretrek\Idp\Facades\Sdk::groups()->removeUser('986b24bb-655a-4fc5-9608-8a8aba83b2dd', '8a8aba83b2dd-655a-4fc5-9608-986b24bb');
```

#### Misc

When using endpoints with pagination the api will return the necessary url to get the following results.
In these cases we provide a `get` method on the sdk.

```php
// Plain sdk
$sdk->get('https://idp-server.com/api/users?per_page=25&page=2');

// Laravel facade
\Coretrek\Idp\Facades\Sdk::get('https://idp-server.com/api/users?per_page=25&page=2');
```

## Testing

You can run the tests with:

```bash
./vendor/bin/phpunit
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Security

If you discover any security related issues, please email [tommyl@coretrek.no](mailto:tommyl@coretrek.no) instead of using the issue tracker.

## Credits

- [Tommy Leirvik](https://github.com/leitom)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
