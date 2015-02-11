<?php

namespace Oro\UserBundle\Tests\Functional\Controller;

use Oro\TestBundle\Test\WebTestCase;

class UserController extends WebTestCase
{
    const USER_NAME = 'newuser';
    const USER_EMAIL = 'newuser@test.com';
    const USER_FULLNAME = 'New Test User';
    const USER_PASSWORD = 'qa123123';
    const USER_ROLE = 'Operator';

    const USER_NAME_CHANGED = 'cnewuser';
    const USER_EMAIL_CHANGED = 'cnewuser@test.com';
    const USER_FULLNAME_CHANGED = 'New Test User Changed';
    const USER_ROLE_CHANGED = 'Manager';

    /**
     * @return null|string
     */
    public function testCreate()
    {
        $client = static::createClient(array(), static::getAdminAuthHeader());

        $url = $client->getContainer()->get('router')->generate('_user_create');
        $crawler = $client->request('GET', $url);

        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for User Create page"
        );

        $translator = $client->getContainer()->get('translator');
        $this->assertTrue($crawler->filter('html:contains("' . $translator->trans('Create new user') . '")')->count() > 0);

        $form = $crawler->selectButton('Create User')->form();
        $form['user_create[username]'] = self::USER_NAME;
        $form['user_create[email]'] = self::USER_EMAIL;
        $form['user_create[fullname]'] = self::USER_FULLNAME;
        $form['user_create[password][first]'] = self::USER_PASSWORD;
        $form['user_create[password][second]'] = self::USER_PASSWORD;
        $role = $crawler->filter('option:contains("' . self::USER_ROLE . '")')->first()->attr('value');
        $form['user_create[role]']->select($role);
        $form['user_create[avatar_file]']->upload(__DIR__ . '/../../Fixtures/test_avatar.jpg');

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();

        $savedMessage = $translator->trans('The User has been saved!');
        $this->assertTrue($crawler->filter('html:contains("' . $savedMessage . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::USER_EMAIL . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::USER_NAME . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::USER_FULLNAME . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::USER_ROLE . '")')->count() > 0);

        $userLink = $crawler->selectLink(self::USER_FULLNAME)->link()->getUri();
        $userId = $this->getParamFromUrl($userLink, 'userId');

        return $userId;
    }

    /**
     * @param $userId
     * @depends testCreate
     *
     * @return null|string
     */
    public function testUpdate($userId)
    {
        $client = static::createClient(array(), static::getAdminAuthHeader());

        $url = $client->getContainer()->get('router')->generate('_user_update', array('userId' => $userId));
        $crawler = $client->request('GET', $url);

        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for User Update page"
        );

        $translator = $client->getContainer()->get('translator');
        $this->assertTrue($crawler->filter('html:contains("' . $translator->trans('Edit User Profile') . '")')->count() > 0);

        $form = $crawler->selectButton('Update')->form();
        $form['user_create[username]'] = self::USER_NAME_CHANGED;
        $form['user_create[email]'] = self::USER_EMAIL_CHANGED;
        $form['user_create[fullname]'] = self::USER_FULLNAME_CHANGED;
        $role = $crawler->filter('option:contains("' . self::USER_ROLE_CHANGED . '")')->first()->attr('value');
        $form['user_create[role]']->select($role);

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();

        $savedMessage = $translator->trans('The User Data has been saved!');
        $this->assertTrue($crawler->filter('html:contains("' . $savedMessage . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::USER_EMAIL_CHANGED . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::USER_NAME_CHANGED . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::USER_FULLNAME_CHANGED . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::USER_ROLE_CHANGED . '")')->count() > 0);

        return $userId;
    }

    /**
     * @param $userId
     * @depends testUpdate
     */
    public function testView($userId)
    {
        $client = static::createClient(array(), static::getAdminAuthHeader());

        $url = $client->getContainer()->get('router')->generate('_user_view', array('userId' =>$userId));
        $crawler = $client->request('GET', $url);

        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for User View page"
        );

        $this->assertTrue($crawler->filter('html:contains("' . self::USER_EMAIL_CHANGED . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::USER_NAME_CHANGED . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::USER_FULLNAME_CHANGED . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::USER_ROLE_CHANGED . '")')->count() > 0);
    }

    public function testIndex()
    {
        $client = static::createClient(array(), static::getAdminAuthHeader());

        $url = $client->getContainer()->get('router')->generate('_user');
        $crawler = $client->request('GET', $url);

        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for Users List page"
        );

        $this->assertTrue($crawler->filter('html:contains("' . self::USER_EMAIL_CHANGED . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::USER_NAME_CHANGED . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::USER_FULLNAME_CHANGED . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("' . self::USER_ROLE_CHANGED . '")')->count() > 0);
    }
}
