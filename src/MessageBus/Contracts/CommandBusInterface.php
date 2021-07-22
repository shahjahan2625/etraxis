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

namespace App\MessageBus\Contracts;

use Symfony\Component\Messenger\Envelope;

/**
 * Command bus interface.
 */
interface CommandBusInterface
{
    /**
     * Handles the given command.
     *
     * @param Envelope|object $command The command or the command pre-wrapped in an envelope
     */
    public function handle($command): void;
}
