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
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversDefaultClass \App\Entity\Field
 */
final class FieldTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);

        $field = new Field($state, FieldType::LIST);

        self::assertSame($state, $field->getState());
        self::assertSame(FieldType::LIST, $field->getType());
        self::assertEmpty($field->getRolePermissions());
        self::assertEmpty($field->getGroupPermissions());
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorException(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Unknown field type: foo');

        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);

        new Field($state, 'foo');
    }

    /**
     * @covers ::getId
     */
    public function testId(): void
    {
        $field = new Field(new State(new Template(new Project()), StateType::INTERMEDIATE), FieldType::LIST);

        $this->setProperty($field, 'id', 1);
        self::assertSame(1, $field->getId());
    }

    /**
     * @covers ::getState
     */
    public function testState(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);

        $field = new Field($state, FieldType::LIST);
        self::assertSame($state, $field->getState());
    }

    /**
     * @covers ::getName
     * @covers ::setName
     */
    public function testName(): void
    {
        $field = new Field(new State(new Template(new Project()), StateType::INTERMEDIATE), FieldType::LIST);

        $field->setName('Priority');
        self::assertSame('Priority', $field->getName());
    }

    /**
     * @covers ::getType
     */
    public function testType(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);

        $field = new Field($state, FieldType::LIST);
        self::assertSame(FieldType::LIST, $field->getType());
    }

    /**
     * @covers ::getDescription
     * @covers ::setDescription
     */
    public function testDescription(): void
    {
        $field = new Field(new State(new Template(new Project()), StateType::INTERMEDIATE), FieldType::LIST);
        self::assertNull($field->getDescription());

        $field->setDescription('Lorem Ipsum');
        self::assertSame('Lorem Ipsum', $field->getDescription());
    }

    /**
     * @covers ::getPosition
     * @covers ::setPosition
     */
    public function testPosition(): void
    {
        $field = new Field(new State(new Template(new Project()), StateType::INTERMEDIATE), FieldType::LIST);

        $field->setPosition(1);
        self::assertSame(1, $field->getPosition());
    }

    /**
     * @covers ::isRequired
     * @covers ::setRequired
     */
    public function testRequired(): void
    {
        $field = new Field(new State(new Template(new Project()), StateType::INTERMEDIATE), FieldType::LIST);

        $field->setRequired(false);
        self::assertFalse($field->isRequired());

        $field->setRequired(true);
        self::assertTrue($field->isRequired());
    }

    /**
     * @covers ::isRemoved
     * @covers ::remove
     */
    public function testRemovedAt(): void
    {
        $field = new Field(new State(new Template(new Project()), StateType::INTERMEDIATE), FieldType::LIST);
        self::assertFalse($field->isRemoved());

        $field->remove();
        self::assertTrue($field->isRemoved());
    }

    /**
     * @covers ::getParameter
     * @covers ::setParameter
     */
    public function testParameters(): void
    {
        $expected = [
            FieldType::CHECKBOX => [Field::DEFAULT => true,  Field::LENGTH => false, Field::MINIMUM => false, Field::MAXIMUM => false],
            FieldType::DATE     => [Field::DEFAULT => true,  Field::LENGTH => false, Field::MINIMUM => true,  Field::MAXIMUM => true],
            FieldType::DECIMAL  => [Field::DEFAULT => true,  Field::LENGTH => false, Field::MINIMUM => true,  Field::MAXIMUM => true],
            FieldType::DURATION => [Field::DEFAULT => true,  Field::LENGTH => false, Field::MINIMUM => true,  Field::MAXIMUM => true],
            FieldType::ISSUE    => [Field::DEFAULT => false, Field::LENGTH => false, Field::MINIMUM => false, Field::MAXIMUM => false],
            FieldType::LIST     => [Field::DEFAULT => true,  Field::LENGTH => false, Field::MINIMUM => false, Field::MAXIMUM => false],
            FieldType::NUMBER   => [Field::DEFAULT => true,  Field::LENGTH => false, Field::MINIMUM => true,  Field::MAXIMUM => true],
            FieldType::STRING   => [Field::DEFAULT => true,  Field::LENGTH => true,  Field::MINIMUM => false, Field::MAXIMUM => false],
            FieldType::TEXT     => [Field::DEFAULT => true,  Field::LENGTH => true,  Field::MINIMUM => false, Field::MAXIMUM => false],
        ];

        foreach ($expected as $type => $parameters) {

            $field = new Field(new State(new Template(new Project()), StateType::INTERMEDIATE), $type);

            foreach ($parameters as $parameter => $supported) {

                if ($supported) {
                    self::assertNull($field->getParameter($parameter));

                    $field->setParameter($parameter, 1);
                    self::assertSame(1, $field->getParameter($parameter));

                    $field->setParameter($parameter, null);
                    self::assertNull($field->getParameter($parameter));
                }
                else {
                    try {
                        $field->getParameter($parameter);

                        throw new Exception(sprintf('Parameter should not be readable for %s: %s', $type, $parameter));
                    }
                    catch (\Exception $exception) {
                        self::assertInstanceOf(\UnexpectedValueException::class, $exception);
                        self::assertSame(sprintf('Unsupported parameter for %s field: %s', $type, $parameter), $exception->getMessage());
                    }

                    try {
                        $field->setParameter($parameter, 1);

                        throw new Exception(sprintf('Parameter should not be writeable for %s: %s', $type, $parameter));
                    }
                    catch (\Exception $exception) {
                        self::assertInstanceOf(\UnexpectedValueException::class, $exception);
                        self::assertSame(sprintf('Unsupported parameter for %s field: %s', $type, $parameter), $exception->getMessage());
                    }
                }
            }
        }
    }

    /**
     * @covers ::getRolePermissions
     */
    public function testRolePermissions(): void
    {
        $field = new Field(new State(new Template(new Project()), StateType::INTERMEDIATE), FieldType::LIST);
        self::assertEmpty($field->getRolePermissions());

        /** @var \Doctrine\Common\Collections\Collection $permissions */
        $permissions = $this->getProperty($field, 'rolePermissions');
        $permissions->add('Permission A');
        $permissions->add('Permission B');

        self::assertSame(['Permission A', 'Permission B'], $field->getRolePermissions()->getValues());
    }

    /**
     * @covers ::getGroupPermissions
     */
    public function testGroupPermissions(): void
    {
        $field = new Field(new State(new Template(new Project()), StateType::INTERMEDIATE), FieldType::LIST);
        self::assertEmpty($field->getGroupPermissions());

        /** @var \Doctrine\Common\Collections\Collection $permissions */
        $permissions = $this->getProperty($field, 'groupPermissions');
        $permissions->add('Permission A');
        $permissions->add('Permission B');

        self::assertSame(['Permission A', 'Permission B'], $field->getGroupPermissions()->getValues());
    }
}
