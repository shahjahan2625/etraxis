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

use App\Dictionary\FieldPermission;
use App\Dictionary\FieldType;
use App\Dictionary\StateType;
use App\Dictionary\SystemRole;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversDefaultClass \App\Entity\FieldRolePermission
 */
final class FieldRolePermissionTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $field = new Field($state, FieldType::LIST);

        $permission = new FieldRolePermission($field, SystemRole::AUTHOR, FieldPermission::READ_WRITE);

        self::assertSame($field, $permission->getField());
        self::assertSame(SystemRole::AUTHOR, $permission->getRole());
        self::assertSame(FieldPermission::READ_WRITE, $permission->getPermission());
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorExceptionRole(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Unknown system role: foo');

        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $field = new Field($state, FieldType::LIST);

        new FieldRolePermission($field, 'foo', FieldPermission::READ_WRITE);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorExceptionPermission(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Unknown permission: bar');

        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $field = new Field($state, FieldType::LIST);

        new FieldRolePermission($field, SystemRole::AUTHOR, 'bar');
    }

    /**
     * @covers ::getField
     */
    public function testField(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $field = new Field($state, FieldType::LIST);

        $permission = new FieldRolePermission($field, SystemRole::AUTHOR, FieldPermission::READ_WRITE);
        self::assertSame($field, $permission->getField());
    }

    /**
     * @covers ::getRole
     */
    public function testRole(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $field = new Field($state, FieldType::LIST);

        $permission = new FieldRolePermission($field, SystemRole::AUTHOR, FieldPermission::READ_WRITE);
        self::assertSame(SystemRole::AUTHOR, $permission->getRole());
    }

    /**
     * @covers ::getPermission
     */
    public function testPermission(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $field = new Field($state, FieldType::LIST);

        $permission = new FieldRolePermission($field, SystemRole::AUTHOR, FieldPermission::READ_WRITE);
        self::assertSame(FieldPermission::READ_WRITE, $permission->getPermission());
    }
}
