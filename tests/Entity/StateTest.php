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

use App\Dictionary\StateResponsible;
use App\Dictionary\StateType;
use App\ReflectionTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversDefaultClass \App\Entity\State
 */
final class StateTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $template = new Template(new Project());

        $state = new State($template, StateType::INITIAL);

        self::assertSame($template, $state->getTemplate());
        self::assertSame(StateType::INITIAL, $state->getType());
        self::assertSame(StateResponsible::REMOVE, $state->getResponsible());
        self::assertEmpty($state->getRoleTransitions());
        self::assertEmpty($state->getGroupTransitions());
        self::assertEmpty($state->getResponsibleGroups());
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorException(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Unknown state type: foo');

        $template = new Template(new Project());

        new State($template, 'foo');
    }

    /**
     * @covers ::getId
     */
    public function testId(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);

        $this->setProperty($state, 'id', 1);
        self::assertSame(1, $state->getId());
    }

    /**
     * @covers ::getTemplate
     */
    public function testTemplate(): void
    {
        $template = new Template(new Project());

        $state = new State($template, StateType::INTERMEDIATE);
        self::assertSame($template, $state->getTemplate());
    }

    /**
     * @covers ::getName
     * @covers ::setName
     */
    public function testName(): void
    {
        $state = new State(new Template(new Project()), StateType::INITIAL);

        $state->setName('New');
        self::assertSame('New', $state->getName());
    }

    /**
     * @covers ::getType
     */
    public function testType(): void
    {
        $template = new Template(new Project());

        $initial      = new State($template, StateType::INITIAL);
        $intermediate = new State($template, StateType::INTERMEDIATE);
        $final        = new State($template, StateType::FINAL);

        self::assertSame(StateType::INITIAL, $initial->getType());
        self::assertSame(StateType::INTERMEDIATE, $intermediate->getType());
        self::assertSame(StateType::FINAL, $final->getType());
    }

    /**
     * @covers ::getResponsible
     * @covers ::setResponsible
     */
    public function testResponsible(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        self::assertSame(StateResponsible::REMOVE, $state->getResponsible());

        $state->setResponsible(StateResponsible::ASSIGN);
        self::assertSame(StateResponsible::ASSIGN, $state->getResponsible());

        $state->setResponsible(StateResponsible::KEEP);
        self::assertSame(StateResponsible::KEEP, $state->getResponsible());

        $state->setResponsible(StateResponsible::REMOVE);
        self::assertSame(StateResponsible::REMOVE, $state->getResponsible());
    }

    /**
     * @covers ::getResponsible
     * @covers ::setResponsible
     */
    public function testResponsibleFinal(): void
    {
        $state = new State(new Template(new Project()), StateType::FINAL);

        $state->setResponsible(StateResponsible::ASSIGN);
        self::assertSame(StateResponsible::REMOVE, $state->getResponsible());
    }

    /**
     * @covers ::getResponsible
     * @covers ::setResponsible
     */
    public function testResponsibleException(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Unknown responsibility type: bar');

        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);

        $state->setResponsible('bar');
    }

    /**
     * @covers ::getNextState
     * @covers ::setNextState
     */
    public function testNextState(): void
    {
        $template  = new Template(new Project());
        $nextState = new State($template, StateType::INTERMEDIATE);

        $state = new State($template, StateType::INTERMEDIATE);
        self::assertNull($state->getNextState());

        $state->setNextState($nextState);
        self::assertSame($nextState, $state->getNextState());

        $state->setNextState(null);
        self::assertNull($state->getNextState());
    }

    /**
     * @covers ::getNextState
     * @covers ::setNextState
     */
    public function testNextStateFinal(): void
    {
        $template  = new Template(new Project());
        $nextState = new State($template, StateType::INTERMEDIATE);

        $state = new State($template, StateType::FINAL);
        self::assertNull($state->getNextState());

        $state->setNextState($nextState);
        self::assertNull($state->getNextState());
    }

    /**
     * @covers ::getNextState
     * @covers ::setNextState
     */
    public function testNextStateException(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Unknown state: alien');

        $template1 = new Template(new Project());
        $template2 = new Template(new Project());
        $nextState = new State($template1, StateType::INTERMEDIATE);
        $nextState->setName('alien');

        $state = new State($template2, StateType::INTERMEDIATE);

        $state->setNextState($nextState);
    }

    /**
     * @covers ::getRoleTransitions
     */
    public function testRoleTransitions(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        self::assertEmpty($state->getRoleTransitions());

        /** @var \Doctrine\Common\Collections\Collection $transitions */
        $transitions = $this->getProperty($state, 'roleTransitions');
        $transitions->add('Transition A');
        $transitions->add('Transition B');

        self::assertSame(['Transition A', 'Transition B'], $state->getRoleTransitions()->getValues());
    }

    /**
     * @covers ::getGroupTransitions
     */
    public function testGroupTransitions(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        self::assertEmpty($state->getGroupTransitions());

        /** @var \Doctrine\Common\Collections\Collection $transitions */
        $transitions = $this->getProperty($state, 'groupTransitions');
        $transitions->add('Transition A');
        $transitions->add('Transition B');

        self::assertSame(['Transition A', 'Transition B'], $state->getGroupTransitions()->getValues());
    }

    /**
     * @covers ::getResponsibleGroups
     */
    public function testResponsibleGroups(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        self::assertEmpty($state->getResponsibleGroups());

        /** @var \Doctrine\Common\Collections\Collection $groups */
        $groups = $this->getProperty($state, 'responsibleGroups');
        $groups->add('Group A');
        $groups->add('Group B');

        self::assertSame(['Group A', 'Group B'], $state->getResponsibleGroups()->getValues());
    }

    /**
     * @covers ::isFinal
     */
    public function testIsFinal(): void
    {
        $template = new Template(new Project());

        $initial      = new State($template, StateType::INITIAL);
        $intermediate = new State($template, StateType::INTERMEDIATE);
        $final        = new State($template, StateType::FINAL);

        self::assertFalse($initial->isFinal());
        self::assertFalse($intermediate->isFinal());
        self::assertTrue($final->isFinal());
    }
}
