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

use Dictionary\StaticDictionary;

/**
 * Timezones.
 */
class Timezone extends StaticDictionary
{
    public const FALLBACK = 'UTC';

    /**
     * {@inheritDoc}
     */
    protected static function dictionary(): array
    {
        $timezones = timezone_identifiers_list();

        return $timezones !== false ? array_combine($timezones, $timezones) : [];
    }
}
