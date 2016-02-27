<?php

include 'vendor/autoload.php';

use Burlap\Burlap;

/**
 * Example usage
 */

$sack = new Burlap();
$sack->mailer_user([function () {
    return 'username';
}]);

$sack->mailer_pass([function () {
    return 'password2';
}]);

$sack->mailer_settings(['mailer_user', 'mailer_pass', function ($c, $user, $pass) {
    $o = new stdclass();
    $o->user = $user . rand();
    $o->pass = $pass . rand();
    return $c->share('mailer_settings', $o);
}]);

$sack->mailer(['mailer_settings', function ($c, $settings) {
    $o = new stdclass();
    $o->one = $settings->user;
    $o->two = $settings->pass;
    $o->three = rand() * 10;
    // return $o;
    return $c->share('mailer', $o);
}]);

$mailer1 = $sack->mailer();
$mailer2 = $sack->mailer();

var_dump(Burlap::$shared);

var_dump($mailer1);
var_dump($mailer2);