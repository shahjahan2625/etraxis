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
 * @coversDefaultClass \App\Entity\File
 */
final class FileTest extends TestCase
{
    use ReflectionTrait;

    private const UUID_PATTERN = '/^([[:xdigit:]]{8}-[[:xdigit:]]{4}-[[:xdigit:]]{4}-[[:xdigit:]]{4}-[[:xdigit:]]{12})$/is';

    /**
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);
        $event = new Event(EventType::FILE_ATTACHED, $issue, $user);

        $file = new File($event, 'example.csv', 2309, 'text/csv');

        self::assertMatchesRegularExpression(self::UUID_PATTERN, $file->getUid());
        self::assertSame($event, $file->getEvent());
        self::assertSame('example.csv', $file->getName());
        self::assertSame(2309, $file->getSize());
        self::assertSame('text/csv', $file->getType());
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorException(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Invalid event: issue.edited');

        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);
        $event = new Event(EventType::ISSUE_EDITED, $issue, $user);

        new File($event, 'example.csv', 2309, 'text/csv');
    }

    /**
     * @covers ::getId
     */
    public function testId(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);
        $event = new Event(EventType::FILE_ATTACHED, $issue, $user);

        $file = new File($event, 'example.csv', 2309, 'text/csv');

        $this->setProperty($file, 'id', 1);
        self::assertSame(1, $file->getId());
    }

    /**
     * @covers ::getEvent
     */
    public function testEvent(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);
        $event = new Event(EventType::FILE_ATTACHED, $issue, $user);

        $file = new File($event, 'example.csv', 2309, 'text/csv');
        self::assertSame($event, $file->getEvent());
    }

    /**
     * @covers ::getUid
     */
    public function testUid(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);
        $event = new Event(EventType::FILE_ATTACHED, $issue, $user);

        $file = new File($event, 'example.csv', 2309, 'text/csv');
        self::assertMatchesRegularExpression(self::UUID_PATTERN, $file->getUid());
    }

    /**
     * @covers ::getName
     */
    public function testName(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);
        $event = new Event(EventType::FILE_ATTACHED, $issue, $user);

        $file = new File($event, 'example.csv', 2309, 'text/csv');
        self::assertSame('example.csv', $file->getName());
    }

    /**
     * @covers ::getSize
     */
    public function testSize(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);
        $event = new Event(EventType::FILE_ATTACHED, $issue, $user);

        $file = new File($event, 'example.csv', 2309, 'text/csv');
        self::assertSame(2309, $file->getSize());
    }

    /**
     * @covers ::getType
     */
    public function testType(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);
        $event = new Event(EventType::FILE_ATTACHED, $issue, $user);

        $file = new File($event, 'example.csv', 2309, 'text/csv');
        self::assertSame('text/csv', $file->getType());
    }

    /**
     * @covers ::isRemoved
     * @covers ::remove
     */
    public function testRemovedAt(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);
        $event = new Event(EventType::FILE_ATTACHED, $issue, $user);

        $file = new File($event, 'example.csv', 2309, 'text/csv');
        self::assertFalse($file->isRemoved());

        $file->remove();
        self::assertTrue($file->isRemoved());
    }
}
