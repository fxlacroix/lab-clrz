<?php

namespace App\Entity;

use App\Entity\Interface\SplitterInterface;
use App\Repository\MemberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Interface\MappingInterface;

#[ORM\Entity(repositoryClass: MemberRepository::class)]
class Member implements SplitterInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'integer')]
    private $age;

    #[ORM\Column(type: 'string', length: 255)]
    private $secretIdentity;

    #[ORM\OneToMany(mappedBy: 'member', targetEntity: Power::class)]
    private $powers;

    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'members')]
    private $team;

    public function __construct()
    {
        $this->powers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getSecretIdentity(): ?string
    {
        return $this->secretIdentity;
    }

    public function setSecretIdentity(string $secretIdentity): self
    {
        $this->secretIdentity = $secretIdentity;

        return $this;
    }

    /**
     * @return Collection<int, Power>
     */
    public function getPowers(): Collection
    {
        return $this->powers;
    }

    public function addPower(Power $power): self
    {
        if(is_array($power)) {
            $power = (Object) $power;
        }

        if (!$this->powers->contains($power)) {
            $this->powers[] = $power;
            $power->setMember($this);
        }

        return $this;
    }

    public function removePower(Power $power): self
    {
        if ($this->powers->removeElement($power)) {
            // set the owning side to null (unless already changed)
            if ($power->getMember() === $this) {
                $power->setMember(null);
            }
        }

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getAveragePowerStrengh(): float {

        if($this->powers->count() == 0) {
            return 0;
        }

        $powers = array_map(function($p) {
            return $p->getStrengh();
        }, $this->powers->toArray());
        return array_sum($powers) / count($powers);
    }

    public function listPowers(): array {
        $powers = [];
        foreach($this->powers as $key => $power) {

            if(! in_array($power->getPowerCode(), array_keys(Power::TRANS))) {
                //throw new \InvalidArgumentException("Invalid power can't be found in translation table.");
            }

            $powers[sprintf("Power%s", $key)] = Power::TRANS[$power->getPowerCode()] ?? $power->getPowerCode();
        }

        return $powers;
    }

    public function getSummary(): array {

        $partMember = [
            "Squad name"                => $this->team->getSquadName(),
            "Home Town"                 => $this->team->getHomeTown(),
            "Name"                      => $this->name,
            "Secret ID"                 => $this->secretIdentity,
            "Age"                       => $this->age,
            "Number of Power"           => $this->powers->count(),
            "Average strengh of member" => $this->getAveragePowerStrengh()
        ];

        $partPower = $this->listPowers();

        return [...$partMember, ...$partPower];
    }
}
