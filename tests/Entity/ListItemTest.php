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

use App\Dictionary\FieldType;
use App\Dictionary\StateType;
use App\ReflectionTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversDefaultClass \App\Entity\ListItem
 */
final class ListItemTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $field = new Field($state, FieldType::LIST);

        $item = new ListItem($field);
        self::assertSame($field, $item->getField());
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorException(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Invalid field type: number');

        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $field = new Field($state, FieldType::NUMBER);

        new ListItem($field);
    }

    /**
     * @covers ::getId
     */
    public function testId(): void
    {
        $field = new Field(new State(new Template(new Project()), StateType::INTERMEDIATE), FieldType::LIST);
        $item  = new ListItem($field);

        $this->setProperty($item, 'id', 1);
        self::assertSame(1, $item->getId());
    }

    /**
     * @covers ::getField
     */
    public function testField(): void
    {
        $field = new Field(new State(new Template(new Project()), StateType::INTERMEDIATE), FieldType::LIST);
        $item  = new ListItem($field);

        self::assertSame($field, $item->getField());
    }

    /**
     * @covers ::getValue
     * @covers ::setValue
     */
    public function testValue(): void
    {
        $field = new Field(new State(new Template(new Project()), StateType::INTERMEDIATE), FieldType::LIST);
        $item  = new ListItem($field);

        $item->setValue(7);
        self::assertSame(7, $item->getValue());
    }

    /**
     * @covers ::getText
     * @covers ::setText
     */
    public function testText(): void
    {
        $field = new Field(new State(new Template(new Project()), StateType::INTERMEDIATE), FieldType::LIST);
        $item  = new ListItem($field);

        $item->setText('July');
        self::assertSame('July', $item->getText());
    }
}
