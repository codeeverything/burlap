<?php

namespace Burlap\Test;

use Burlap\Burlap;

class BurlapTest extends \PHPUnit_Framework_TestCase {
    
    private $__sack;
    
    public function setup() {
        $this->__sack = new Burlap();
    }
    
    public function tearDown() {
        unset($this->__sack);
        Burlap::$shared = [];
    }
    
    public function testSetBurlapProperty() {
        $this->__sack->username([function () {
            return 'user';
        }]);
        
        $this->assertEquals($this->__sack->username(), 'user');
    }
    
    public function testSetBurlapService() {
        $this->__sack->username([function () {
            return 'user';
        }]);
        
        $this->__sack->login(['username', function ($c, $user) {
            return $user === 'user';
        }]);
        
        $this->assertTrue($this->__sack->login());
        
        $this->__sack->username([function () {
            return 'user2';
        }]);
        
        $this->__sack->login(['username', function ($c, $user) {
            return $user === 'user';
        }]);
        
        $this->assertFalse($this->__sack->login());
    }
    
    public function testSetBurlapInstance() {
        $this->__sack->username([function ($c) {
            return $c->share('username', 'user' . rand());
        }]);
        
        $user1 = $this->__sack->username();
        $user2 = $this->__sack->username();
        
        $this->assertEquals($user1, $user2);
    }
    
    public function testSetBurlapNew() {
        $this->__sack->username([function ($c) {
            return 'user' . rand() * 10;
        }]);
        
        $user1 = $this->__sack->username();
        $user2 = $this->__sack->username();
        
        $this->assertNotEquals($user1, $user2);
    }
    
}