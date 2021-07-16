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

use App\Dictionary\SystemRole;
use App\Dictionary\TemplatePermission;
use App\ReflectionTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversDefaultClass \App\Entity\TemplateRolePermission
 */
final class TemplateRolePermissionTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $project = new Project();
        $this->setProperty($project, 'id', 1);

        $template = new Template($project);
        $this->setProperty($template, 'id', 2);

        $permission = new TemplateRolePermission($template, SystemRole::AUTHOR, TemplatePermission::EDIT_ISSUES);
        self::assertSame($template, $permission->getTemplate());
        self::assertSame(SystemRole::AUTHOR, $permission->getRole());
        self::assertSame(TemplatePermission::EDIT_ISSUES, $permission->getPermission());
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorExceptionRole(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Unknown system role: foo');

        $project = new Project();
        $this->setProperty($project, 'id', 1);

        $template = new Template($project);
        $this->setProperty($template, 'id', 2);

        new TemplateRolePermission($template, 'foo', TemplatePermission::EDIT_ISSUES);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorExceptionPermission(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Unknown permission: bar');

        $project = new Project();
        $this->setProperty($project, 'id', 1);

        $template = new Template($project);
        $this->setProperty($template, 'id', 2);

        new TemplateRolePermission($template, SystemRole::AUTHOR, 'bar');
    }

    /**
     * @covers ::getTemplate
     */
    public function testTemplate(): void
    {
        $template = new Template(new Project());

        $permission = new TemplateRolePermission($template, SystemRole::AUTHOR, TemplatePermission::EDIT_ISSUES);
        self::assertSame($template, $permission->getTemplate());
    }

    /**
     * @covers ::getRole
     */
    public function testRole(): void
    {
        $template = new Template(new Project());

        $permission = new TemplateRolePermission($template, SystemRole::AUTHOR, TemplatePermission::EDIT_ISSUES);
        self::assertSame(SystemRole::AUTHOR, $permission->getRole());
    }

    /**
     * @covers ::getPermission
     */
    public function testPermission(): void
    {
        $template = new Template(new Project());

        $permission = new TemplateRolePermission($template, SystemRole::AUTHOR, TemplatePermission::EDIT_ISSUES);
        self::assertSame(TemplatePermission::EDIT_ISSUES, $permission->getPermission());
    }
}
