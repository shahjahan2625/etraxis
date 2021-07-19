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
 * @coversDefaultClass \App\Entity\FieldValue
 */
final class FieldValueTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $state      = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $field      = new Field($state, FieldType::NUMBER);
        $user       = new User();
        $issue      = new Issue($state, $user);
        $event      = new Event(EventType::ISSUE_EDITED, $issue, $user);
        $transition = new Transition($event, $state);

        $fieldValue = new FieldValue($transition, $field, null);

        self::assertSame($transition, $fieldValue->getTransition());
        self::assertSame($field, $fieldValue->getField());
        self::assertNull($fieldValue->getValue());

        $fieldValue = new FieldValue($transition, $field, 1);

        self::assertSame($transition, $fieldValue->getTransition());
        self::assertSame($field, $fieldValue->getField());
        self::assertSame(1, $fieldValue->getValue());
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorException(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Unknown field: foo');

        $template   = new Template(new Project());
        $state      = new State($template, StateType::INTERMEDIATE);
        $user       = new User();
        $issue      = new Issue($state, $user);
        $event      = new Event(EventType::ISSUE_EDITED, $issue, $user);
        $transition = new Transition($event, $state);

        $state2    = new State($template, StateType::FINAL);
        $field     = new Field($state2, FieldType::NUMBER);

        $field->setName('foo');

        new FieldValue($transition, $field, null);
    }

    /**
     * @covers ::getId
     */
    public function testId(): void
    {
        $state      = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $field      = new Field($state, FieldType::NUMBER);
        $user       = new User();
        $issue      = new Issue($state, $user);
        $event      = new Event(EventType::ISSUE_EDITED, $issue, $user);
        $transition = new Transition($event, $state);

        $fieldValue = new FieldValue($transition, $field, null);

        $this->setProperty($fieldValue, 'id', 1);
        self::assertSame(1, $fieldValue->getId());
    }

    /**
     * @covers ::getTransition
     */
    public function testTransition(): void
    {
        $state      = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $field      = new Field($state, FieldType::NUMBER);
        $user       = new User();
        $issue      = new Issue($state, $user);
        $event      = new Event(EventType::ISSUE_EDITED, $issue, $user);
        $transition = new Transition($event, $state);

        $fieldValue = new FieldValue($transition, $field, null);
        self::assertSame($transition, $fieldValue->getTransition());
    }

    /**
     * @covers ::getField
     */
    public function testField(): void
    {
        $state      = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $field      = new Field($state, FieldType::NUMBER);
        $user       = new User();
        $issue      = new Issue($state, $user);
        $event      = new Event(EventType::ISSUE_EDITED, $issue, $user);
        $transition = new Transition($event, $state);

        $fieldValue = new FieldValue($transition, $field, null);
        self::assertSame($field, $fieldValue->getField());
    }

    /**
     * @covers ::getValue
     */
    public function testValue(): void
    {
        $state      = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $field      = new Field($state, FieldType::NUMBER);
        $user       = new User();
        $issue      = new Issue($state, $user);
        $event      = new Event(EventType::ISSUE_EDITED, $issue, $user);
        $transition = new Transition($event, $state);

        $fieldValue = new FieldValue($transition, $field, null);
        self::assertNull($fieldValue->getValue());

        $fieldValue = new FieldValue($transition, $field, 1);
        self::assertSame(1, $fieldValue->getValue());
    }
}
