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

use App\Dictionary\StateType;
use App\Dictionary\SystemRole;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversDefaultClass \App\Entity\StateRoleTransition
 */
final class StateRoleTransitionTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $template = new Template(new Project());
        $from     = new State($template, StateType::INITIAL);
        $to       = new State($template, StateType::INTERMEDIATE);

        $transition = new StateRoleTransition($from, $to, SystemRole::AUTHOR);

        self::assertSame($from, $transition->getFromState());
        self::assertSame($to, $transition->getToState());
        self::assertSame(SystemRole::AUTHOR, $transition->getRole());
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorExceptionStates(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('States must belong the same template.');

        $project   = new Project();
        $template1 = new Template($project);
        $template2 = new Template($project);
        $from      = new State($template1, StateType::INITIAL);
        $to        = new State($template2, StateType::INTERMEDIATE);

        new StateRoleTransition($from, $to, 'foo');
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorExceptionRole(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Unknown system role: foo');

        $template = new Template(new Project());
        $from     = new State($template, StateType::INITIAL);
        $to       = new State($template, StateType::INTERMEDIATE);

        new StateRoleTransition($from, $to, 'foo');
    }

    /**
     * @covers ::getFromState
     */
    public function testFromState(): void
    {
        $template = new Template(new Project());
        $from     = new State($template, StateType::INITIAL);
        $to       = new State($template, StateType::INTERMEDIATE);

        $transition = new StateRoleTransition($from, $to, SystemRole::AUTHOR);
        self::assertSame($from, $transition->getFromState());
    }

    /**
     * @covers ::getToState
     */
    public function testToState(): void
    {
        $template = new Template(new Project());
        $from     = new State($template, StateType::INITIAL);
        $to       = new State($template, StateType::INTERMEDIATE);

        $transition = new StateRoleTransition($from, $to, SystemRole::AUTHOR);
        self::assertSame($to, $transition->getToState());
    }

    /**
     * @covers ::getRole
     */
    public function testRole(): void
    {
        $template = new Template(new Project());
        $from     = new State($template, StateType::INITIAL);
        $to       = new State($template, StateType::INTERMEDIATE);

        $transition = new StateRoleTransition($from, $to, SystemRole::AUTHOR);
        self::assertSame(SystemRole::AUTHOR, $transition->getRole());
    }
}
