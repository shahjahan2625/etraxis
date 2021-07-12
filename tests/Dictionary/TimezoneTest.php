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

namespace App\Dictionary;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversDefaultClass \App\Dictionary\Timezone
 */
final class TimezoneTest extends TestCase
{
    /**
     * @covers ::dictionary
     */
    public function testDictionary(): void
    {
        self::assertSame(timezone_identifiers_list(), Timezone::keys());
        self::assertSame(timezone_identifiers_list(), Timezone::values());
    }
}
