<?php

namespace Oro\ProjectBundle\Tests\Unit\Entity;

use Oro\ProjectBundle\Entity\Project;

class ProjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $property
     * @param string $value
     * @param string $expected
     * @dataProvider getSetDataProvider
     */
    public function testGetSet($property, $value, $expected)
    {
        $obj = new Project();
        call_user_func_array(array($obj, 'set' . ucfirst($property)), array($value));
        $this->assertEquals($expected, call_user_func_array(array($obj, 'get' . ucfirst($property)), array()));
    }
    /**
     * Data provider
     *
     * @return array
     */
    public function getSetDataProvider()
    {
        return array(
            'code'    => array('code', 'TST', 'TST'),
            'label' => array('label', 'test_label', 'test_label'),
            'summary' => array('summary', 'Test Summary', 'Test Summary'),
        );
    }
}
