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

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

/**
 * Event bus.
 */
class EventBus implements Contracts\EventBusInterface
{
    protected MessageBusInterface $eventBus;

    /**
     * @codeCoverageIgnore Dependency Injection constructor
     */
    public function __construct(MessageBusInterface $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    /**
     * {@inheritDoc}
     */
    public function send($event): void
    {
        $stamp   = new DispatchAfterCurrentBusStamp();
        $message = new Envelope($event, [$stamp]);

        $this->eventBus->dispatch($message);
    }

    /**
     * {@inheritDoc}
     */
    public function sendAsync($event): void
    {
        $this->eventBus->dispatch($event);
    }
}
