<?php

namespace Oro\IssueBundle\Tests\Unit\Entity;

use Oro\IssueBundle\Entity\Activity;

class ActivityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $property
     * @param string $value
     * @param string $expected
     * @dataProvider getSetDataProvider
     */
    public function testGetSet($property, $value, $expected)
    {
        $obj = new Activity();
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
        $user = $this->getMock('Oro\UserBundle\Entity\User');
        $issue = $this->getMock('Oro\IssueBundle\Entity\Issue');
        $created = new \DateTime();

        return array(
            'comment'    => array('comment', 'Test Comment', 'Test Comment'),
            'type'    => array('type', 'test_type', 'test_type'),
            'issueNewStatus'    => array('issueNewStatus', 'test_new_status', 'test_new_status'),
            'created' => array('created', $created, $created),
            'user' => array('user', $user, $user),
            'issue' => array('issue', $issue, $issue),
        );
    }
}
