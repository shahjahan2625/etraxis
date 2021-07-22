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

namespace App\MessageBus;

use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Command bus.
 */
class CommandBus implements Contracts\CommandBusInterface
{
    protected MessageBusInterface $commandBus;

    /**
     * @codeCoverageIgnore Dependency Injection constructor
     */
    public function __construct(MessageBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * {@inheritDoc}
     */
    public function handle($command): void
    {
        $this->commandBus->dispatch($command);
    }
}
