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
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * LDAP credentials checker.
 */
class LdapCredentialsChecker
{
    protected LdapInterface $ldap;

    /**
     * @codeCoverageIgnore Dependency Injection constructor
     */
    public function __construct(LdapInterface $ldap)
    {
        $this->ldap = $ldap;
    }

    /**
     * Checks credentials of a LDAP user.
     *
     * @param string        $credentials Password entered in the login form
     * @param UserInterface $user        LDAP user object
     *
     * @return bool Whether the password is valid
     */
    public function __invoke(string $credentials, UserInterface $user): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($user->getAccountProvider() !== AccountProvider::LDAP) {
            return false;
        }

        return $this->ldap->checkCredentials($user->getAccountUid(), $credentials);
    }
}
