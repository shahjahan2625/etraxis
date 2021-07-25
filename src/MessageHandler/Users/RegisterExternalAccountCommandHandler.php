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

namespace App\MessageHandler\Users;

use App\Entity\User;
use App\Message\Users\RegisterExternalAccountCommand;
use App\MessageBus\Contracts\CommandHandlerInterface;
use App\Repository\Contracts\UserRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Command handler.
 */
final class RegisterExternalAccountCommandHandler implements CommandHandlerInterface
{
    private LoggerInterface         $logger;
    private UserRepositoryInterface $repository;
    private string                  $locale;

    /**
     * @codeCoverageIgnore Dependency Injection constructor
     */
    public function __construct(LoggerInterface $logger, UserRepositoryInterface $repository, string $locale)
    {
        $this->logger     = $logger;
        $this->repository = $repository;
        $this->locale     = $locale;
    }

    /**
     * Handles the given command.
     */
    public function __invoke(RegisterExternalAccountCommand $command): void
    {
        /** @var User $user */
        $user = $this->repository->findOneByProviderUid($command->getProvider(), $command->getUid());

        // If we can't find the account by its UID, try to find by the email.
        if ($user === null) {
            $this->logger->info('Cannot find by UID.', [
                'provider' => $command->getProvider(),
                'uid'      => $command->getUid(),
            ]);

            $user = $this->repository->findOneByEmail($command->getEmail());
        }

        // Register new account.
        if ($user === null) {
            $this->logger->info('Register external account.', [
                'email'    => $command->getEmail(),
                'fullname' => $command->getFullname(),
            ]);

            $user = new User();

            $user->setLocale($this->locale);
        }
        // The account already exists - update it.
        else {
            $this->logger->info('Update external account.', [
                'email'    => $command->getEmail(),
                'fullname' => $command->getFullname(),
            ]);
        }

        $user
            ->setEmail($command->getEmail())
            ->setPassword(null)
            ->setFullname($command->getFullname())
            ->setAccountProvider($command->getProvider())
            ->setAccountUid($command->getUid())
        ;

        $this->repository->persist($user);
    }
}
