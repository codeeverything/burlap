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
    
}