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
use App\Dictionary\FieldType;
use App\Dictionary\StateType;
use App\ReflectionTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversDefaultClass \App\Entity\Change
 */
final class ChangeTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $field = new Field($state, FieldType::NUMBER);
        $user  = new User();
        $issue = new Issue($state, $user);
        $event = new Event(EventType::ISSUE_EDITED, $issue, $user);

        $change = new Change($event, $field, 1, null);

        self::assertSame($event, $change->getEvent());
        self::assertSame($field, $change->getField());
        self::assertSame(1, $change->getOldValue());
        self::assertNull($change->getNewValue());

        $change = new Change($event, null, null, 1);

        self::assertSame($event, $change->getEvent());
        self::assertNull($change->getField());
        self::assertNull($change->getOldValue());
        self::assertSame(1, $change->getNewValue());
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorException(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Unknown field: foo');

        $project  = new Project();
        $template = new Template($project);
        $state    = new State($template, StateType::INTERMEDIATE);
        $user     = new User();
        $issue    = new Issue($state, $user);
        $event    = new Event(EventType::ISSUE_EDITED, $issue, $user);

        $template2 = new Template($project);
        $state2    = new State($template2, StateType::FINAL);
        $field     = new Field($state2, FieldType::NUMBER);

        $field->setName('foo');

        new Change($event, $field, 1, null);
    }

    /**
     * @covers ::getId
     */
    public function testId(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $field = new Field($state, FieldType::NUMBER);
        $user  = new User();
        $issue = new Issue($state, $user);
        $event = new Event(EventType::ISSUE_EDITED, $issue, $user);

        $change = new Change($event, $field, 1, null);

        $this->setProperty($change, 'id', 1);
        self::assertSame(1, $change->getId());
    }

    /**
     * @covers ::getEvent
     */
    public function testEvent(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $field = new Field($state, FieldType::NUMBER);
        $user  = new User();
        $issue = new Issue($state, $user);
        $event = new Event(EventType::ISSUE_EDITED, $issue, $user);

        $change = new Change($event, $field, 1, null);
        self::assertSame($event, $change->getEvent());
    }

    /**
     * @covers ::getField
     */
    public function testField(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $field = new Field($state, FieldType::NUMBER);
        $user  = new User();
        $issue = new Issue($state, $user);
        $event = new Event(EventType::ISSUE_EDITED, $issue, $user);

        $change = new Change($event, $field, 1, null);
        self::assertSame($field, $change->getField());

        $change = new Change($event, null, 1, null);
        self::assertNull($change->getField());
    }

    /**
     * @covers ::getOldValue
     */
    public function testOldValue(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $field = new Field($state, FieldType::NUMBER);
        $user  = new User();
        $issue = new Issue($state, $user);
        $event = new Event(EventType::ISSUE_EDITED, $issue, $user);

        $change = new Change($event, $field, 1, null);
        self::assertSame(1, $change->getOldValue());

        $change = new Change($event, $field, null, 1);
        self::assertNull($change->getOldValue());
    }

    /**
     * @covers ::getNewValue
     */
    public function testNewValue(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $field = new Field($state, FieldType::NUMBER);
        $user  = new User();
        $issue = new Issue($state, $user);
        $event = new Event(EventType::ISSUE_EDITED, $issue, $user);

        $change = new Change($event, $field, 1, null);
        self::assertNull($change->getNewValue());

        $change = new Change($event, $field, null, 1);
        self::assertSame(1, $change->getNewValue());
    }
}
