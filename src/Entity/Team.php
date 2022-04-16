<?php

namespace App\Entity;

use App\Entity\Interface\SplitterInterface;
use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team implements SplitterInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $squadName;

    #[ORM\Column(type: 'string', length: 255)]
    private $homeTown;

    #[ORM\Column(type: 'integer')]
    private $formed;

    #[ORM\Column(type: 'string', length: 255)]
    private $secretBase;

    #[ORM\Column(type: 'boolean')]
    private $active;

    #[ORM\OneToMany(mappedBy: 'team', targetEntity: Member::class)]
    private $members;

    public function __construct()
    {
        $this->members = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSquadName(): ?string
    {
        return $this->squadName;
    }

    public function setSquadName(string $squadName): self
    {
        $this->squadName = $squadName;

        return $this;
    }

    public function getHomeTown(): ?string
    {
        return $this->homeTown;
    }

    public function setHomeTown(string $homeTown): self
    {
        $this->homeTown = $homeTown;

        return $this;
    }

    public function getFormed(): ?int
    {
        return $this->formed;
    }

    public function setFormed(int $formed): self
    {
        $this->formed = $formed;

        return $this;
    }

    public function getSecretBase(): ?string
    {
        return $this->secretBase;
    }

    public function setSecretBase(string $secretBase): self
    {
        $this->secretBase = $secretBase;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection<int, Member>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember($member): self
    {
        if(is_array($member)) {
            $member = (Object) $member;
        }

        if (!$this->members->contains($member)) {
            $this->members[] = $member;
            $member->setTeam($this);
        }

        return $this;
    }

    public function setMembers($members): self
    {
        $this->members = $members;
        return $this;
    }
    public function removeMember(Member $member): self
    {
        if ($this->members->removeElement($member)) {
            // set the owning side to null (unless already changed)
            if ($member->getTeam() === $this) {
                $member->setTeam(null);
            }
        }

        return $this;
    }

    public function getAverageMemberAge(): float
    {
        $ages =   array_map(function($m) {return $m->getAge();}, $this->members->toArray());
        return array_sum($ages) / count($ages);
    }

    public function getAverageMemberStrengh(): float
    {
        $strenghs = array_map(function($m) {return $m->getAveragePowerStrengh();}, $this->members->toArray());
        return array_sum($strenghs) / count($strenghs);
    }

    public function getSummary(): array
    {
        return [
            'Squad Name'                =>  $this->squadName,
            'HomeTown'                  =>  $this->homeTown,
            'Formed Year'               =>  $this->formed,
            'Base'                      =>  $this->secretBase,
            'Number of members'         =>  $this->members->count(),
            'Average Age'               =>  $this->getAverageMemberAge(),
            'Average strengh of team'   =>  $this->getAverageMemberStrengh(),
            'Is Active'                 =>  $this->active

        ];
    }
}
