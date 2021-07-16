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

use App\Dictionary\TemplatePermission;
use App\ReflectionTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversDefaultClass \App\Entity\TemplateGroupPermission
 */
final class TemplateGroupPermissionTest extends TestCase
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

        $group = new Group($project);
        $this->setProperty($group, 'id', 3);

        $permission = new TemplateGroupPermission($template, $group, TemplatePermission::EDIT_ISSUES);
        self::assertSame($template, $permission->getTemplate());
        self::assertSame($group, $permission->getGroup());
        self::assertSame(TemplatePermission::EDIT_ISSUES, $permission->getPermission());
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorExceptionGroup(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Unknown group: foo');

        $project1 = new Project();
        $this->setProperty($project1, 'id', 1);

        $project2 = new Project();
        $this->setProperty($project2, 'id', 2);

        $template = new Template($project1);
        $this->setProperty($template, 'id', 3);

        $group = new Group($project2);
        $this->setProperty($group, 'id', 4);
        $group->setName('foo');

        new TemplateGroupPermission($template, $group, TemplatePermission::EDIT_ISSUES);
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

        $group = new Group($project);
        $this->setProperty($group, 'id', 3);

        new TemplateGroupPermission($template, $group, 'bar');
    }

    /**
     * @covers ::getTemplate
     */
    public function testTemplate(): void
    {
        $project  = new Project();
        $template = new Template($project);
        $group    = new Group($project);

        $permission = new TemplateGroupPermission($template, $group, TemplatePermission::EDIT_ISSUES);
        self::assertSame($template, $permission->getTemplate());
    }

    /**
     * @covers ::getGroup
     */
    public function testGroup(): void
    {
        $project  = new Project();
        $template = new Template($project);
        $group    = new Group($project);

        $permission = new TemplateGroupPermission($template, $group, TemplatePermission::EDIT_ISSUES);
        self::assertSame($group, $permission->getGroup());
    }

    /**
     * @covers ::getPermission
     */
    public function testPermission(): void
    {
        $project  = new Project();
        $template = new Template($project);
        $group    = new Group($project);

        $permission = new TemplateGroupPermission($template, $group, TemplatePermission::EDIT_ISSUES);
        self::assertSame(TemplatePermission::EDIT_ISSUES, $permission->getPermission());
    }
}
