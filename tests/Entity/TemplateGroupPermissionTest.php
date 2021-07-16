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
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversDefaultClass \App\Entity\TemplateGroupPermission
 */
final class TemplateGroupPermissionTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $project  = new Project();
        $template = new Template($project);
        $group    = new Group($project);

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
        $project2 = new Project();
        $template = new Template($project1);

        $group = new Group($project2);
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

        $project  = new Project();
        $template = new Template($project);
        $group    = new Group($project);

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
