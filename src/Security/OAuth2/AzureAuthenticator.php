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
use KnpU\OAuth2ClientBundle\Client\Provider\AzureClient;
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
 * Authenticates a user against Microsoft Azure OAuth2 server.
 */
class AzureAuthenticator extends AbstractAuthenticator implements AuthenticatorInterface
{
    protected CommandBusInterface   $commandBus;
    protected OAuth2ClientInterface $client;

    /**
     * @codeCoverageIgnore Dependency Injection constructor
     */
    public function __construct(AzureClient $client, CommandBusInterface $commandBus)
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

        return $route === 'oauth2_azure' && $hasState && $hasCode;
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(Request $request): PassportInterface
    {
        try {
            $token = $this->client->getAccessToken();

            /** @var \TheNetworg\OAuth2\Client\Provider\AzureResourceOwner $owner */
            $owner = $this->client->fetchUserFromToken($token);
            $email = $owner->claim('email');
            $name  = $owner->claim('name');
        }
        catch (\Throwable $throwable) {
            throw new AuthenticationException('Bad credentials.', 0, $throwable);
        }

        if ($owner->getId() === null || $email === null || $name === null) {
            throw new AuthenticationException('Bad credentials.');
        }

        $command = new RegisterExternalAccountCommand($email, $name, AccountProvider::AZURE, $owner->getId());

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
