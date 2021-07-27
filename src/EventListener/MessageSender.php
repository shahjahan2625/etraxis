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

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Message;

/**
 * Sets sender info for each outgoing email.
 */
class MessageSender implements EventSubscriberInterface
{
    protected string $sender;

    /**
     * @codeCoverageIgnore Dependency Injection constructor
     */
    public function __construct(string $sender)
    {
        $this->sender = $sender;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            MessageEvent::class => 'onMessage',
        ];
    }

    /**
     * Sets sender info for each outgoing email.
     */
    public function onMessage(MessageEvent $event): void
    {
        $message = $event->getMessage();

        if (!$message instanceof Message) {
            return;
        }

        $headers = $message->getHeaders();

        if (!$headers->has('from')) {
            $headers->addMailboxListHeader('from', [new Address($this->sender, 'eTraxis')]);
        }

        if (!$headers->has('reply-to')) {
            $from = $headers->get('from')->getBody();
            $headers->addMailboxListHeader('reply-to', $from);
        }

        $message->setHeaders($headers);
    }
}
