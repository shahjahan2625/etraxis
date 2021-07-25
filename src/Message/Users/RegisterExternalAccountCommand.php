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

namespace App\Message\Users;

use App\Dictionary\AccountProvider;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Registers new external account, or updates it if already exists.
 */
final class RegisterExternalAccountCommand
{
    /**
     * Email address.
     *
     * @Assert\NotBlank
     * @Assert\Length(max="254")
     * @Assert\Email
     */
    private string $email;

    /**
     * Full name.
     *
     * @Assert\NotBlank
     * @Assert\Length(max="50")
     */
    private string $fullname;

    /**
     * Account provider (see the "AccountProvider" dictionary).
     *
     * @Assert\NotBlank
     * @Assert\Choice(callback={"App\Dictionary\AccountProvider", "keys"}, strict=true)
     */
    private string $provider;

    /**
     * Account UID as in the external provider's system.
     *
     * @Assert\NotBlank
     * @Assert\Length(max="128")
     */
    private string $uid;

    /**
     * Initializes the command.
     */
    public function __construct(string $email, string $fullname, string $provider, string $uid)
    {
        if (!AccountProvider::has($provider)) {
            throw new \UnexpectedValueException('Unknown account provider: ' . $provider);
        }

        if ($provider === AccountProvider::ETRAXIS) {
            throw new \UnexpectedValueException('Invalid account provider: ' . $provider);
        }

        $this->email    = $email;
        $this->fullname = $fullname;
        $this->provider = $provider;
        $this->uid      = $uid;
    }

    /**
     * Property getter.
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Property getter.
     */
    public function getFullname(): string
    {
        return $this->fullname;
    }

    /**
     * Property getter.
     */
    public function getProvider(): string
    {
        return $this->provider;
    }

    /**
     * Property getter.
     */
    public function getUid(): string
    {
        return $this->uid;
    }
}
