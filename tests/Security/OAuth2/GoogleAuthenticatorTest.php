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

namespace App\Security\OAuth2;

use App\Entity\User;
use App\MessageBus\Contracts\CommandBusInterface;
use App\TransactionalTestCase;
use KnpU\OAuth2ClientBundle\Client\Provider\GoogleClient;
use KnpU\OAuth2ClientBundle\Exception\InvalidStateException;
use KnpU\OAuth2ClientBundle\Exception\MissingAuthorizationCodeException;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken;

/**
 * @internal
 * @coversDefaultClass \App\Security\OAuth2\GoogleAuthenticator
 */
final class GoogleAuthenticatorTest extends TransactionalTestCase
{
    private GoogleClient        $client;
    private CommandBusInterface $commandBus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client     = self::getContainer()->get(GoogleClient::class);
        $this->commandBus = self::getContainer()->get(CommandBusInterface::class);
    }

    /**
     * @covers ::supports
     */
    public function testSupportsSuccess(): void
    {
        $request = new Request([
            'code'  => 'valid-code',
            'state' => 'secret',
        ], [], [
            '_route' => 'oauth2_google',
        ]);

        $authenticator = new GoogleAuthenticator($this->client, $this->commandBus);
        self::assertTrue($authenticator->supports($request));
    }

    /**
     * @covers ::supports
     */
    public function testSupportsMissing(): void
    {
        $request = new Request([], [], [
            '_route' => 'oauth2_google',
        ]);

        $authenticator = new GoogleAuthenticator($this->client, $this->commandBus);
        self::assertFalse($authenticator->supports($request));
    }

    /**
     * @covers ::supports
     */
    public function testSupportsWrongRoute(): void
    {
        $request = new Request([
            'code'  => 'valid-code',
            'state' => 'secret',
        ], [], [
            '_route' => 'login',
        ]);

        $authenticator = new GoogleAuthenticator($this->client, $this->commandBus);
        self::assertFalse($authenticator->supports($request));
    }

    /**
     * @covers ::authenticate
     */
    public function testAuthenticateSuccess(): void
    {
        $token = $this->createMock(AccessToken::class);

        $owner = new GoogleUser([
            'sub'   => '423729',
            'email' => 'anna@example.com',
            'name'  => 'Anna Rodygina',
        ]);

        $client = $this->createMock(GoogleClient::class);
        $client
            ->method('getAccessToken')
            ->willReturn($token)
        ;
        $client
            ->method('fetchUserFromToken')
            ->willReturn($owner)
        ;

        $authenticator = new GoogleAuthenticator($client, $this->commandBus);

        $user = $this->doctrine->getRepository(User::class)->findOneBy(['email' => 'anna@example.com']);
        self::assertNull($user);

        $passport = $authenticator->authenticate(new Request());

        self::assertInstanceOf(SelfValidatingPassport::class, $passport);
        self::assertTrue($passport->hasBadge(UserBadge::class));

        /** @var UserBadge $badge */
        $badge = $passport->getBadge(UserBadge::class);
        self::assertSame('anna@example.com', $badge->getUserIdentifier());

        $user = $this->doctrine->getRepository(User::class)->findOneBy(['email' => 'anna@example.com']);
        self::assertNotNull($user);
    }

    /**
     * @covers ::authenticate
     */
    public function testAuthenticateInvalidState(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Bad credentials.');

        $owner = new GoogleUser([
            'sub'   => '423729',
            'email' => 'anna@example.com',
            'name'  => 'Anna Rodygina',
        ]);

        $client = $this->createMock(GoogleClient::class);
        $client
            ->method('getAccessToken')
            ->willThrowException(new InvalidStateException())
        ;
        $client
            ->method('fetchUserFromToken')
            ->willReturn($owner)
        ;

        $authenticator = new GoogleAuthenticator($client, $this->commandBus);

        $passport = $authenticator->authenticate(new Request());
        self::assertFalse($passport->hasBadge(UserBadge::class));
    }

    /**
     * @covers ::authenticate
     */
    public function testAuthenticateInvalidCode(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Bad credentials.');

        $token = $this->createMock(AccessToken::class);

        $client = $this->createMock(GoogleClient::class);
        $client
            ->method('getAccessToken')
            ->willReturn($token)
        ;
        $client
            ->method('fetchUserFromToken')
            ->willThrowException(new MissingAuthorizationCodeException())
        ;

        $authenticator = new GoogleAuthenticator($client, $this->commandBus);

        $passport = $authenticator->authenticate(new Request());
        self::assertFalse($passport->hasBadge(UserBadge::class));
    }

    /**
     * @covers ::onAuthenticationSuccess
     */
    public function testOnAuthenticationSuccess(): void
    {
        $request = new Request([
            'code'  => 'valid-code',
            'state' => 'secret',
        ], [], [
            '_route' => 'oauth2_google',
        ]);

        $authenticator = new GoogleAuthenticator($this->client, $this->commandBus);

        $token    = new PostAuthenticationToken(new User(), 'main', [User::ROLE_USER]);
        $response = $authenticator->onAuthenticationSuccess($request, $token, 'main');

        self::assertNull($response);
    }

    /**
     * @covers ::onAuthenticationFailure
     */
    public function testOnAuthenticationFailure(): void
    {
        $request = new Request([
            'code'  => 'valid-code',
            'state' => 'secret',
        ], [], [
            '_route' => 'oauth2_google',
        ]);

        $authenticator = new GoogleAuthenticator($this->client, $this->commandBus);

        $exception = new AuthenticationException('Bad credentials.');
        $response  = $authenticator->onAuthenticationFailure($request, $exception);

        self::assertNull($response);
    }
}
