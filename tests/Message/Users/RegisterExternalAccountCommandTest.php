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

namespace App\Message\Users;

use App\Dictionary\AccountProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversDefaultClass \App\Message\Users\RegisterExternalAccountCommand
 */
final class RegisterExternalAccountCommandTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getEmail
     * @covers ::getFullname
     * @covers ::getProvider
     * @covers ::getUid
     */
    public function testConstructor(): void
    {
        $command = new RegisterExternalAccountCommand('anna@example.com', 'Anna Rodygina', AccountProvider::LDAP, '123');

        self::assertSame('anna@example.com', $command->getEmail());
        self::assertSame('Anna Rodygina', $command->getFullname());
        self::assertSame(AccountProvider::LDAP, $command->getProvider());
        self::assertSame('123', $command->getUid());
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorUnknownProvider(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Unknown account provider: noname');

        new RegisterExternalAccountCommand('anna@example.com', 'Anna Rodygina', 'noname', '123');
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorInvalidProvider(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Invalid account provider: etraxis');

        new RegisterExternalAccountCommand('anna@example.com', 'Anna Rodygina', AccountProvider::ETRAXIS, '123');
    }
}
