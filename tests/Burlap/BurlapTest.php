<?php

namespace Burlap\Test;

use Burlap\Burlap;
use Burlap\Exception\ContainerException;

class BurlapTest extends \PHPUnit_Framework_TestCase {
    
    private $sack;
    
    public function setup() {
        $this->sack = new Burlap();
        
        $delegate = new Burlap();
        $delegate->user([function ($c) {
            return '1234';
        }]);
        
        $this->delegated = new Burlap($delegate);
        $this->delegated->whoAmI(['user', function ($c, $who) {
            return "$who: I am not a number, I am a free man";
        }]);
    }
    
    public function tearDown() {
        unset($this->sack);
        Burlap::$shared = [];
    }
    
    public function testDelegateDependency() {
        $this->assertEquals($this->delegated->whoAmI(), "1234: I am not a number, I am a free man");
    }
    
    public function testSetBurlapProperty() {
        $this->sack->username([function () {
            return 'user';
        }]);
        
        $this->assertEquals($this->sack->username(), 'user');
    }
    
    public function testSetBurlapService() {
        $this->sack->username([function () {
            return 'user';
        }]);
        
        $this->sack->login(['username', function ($c, $user) {
            return $user === 'user';
        }]);
        
        $this->assertTrue($this->sack->login());
        
        $this->sack->username([function () {
            return 'user2';
        }]);
        
        $this->sack->login(['username', function ($c, $user) {
            return $user === 'user';
        }]);
        
        $this->assertFalse($this->sack->login());
    }
    
    public function testSetBurlapInstance() {
        $this->sack->username([function ($c) {
            return $c->share('username', 'user' . rand());
        }]);
        
        $user1 = $this->sack->username();
        $user2 = $this->sack->username();
        
        $this->assertEquals($user1, $user2);
    }
    
    public function testSetBurlapNew() {
        $this->sack->username([function ($c) {
            return 'user' . rand() * 10;
        }]);
        
        $user1 = $this->sack->username();
        $user2 = $this->sack->username();
        
        $this->assertNotEquals($user1, $user2);
    }
    
}