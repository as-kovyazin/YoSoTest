<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $apiKey;

    /**
     * @ORM\OneToMany(targetEntity=Briefcase::class, mappedBy="userId")
     */
    private $briefcases;

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
            $this->briefcases[] = $briefcase;
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

    public function getRoles(): array
    {
        // TODO: Implement getRoles() method.
        return [];
    }

    public function getPassword(): ?string
    {
        // TODO: Implement getPassword() method.
        return null;
    }

    public function getSalt(): ?string
    {
        // TODO: Implement getSalt() method.
        return null;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUsername(): string
    {
        // TODO: Implement getUsername() method.
        return "";
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement @method string getUserIdentifier()
    }

    public function getUserIdentifier(): string
    {
        return $this->apiKey;
    }
}
