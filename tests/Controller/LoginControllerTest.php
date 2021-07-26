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
 * @coversDefaultClass \App\Controller\LoginController
 */
final class LoginControllerTest extends WebTestCase
{
    /**
     * @covers ::__invoke
     */
    public function testAnonymous(): void
    {
        $client = self::createClient();
        $client->request(Request::METHOD_GET, '/login');

        self::assertTrue($client->getResponse()->isOk());
    }

    /**
     * @covers ::__invoke
     */
    public function testUser(): void
    {
        $client   = self::createClient();
        $doctrine = self::getContainer()->get('doctrine');

        $user = $doctrine->getRepository(User::class)->findOneBy(['email' => 'artem@example.com']);

        $client->loginUser($user);
        $client->request(Request::METHOD_GET, '/login');

        self::assertTrue($client->getResponse()->isRedirect('/'));
    }
}
