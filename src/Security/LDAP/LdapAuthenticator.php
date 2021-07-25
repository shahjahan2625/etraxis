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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;

/**
 * Authenticates users using LDAP server.
 */
class LdapAuthenticator extends AbstractAuthenticator implements AuthenticatorInterface
{
    protected UrlGeneratorInterface  $urlGenerator;
    protected LdapUserLoader         $ldapUserLoader;
    protected LdapCredentialsChecker $ldapCredentialsChecker;

    /**
     * @codeCoverageIgnore Dependency Injection constructor
     */
    public function __construct(
        UrlGeneratorInterface  $urlGenerator,
        LdapUserLoader         $ldapUserLoader,
        LdapCredentialsChecker $ldapCredentialsChecker
    )
    {
        $this->urlGenerator           = $urlGenerator;
        $this->ldapUserLoader         = $ldapUserLoader;
        $this->ldapCredentialsChecker = $ldapCredentialsChecker;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(Request $request): ?bool
    {
        return $request->isMethod('POST')
            && $request->getPathInfo() === $this->urlGenerator->generate('login')
            && $request->request->has('email')
            && $request->request->has('password');
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(Request $request): PassportInterface
    {
        $email    = $request->request->get('email', '');
        $password = $request->request->get('password', '');

        return new Passport(
            new UserBadge($email, $this->ldapUserLoader),
            new CustomCredentials($this->ldapCredentialsChecker, $password),
            [
                new CsrfTokenBadge('authenticate', $request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return null;
    }
}
