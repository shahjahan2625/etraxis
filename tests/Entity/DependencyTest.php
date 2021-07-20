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

use App\Dictionary\EventType;
use App\Dictionary\StateType;
use App\ReflectionTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversDefaultClass \App\Entity\Dependency
 */
final class DependencyTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $state  = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user   = new User();
        $issue  = new Issue($state, $user);
        $issue2 = new Issue($state, $user);
        $event  = new Event(EventType::DEPENDENCY_ADDED, $issue, $user);

        $dependency = new Dependency($event, $issue2);

        self::assertSame($event, $dependency->getEvent());
        self::assertSame($issue2, $dependency->getIssue());
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorException(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Invalid event: issue.edited');

        $state  = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user   = new User();
        $issue  = new Issue($state, $user);
        $issue2 = new Issue($state, $user);
        $event  = new Event(EventType::ISSUE_EDITED, $issue, $user);

        new Dependency($event, $issue2);
    }

    /**
     * @covers ::getId
     */
    public function testId(): void
    {
        $state  = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user   = new User();
        $issue  = new Issue($state, $user);
        $issue2 = new Issue($state, $user);
        $event  = new Event(EventType::DEPENDENCY_ADDED, $issue, $user);

        $dependency = new Dependency($event, $issue2);

        $this->setProperty($dependency, 'id', 1);
        self::assertSame(1, $dependency->getId());
    }

    /**
     * @covers ::getEvent
     */
    public function testEvent(): void
    {
        $state  = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user   = new User();
        $issue  = new Issue($state, $user);
        $issue2 = new Issue($state, $user);
        $event  = new Event(EventType::DEPENDENCY_ADDED, $issue, $user);

        $dependency = new Dependency($event, $issue2);

        self::assertSame($event, $dependency->getEvent());
    }

    /**
     * @covers ::getIssue
     */
    public function testIssue(): void
    {
        $state  = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user   = new User();
        $issue  = new Issue($state, $user);
        $issue2 = new Issue($state, $user);
        $event  = new Event(EventType::DEPENDENCY_ADDED, $issue, $user);

        $dependency = new Dependency($event, $issue2);

        self::assertSame($issue2, $dependency->getIssue());
    }
}
