<?php

namespace Oro\IssueBundle\Tests\Unit\Entity;

use Oro\IssueBundle\Entity\Issue;

class IssueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $property
     * @param string $value
     * @param string $expected
     * @dataProvider getSetDataProvider
     */
    public function testGetSet($property, $value, $expected)
    {
        $obj = new Issue();
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
        $parentIssue = $this->getMock('Oro\IssueBundle\Entity\Issue');
        $project = $this->getMock('Oro\ProjectBundle\Entity\Project');
        $reporter = $this->getMock('Oro\UserBundle\Entity\User');

        return array(
            'description'    => array('description', 'Test Description', 'Test Description'),
            'priority' => array('priority', 'test_priority', 'test_priority'),
            'resolution' => array('resolution', 'test_resolution', 'test_resolution'),
            'status' => array('status', 'test_status', 'test_status'),
            'summary' => array('summary', 'test_summary', 'test_summary'),
            'type' => array('type', 'test_type', 'test_type'),
            'parent' => array('parent', $parentIssue, $parentIssue),
            'project' => array('project', $project, $project),
            'reporter' => array('reporter', $reporter, $reporter),
        );
    }
}
