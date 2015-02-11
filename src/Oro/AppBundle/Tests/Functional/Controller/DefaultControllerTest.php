<?php

namespace Oro\AppBundle\Tests\Functional\Controller;

use Oro\TestBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient(array(), static::getAdminAuthHeader());

        $url = $client->getContainer()->get('router')->generate('homepage');
        $crawler = $client->request('GET', $url);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $translator = $client->getContainer()->get('translator');
        $this->assertTrue($crawler->filter('html:contains("' . $translator->trans('Dashboard') . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . $translator->trans('Activities') . '")')->count() > 0);
        $issueCollaboratorText = $translator->trans('Issues where user is collaborator');
        $this->assertTrue($crawler->filter('html:contains("' . $issueCollaboratorText . '")')->count() > 0);
    }
}
