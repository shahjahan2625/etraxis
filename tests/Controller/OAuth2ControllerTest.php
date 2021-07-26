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
 * @coversDefaultClass \App\Controller\OAuth2Controller
 */
final class OAuth2ControllerTest extends WebTestCase
{
    /**
     * @covers ::github
     */
    public function testGithubAnonymous(): void
    {
        $client = self::createClient();
        $client->request(Request::METHOD_GET, '/oauth2/github');

        self::assertTrue($client->getResponse()->isRedirection());

        $location = $client->getResponse()->headers->get('Location');
        self::assertMatchesRegularExpression('/^(https:\/\/github.com\/)(.)+$/i', $location);
    }

    /**
     * @covers ::github
     */
    public function testGithubAuthenticated(): void
    {
        $client   = self::createClient();
        $doctrine = self::getContainer()->get('doctrine');

        $user = $doctrine->getRepository(User::class)->findOneBy(['email' => 'artem@example.com']);

        $client->loginUser($user);
        $client->request(Request::METHOD_GET, '/oauth2/github');

        self::assertTrue($client->getResponse()->isRedirect('/'));
    }

    /**
     * @covers ::google
     */
    public function testGoogleAnonymous(): void
    {
        $client = self::createClient();
        $client->request(Request::METHOD_GET, '/oauth2/google');

        self::assertTrue($client->getResponse()->isRedirection());

        $location = $client->getResponse()->headers->get('Location');
        self::assertMatchesRegularExpression('/^(https:\/\/accounts.google.com\/)(.)+$/i', $location);
    }

    /**
     * @covers ::google
     */
    public function testGoogleAuthenticated(): void
    {
        $client   = self::createClient();
        $doctrine = self::getContainer()->get('doctrine');

        $user = $doctrine->getRepository(User::class)->findOneBy(['email' => 'artem@example.com']);

        $client->loginUser($user);
        $client->request(Request::METHOD_GET, '/oauth2/google');

        self::assertTrue($client->getResponse()->isRedirect('/'));
    }
}
