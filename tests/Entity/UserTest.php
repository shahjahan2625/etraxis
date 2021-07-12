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
        $user = new User();

        self::assertFalse($user->isAdmin());
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
}
