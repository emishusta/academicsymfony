<?php

namespace Oro\IssueBundle\Tests\Functional\Controller;

use Oro\TestBundle\Test\WebTestCase;

class IssueController extends WebTestCase
{
    const PROJECT_NAME = 'Project Test #1';

    const ISSUE_SUMMARY = 'Test Issue #1.3';
    const ISSUE_DESCRIPTION = 'Description for Test Issue #1.3';
    const ISSUE_TYPE = 'Task';
    const ISSUE_PRIORITY = 'Blocker';

    const ISSUE_SUMMARY_CHANGED = 'Test Issue #1.3 - Changed';
    const ISSUE_DESCRIPTION_CHANGED = 'Changed Description';
    const ISSUE_PRIORITY_CHANGED = 'Minor';

    /**
     * @return int|null
     */
    public function testCreate()
    {
        $client = static::createClient(array(), static::getAdminAuthHeader());

        $url = $client->getContainer()->get('router')->generate('_project');
        $crawler = $client->request('GET', $url);

        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for Project List page"
        );

        $projectLink = $crawler->selectLink(self::PROJECT_NAME)->link();

        $crawler = $client->click($projectLink);

        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for Project page"
        );

        $newIssueLink = $crawler->selectLink('Create New Issue')->link();

        $crawler = $client->click($newIssueLink);

        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for create Issue page"
        );

        $translator = $client->getContainer()->get('translator');
        $this->assertTrue($crawler->filter('html:contains("' . $translator->trans('Create New Issue') . '")')->count() > 0);

        $form = $crawler->selectButton('Create')->form();
        $projectId = $crawler->filter('option:contains("' . self::PROJECT_NAME . '")')->first()->attr('value');
        $form['issue_create[project]']->select($projectId);
        $form['issue_create[summary]'] = self::ISSUE_SUMMARY;
        $form['issue_create[description]'] = self::ISSUE_DESCRIPTION;
        $typeValue = $crawler->filter('option:contains("' . self::ISSUE_TYPE . '")')->first()->attr('value');
        $form['issue_create[type]']->select($typeValue);
        $priorityValue = $crawler->filter('option:contains("' . self::ISSUE_PRIORITY . '")')->first()->attr('value');
        $form['issue_create[priority]']->select($priorityValue);

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();

        $savedMessage = $translator->trans('The Issue has been saved!');
        $this->assertTrue($crawler->filter('html:contains("' . $savedMessage . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::ISSUE_SUMMARY . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::ISSUE_DESCRIPTION . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::PROJECT_NAME . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::ISSUE_TYPE . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::ISSUE_PRIORITY . '")')->count() > 0);

        $url = $client->getHistory()->current()->getUri();
        $issueId = $this->getParamFromUrl($url, 'issueId');

        $this->assertNotNull($issueId);

        return $issueId;
    }

    /**
     * @param int $issueId
     * @depends testCreate
     *
     * @return null|int
     */
    public function testIndex($issueId)
    {
        $client = static::createClient(array(), static::getAdminAuthHeader());

        $url = $client->getContainer()->get('router')->generate('_issue');
        $crawler = $client->request('GET', $url);

        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for Issues List page"
        );

        $this->assertTrue($crawler->filter('html:contains("' . self::ISSUE_SUMMARY . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::ISSUE_TYPE . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::ISSUE_PRIORITY . '")')->count() > 0);

        return $issueId;
    }

    /**
     * @param int $issueId
     * @depends testIndex
     *
     * @return null|int
     */
    public function testView($issueId)
    {
        $client = static::createClient(array(), static::getAdminAuthHeader());

        $url = $client->getContainer()->get('router')->generate('_issue_view', array('issueId' => $issueId));
        $crawler = $client->request('GET', $url);

        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for Issue page"
        );

        $this->assertTrue($crawler->filter('html:contains("' . self::ISSUE_SUMMARY . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::ISSUE_TYPE . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::ISSUE_PRIORITY . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::ISSUE_DESCRIPTION . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::PROJECT_NAME . '")')->count() > 0);

        return $issueId;
    }

    /**
     * @param $issueId
     * @depends testView
     *
     * @return mixed
     */
    public function testUpdate($issueId)
    {
        $client = static::createClient(array(), static::getAdminAuthHeader());
        $url = $client->getContainer()->get('router')->generate('_issue_update', array('issueId' => $issueId));
        $crawler = $client->request('GET', $url);

        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for Issue Update page"
        );

        $translator = $client->getContainer()->get('translator');
        $this->assertTrue($crawler->filter('html:contains("' . $translator->trans('Edit Issue') . '")')->count() > 0);

        $form = $crawler->selectButton('Update')->form();
        $form['issue_update[summary]'] = self::ISSUE_SUMMARY_CHANGED;
        $form['issue_update[description]'] = self::ISSUE_DESCRIPTION_CHANGED;
        $priorityValue = $crawler->filter('option:contains("' . self::ISSUE_PRIORITY_CHANGED . '")')->first()->attr('value');
        $form['issue_update[priority]']->select($priorityValue);

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();

        $savedMessage = $translator->trans('The Issue Data has been saved!');
        $this->assertTrue($crawler->filter('html:contains("' . $savedMessage . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::ISSUE_SUMMARY_CHANGED . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::ISSUE_DESCRIPTION_CHANGED . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::ISSUE_PRIORITY_CHANGED . '")')->count() > 0);

        return $issueId;
    }
}
