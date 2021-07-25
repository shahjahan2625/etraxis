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
use App\TransactionalTestCase;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @internal
 * @coversDefaultClass \App\Security\LDAP\LdapCredentialsChecker
 */
final class LdapCredentialsCheckerTest extends TransactionalTestCase
{
    private LdapInterface $ldap;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ldap = $this->createMock(LdapInterface::class);
        $this->ldap
            ->method('checkCredentials')
            ->willReturnMap([
                ['uid=einstein,dc=example,dc=com', 'secret', true],
                ['uid=einstein,dc=example,dc=com', 'wrong', false],
            ])
        ;
    }

    /**
     * @covers ::__invoke
     */
    public function testSuccess(): void
    {
        $repository = $this->doctrine->getRepository(User::class);
        $user       = $repository->findOneByEmail('einstein@ldap.forumsys.com');

        $credentialsChecker = new LdapCredentialsChecker($this->ldap);

        self::assertTrue($credentialsChecker('secret', $user));
    }

    /**
     * @covers ::__invoke
     */
    public function testWrongPassword(): void
    {
        $repository = $this->doctrine->getRepository(User::class);
        $user       = $repository->findOneByEmail('einstein@ldap.forumsys.com');

        $credentialsChecker = new LdapCredentialsChecker($this->ldap);

        self::assertFalse($credentialsChecker('wrong', $user));
    }

    /**
     * @covers ::__invoke
     */
    public function testNotUser(): void
    {
        $user = new class() implements UserInterface {
            public function getRoles(): array
            {
                return [];
            }

            public function getPassword(): ?string
            {
                return null;
            }

            public function getSalt(): ?string
            {
                return null;
            }

            public function eraseCredentials(): void
            {
            }

            public function getUsername(): string
            {
                return '';
            }

            public function getUserIdentifier(): string
            {
                return '';
            }
        };

        $credentialsChecker = new LdapCredentialsChecker($this->ldap);

        self::assertFalse($credentialsChecker('secret', $user));
    }

    /**
     * @covers ::__invoke
     */
    public function testInternalUser(): void
    {
        $repository = $this->doctrine->getRepository(User::class);
        $user       = $repository->findOneByEmail('artem@example.com');

        $credentialsChecker = new LdapCredentialsChecker($this->ldap);

        self::assertFalse($credentialsChecker('secret', $user));
    }
}
