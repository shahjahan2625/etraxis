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
 * @coversDefaultClass \App\Entity\Transition
 */
final class TransitionTest extends TestCase
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
        $event = new Event(EventType::STATE_CHANGED, $issue, $user);

        $transition = new Transition($event, $state);

        self::assertSame($event, $transition->getEvent());
        self::assertSame($state, $transition->getState());
        self::assertEmpty($transition->getValues());
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorExceptionEvent(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Invalid event: issue.edited');

        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);
        $event = new Event(EventType::ISSUE_EDITED, $issue, $user);

        new Transition($event, $state);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorExceptionState(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Unknown state: foo');

        $project  = new Project();
        $template = new Template($project);
        $state    = new State($template, StateType::INTERMEDIATE);
        $user     = new User();
        $issue    = new Issue($state, $user);
        $event    = new Event(EventType::STATE_CHANGED, $issue, $user);

        $template2 = new Template($project);
        $state2    = new State($template2, StateType::FINAL);

        $state2->setName('foo');

        new Transition($event, $state2);
    }

    /**
     * @covers ::getId
     */
    public function testId(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);
        $event = new Event(EventType::STATE_CHANGED, $issue, $user);

        $transition = new Transition($event, $state);

        $this->setProperty($transition, 'id', 1);
        self::assertSame(1, $transition->getId());
    }

    /**
     * @covers ::getEvent
     */
    public function testEvent(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);
        $event = new Event(EventType::STATE_CHANGED, $issue, $user);

        $transition = new Transition($event, $state);
        self::assertSame($event, $transition->getEvent());
    }

    /**
     * @covers ::getState
     */
    public function testState(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);
        $event = new Event(EventType::STATE_CHANGED, $issue, $user);

        $transition = new Transition($event, $state);
        self::assertSame($state, $transition->getState());
    }

    /**
     * @covers ::getValues
     */
    public function testValues(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);
        $event = new Event(EventType::STATE_CHANGED, $issue, $user);

        $transition = new Transition($event, $state);
        self::assertEmpty($transition->getValues());

        /** @var \Doctrine\Common\Collections\Collection $values */
        $values = $this->getProperty($transition, 'values');
        $values->add('Value A');
        $values->add('Value B');

        self::assertSame(['Value A', 'Value B'], $transition->getValues()->getValues());
    }
}
