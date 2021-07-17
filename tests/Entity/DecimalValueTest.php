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

use App\ReflectionTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversDefaultClass \App\Entity\DecimalValue
 */
final class DecimalValueTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $expected = '1234567890.0987654321';
        $decimal  = new DecimalValue($expected);

        self::assertSame($expected, $decimal->getValue());
    }

    /**
     * @covers ::getId
     */
    public function testId(): void
    {
        $expected = '1234567890.0987654321';
        $decimal  = new DecimalValue($expected);

        $this->setProperty($decimal, 'id', 1);
        self::assertSame(1, $decimal->getId());
    }

    /**
     * @covers ::getValue
     */
    public function testValue(): void
    {
        $expected = '1234567890.0987654321';
        $decimal  = new DecimalValue($expected);

        self::assertSame($expected, $decimal->getValue());
    }

    /**
     * @covers ::trim
     */
    public function testTrim(): void
    {
        $decimal = new DecimalValue('0100');
        self::assertSame('100', $decimal->getValue());

        $decimal = new DecimalValue('03.1415000000');
        self::assertSame('3.1415', $decimal->getValue());

        $decimal = new DecimalValue('00.1415000000');
        self::assertSame('0.1415', $decimal->getValue());

        $decimal = new DecimalValue('03.0000000000');
        self::assertSame('3', $decimal->getValue());

        $decimal = new DecimalValue('00.0000000000');
        self::assertSame('0', $decimal->getValue());
    }
}
