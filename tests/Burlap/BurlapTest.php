<?php

namespace Burlap\Test;

use Burlap\Burlap;

class BurlapTest extends \PHPUnit_Framework_TestCase {
    
    public function testSetBurlapProperty() {
        $sack = new Burlap();
        
        $sack->username([function () {
            return 'user';
        }]);
        
        $this->assertEquals($sack->username(), 'user');
    }
    
     public function testSetBurlapService() {
        $sack = new Burlap();
        
        $sack->username([function () {
            return 'user';
        }]);
        
        $sack->login(['username', function ($c, $user) {
            return $user === 'user';
        }]);
        
        $this->assertTrue($sack->login());
        
        $sack->username([function () {
            return 'user2';
        }]);
        
        $sack->login(['username', function ($c, $user) {
            return $user === 'user';
        }]);
        
        $this->assertFalse($sack->login());
    }
    
}