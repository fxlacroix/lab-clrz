<?php

namespace App\Entity;

use App\Repository\PowerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PowerRepository::class)]
class Power
{
    CONST TRANS = [
        'RR'    => 'Radiation resistance',
        'TT'    => 'Turning tiny',
        'TB'    => 'Radiation blast',
        'MTP'   => 'Million tonne punch',
        'DR'    => 'Damage resistance',
        'SR'    => 'Superhuman reflexes',
        'IM'    => 'Immortality',
        'HI'    => 'Heat Immunity',
        'IF'    => 'Inferno',
        'TEL'   => 'Teleportation',
        'IT'    => 'Interdimensional travel',
        'CC'    => 'Cheese Control',
        'DRF'   => 'Drink really fast',
        'HSW'   => 'Hyper slowing writer',
        'AL'    => 'Always late',
        'J2F'   => 'Jump 2 feets up',
        'NSJ'   => 'Never stop jumping',
        'CAL'   => 'Cry a lot',
        'STC'   => 'Sing to charm',
        'IG'    => 'Infernal groove',
        'BAD'   => 'Burn all dancfloors',
        'M'     => 'Mortality',
        'INV'   => 'Invisibility'
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 10)]
    private $powerCode;

    #[ORM\Column(type: 'integer')]
    private $strengh;

    #[ORM\ManyToOne(targetEntity: Member::class, inversedBy: 'powers')]
    private $member;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPowerCode(): ?string
    {
        return $this->powerCode;
    }

    public function setPowerCode(string $powerCode): self
    {
        $this->powerCode = $powerCode;

        return $this;
    }

    public function getStrengh(): ?int
    {
        return $this->strengh;
    }

    public function setStrengh(int $strengh): self
    {
        $this->strengh = $strengh;

        return $this;
    }

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function setMember(?Member $member): self
    {
        $this->member = $member;

        return $this;
    }
}
