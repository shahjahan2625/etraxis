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

use App\Dictionary\AccountProvider;
use App\Message\Users\RegisterExternalAccountCommand;
use App\MessageBus\Contracts\CommandBusInterface;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Client\Provider\GoogleClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

/**
 * Authenticates a user against Google OAuth2 server.
 */
class GoogleAuthenticator extends AbstractAuthenticator implements AuthenticatorInterface
{
    private CommandBusInterface   $commandBus;
    private OAuth2ClientInterface $client;

    /**
     * @codeCoverageIgnore Dependency Injection constructor
     */
    public function __construct(GoogleClient $client, CommandBusInterface $commandBus)
    {
        $this->client     = $client;
        $this->commandBus = $commandBus;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(Request $request): ?bool
    {
        $route    = $request->attributes->get('_route');
        $hasState = $request->query->has('state');
        $hasCode  = $request->query->has('code');

        return $route === 'oauth2_google' && $hasState && $hasCode;
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(Request $request): PassportInterface
    {
        try {
            $token = $this->client->getAccessToken();

            /** @var \League\OAuth2\Client\Provider\GoogleUser $owner */
            $owner = $this->client->fetchUserFromToken($token);
            $email = $owner->getEmail();
            $name  = $owner->getName();
        }
        catch (\Throwable $throwable) {
            throw new AuthenticationException('Bad credentials.', 0, $throwable);
        }

        if ($owner->getId() === null || $email === null || $name === null) {
            throw new AuthenticationException('Bad credentials.');
        }

        $command = new RegisterExternalAccountCommand($email, $name, AccountProvider::GOOGLE, $owner->getId());

        $this->commandBus->handle($command);

        return new SelfValidatingPassport(
            new UserBadge($command->getEmail())
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
