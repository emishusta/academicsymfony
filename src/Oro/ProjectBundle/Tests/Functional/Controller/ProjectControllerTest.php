<?php

namespace Oro\ProjectBundle\Tests\Functional\Controller;

use Oro\TestBundle\Test\WebTestCase;

class ProjectController extends WebTestCase
{
    const PROJECT_NAME = 'Project Test #1';
    const PROJECT_CODE = 'PT1';
    const PROJECT_SUMMARY_PART = 'Lorem ipsum dolor sit amet';

    const PROJECT_NEW_LABEL = 'Project Test #3';
    const PROJECT_NEW_SUMMARY = 'Project Summary Test';
    const PROJECT_NEW_CODE = 'PT3';
    const PROJECT_NEW_USER = 'Admin Adminov';

    const PROJECT_CHANGED_LABEL = 'Project Test #3 Changed';
    const PROJECT_CHANGED_CODE = 'PC3';

    public function testIndex()
    {
        $client = static::createClient(array(), static::getAdminAuthHeader());

        $url = $client->getContainer()->get('router')->generate('_project');
        $crawler = $client->request('GET', $url);

        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for Project List page"
        );

        $this->assertTrue($crawler->filter('html:contains("' . self::PROJECT_NAME . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::PROJECT_CODE . '")')->count() > 0);

        $projectLink = $crawler->selectLink(self::PROJECT_NAME)->link()->getUri();
        $projectId = $this->getParamFromUrl($projectLink, 'projectId');

        return $projectId;
    }

    /**
     * @param int $projectId
     * @depends testIndex
     */
    public function testView($projectId)
    {
        $client = static::createClient(array(), static::getAdminAuthHeader());

        $url = $client->getContainer()->get('router')->generate('_project_view', array('projectId' =>$projectId));
        $crawler = $client->request('GET', $url);

        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for Project page"
        );

        $this->assertTrue($crawler->filter('html:contains("' . self::PROJECT_NAME . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::PROJECT_CODE . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::PROJECT_SUMMARY_PART . '")')->count() > 0);
        $translator = $client->getContainer()->get('translator');
        $this->assertTrue($crawler->filter('html:contains("' . $translator->trans('Issues') . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . $translator->trans('Activities') . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . $translator->trans('Members') . '")')->count() > 0);
    }

    public function testCreate()
    {
        $client = static::createClient(array(), static::getAdminAuthHeader());

        $url = $client->getContainer()->get('router')->generate('_project_create');
        $crawler = $client->request('GET', $url);

        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for Project Create page"
        );

        $translator = $client->getContainer()->get('translator');
        $this->assertTrue($crawler->filter('html:contains("' . $translator->trans('Create New Project') . '")')->count() > 0);

        $form = $crawler->selectButton('Create')->form();
        $form['project_create[label]'] = self::PROJECT_NEW_LABEL;
        $form['project_create[summary]'] = self::PROJECT_NEW_SUMMARY;
        $form['project_create[code]'] = self::PROJECT_NEW_CODE;
        $userId = $crawler->filter('option:contains("' . self::PROJECT_NEW_USER . '")')->first()->attr('value');
        $form['project_create[members]']->select($userId);

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();

        $savedMessage = $translator->trans('The Project has been saved!');
        $this->assertTrue($crawler->filter('html:contains("' . $savedMessage . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::PROJECT_NEW_LABEL . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::PROJECT_NEW_CODE . '")')->count() > 0);
    }

    public function testUpdate()
    {
        $client = static::createClient(array(), static::getAdminAuthHeader());

        $url = $client->getContainer()->get('router')->generate('_project');
        $crawler = $client->request('GET', $url);

        $projectLink = $crawler->selectLink(self::PROJECT_NAME)->link();
        $crawler = $client->click($projectLink);

        $translator = $client->getContainer()->get('translator');
        $projectEditLink = $crawler->selectLink($translator->trans('Edit Project'))->link();
        $crawler = $client->click($projectEditLink);

        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for Project Edit page"
        );

        $this->assertTrue($crawler->filter('html:contains("' . $translator->trans('Edit Project') . '")')->count() > 0);

        $form = $crawler->selectButton('Update')->form();
        $form['project_create[label]'] = self::PROJECT_CHANGED_LABEL;
        $form['project_create[code]'] = self::PROJECT_CHANGED_CODE;

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();

        $savedMessage = $translator->trans('The Project Data has been saved!');
        $this->assertTrue($crawler->filter('html:contains("' . $savedMessage . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::PROJECT_CHANGED_LABEL . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::PROJECT_CHANGED_CODE . '")')->count() > 0);
    }
}
