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

namespace App\Security\LDAP;

use App\Entity\User;
use App\ReflectionTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken;

/**
 * @internal
 * @coversDefaultClass \App\Security\LDAP\LdapAuthenticator
 */
final class LdapAuthenticatorTest extends TestCase
{
    use ReflectionTrait;

    private LdapAuthenticator $authenticator;

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

        $userLoader         = $this->createMock(LdapUserLoader::class);
        $credentialsChecker = $this->createMock(LdapCredentialsChecker::class);

        $this->authenticator = new LdapAuthenticator($urlGenerator, $userLoader, $credentialsChecker);
    }

    /**
     * @covers ::supports
     */
    public function testSupportsSuccess(): void
    {
        $request = new Request([], [
            'email'    => 'einstein@example.com',
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
            'email'    => 'einstein@example.com',
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
            'email'    => 'einstein@example.com',
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
            'email'    => 'einstein@example.com',
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
            'email'    => 'einstein@example.com',
            'password' => 'secret',
        ]);

        $passport = $this->authenticator->authenticate($request);

        self::assertTrue($passport->hasBadge(UserBadge::class));
        self::assertTrue($passport->hasBadge(CustomCredentials::class));
        self::assertTrue($passport->hasBadge(CsrfTokenBadge::class));
        self::assertTrue($passport->hasBadge(RememberMeBadge::class));

        /** @var UserBadge $badge */
        $badge = $passport->getBadge(UserBadge::class);
        self::assertSame('einstein@example.com', $badge->getUserIdentifier());

        /** @var CustomCredentials $badge */
        $badge = $passport->getBadge(CustomCredentials::class);
        self::assertSame('secret', $this->getProperty($badge, 'credentials'));

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
            'email'    => 'einstein@example.com',
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
        $request = new Request([], [
            'email'    => 'einstein@example.com',
            'password' => 'secret',
        ]);

        $exception = new AuthenticationException('Bad credentials.');
        $response  = $this->authenticator->onAuthenticationFailure($request, $exception);

        self::assertNull($response);
    }
}
