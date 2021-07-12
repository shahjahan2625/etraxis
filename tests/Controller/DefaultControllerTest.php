<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2018 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis. If not, see <https://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 * @coversDefaultClass \App\Controller\DefaultController
 */
final class DefaultControllerTest extends WebTestCase
{
    /**
     * @covers ::homepage
     */
    public function testHomepageAnon(): void
    {
        $client = self::createClient();
        $client->request(Request::METHOD_GET, '/');

        self::assertTrue($client->getResponse()->isRedirect('/login'));
    }

    /**
     * @covers ::homepage
     */
    public function testHomepageUser(): void
    {
        $client   = self::createClient();
        $doctrine = self::getContainer()->get('doctrine');

        $user = $doctrine->getRepository(User::class)->findOneBy(['email' => 'artem@example.com']);

        $client->loginUser($user);
        $client->request(Request::METHOD_GET, '/');

        self::assertTrue($client->getResponse()->isOk());
    }

    /**
     * @covers ::admin
     */
    public function testAdminAnon(): void
    {
        $client = self::createClient();
        $client->request(Request::METHOD_GET, '/admin/');

        self::assertTrue($client->getResponse()->isRedirect('/login'));
    }

    /**
     * @covers ::admin
     */
    public function testAdminUser(): void
    {
        $client   = self::createClient();
        $doctrine = self::getContainer()->get('doctrine');

        $user = $doctrine->getRepository(User::class)->findOneBy(['email' => 'artem@example.com']);

        $client->loginUser($user);
        $client->request(Request::METHOD_GET, '/admin/');

        self::assertTrue($client->getResponse()->isForbidden());
    }

    /**
     * @covers ::admin
     */
    public function testAdminAdmin(): void
    {
        $client   = self::createClient();
        $doctrine = self::getContainer()->get('doctrine');

        $user = $doctrine->getRepository(User::class)->findOneBy(['email' => 'admin@example.com']);

        $client->loginUser($user);
        $client->request(Request::METHOD_GET, '/admin/');

        self::assertTrue($client->getResponse()->isOk());
    }
}
