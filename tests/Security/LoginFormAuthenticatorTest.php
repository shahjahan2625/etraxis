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

namespace App\Security;

use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken;

/**
 * @internal
 * @coversDefaultClass \App\Security\LoginFormAuthenticator
 */
final class LoginFormAuthenticatorTest extends TestCase
{
    private LoginFormAuthenticator $authenticator;

    protected function setUp(): void
    {
        parent::setUp();

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator
            ->method('generate')
            ->willReturnMap([
                ['homepage', [], UrlGeneratorInterface::ABSOLUTE_PATH, '/'],
                ['login',    [], UrlGeneratorInterface::ABSOLUTE_PATH, '/login'],
            ])
        ;

        $this->authenticator = new LoginFormAuthenticator($urlGenerator);
    }

    /**
     * @covers ::supports
     */
    public function testSupportsSuccess(): void
    {
        $request = new Request([], [
            'email'    => 'admin@example.com',
            'password' => 'secret',
        ]);

        $request->setMethod(Request::METHOD_POST);
        $request->server->set('REQUEST_URI', '/login');

        self::assertTrue($this->authenticator->supports($request));
    }

    /**
     * @covers ::supports
     */
    public function testSupportsNotPost(): void
    {
        $request = new Request([], [
            'email'    => 'admin@example.com',
            'password' => 'secret',
        ]);

        $request->setMethod(Request::METHOD_GET);
        $request->server->set('REQUEST_URI', '/login');

        self::assertFalse($this->authenticator->supports($request));
    }

    /**
     * @covers ::supports
     */
    public function testSupportsWrongUrl(): void
    {
        $request = new Request([], [
            'email'    => 'admin@example.com',
            'password' => 'secret',
        ]);

        $request->setMethod(Request::METHOD_POST);
        $request->server->set('REQUEST_URI', '/logout');

        self::assertFalse($this->authenticator->supports($request));
    }

    /**
     * @covers ::supports
     */
    public function testSupportsMissingData(): void
    {
        $request = new Request([
            'email'    => 'admin@example.com',
            'password' => 'secret',
        ]);

        $request->setMethod(Request::METHOD_POST);
        $request->server->set('REQUEST_URI', '/login');

        self::assertFalse($this->authenticator->supports($request));
    }

    /**
     * @covers ::authenticate
     */
    public function testAuthenticate(): void
    {
        $request = new Request([], [
            'email'    => 'admin@example.com',
            'password' => 'secret',
        ]);

        $passport = $this->authenticator->authenticate($request);

        self::assertTrue($passport->hasBadge(UserBadge::class));
        self::assertTrue($passport->hasBadge(PasswordCredentials::class));
        self::assertTrue($passport->hasBadge(CsrfTokenBadge::class));
        self::assertTrue($passport->hasBadge(RememberMeBadge::class));

        /** @var UserBadge $badge */
        $badge = $passport->getBadge(UserBadge::class);
        self::assertSame('admin@example.com', $badge->getUserIdentifier());

        /** @var PasswordCredentials $badge */
        $badge = $passport->getBadge(PasswordCredentials::class);
        self::assertSame('secret', $badge->getPassword());

        /** @var CsrfTokenBadge $badge */
        $badge = $passport->getBadge(CsrfTokenBadge::class);
        self::assertSame('authenticate', $badge->getCsrfTokenId());
    }

    /**
     * @covers ::onAuthenticationSuccess
     */
    public function testOnAuthenticationSuccess(): void
    {
        $request = new Request([], [
            'email'    => 'admin@example.com',
            'password' => 'secret',
        ]);

        $token    = new PostAuthenticationToken(new User(), 'main', [User::ROLE_USER]);
        $response = $this->authenticator->onAuthenticationSuccess($request, $token, 'main');

        self::assertNull($response);
    }

    /**
     * @covers ::onAuthenticationFailure
     */
    public function testOnAuthenticationFailure(): void
    {
        $exception = new AuthenticationException('Bad credentials.');

        $session = $this->createMock(SessionInterface::class);
        $session
            ->expects(self::exactly(2))
            ->method('set')
            ->withConsecutive(
                [Security::AUTHENTICATION_ERROR, $exception],
                [Security::LAST_USERNAME, 'admin@example.com'],
            )
        ;

        $request = new Request([], [
            'email'    => 'admin@example.com',
            'password' => 'secret',
        ]);

        $request->setSession($session);

        $response = $this->authenticator->onAuthenticationFailure($request, $exception);

        self::assertNull($response);
    }
}
