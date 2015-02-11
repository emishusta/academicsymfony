<?php

namespace Oro\IssueBundle\Tests\Unit\Entity;

use Oro\IssueBundle\Entity\Comment;

class CommentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $property
     * @param string $value
     * @param string $expected
     * @dataProvider getSetDataProvider
     */
    public function testGetSet($property, $value, $expected)
    {
        $obj = new Comment();
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
        $author = $this->getMock('Oro\UserBundle\Entity\User');
        $issue = $this->getMock('Oro\IssueBundle\Entity\Issue');
        $created = new \DateTime();

        return array(
            'body'    => array('body', 'Test Comment Body', 'Test Comment Body'),
            'created' => array('created', $created, $created),
            'author' => array('author', $author, $author),
            'issue' => array('issue', $issue, $issue),
        );
    }
}
