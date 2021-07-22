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

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * Marker interface for event handlers.
 */
interface EventHandlerInterface extends MessageHandlerInterface
{
}
