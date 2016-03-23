[![Build Status](https://travis-ci.org/codeeverything/burlap.svg?branch=master)](https://travis-ci.org/codeeverything/burlap)

# Burlap

Burlap is a simple Dependency Injection Container for PHP, inspired by [Fabien Potencier's](https://github.com/fabpot) [Twittee](https://github.com/fabpot/twittee) and [series on 
Dependency Injection](http://fabien.potencier.org/what-is-dependency-injection.html).

To play nice with others Burlap implements the ContainerInterface portion of the [Container Interoperability standard](https://github.com/container-interop/container-interop). I'm considering the delegate container option for a later version :)

### Contributions 

**NB:** This is not intended to be production ready, but suggestions and PRs are welcomed! :)

### Testing

Run ```vendor/bin/phpunit``` in your terminal from the root of the project

## Example Usage

Drawing upon Fabien Potencier's example in his dependency injection series, let's imagine setting up a mailer service.

> Adding items to a Burlap container follows the same pattern as defining a service in AngularJS, where a single array argument is passed with the final element of this being the function to run, and all prior elements defining dependencies for that function. These dependencies should be services already registered with Burlap and are passed as arguments to the service being defined.

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
// when defined, a service receives the container as it's first argument and it's dependencies thereafter
$sack->mailer_settings(['mailer_user', 'mailer_pass', function ($c, $user, $pass) {
    $o = new stdclass();
    $o->user = $user . rand();
    $o->pass = $pass . rand();
    
    // return a single instance of the service by using Burlap's "share" function
    return $c->share('mailer_settings', $o);
}]);
```

Finally, create the ```mailer``` service, making use of the previously defined services/parameters as dependencies:

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

With the service defined, we can now make use of it:

```php
// setup two mailers, since the service is shared these will be identical
$mailer1 = $sack->mailer();
$mailer2 = $sack->mailer();

// dump the list of defined services
var_dump($sack->container);
```

## TODO

- [ ] Update docs to show interop way of getting a service
- [ ] Update tests to check for expected exceptions and test get() and has() methods
- [ ] Allow "parameters" to be set with ArrayAccess? Only non-callable items should be allowed... ```$sack['param1'] = 'this is a param';```
- [ ] Allow "parameters" to be accessed from the container with ArrayAccess? ```$sack['param1']```