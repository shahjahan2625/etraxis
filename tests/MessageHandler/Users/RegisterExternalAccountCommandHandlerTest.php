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

namespace App\MessageHandler\Users;

use App\Dictionary\AccountProvider;
use App\Entity\User;
use App\Message\Users\RegisterExternalAccountCommand;
use App\MessageBus\Contracts\CommandBusInterface;
use App\Repository\Contracts\UserRepositoryInterface;
use App\TransactionalTestCase;

/**
 * @internal
 * @covers \App\MessageHandler\Users\RegisterExternalAccountCommandHandler::__invoke
 */
final class RegisterExternalAccountCommandHandlerTest extends TransactionalTestCase
{
    private CommandBusInterface     $commandBus;
    private UserRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commandBus = self::getContainer()->get(CommandBusInterface::class);
        $this->repository = $this->doctrine->getRepository(User::class);
    }

    public function testNewUser(): void
    {
        /** @var User $user */
        $user = $this->repository->findOneByEmail('anna@example.com');
        self::assertNull($user);

        $command = new RegisterExternalAccountCommand('anna@example.com', 'Anna Rodygina', AccountProvider::LDAP, 'ldap-a56eb4e9');

        $this->commandBus->handle($command);

        $user = $this->repository->findOneByEmail('anna@example.com');
        self::assertInstanceOf(User::class, $user);

        self::assertSame('anna@example.com', $user->getEmail());
        self::assertSame('Anna Rodygina', $user->getFullname());
        self::assertSame(AccountProvider::LDAP, $user->getAccountProvider());
        self::assertSame('ldap-a56eb4e9', $user->getAccountUid());
        self::assertSame('en', $user->getLocale());
    }

    public function testExistingUserByUid(): void
    {
        /** @var User $user */
        $user = $this->repository->findOneByEmail('einstein@ldap.forumsys.com');
        self::assertNotNull($user);

        self::assertSame('einstein@ldap.forumsys.com', $user->getEmail());
        self::assertSame('Albert Einstein', $user->getFullname());
        self::assertSame(AccountProvider::LDAP, $user->getAccountProvider());
        self::assertSame('uid=einstein,dc=example,dc=com', $user->getAccountUid());

        $command = new RegisterExternalAccountCommand('anna@example.com', 'Anna Rodygina', AccountProvider::LDAP, 'uid=einstein,dc=example,dc=com');

        $this->commandBus->handle($command);

        $this->doctrine->getManager()->refresh($user);

        self::assertSame('anna@example.com', $user->getEmail());
        self::assertSame('Anna Rodygina', $user->getFullname());
        self::assertSame(AccountProvider::LDAP, $user->getAccountProvider());
        self::assertSame('uid=einstein,dc=example,dc=com', $user->getAccountUid());
    }

    public function testExistingUserByEmail(): void
    {
        /** @var User $user */
        $user = $this->repository->findOneByEmail('artem@example.com');
        self::assertNotNull($user);

        self::assertSame('artem@example.com', $user->getEmail());
        self::assertSame('Artem Rodygin', $user->getFullname());
        self::assertSame(AccountProvider::ETRAXIS, $user->getAccountProvider());
        self::assertNotSame('ldap-a56eb4e9', $user->getAccountUid());

        $command = new RegisterExternalAccountCommand('artem@example.com', 'Tomas Rodriges', AccountProvider::LDAP, 'ldap-a56eb4e9');

        $this->commandBus->handle($command);

        $this->doctrine->getManager()->refresh($user);

        self::assertSame('artem@example.com', $user->getEmail());
        self::assertSame('Tomas Rodriges', $user->getFullname());
        self::assertSame(AccountProvider::LDAP, $user->getAccountProvider());
        self::assertSame('ldap-a56eb4e9', $user->getAccountUid());
    }
}
