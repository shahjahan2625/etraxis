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
 * @coversDefaultClass \App\Entity\Event
 */
final class EventTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);

        $event = new Event(EventType::ISSUE_CREATED, $issue, $user, 'Artem Rodygin');

        self::assertSame(EventType::ISSUE_CREATED, $event->getType());
        self::assertSame($issue, $event->getIssue());
        self::assertSame($user, $event->getUser());
        self::assertSame('Artem Rodygin', $event->getParameter());
    }

    /**
     * @covers ::getId
     */
    public function testId(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);

        $event = new Event(EventType::ISSUE_EDITED, $issue, $user);

        $this->setProperty($event, 'id', 1);
        self::assertSame(1, $event->getId());
    }

    /**
     * @covers ::getType
     */
    public function testType(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);

        $event = new Event(EventType::ISSUE_EDITED, $issue, $user);
        self::assertSame(EventType::ISSUE_EDITED, $event->getType());
    }

    /**
     * @covers ::getIssue
     */
    public function testIssue(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);

        $event = new Event(EventType::ISSUE_EDITED, $issue, $user);
        self::assertSame($issue, $event->getIssue());
    }

    /**
     * @covers ::getUser
     */
    public function testUser(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);

        $event = new Event(EventType::ISSUE_EDITED, $issue, $user);
        self::assertSame($user, $event->getUser());
    }

    /**
     * @covers ::getCreatedAt
     */
    public function testCreatedAt(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);

        $event = new Event(EventType::ISSUE_EDITED, $issue, $user);
        self::assertLessThanOrEqual(2, time() - $event->getCreatedAt());
    }

    /**
     * @covers ::getParameter
     */
    public function testParameter(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);

        $event = new Event(EventType::ISSUE_CREATED, $issue, $user, 'Artem Rodygin');
        self::assertSame('Artem Rodygin', $event->getParameter());

        $event = new Event(EventType::ISSUE_EDITED, $issue, $user);
        self::assertNull($event->getParameter());
    }
}
