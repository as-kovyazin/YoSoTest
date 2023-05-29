<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $apiKey = null;

    #[ORM\OneToMany(mappedBy: 'userId', targetEntity: Briefcase::class)]
    private Collection $briefcases;

    public function __construct()
    {
        $this->briefcases = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * @return Collection<int, Briefcase>
     */
    public function getBriefcases(): Collection
    {
        return $this->briefcases;
    }

    public function addBriefcase(Briefcase $briefcase): self
    {
        if (!$this->briefcases->contains($briefcase)) {
            $this->briefcases->add($briefcase);
            $briefcase->setUserId($this);
        }

        return $this;
    }

    public function removeBriefcase(Briefcase $briefcase): self
    {
        if ($this->briefcases->removeElement($briefcase)) {
            // set the owning side to null (unless already changed)
            if ($briefcase->getUserId() === $this) {
                $briefcase->setUserId(null);
            }
        }

        return $this;
    }
}
