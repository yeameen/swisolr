<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'PHPUnit/Framework.php';

/**
 * Description of ArrayTest
 *
 * @author developer
 */
class ArrayTest extends PHPUnit_Framework_TestCase{


    public function testIfNewArrayIsEmpty() {
        $fixtures = array();
        $this->assertEquals(0, sizeof($fixtures));
    }

    public function testArrayContainingElement() {
        $fixtures = array('element');
        $this->assertEquals(1, sizeof($fixtures));
    }

    /**
     * @dataProvider provider
     */
    public function testAddition($first, $second, $third) {
        $this->assertEquals($third, $first + $second);
    }

    public function provider() {
        return array(
            array(0, 0, 0),
            array(1, 3, 4),
            array(1, 1, 2)
        );
    }
}
?>
