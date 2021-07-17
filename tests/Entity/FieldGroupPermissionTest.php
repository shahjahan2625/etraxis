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
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversDefaultClass \App\Entity\FieldGroupPermission
 */
final class FieldGroupPermissionTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $project = new Project();
        $group   = new Group($project);
        $state   = new State(new Template($project), StateType::INTERMEDIATE);
        $field   = new Field($state, FieldType::LIST);

        $permission = new FieldGroupPermission($field, $group, FieldPermission::READ_WRITE);

        self::assertSame($field, $permission->getField());
        self::assertSame($group, $permission->getGroup());
        self::assertSame(FieldPermission::READ_WRITE, $permission->getPermission());
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorExceptionGroup(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Unknown group: foo');

        $project1 = new Project();
        $project2 = new Project();

        $group = new Group($project2);
        $group->setName('foo');

        $state = new State(new Template($project1), StateType::INTERMEDIATE);
        $field = new Field($state, FieldType::LIST);

        new FieldGroupPermission($field, $group, FieldPermission::READ_WRITE);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorExceptionPermission(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Unknown permission: bar');

        $project = new Project();
        $group   = new Group($project);
        $state   = new State(new Template($project), StateType::INTERMEDIATE);
        $field   = new Field($state, FieldType::LIST);

        new FieldGroupPermission($field, $group, 'bar');
    }

    /**
     * @covers ::getField
     */
    public function testField(): void
    {
        $project = new Project();
        $group   = new Group($project);
        $state   = new State(new Template($project), StateType::INTERMEDIATE);
        $field   = new Field($state, FieldType::LIST);

        $permission = new FieldGroupPermission($field, $group, FieldPermission::READ_WRITE);
        self::assertSame($field, $permission->getField());
    }

    /**
     * @covers ::getGroup
     */
    public function testGroup(): void
    {
        $project = new Project();
        $group   = new Group($project);
        $state   = new State(new Template($project), StateType::INTERMEDIATE);
        $field   = new Field($state, FieldType::LIST);

        $permission = new FieldGroupPermission($field, $group, FieldPermission::READ_WRITE);
        self::assertSame($group, $permission->getGroup());
    }

    /**
     * @covers ::getPermission
     */
    public function testPermission(): void
    {
        $project = new Project();
        $group   = new Group($project);
        $state   = new State(new Template($project), StateType::INTERMEDIATE);
        $field   = new Field($state, FieldType::LIST);

        $permission = new FieldGroupPermission($field, $group, FieldPermission::READ_WRITE);
        self::assertSame(FieldPermission::READ_WRITE, $permission->getPermission());
    }
}
