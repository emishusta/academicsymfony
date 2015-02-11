<?php

namespace Oro\IssueBundle\Tests\Functional\Controller;

use Oro\TestBundle\Test\WebTestCase;

class CommentController extends WebTestCase
{
    const ISSUE_SUMMARY = 'Test Issue #1.1';
    const COMMENT_BODY = 'New Test Comment!';
    const COMMENT_BODY_CHANGED = 'Changed New Test Comment';

    public function testCreate()
    {
        $client = static::createClient(array(), static::getAdminAuthHeader());

        $url = $client->getContainer()->get('router')->generate('_issue');
        $crawler = $client->request('GET', $url);

        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for Issues List page"
        );

        $issueLink = $crawler->selectLink(self::ISSUE_SUMMARY)->link();

        $crawler = $client->click($issueLink);

        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for create Issue page"
        );

        $this->assertTrue($crawler->filter('html:contains("Add New Comment")')->count() > 0);

        $url = $client->getHistory()->current()->getUri();
        $issueId = $this->getParamFromUrl($url, 'issueId');

        $this->assertNotNull($issueId);

        $form = $crawler->selectButton('Submit')->form();
        $form['issue_comment_create[body]'] = self::COMMENT_BODY;
        $form['issue_comment_create[issue]'] = $issueId;

        $crawler = $client->submit($form);

        $this->assertTrue($crawler->filter('html:contains("' . self::COMMENT_BODY . '")')->count() > 0);

        return $issueId;
    }

    /**
     * @param $issueId
     * @depends testCreate
     *
     * @return mixed
     */
    public function testUpdate($issueId)
    {
        $client = static::createClient(array(), static::getAdminAuthHeader());

        $url = $client->getContainer()->get('router')->generate('_issue_view', array('issueId' => $issueId));
        $crawler = $client->request('GET', $url);

        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for Issue page"
        );

        $commentId = $crawler->filter('p.comment-toolbar')->first()->attr('id');

        $url = $client->getContainer()->get('router')
            ->generate('_issue_comment_update', array('commentId' => $commentId));
        $parameters = array(
            'issue_comment_create[body]' => self::COMMENT_BODY_CHANGED,
            'issue_comment_create[issue]' => $issueId,
        );
        $crawler = $client->request('POST', $url, $parameters);

        $this->assertTrue($crawler->filter('html:contains("' . self::COMMENT_BODY_CHANGED . '")')->count() > 0);

        return $commentId;
    }

    /**
     * @param $commentId
     * @depends testUpdate
     */
    public function testDelete($commentId)
    {
        $client = static::createClient(array(), static::getAdminAuthHeader());

        $url = $client->getContainer()->get('router')
            ->generate('_issue_comment_update', array('commentId' => $commentId));
        $crawler = $client->request('GET', $url);

        $this->assertFalse($crawler->filter('html:contains("' . self::COMMENT_BODY_CHANGED . '")')->count() > 0);
    }
}

