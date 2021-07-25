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

namespace App\Entity;

use App\Dictionary\AccountProvider;
use App\ReflectionTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversDefaultClass \App\Entity\User
 */
final class UserTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $uuid_pattern = '/^([[:xdigit:]]{8}-[[:xdigit:]]{4}-[[:xdigit:]]{4}-[[:xdigit:]]{4}-[[:xdigit:]]{12})$/is';

        $user = new User();

        self::assertFalse($user->isAdmin());
        self::assertSame(AccountProvider::ETRAXIS, $user->getAccountProvider());
        self::assertMatchesRegularExpression($uuid_pattern, $user->getAccountUid());
        self::assertEmpty($user->getGroups());
    }

    /**
     * @covers ::getUserIdentifier
     */
    public function testGetUserIdentifier(): void
    {
        $user = new User();

        $user->setEmail('anna@example.com');
        self::assertSame('anna@example.com', $user->getUserIdentifier());
    }

    /**
     * @covers ::getRoles
     */
    public function testRoles(): void
    {
        $user = new User();
        self::assertSame([User::ROLE_USER], $user->getRoles());

        $user->setAdmin(true);
        self::assertSame([User::ROLE_ADMIN], $user->getRoles());
    }

    /**
     * @covers ::getId
     */
    public function testId(): void
    {
        $user = new User();

        $this->setProperty($user, 'id', 1);
        self::assertSame(1, $user->getId());
    }

    /**
     * @covers ::getEmail
     * @covers ::setEmail
     */
    public function testEmail(): void
    {
        $user = new User();

        $user->setEmail('anna@example.com');
        self::assertSame('anna@example.com', $user->getEmail());
    }

    /**
     * @covers ::getPassword
     * @covers ::setPassword
     */
    public function testPassword(): void
    {
        $user = new User();
        self::assertNull($user->getPassword());

        $user->setPassword('secret');
        self::assertSame('secret', $user->getPassword());
    }

    /**
     * @covers ::getFullname
     * @covers ::setFullname
     */
    public function testFullname(): void
    {
        $user = new User();

        $user->setFullname('Anna Rodygina');
        self::assertSame('Anna Rodygina', $user->getFullname());
    }

    /**
     * @covers ::getDescription
     * @covers ::setDescription
     */
    public function testDescription(): void
    {
        $user = new User();
        self::assertNull($user->getDescription());

        $user->setDescription('Very lovely daughter');
        self::assertSame('Very lovely daughter', $user->getDescription());
    }

    /**
     * @covers ::isAdmin
     * @covers ::setAdmin
     */
    public function testAdmin(): void
    {
        $user = new User();
        self::assertFalse($user->isAdmin());

        $user->setAdmin(true);
        self::assertTrue($user->isAdmin());

        $user->setAdmin(false);
        self::assertFalse($user->isAdmin());
    }

    /**
     * @covers ::getAccountProvider
     * @covers ::setAccountProvider
     */
    public function testAccountProvider(): void
    {
        $user = new User();
        self::assertSame(AccountProvider::ETRAXIS, $user->getAccountProvider());

        $user->setAccountProvider(AccountProvider::LDAP);
        self::assertSame(AccountProvider::LDAP, $user->getAccountProvider());
    }

    /**
     * @covers ::setAccountProvider
     */
    public function testAccountProviderException(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Unknown account provider: noname');

        $user = new User();
        $user->setAccountProvider('noname');
    }

    /**
     * @covers ::getAccountUid
     * @covers ::setAccountUid
     */
    public function testAccountUid(): void
    {
        $expected = '80fe8ef1-00ba-4d37-9028-6d92db603c91';

        $user = new User();
        self::assertNotSame($expected, $user->getAccountUid());

        $user->setAccountUid($expected);
        self::assertSame($expected, $user->getAccountUid());
    }

    /**
     * @covers ::getLocale
     * @covers ::setLocale
     */
    public function testLocale(): void
    {
        $user = new User();
        self::assertSame('en', $user->getLocale());

        $user->setLocale('ru');
        self::assertSame('ru', $user->getLocale());

        $user->setLocale('xx');
        self::assertSame('ru', $user->getLocale());
    }

    /**
     * @covers ::getTimezone
     * @covers ::setTimezone
     */
    public function testTimezone(): void
    {
        $user = new User();
        self::assertSame('UTC', $user->getTimezone());

        $user->setTimezone('Pacific/Auckland');
        self::assertSame('Pacific/Auckland', $user->getTimezone());

        $user->setTimezone('Unknown');
        self::assertSame('Pacific/Auckland', $user->getTimezone());
    }

    /**
     * @covers ::getGroups
     */
    public function testGroups(): void
    {
        $user = new User();
        self::assertEmpty($user->getGroups());

        /** @var \Doctrine\Common\Collections\Collection $groups */
        $groups = $this->getProperty($user, 'groups');
        $groups->add('Group A');
        $groups->add('Group B');

        self::assertSame(['Group A', 'Group B'], $user->getGroups()->getValues());
    }

    /**
     * @covers ::isAccountExternal
     */
    public function testIsAccountExternal(): void
    {
        $user = new User();
        self::assertFalse($user->isAccountExternal());

        $user->setAccountProvider(AccountProvider::LDAP);
        self::assertTrue($user->isAccountExternal());

        $user->setAccountProvider(AccountProvider::ETRAXIS);
        self::assertFalse($user->isAccountExternal());
    }
}
