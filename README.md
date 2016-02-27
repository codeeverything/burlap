# Burlap

Burlap is a simple Dependency Injection Container for PHP, inspired by Fabien Potencier's Twitee and series on 
ependency Injection.

**NB:** This is not intended to be production ready, but suggestions and PRs are welcomed! :)

## Example Usage

Drawing upon Fabien Potencier's example in his dependency injection series, let's imagine setting up a mailer service.

Create parameters for username and password:

```php
$sack = new Burlap();
$sack->mailer_user([function () {
    return 'username';
}]);

$sack->mailer_pass([function () {
    return 'password';
}]);
```

Create a ```mailer_settings``` service to grab these:

```php
$sack->mailer_settings(['mailer_user', 'mailer_pass', function ($c, $user, $pass) {
    $o = new stdclass();
    $o->user = $user . rand();
    $o->pass = $pass . rand();
    
    // return a single instance of the service by using Burlap's "share" function
    return $c->share('mailer_settings', $o);
}]);
```

Finally, create the ```mailer``` service, making use of the previously define services/parameters as dependencies:

```php
$sack->mailer(['mailer_settings', function ($c, $settings) {
    $o = new stdclass();
    $o->one = $settings->user;
    $o->two = $settings->pass;
    $o->three = rand() * 10;
    // return a single instance of the service by using Burlap's "share" function
    return $c->share('mailer', $o);
}]);
```

With the service defined, we can not make use of it:

```php
// setup two mailers, since the service is shared these will be identical
$mailer1 = $sack->mailer();
$mailer2 = $sack->mailer();

// dump the list of defined services
var_dump($sack->container);
```