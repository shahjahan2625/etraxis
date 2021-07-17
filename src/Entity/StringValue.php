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

use App\Repository\StringValueRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * String value.
 *
 * @ORM\Entity(repositoryClass=StringValueRepository::class)
 * @ORM\Table(name="string_values")
 */
class StringValue
{
    // Constraints.
    public const MAX_VALUE = 250;

    /**
     * Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * Value hash.
     *
     * @ORM\Column(name="token", type="string", length=32, unique=true)
     */
    protected string $token;

    /**
     * String value.
     *
     * @ORM\Column(name="value", type="string", length=250)
     */
    protected string $value;

    /**
     * Creates new string value.
     */
    public function __construct(string $value)
    {
        $this->token = md5($value);
        $this->value = $value;
    }

    /**
     * Property getter.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Property getter.
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
