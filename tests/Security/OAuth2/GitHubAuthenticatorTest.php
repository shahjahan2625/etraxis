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
use KnpU\OAuth2ClientBundle\Client\Provider\GithubClient;
use KnpU\OAuth2ClientBundle\Exception\InvalidStateException;
use KnpU\OAuth2ClientBundle\Exception\MissingAuthorizationCodeException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken;

/**
 * @internal
 * @coversDefaultClass \App\Security\OAuth2\GitHubAuthenticator
 */
final class GitHubAuthenticatorTest extends TransactionalTestCase
{
    private GithubClient        $client;
    private CommandBusInterface $commandBus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client     = self::getContainer()->get(GithubClient::class);
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
            '_route' => 'oauth2_github',
        ]);

        $authenticator = new GitHubAuthenticator($this->client, $this->commandBus);
        self::assertTrue($authenticator->supports($request));
    }

    /**
     * @covers ::supports
     */
    public function testSupportsMissing(): void
    {
        $request = new Request([], [], [
            '_route' => 'oauth2_github',
        ]);

        $authenticator = new GitHubAuthenticator($this->client, $this->commandBus);
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

        $authenticator = new GitHubAuthenticator($this->client, $this->commandBus);
        self::assertFalse($authenticator->supports($request));
    }

    /**
     * @covers ::authenticate
     */
    public function testAuthenticateSuccessWithPublicEmail(): void
    {
        $token = $this->createMock(AccessToken::class);

        $owner = new GithubResourceOwner([
            'id'    => '423729',
            'email' => 'anna@example.com',
            'name'  => 'Anna Rodygina',
        ]);

        $client = $this->createMock(GithubClient::class);
        $client
            ->method('getAccessToken')
            ->willReturn($token)
        ;
        $client
            ->method('fetchUserFromToken')
            ->willReturn($owner)
        ;

        $authenticator = new GitHubAuthenticator($client, $this->commandBus);

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
    public function testAuthenticateSuccessWithPrivateEmail(): void
    {
        $token = $this->createMock(AccessToken::class);

        $owner = new GithubResourceOwner([
            'id'   => '423729',
            'name' => 'Anna Rodygina',
        ]);

        $emails = [
            [
                'email'      => 'anna@example.com',
                'primary'    => true,
                'verified'   => true,
                'visibility' => 'private',
            ],
            [
                'email'      => 'anna@users.noreply.github.com',
                'primary'    => false,
                'verified'   => true,
                'visibility' => null,
            ],
        ];

        $body = $this->createMock(StreamInterface::class);
        $body
            ->method('getContents')
            ->willReturn(json_encode($emails))
        ;

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->method('getBody')
            ->willReturn($body)
        ;

        $provider = $this->createMock(AbstractProvider::class);
        $provider
            ->method('getResourceOwner')
            ->willReturn($owner)
        ;
        $provider
            ->method('getAuthenticatedRequest')
            ->willReturn($this->createMock(RequestInterface::class))
        ;
        $provider
            ->method('getResponse')
            ->willReturn($response)
        ;

        $client = $this->createMock(GithubClient::class);
        $client
            ->method('getAccessToken')
            ->willReturn($token)
        ;
        $client
            ->method('fetchUserFromToken')
            ->willReturn($owner)
        ;
        $client
            ->method('getOAuth2Provider')
            ->willReturn($provider)
        ;

        $authenticator = new GitHubAuthenticator($client, $this->commandBus);

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
    public function testAuthenticateMissingEmail(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Bad credentials.');

        $token = $this->createMock(AccessToken::class);

        $owner = new GithubResourceOwner([
            'id'   => '423729',
            'name' => 'Anna Rodygina',
        ]);

        $emails = [
            [
                'email'      => 'anna@example.com',
                'primary'    => false,
                'verified'   => true,
                'visibility' => 'private',
            ],
            [
                'email'      => 'anna@users.noreply.github.com',
                'primary'    => false,
                'verified'   => true,
                'visibility' => null,
            ],
        ];

        $body = $this->createMock(StreamInterface::class);
        $body
            ->method('getContents')
            ->willReturn(json_encode($emails))
        ;

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->method('getBody')
            ->willReturn($body)
        ;

        $provider = $this->createMock(AbstractProvider::class);
        $provider
            ->method('getResourceOwner')
            ->willReturn($owner)
        ;
        $provider
            ->method('getAuthenticatedRequest')
            ->willReturn($this->createMock(RequestInterface::class))
        ;
        $provider
            ->method('getResponse')
            ->willReturn($response)
        ;

        $client = $this->createMock(GithubClient::class);
        $client
            ->method('getAccessToken')
            ->willReturn($token)
        ;
        $client
            ->method('fetchUserFromToken')
            ->willReturn($owner)
        ;
        $client
            ->method('getOAuth2Provider')
            ->willReturn($provider)
        ;

        $authenticator = new GitHubAuthenticator($client, $this->commandBus);

        $passport = $authenticator->authenticate(new Request());
        self::assertFalse($passport->hasBadge(UserBadge::class));
    }

    /**
     * @covers ::authenticate
     */
    public function testAuthenticateInvalidState(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Bad credentials.');

        $owner = new GithubResourceOwner([
            'id'    => '423729',
            'email' => 'anna@example.com',
            'name'  => 'Anna Rodygina',
        ]);

        $client = $this->createMock(GithubClient::class);
        $client
            ->method('getAccessToken')
            ->willThrowException(new InvalidStateException())
        ;
        $client
            ->method('fetchUserFromToken')
            ->willReturn($owner)
        ;

        $authenticator = new GitHubAuthenticator($client, $this->commandBus);

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

        $client = $this->createMock(GithubClient::class);
        $client
            ->method('getAccessToken')
            ->willReturn($token)
        ;
        $client
            ->method('fetchUserFromToken')
            ->willThrowException(new MissingAuthorizationCodeException())
        ;

        $authenticator = new GitHubAuthenticator($client, $this->commandBus);

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
            '_route' => 'oauth2_github',
        ]);

        $authenticator = new GitHubAuthenticator($this->client, $this->commandBus);

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
            '_route' => 'oauth2_github',
        ]);

        $authenticator = new GitHubAuthenticator($this->client, $this->commandBus);

        $exception = new AuthenticationException('Bad credentials.');
        $response  = $authenticator->onAuthenticationFailure($request, $exception);

        self::assertNull($response);
    }
}
