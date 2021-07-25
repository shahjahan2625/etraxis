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

use App\Dictionary\AccountProvider;
use App\Message\Users\RegisterExternalAccountCommand;
use App\MessageBus\Contracts\CommandBusInterface;
use App\Repository\Contracts\UserRepositoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * LDAP user loader.
 */
class LdapUserLoader
{
    protected LdapInterface           $ldap;
    protected CommandBusInterface     $commandBus;
    protected UserRepositoryInterface $repository;

    /**
     * @codeCoverageIgnore Dependency Injection constructor
     */
    public function __construct(LdapInterface $ldap, CommandBusInterface $commandBus, UserRepositoryInterface $repository)
    {
        $this->ldap       = $ldap;
        $this->commandBus = $commandBus;
        $this->repository = $repository;
    }

    /**
     * Loads user from LDAP server.
     *
     * @param string $userIdentifier Email address entered in the login form
     *
     * @return null|UserInterface LDAP user object, if found
     */
    public function __invoke(string $userIdentifier): ?UserInterface
    {
        $dn = $fullname = '';

        if (!$this->ldap->findUser($userIdentifier, $dn, $fullname)) {
            return null;
        }

        $command = new RegisterExternalAccountCommand($userIdentifier, $fullname, AccountProvider::LDAP, $dn);

        $this->commandBus->handle($command);

        return $this->repository->findOneByEmail($userIdentifier);
    }
}
